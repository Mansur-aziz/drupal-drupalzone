<?php
namespace Drupal\contact_api\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\node\Entity\Node;

class ContactApiController {
  public function submit(Request $request) {
    $csrf_token = $request->headers->get('X-CSRF-Token');
    $token_service = \Drupal::service('csrf_token');
  
    if (!$token_service->validate($csrf_token, 'rest')) {
      return new JsonResponse(['error' => 'Invalid CSRF token.'], 403);
    }

    $data = json_decode($request->getContent(), TRUE);

    if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
      return new JsonResponse(['error' => 'Missing required fields.'], 400);
    }

    try {
        $storage = \Drupal::entityTypeManager()->getStorage('contact_submission');

        $contact = $storage->create([
        'name' => $request_data['name'],
        'email' => $request_data['email'],
        'message' => $request_data['message'],
        'langcode' => \Drupal::languageManager()->getCurrentLanguage()->getId(),
        ]);

        $contact->save();


      return new JsonResponse(['message' => 'Contact saved successfully.']);
    } catch (\Exception $e) {
      return new JsonResponse(['error' => 'Failed to save contact.'], 500);
    }
  }
}
