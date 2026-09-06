<?php

namespace Drupal\course_import_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Site\Settings;
use Drupal\file\FileRepositoryInterface;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * POST API for creating or updating a single "topics" lesson node.
 *
 * Authenticated by a static shared secret (the "X-Api-Key" header), checked
 * against $settings['course_import_api_key'] in settings.local.php /
 * settings.php — never stored in exported config, never in git.
 *
 * This intentionally mirrors the local .import_staging/import_lesson.php and
 * update_lesson.php drush scripts field-for-field, so the same lesson data
 * (title, tutorial, sub_tutorial, skill_level, body HTML with {{IMAGE:token}}
 * placeholders, images, SEO fields, menu title, versions) can be pushed here
 * over HTTP instead of requiring shell access to this site.
 */
class CourseImportController extends ControllerBase {

  /**
   * Maximum accepted upload size per image, in bytes (5 MB).
   */
  private const MAX_IMAGE_BYTES = 5 * 1024 * 1024;

  /**
   * Image MIME types this endpoint will accept.
   */
  private const ALLOWED_IMAGE_MIME_TYPES = [
    'image/png',
    'image/jpeg',
    'image/webp',
    'image/gif',
  ];

  public function __construct(
    private readonly FileRepositoryInterface $fileRepository,
    private readonly FileSystemInterface $fileSystem,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('file.repository'),
      $container->get('file_system'),
    );
  }

  /**
   * POST /api/course-import/topic
   */
  public function importTopic(Request $request): JsonResponse {
    $auth_error = $this->checkApiKey($request);
    if ($auth_error) {
      return $auth_error;
    }

    try {
      $input = $this->parseAndValidateInput($request);
    }
    catch (\InvalidArgumentException $e) {
      return new JsonResponse(['success' => FALSE, 'error' => $e->getMessage()], 422);
    }

    try {
      $tutorial_term = $this->getOrCreateTerm('tutorials', $input['tutorial']);
      $sub_tutorial_term = $this->getOrCreateSubTutorial($input['sub_tutorial'], $tutorial_term);
      $skill_term = $this->loadRequiredTerm('skill_level', $input['skill_level']);
      $version_target_ids = $this->loadRequiredVersionTerms($input['versions']);
    }
    catch (\RuntimeException $e) {
      return new JsonResponse(['success' => FALSE, 'error' => $e->getMessage()], 422);
    }

    try {
      $body_html = $this->resolveImages($request, $input['body_html'], $input['images_meta']);
    }
    catch (\RuntimeException $e) {
      return new JsonResponse(['success' => FALSE, 'error' => $e->getMessage()], 422);
    }

    if (str_contains($body_html, '{{IMAGE:')) {
      return new JsonResponse([
        'success' => FALSE,
        'error' => 'body_html still contains an unresolved {{IMAGE:...}} placeholder — every token used in body_html must have a matching entry in images_meta with a matching uploaded file.',
      ], 422);
    }

    try {
      $existing_nids = $this->entityTypeManager()->getStorage('node')->getQuery()
        ->condition('type', 'topics')
        ->condition('field_tutorial', $tutorial_term->id())
        ->condition('field_lesson_no', $input['lesson_no'])
        ->accessCheck(FALSE)
        ->execute();
    }
    catch (\Exception $e) {
      return new JsonResponse(['success' => FALSE, 'error' => 'Database error while checking for an existing lesson.'], 500);
    }

    if (count($existing_nids) > 1) {
      return new JsonResponse([
        'success' => FALSE,
        'error' => 'Multiple existing nodes found for this tutorial + lesson_no combination: ' . implode(',', $existing_nids) . '. Resolve the duplicate before importing.',
      ], 409);
    }

    $is_update = count($existing_nids) === 1;
    $node = $is_update ? Node::load(reset($existing_nids)) : Node::create(['type' => 'topics']);

    $node->setTitle($input['title']);
    $node->set('field_content', ['value' => $body_html, 'format' => 'full_html']);
    $node->set('field_tutorial', ['target_id' => $tutorial_term->id()]);
    $node->set('field_sub_tutorial_category', ['target_id' => $sub_tutorial_term->id()]);
    $node->set('field_skill_level', ['target_id' => $skill_term->id()]);
    $node->set('field_keywords', $input['keywords']);
    $node->set('field_meta_description', $input['meta_description']);
    $node->set('field_menu_title', $input['menu_title']);
    $node->set('field_lesson_no', $input['lesson_no']);
    $node->set('field_versions', $version_target_ids);
    if ($is_update) {
      $node->setNewRevision(FALSE);
    }
    else {
      $node->setPublished(TRUE);
    }

    try {
      $node->save();
    }
    catch (\Exception $e) {
      $this->getLogger('course_import_api')->error('Failed saving topics node: @message', ['@message' => $e->getMessage()]);
      return new JsonResponse(['success' => FALSE, 'error' => 'Failed to save the node.'], 500);
    }

    return new JsonResponse([
      'success' => TRUE,
      'action' => $is_update ? 'updated' : 'created',
      'node_id' => (int) $node->id(),
      'edit_url' => $node->toUrl('edit-form')->setAbsolute()->toString(),
      'view_url' => $node->toUrl('canonical')->setAbsolute()->toString(),
    ], $is_update ? 200 : 201);
  }

  /**
   * Validates the X-Api-Key header against the configured secret.
   *
   * Uses hash_equals() for a timing-safe comparison, and logs (without ever
   * echoing back) failed attempts so misuse is visible in the site's logs.
   */
  private function checkApiKey(Request $request): ?JsonResponse {
    $configured_key = Settings::get('course_import_api_key');
    if (!is_string($configured_key) || $configured_key === '') {
      $this->getLogger('course_import_api')->error('course_import_api_key is not configured in settings — refusing all requests.');
      return new JsonResponse(['success' => FALSE, 'error' => 'This endpoint is not configured.'], 503);
    }

    $provided_key = $request->headers->get('X-Api-Key', '');
    if ($provided_key === '' || !hash_equals($configured_key, $provided_key)) {
      $this->getLogger('course_import_api')->warning('Rejected course-import request with invalid or missing API key from @ip.', [
        '@ip' => $request->getClientIp() ?? 'unknown',
      ]);
      return new JsonResponse(['success' => FALSE, 'error' => 'Invalid or missing X-Api-Key header.'], 401);
    }

    return NULL;
  }

  /**
   * Reads and validates every required text field from the request.
   *
   * @throws \InvalidArgumentException
   *   If a required field is missing or malformed.
   */
  private function parseAndValidateInput(Request $request): array {
    $required = [
      'title', 'tutorial', 'sub_tutorial', 'skill_level', 'menu_title',
      'keywords', 'meta_description', 'lesson_no', 'versions', 'body_html',
    ];
    $missing = [];
    $values = [];
    foreach ($required as $field) {
      $value = $request->request->get($field);
      if ($value === NULL || $value === '') {
        $missing[] = $field;
        continue;
      }
      $values[$field] = $value;
    }
    if ($missing) {
      throw new \InvalidArgumentException('Missing required field(s): ' . implode(', ', $missing));
    }

    if (!ctype_digit((string) $values['lesson_no']) || (int) $values['lesson_no'] < 1) {
      throw new \InvalidArgumentException('lesson_no must be a positive integer.');
    }
    $values['lesson_no'] = (int) $values['lesson_no'];

    $versions = array_filter(array_map('trim', explode(',', (string) $values['versions'])));
    if (!$versions) {
      throw new \InvalidArgumentException('versions must be a non-empty comma-separated list, e.g. "10,11".');
    }
    $values['versions'] = $versions;

    $images_meta_raw = $request->request->get('images_meta', '[]');
    $images_meta = json_decode((string) $images_meta_raw, TRUE);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($images_meta)) {
      throw new \InvalidArgumentException('images_meta must be a JSON array, e.g. [{"token":"primary","alt":"...","caption":"..."}].');
    }
    foreach ($images_meta as $i => $image) {
      if (!is_array($image) || empty($image['token']) || !is_string($image['token'])) {
        throw new \InvalidArgumentException("images_meta[$i] must be an object with at least a string 'token'.");
      }
    }
    $values['images_meta'] = $images_meta;

    return $values;
  }

  /**
   * Gets or creates a taxonomy term by vid + name (no special scoping).
   */
  private function getOrCreateTerm(string $vid, string $name): Term {
    $terms = $this->entityTypeManager()->getStorage('taxonomy_term')
      ->loadByProperties(['vid' => $vid, 'name' => $name]);
    if ($terms) {
      return reset($terms);
    }
    $term = Term::create(['vid' => $vid, 'name' => $name]);
    $term->save();
    $this->getLogger('course_import_api')->notice('Created taxonomy term (@vid): @name [tid=@tid]', [
      '@vid' => $vid,
      '@name' => $name,
      '@tid' => $term->id(),
    ]);
    return $term;
  }

  /**
   * Gets or creates a Sub Tutorial term scoped to its parent Tutorial term.
   *
   * Mirrors import_lesson.php: a Sub Tutorial name is not globally unique —
   * it's only unique per parent Tutorial — so this searches existing terms
   * with a matching field_parent_tutorial before creating a new one.
   */
  private function getOrCreateSubTutorial(string $name, Term $tutorial_term): Term {
    $candidates = $this->entityTypeManager()->getStorage('taxonomy_term')
      ->loadByProperties(['vid' => 'sub_tutorials', 'name' => $name]);
    foreach ($candidates as $candidate) {
      if ($candidate->hasField('field_parent_tutorial')
        && !$candidate->get('field_parent_tutorial')->isEmpty()
        && $candidate->get('field_parent_tutorial')->target_id == $tutorial_term->id()) {
        return $candidate;
      }
    }
    $term = Term::create([
      'vid' => 'sub_tutorials',
      'name' => $name,
      'field_parent_tutorial' => ['target_id' => $tutorial_term->id()],
    ]);
    $term->save();
    $this->getLogger('course_import_api')->notice('Created sub_tutorial term: @name [tid=@tid]', [
      '@name' => $name,
      '@tid' => $term->id(),
    ]);
    return $term;
  }

  /**
   * Loads a pre-existing term by vid + name — does NOT create one.
   *
   * Skill Level and Versions are treated as a fixed, site-managed vocabulary
   * (matching import_lesson.php's behavior) so a typo in the caller's data
   * fails loudly instead of silently creating a stray new term.
   *
   * @throws \RuntimeException
   */
  private function loadRequiredTerm(string $vid, string $name): Term {
    $terms = $this->entityTypeManager()->getStorage('taxonomy_term')
      ->loadByProperties(['vid' => $vid, 'name' => $name]);
    if (!$terms) {
      throw new \RuntimeException("Term not found in vocabulary '$vid': \"$name\". This vocabulary is not auto-created — create the term first if it's genuinely new.");
    }
    return reset($terms);
  }

  /**
   * @return array<int, array{target_id: int}>
   * @throws \RuntimeException
   */
  private function loadRequiredVersionTerms(array $version_names): array {
    $target_ids = [];
    foreach ($version_names as $version_name) {
      $term = $this->loadRequiredTerm('versions', $version_name);
      $target_ids[] = ['target_id' => $term->id()];
    }
    return $target_ids;
  }

  /**
   * Uploads every image referenced in $images_meta and substitutes its real
   * <img> tag into $body_html wherever {{IMAGE:token}} appears.
   *
   * Each image's file is expected under the uploaded-file field name
   * "image_<token>" (e.g. a token of "primary" uploads as "image_primary").
   *
   * @throws \RuntimeException
   */
  private function resolveImages(Request $request, string $body_html, array $images_meta): string {
    foreach ($images_meta as $image) {
      $token = $image['token'];
      $field_name = 'image_' . $token;
      $uploaded_file = $request->files->get($field_name);

      if (!$uploaded_file) {
        // Not every {{IMAGE:token}} necessarily needs a fresh upload on an
        // update — but if the placeholder is present and nothing was
        // uploaded for it, that's a caller error worth failing loudly on.
        if (str_contains($body_html, '{{IMAGE:' . $token . '}}')) {
          throw new \RuntimeException("images_meta references token \"$token\" but no file was uploaded under field \"$field_name\".");
        }
        continue;
      }

      if (!$uploaded_file->isValid()) {
        throw new \RuntimeException("Uploaded file for token \"$token\" failed to upload correctly ({$uploaded_file->getErrorMessage()}).");
      }
      if ($uploaded_file->getSize() > self::MAX_IMAGE_BYTES) {
        throw new \RuntimeException("Uploaded file for token \"$token\" exceeds the " . (self::MAX_IMAGE_BYTES / 1024 / 1024) . 'MB limit.');
      }

      // Validate by actually decoding the image rather than trusting a
      // client-supplied or fileinfo-guessed MIME type — both are easy to
      // spoof or get wrong (some environments even mis-detect real PNGs as
      // application/octet-stream when the fileinfo extension is unreliable).
      $image_info = @getimagesize($uploaded_file->getRealPath());
      if ($image_info === FALSE || !in_array($image_info['mime'], self::ALLOWED_IMAGE_MIME_TYPES, TRUE)) {
        $detected = $image_info['mime'] ?? 'unrecognized/not-an-image';
        throw new \RuntimeException("Uploaded file for token \"$token\" is not a supported image (detected: \"$detected\"). Allowed: " . implode(', ', self::ALLOWED_IMAGE_MIME_TYPES));
      }
      [$width, $height] = $image_info;

      $data = file_get_contents($uploaded_file->getRealPath());
      $filename = $this->fileSystem->basename($uploaded_file->getClientOriginalName());
      $file = $this->fileRepository->writeData(
        $data,
        'public://inline-images/' . $filename,
        FileSystemInterface::EXISTS_RENAME
      );
      $file->setPermanent();
      $file->save();
      $alt = htmlspecialchars((string) ($image['alt'] ?? ''), ENT_QUOTES);
      $caption = htmlspecialchars((string) ($image['caption'] ?? ''), ENT_QUOTES);
      $img_tag = sprintf(
        '<img src="%s" data-entity-uuid="%s" data-entity-type="file" alt="%s"%s%s data-caption="%s">',
        $file->createFileUrl(FALSE),
        $file->uuid(),
        $alt,
        $width ? " width=\"$width\"" : '',
        $height ? " height=\"$height\"" : '',
        $caption
      );
      $body_html = str_replace('{{IMAGE:' . $token . '}}', $img_tag, $body_html);
    }

    return $body_html;
  }

}
