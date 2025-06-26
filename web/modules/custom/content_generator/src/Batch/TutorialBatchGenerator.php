<?php

namespace Drupal\content_generator\Batch;

use Drupal\node\Entity\Node;
use GuzzleHttp\Client;

class TutorialBatchGenerator {

  public static function generate($topic_title, $menu_title, $tutorial_tid, $assistant_id, $thread_id, $lesson_number, &$context) {
    $api_key = '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';
    $client = new Client();

    // Step 1: Send prompt
    $prompt = "Write a long-form Drupal tutorial blog post in HTML with meta tags for topic: '$topic_title'";

    $msg_res = $client->post("https://api.openai.com/v1/threads/$thread_id/messages", [
      'headers' => [
        'Authorization' => "Bearer $api_key",
        'Content-Type' => 'application/json',
        'OpenAI-Beta' => 'assistants=v2',
      ],
      'json' => ['role' => 'user', 'content' => $prompt],
    ]);

    // Start the assistant run
    $run_res = $client->post("https://api.openai.com/v1/threads/$thread_id/runs", [
        'headers' => [
        'Authorization' => "Bearer $api_key",
        'Content-Type' => 'application/json',
        'OpenAI-Beta' => 'assistants=v2',
        ],
        'json' => ['assistant_id' => $assistant_id],
    ]);
    
    $run_data = json_decode($run_res->getBody()->getContents(), TRUE);
    $run_id = $run_data['id'];
    
    // ✅ Poll until run is complete (max 30 seconds)
    $run_status = 'queued';
    $tries = 0;
    do {
        sleep(2); // wait 2 seconds between checks
        $tries++;
    
        $status_res = $client->get("https://api.openai.com/v1/threads/$thread_id/runs/$run_id", [
        'headers' => [
            'Authorization' => "Bearer $api_key",
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2',
        ],
        ]);
    
        $status_data = json_decode($status_res->getBody()->getContents(), TRUE);
        $run_status = $status_data['status'];
    
    } while ($run_status !== 'completed' && $run_status !== 'failed' && $tries < 15);
    
    if ($run_status !== 'completed') {
        \Drupal::logger('tutorial_article_generator')->error('Run did not complete: @status', ['@status' => $run_status]);
        return;
    }
  

    $res = $client->get("https://api.openai.com/v1/threads/$thread_id/messages", [
      'headers' => [
        'Authorization' => "Bearer $api_key",
        'Content-Type' => 'application/json',
        'OpenAI-Beta' => 'assistants=v2',
      ],
    ]);

    $body = json_decode($res->getBody()->getContents(), TRUE);
    $content = $body['data'][0]['content'][0]['text']['value'] ?? '';

    // Extract <title>, meta tags
    preg_match('/<h1[^>]*>(.*?)<\/h1>/', $content, $title_match);
    preg_match('/<meta name="description" content="(.*?)"/', $content, $desc_match);
    preg_match('/<meta name="keywords" content="(.*?)"/', $content, $key_match);

    $title = $title_match[1] ?? $topic_title;
    $metades = $desc_match[1] ?? '';
    $keywords = $key_match[1] ?? '';

    // ✅ Remove <h1> and <meta> tags from the content
    $content = preg_replace('/<h1[^>]*>.*?<\/h1>/si', '', $content);
    $content = preg_replace('/<meta[^>]+>/i', '', $content);
    $content = preg_replace('/<title[^>]*>.*?<\/title>/si', '', $content);
    $content = preg_replace('/<\/?(html|head|body)[^>]*>/i', '', $content);
    $content = str_replace(['```html', '```'], '', $content);
    $content = trim($content);

    // Create node
    $node = Node::create([
      'type' => 'topics',
      'title' => $topic_title,
      'field_content' => [
        'value' => $content,
        'format' => 'full_html',
      ],
      'field_meta_description' => $metades,
      'field_keywords' => $keywords,
      'field_lesson_no' => $lesson_number,
      'field_menu_title' => $menu_title,
      'field_tutorial' => ['target_id' => $tutorial_tid],
    //   'field_assistant_id' => $assistant_id,
    //   'field_thread_id' => $thread_id,
      'status' => 1,
    ]);

    $node->save();
    $context['message'] = t('Created node: @title', ['@title' => $title]);
  }
}
