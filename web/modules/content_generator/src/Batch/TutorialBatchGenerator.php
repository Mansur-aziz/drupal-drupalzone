<?php

namespace Drupal\content_generator\Batch;

use Drupal\taxonomy\Entity\Term;
use Drupal\node\Entity\Node;
use GuzzleHttp\Client;

class TutorialBatchGenerator {

  protected static function waitForRunCompletion($client, $thread_id, $run_id, $api_key, $max_tries = 15) {
    $tries = 0;
    $run_status = 'queued';

    while ($run_status !== 'completed' && $run_status !== 'failed' && $tries < $max_tries) {
      sleep(2); // Wait 2 seconds
      $tries++;

      $status_res = $client->get("https://api.openai.com/v1/threads/{$thread_id}/runs/{$run_id}", [
        'headers' => [
          'Authorization' => "Bearer {$api_key}",
          'Content-Type' => 'application/json',
          'OpenAI-Beta' => 'assistants=v2',
        ],
      ]);

      $status_data = json_decode($status_res->getBody()->getContents(), TRUE);
      $run_status = $status_data['status'];
    }

    return [
      'status' => $run_status,
      'data' => $status_data ?? [],
    ];
  }

  public static function generate($topic_title, $menu_title, $tutorial_tid, $assistant_id, $thread_id, $lesson_number, $sub_tutorial_tid, $versions_tid, $next_title, &$context) {
    $api_key = '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';
    $client = new Client();

    $tutorial_name = '';
    $term = Term::load($tutorial_tid);
    if ($term) {
      $tutorial_name = $term->getName();
    }

    $is_first = ($lesson_number == 1);
    $intro = $is_first
      ? "This is the first lesson of our Drupal tutorial series: \"$tutorial_name\". Begin with a brief, engaging introduction to the overall series and what learners will achieve. Introduce the topic: \"$topic_title\". Do not include this intro in future lessons."
      : "This is lesson #Topic Name: \"$topic_title\". Continue building on the previously explained concepts.";

    $next_tease = $next_title
      ? "At the end of this lesson, provide a teaser for the next topic: \"$next_title\" to maintain flow and keep the learner engaged."
      : "";

    $prompt = <<<PROMPT
You are a professional Drupal instructor writing a structured tutorial for the series titled: "$tutorial_name".

$intro

Write a detailed (at least 800 words) lesson for: "$topic_title".

Requirements:
- Use clean, semantic HTML with proper headings (e.g., <h1> for the title, <h2> for sections)
- Include SEO-friendly <meta name="description"> and <meta name="keywords"> at the top
- Explain concepts with clarity, using consistent examples throughout the series
- Code examples Should be in proper code block HTML tags
- Assume the learner is new to Drupal and guide them as a human mentor would
- Avoid repeating generic welcomes in every lesson
- $next_tease

Only return the HTML content of the article.
PROMPT;

    // 🔐 Short delay to avoid message-before-run conflict in fast batches
    sleep(1);

    // Step 1: Send message to thread
    // $msg_res = $client->post("https://api.openai.com/v1/threads/{$thread_id}/messages", [
    //   'headers' => [
    //     'Authorization' => "Bearer {$api_key}",
    //     'Content-Type' => 'application/json',
    //     'OpenAI-Beta' => 'assistants=v2',
    //   ],
    //   'json' => ['role' => 'user', 'content' => $prompt],
    // ]);
    try {
      $msg_res = $client->post("https://api.openai.com/v1/threads/{$thread_id}/messages", [
        'headers' => [
          'Authorization' => "Bearer {$api_key}",
          'Content-Type' => 'application/json',
          'OpenAI-Beta' => 'assistants=v2',
        ],
        'json' => ['role' => 'user', 'content' => $prompt],
      ]);
    } catch (\GuzzleHttp\Exception\ClientException $e) {
      $response_body = json_decode($e->getResponse()->getBody()->getContents(), true);
      $error_message = $response_body['error']['message'] ?? '';
    
      // Handle rate limit error
      if (strpos($error_message, 'Rate limit reached') !== false) {
        \Drupal::logger('tutorial_article_generator')->warning('Rate limit hit for topic "@topic": @msg', [
          '@topic' => $topic_title,
          '@msg' => $error_message,
        ]);
        
        // Extract wait time if available
        if (preg_match('/try again in ([\d\.]+)s/', $error_message, $matches)) {
          $wait_time = (float) $matches[1];
          sleep((int) ceil($wait_time));
        } else {
          sleep(10); // fallback wait
        }
    
        // Retry once after wait
        $msg_res = $client->post("https://api.openai.com/v1/threads/{$thread_id}/messages", [
          'headers' => [
            'Authorization' => "Bearer {$api_key}",
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2',
          ],
          'json' => ['role' => 'user', 'content' => $prompt],
        ]);
      } else {
        // Log and skip any other errors
        \Drupal::logger('tutorial_article_generator')->error('Error sending message for topic "@topic": @msg', [
          '@topic' => $topic_title,
          '@msg' => $error_message,
        ]);
        return;
      }
    }    

    // Step 2: Start run
    $run_res = $client->post("https://api.openai.com/v1/threads/{$thread_id}/runs", [
      'headers' => [
        'Authorization' => "Bearer {$api_key}",
        'Content-Type' => 'application/json',
        'OpenAI-Beta' => 'assistants=v2',
      ],
      'json' => ['assistant_id' => $assistant_id],
    ]);

    $run_data = json_decode($run_res->getBody()->getContents(), TRUE);
    $run_id = $run_data['id'];

    // Step 3: Wait for run completion
    $result = self::waitForRunCompletion($client, $thread_id, $run_id, $api_key);
    if ($result['status'] !== 'completed') {
      $error_reason = $result['data']['last_error']['message'] ?? 'Unknown error';
      \Drupal::logger('tutorial_article_generator')->error('Run failed for topic "@topic": @reason', [
        '@topic' => $topic_title,
        '@reason' => $error_reason,
      ]);
      return;
    }

    // Step 4: Fetch messages (output)
    $res = $client->get("https://api.openai.com/v1/threads/{$thread_id}/messages", [
      'headers' => [
        'Authorization' => "Bearer {$api_key}",
        'Content-Type' => 'application/json',
        'OpenAI-Beta' => 'assistants=v2',
      ],
    ]);

    $body = json_decode($res->getBody()->getContents(), TRUE);
    $content = $body['data'][0]['content'][0]['text']['value'] ?? '';

    // Step 5: Extract title, meta tags
    preg_match('/<h1[^>]*>(.*?)<\/h1>/', $content, $title_match);
    preg_match('/<meta name="description" content="(.*?)"/', $content, $desc_match);
    preg_match('/<meta name="keywords" content="(.*?)"/', $content, $key_match);

    $title = $title_match[1] ?? $topic_title;
    $metades = $desc_match[1] ?? '';
    $keywords = $key_match[1] ?? '';

    // Step 6: Cleanup content
    $content = preg_replace('/<h1[^>]*>.*?<\/h1>/si', '', $content);
    $content = preg_replace('/<meta[^>]+>/i', '', $content);
    $content = preg_replace('/<title[^>]*>.*?<\/title>/si', '', $content);
    $content = preg_replace('/<\/?(html|head|body)[^>]*>/i', '', $content);
    $content = str_replace(['```html', '```'], '', $content);
    $content = preg_replace('/<style[^>]*>.*?<\/style>/si', '', $content);
    $content = trim($content);

    // Step 7: Create node
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
      'field_sub_tutorial_category' => ['target_id' => $sub_tutorial_tid],
      'status' => 1,
    ]);

    foreach ($versions_tid as $tid) {
      $node->field_versions[] = ['target_id' => $tid];
    }

    $node->save();
    $context['results'][] = $title;
    $context['message'] = t('Created node: @title', ['@title' => $title]);
  }
}
