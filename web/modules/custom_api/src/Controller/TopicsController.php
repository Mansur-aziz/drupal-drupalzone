<?php

namespace Drupal\custom_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller for the topics API endpoint.
 */
class TopicsController extends ControllerBase {

  /**
   * Returns topics for a specific tutorial.
   *
   * @param string $tid
   *   The tutorial taxonomy term ID.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response containing topics.
   */
  public function getTopic($tid) {
    try {
      $term = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->load($tid);

      if (!$term || $term->bundle() !== 'tutorials') {
        return new JsonResponse(['error' => 'Tutorial not found'], 404);
      }else{
        $terms_data = [];
        // Query terms of the 'tutorials' vocabulary
        $query = \Drupal::entityQuery('taxonomy_term')
          ->condition('vid', 'sub_tutorials')
          ->condition('field_parent_tutorial', $tid)
          ->condition('status', 1)
          ->accessCheck(TRUE);
        $tids = $query->execute();
        if (!empty($tids)) {
          $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($tids);
          foreach ($terms as $term) {
            $terms_data['data'][] = [
              'id' => $term->id(),
              'heading' => $term->getName(),
              'topics' => $this->extractNode($tid,$term->id()),
            ];
          }
        }
      }

      $response = new JsonResponse($terms_data);
      $response->headers->set('Content-Type', 'application/json');
      return $response;

    } catch (\Exception $e) {
      \Drupal::logger('custom_api')->error($e->getMessage());
      return new JsonResponse(['error' => 'Internal server error'], 500);
    }
  }
  public function extractNode($tid , $stid) {
    $query = \Drupal::entityQuery('node')
    ->condition('type', 'topics')
    ->condition('status', 1)
    ->condition('field_tutorial', $tid)
    ->condition('field_sub_tutorial_category', $stid)
    ->condition('status', 1)
    ->accessCheck(TRUE)
    ->sort('field_lesson_no', 'ASC');
    // ->sort('weight', 'ASC');

    $nids = $query->execute();
    $topics_data = ['data' => []];

    if (!empty($nids)) {
      $nodes = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->loadMultiple($nids);

      foreach ($nodes as $node) {
        $topics_data[] = [
          'slug'=> $node->toUrl()->toString(),
          'id' => $node->id(),
          'lesson_no' => $node->get('field_lesson_no')->value ? $node->get('field_lesson_no')->value : $node->id(),
          'menu_title' => $node->get('field_menu_title')->value ? $node->get('field_menu_title')->value : $node->getTitle()
        ];
      }
      return $topics_data;
    }
  }
}
