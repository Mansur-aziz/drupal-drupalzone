<?php

namespace Drupal\custom_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;

/**
 * Controller for the topics API endpoint.
 */
class SearchController extends ControllerBase {

  /**
   * Returns topics for a specific tutorial.
   *
   * @param string $tid
   *   The tutorial taxonomy term ID.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response containing topics.
   */
  public function getSearchResult(Request $request) {

    try {

      $query = Xss::filter($request->query->get('query'));
      
      $entity_query = \Drupal::entityQuery('node')->condition('type', 'topics');
      $entity_query->accessCheck(FALSE);
      $entity_query->condition('status', 1);
      if (!empty($query)) {
        $entity_query->condition('title', '%' . $query . '%', 'LIKE');
        $nids = $entity_query->execute();
      } else {
        $nids = [];
      }
      
      $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);
      $nodes = array_reverse($nodes);
      

      foreach ($nodes as $node) {
        // $node = ($node->hasTranslation($this->currentLanguage)) ? $node->getTranslation($this->currentLanguage) : $node;
        $node_url = Url::fromRoute('entity.node.canonical', ['node' => $node->id()])->toString();
        // $node_image = $this->getStoryThumbnail($node);
        $term_name = \Drupal\taxonomy\Entity\Term::load($node->get('field_tutorial')->target_id)->get('name')->value;
        $tutorial_slug = \Drupal\taxonomy\Entity\Term::load($node->get('field_tutorial')->target_id)->toUrl()->toString();
        // \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load();
        $stories[] = [
          "id"    => $node->id(),
          "title" => $node->getTitle(),
          "menu_title" => $node->get('field_menu_title')->value,
          "lesson_no" => $node->get('field_lesson_no')->value,
          "tutorial_id" => $node->get('field_tutorial')->target_id,
          "tutorial_name" => $term_name,
          'tutorial_slug' => $tutorial_slug
        ];
      }
      return new JsonResponse($stories);
    } catch (Exception $e) {
      return new JsonResponse([]);
    }
  
  }
}
