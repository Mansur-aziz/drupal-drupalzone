<?php

namespace Drupal\custom_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\taxonomy\Entity\Term;

/**
 * Controller for the topics API endpoint.
 */
class SingleTopicsController extends ControllerBase {

  /**
   * Returns a specific topic by ID.
   *
   * @param string $nid
   *   The topic ID.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response containing topic details.
   */
  public function getTopic($nid) {
    try {
      $node = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->load($nid);

      if (!$node || $node->bundle() !== 'topics') {
        return new JsonResponse(['error' => 'Topic not found'], 404);
      }
      $term_name = \Drupal\taxonomy\Entity\Term::load($node->get('field_tutorial')->getValue()[0]['target_id'])->get('name')->value;

      // $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($node->get('field_tutorial')->getValue()[0]['target_id']);
      

      // date fixing
      // Get the node's creation timestamp.
      $created_time = $node->getCreatedTime();

      // Use Drupal's date formatter service.
      $date_formatter = \Drupal::service('date.formatter');

      // Format for the dateTime attribute (ISO 8601).
      $datetime = $date_formatter->format($created_time, 'custom', 'c');

      // Format for the title attribute (human-readable).
      $title = $date_formatter->format($created_time, 'custom', 'l, F j, Y - H:i');

      // Format for the display text (e.g., December 8, 2020).
      $display_date = $date_formatter->format($created_time, 'custom', 'F j, Y');
      // Versions

      $term_ids = [];
      $term_names = [];
      // Assuming 'field_versions' is the machine name of your multi-value taxonomy field
      foreach ($node->get('field_versions') as $item) {
        if (!empty($item->target_id)) {
          $term_ids[] = $item->target_id;
        }
      }

      if (!empty($term_ids)) {
        $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple(array_reverse($term_ids));
        foreach ($terms as $term) {
          $term_names[] = $term->getName();
        }
      }

      // Prepare the variables for the template.
      $variables = [
        'datetime' => $datetime,
        'title' => $title,
        'display_date' => $display_date,
      ];


      $response = new JsonResponse([
        'data' => [
          'id' => $node->id(),
          'title' => $node->getTitle(),
          'created' => $variables,
          'attributes' => [
            'content' => $node->hasField('field_content') ? $node->get('field_content')->value : '',
            'skill_level' => $node->hasField('field_skill_level') ? $node->get('field_skill_level')->value : '',
            'tutorial_name' => $term_name ? $term_name : '',
            'created' => gmdate('c', $node->getCreatedTime()), // ISO 8601 creation date
            'updated' => gmdate('c', $node->getChangedTime()), // ISO 8601 last updated date
            'lesson_no' => $node->hasField('field_lesson_no') ? $node->get('field_lesson_no')->value : null,
            'menu_title' => $node->hasField('field_menu_title') ? $node->get('field_menu_title')->value : $node->getTitle(),
            'versions' => $term_names,
          ],
          'meta' => [
            'keywords' => $node->hasField('field_keywords') ? $node->get('field_keywords')->value : $term_name . ',' . $node->getTitle(),
            'meta_description' => $node->hasField('field_meta_description') ? $node->get('field_meta_description')->value : $term_name . ',' . $node->getTitle(),
          ],
        ]
      ]);
      

      // Set no-cache headers
      $response->headers->set('Cache-Control', 'no-cache, must-revalidate');
      $response->headers->set('Expires', 'Sun, 19 Nov 1978 05:00:00 GMT');
      
      return $response;
    }
    catch (\Exception $e) {
      return new JsonResponse(['error' => 'Internal server error'], 500);
    }
  }

}
