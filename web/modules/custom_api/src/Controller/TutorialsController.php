<?php

namespace Drupal\custom_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller for the tutorials API endpoint.
 */
class TutorialsController extends ControllerBase {

  /**
   * Returns a list of tutorials.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response containing tutorials list.
   */
  public function getTopics() {
    $terms_data = [];
    
    // Query terms of the 'tutorials' vocabulary
    $query = \Drupal::entityQuery('taxonomy_term')
      ->condition('vid', 'tutorials')
      ->accessCheck(TRUE)
      ->condition('status', 1)
      // ->sort('name', 'ASC');
      ->sort('weight', 'ASC');
    
    $tids = $query->execute();

    if (!empty($tids)) {
      $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($tids);
      
      foreach ($terms as $term) {
        $terms_data['data'][] = [
          'id' => $term->id(),
          'attributes' => [
            'name' => $term->getName(),
            'banner' => $term->field_tutorial_banner->entity ? \Drupal::service('file_url_generator')->generateAbsoluteString($term->field_tutorial_banner->entity->getFileUri()) : NULL,
            'slug' => $term->toUrl()->toString(),
          ]
        ];
      }
    }

    $response = new JsonResponse($terms_data);
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }
}
