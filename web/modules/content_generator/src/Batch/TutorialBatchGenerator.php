<?php

namespace Drupal\content_generator\Batch;
use Drupal\taxonomy\Entity\Term;


use Drupal\node\Entity\Node;
use GuzzleHttp\Client;

class TutorialBatchGenerator {

  public static function generate($topic_title, $menu_title, $tutorial_tid, $assistant_id, $thread_id, $lesson_number, $sub_tutorial_tid, $versions_tid, $next_title, &$context) {
    $api_key = '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';
    $client = new Client();

    $turorial_name ="";
    $term = Term::load($tutorial_tid);
    if ($term) {
      $turorial_name = $term->getName();
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
  
  Write a detailed lesson for: "$topic_title".
  
  Requirements:
  - Use clean, semantic HTML with proper headings (e.g., <h1> for the title, <h2> for sections)
  - Include SEO-friendly <meta name="description"> and <meta name="keywords"> at the top
  - Explain concepts with clarity, using consistent examples throughout the series
  - Assume the learner is new to Drupal and guide them as a human mentor would
  - Avoid repeating generic welcomes in every lesson
  - $next_tease
  
  Only return the HTML content of the article.
  PROMPT;



  //   $is_first = ($lesson_number == 1);
    
  //   $intro = $is_first
  //     ? "This is the first lesson in a new Drupal tutorial series (tutorial name: $turorial_name). Begin with a warm, brief introduction explaining the series’ purpose and what the user will achieve by the end. Mention that this is a hands-on course using a consistent, practical example throughout to reinforce concepts. Avoid sounding robotic or repetitive."
  //     : "Do not include any welcome or course introduction. This is a continuation, so build naturally on previous concepts, assuming the learner is following step by step.";
  
  //   $next_topic_line = $next_title
  //     ? "At the end of this lesson, include a short preview like: 'Next, we’ll dive into \"$next_title\" where we’ll continue expanding your Drupal knowledge.'"
  //     : "";
  
  //     $prompt = <<<PROMPT
  // You are a professional Drupal instructor writing a tutorial for tutorial name #$turorial_name and lesson #$lesson_number.
  
  // Topic Title: "$topic_title"
  
  // Instructions:
  // 1. $intro
  
  // 2. Assume the user is a complete beginner. Explain everything clearly and patiently, like a real human instructor guiding them in a classroom.
  
  // 3. Use the same practical example project across all lessons. Reference it naturally to show how each topic builds upon the last.
  
  // 4. Format your output in clean and valid HTML:
  //    - Wrap all content in `<html>`, `<head>`, and `<body>` tags.
  //    - Use a single `<h1>` for "$topic_title".
  //    - Add SEO-friendly meta tags inside `<head>`:
  //      - `<meta name="description">` – summarize the lesson using relevant keywords.
  //      - `<meta name="keywords">` – use Drupal-specific and topic-related keywords.
  
  // 5. Organize the body using proper headings (`<h2>`, `<h3>`), paragraphs, and code snippets where appropriate.
  
  // 6. $next_topic_line
  
  // Make the tutorial easy to read, beginner-friendly, SEO-optimized, and part of a cohesive learning journey.
  // PROMPT;


    // Step 1: Send prompt
    // $prompt = "Write a long-form Drupal tutorial blog post in HTML with meta tags for topic: '$topic_title'";
    // $prompt = "You're writing a structured Drupal '$turorial_name'. This lesson is titled '$topic_title'. 
    // Explain it in depth and build upon the previous concepts. Include HTML formatting, <meta> tags, and proper <h1>.";
//     $prompt = <<<PROMPT
// You are a professional Drupal instructor creating a structured tutorial series.

// Tutorial Name (based on taxonomy ID: $tutorial_tid)
// Lesson Number: $lesson_number  
// Menu Title: $menu_title  
// Current Lesson Title: "$topic_title"

// Instructions:

// 1. If this is the first message in the thread, start with a friendly introduction to the Drupal tutorial series.  
//    - Describe what the learner can expect to gain from the course.  
//    - Give an overview of how the lessons are structured.  
//    - Set the tone for a supportive, beginner-friendly learning path.

// 2. Teach as if the reader has **never read any lesson before**. Build everything from the ground up and clearly explain each concept.

// 3. Use a **consistent example project** across all lessons to demonstrate practical usage. Introduce the example in early lessons and carry it forward.

// 4. Structure the output in clean, semantic **HTML**:
//    - Start with `<h1>` using the current lesson title: `$topic_title`
//    - Use `<h2>`, `<p>`, `<code>` tags appropriately for sections and examples.
//    - Wrap the output in a complete HTML structure.
//    - Include `<meta name="description">` and `<meta name="keywords">` with SEO-rich text summarizing the content and target keywords.

// 5. Make the lesson **SEO-friendly** by:
//    - Including a short but clear summary inside the `<meta name="description">`
//    - Embedding relevant Drupal-related keywords in the `<meta name="keywords">` tag
//    - Using keyword-rich headers and intro paragraphs

// 6. If a next topic exists (in this case: "$next_title"), close with a natural forward-looking sentence such as:
//    **"In the next lesson, we'll explore: $next_title"**

// Speak with a natural, engaging, and professional tone — like a real human instructor who understands how to teach clearly and patiently.

// PROMPT;

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
    $content = preg_replace('/<style[^>]*>.*?<\/style>/si', '', $content);
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
      'field_sub_tutorial_category' => ['target_id' => $sub_tutorial_tid],
    //   'field_assistant_id' => $assistant_id,
    //   'field_thread_id' => $thread_id,
      'status' => 1,
    ]);
    // Set taxonomy terms
    foreach ($versions_tid as $tid) {
      $node->field_versions[] = ['target_id' => $tid];
    }

    $node->save();
    $context['message'] = t('Created node: @title', ['@title' => $title]);
  }
}
