<?php
namespace Drupal\content_generator\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\node\Entity\Node;
use Drupal\Core\Batch\BatchBuilder;


class TutorialArticleGeneratorForm extends FormBase {

  public function getFormId() {
    return 'tutorial_article_generator_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form_state->setRebuild(TRUE);
    $selected_tid = $form_state->getValue('tutorial');

  // 1. Tutorial dropdown
  $tutorial_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('tutorials');
  $tutorial_options = [];
  foreach ($tutorial_terms as $term) {
    $tutorial_options[$term->tid] = $term->name;
  }

  $form['tutorial'] = [
    '#type' => 'select',
    '#title' => $this->t('Tutorial'),
    '#options' => $tutorial_options,
    '#empty_option' => $this->t('- Select -'),
    '#ajax' => [
      'callback' => '::updateSubTutorials',
      'wrapper' => 'sub-tutorial-wrapper',
    ],
  ];

  // 2. Sub Tutorial dropdown
  $form['sub_tutorial_wrapper'] = [
    '#type' => 'container',
    '#attributes' => ['id' => 'sub-tutorial-wrapper'], // ✅ match with AJAX wrapper
  ];

  $sub_options = [];

  if ($selected_tid) {
    // Load sub tutorials referencing this tutorial
    // $query = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->getQuery();
    $query = \Drupal::entityTypeManager()
  ->getStorage('taxonomy_term')
  ->getQuery()
  ->accessCheck(TRUE); // ✅ or FALSE
    $tids = $query->condition('vid', 'sub_tutorials')
      ->condition('field_parent_tutorial.target_id', $selected_tid)
      ->execute();

    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($tids);
    foreach ($terms as $term) {
      $sub_options[$term->id()] = $term->getName();
    }
  }

  $form['sub_tutorial_wrapper']['sub_tutorials'] = [
    '#type' => 'select',
    '#title' => $this->t('Sub Tutorial'),
    '#options' => $sub_options,
    '#empty_option' => $this->t('- Select a tutorial first -'),
  ];

  $form['total_words'] = [
    '#type' => 'select',
    '#title' => $this->t('Select Number of Words for Generating Content Using AI.'),
    '#options' => [
      '200' => t('200 Words'),
      '400' => t('400 Words'),
      '600' => t('600 Words'),
      '800' => t('800 Words'),
      '1000' => t('1000 Words'),
      '1500' => t('1500 Words'),
      '2000' => t('2000 Words'),
    ],
    '#empty_option' => $this->t('- Select a Topic Words -'),
  ];




    $terms_versions = \Drupal::entityTypeManager()
  ->getStorage('taxonomy_term')
  ->loadTree('versions'); // Replace with your vocab name
    $version_options = [];
    foreach ($terms_versions as $term_version) {
      $version_options[$term_version->tid] = $term_version->name;
    }
    $form['tags'] = [
      '#type' => 'select',
      '#title' => $this->t('Versions'),
      '#options' => $version_options,
      '#multiple' => TRUE,
      '#size' => 5, // Size of the select box
      '#default_value' => $default_term_ids ?? [],
    ];
    

    // $form['topics'] = [
    //   '#type' => 'textarea',
    //   '#title' => $this->t('Topics (one per line)'),
    //   '#required' => TRUE,
    // ];
    $form['topics'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Topics'),
      '#description' => $this->t('Enter topics as: Menu Title => Article Title, one per line.'),
      '#required' => TRUE,
    ];
    

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Generate Articles'),
    ];

    return $form;
  }
  public function updateSubTutorials(array &$form, FormStateInterface $form_state) {
    return $form['sub_tutorial_wrapper'];
  }
  

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $tutorial_tid = $form_state->getValue('tutorial');
    $sub_tutorial_tid = $form_state->getValue('sub_tutorials');
    $versions_tid = $form_state->getValue('tags');
    $total_words = $form_state->getValue('total_words');
    
    // $topics = array_filter(array_map('trim', explode("\n", $form_state->getValue('topics'))));
    
    $raw_topics = array_filter(array_map('trim', explode("\n", $form_state->getValue('topics'))));
    $topics = [];

    foreach ($raw_topics as $line) {
      if (strpos($line, '=>') !== FALSE) {
        [$menu, $title] = array_map('trim', explode('=>', $line, 2));
        $topics[] = ['menu_title' => $menu, 'title' => $title];
      }
    }

  
    $term = Term::load($tutorial_tid);
    $assistant_id = $term->get('field_assistant_id')->value;
    $thread_id = $term->get('field_thread_id')->value;
  
    // If no assistant/thread exists, generate them now.
    if (!$assistant_id || !$thread_id) {
      $api_key = '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';
      $client = new \GuzzleHttp\Client();
      $headers = [
        'Authorization' => "Bearer $api_key",
        'Content-Type' => 'application/json',
        'OpenAI-Beta' => 'assistants=v2',
      ];
      if(!$assistant_id){
        // Create assistant
        $res1 = $client->post('https://api.openai.com/v1/assistants', [
          'headers' => $headers,
          'json' => [
            'name' => 'Drupal Tutorial Writer',
            'instructions' => 'You are an expert Drupal instructor. Generate well-structured, SEO-friendly HTML tutorials. Use simple language, consistent examples, proper HTML formatting (<h1>, <h2>, etc.), and include <meta> tags for SEO. Every lesson should build on the previous one if it exists. Engage the learner with a clear tone, like a professional human trainer.',
            'model' => 'gpt-4o',
          ],
        ]);
        $assistant_data = json_decode($res1->getBody()->getContents(), TRUE);
        $assistant_id = $assistant_data['id'];
        // Save IDs to term
        $term->set('field_assistant_id', $assistant_id);
      }else{
        // Get max lesson number for this tutorial
        $query_last = \Drupal::entityQuery('node')
        ->accessCheck(FALSE)
        ->condition('type', 'topics')
        ->condition('field_tutorial.target_id', $tutorial_tid)
        ->sort('field_lesson_no', 'DESC')
        ->range(0, 1);
        $last_nid = $query_last->execute();
    
        if (!empty($last_nid)) {
          $latest_nid_ready = reset($last_nid);
          $node_content = Node::load($latest_nid_ready);
          $summary_body = strip_tags(($node_content->get('field_content')->value));
          $summary = substr($summary_body, 0, 3000); // limit context size
        }
      }
      if(!$thread_id){
        // Create thread
        $res2 = $client->post('https://api.openai.com/v1/threads', [
          'headers' => $headers,
        ]);
        $thread_data = json_decode($res2->getBody()->getContents(), TRUE);
        $thread_id = $thread_data['id'];
        $term->set('field_thread_id', $thread_id);
      }
      $term->save();
    }
  
    // Get max lesson number for this tutorial
    $query = \Drupal::entityQuery('node')
      ->accessCheck(FALSE)
      ->condition('type', 'topics')
      ->condition('field_tutorial.target_id', $tutorial_tid)
      ->sort('field_lesson_no', 'DESC')
      ->range(0, 1);

    $nids = $query->execute();
    $start_lesson = 1;

    if (!empty($nids)) {
      $latest_nid = reset($nids);
      $node = Node::load($latest_nid);
      $start_lesson = ((int) $node->get('field_lesson_no')->value) + 1;
    }
    $lesson_number = $start_lesson;


    $operations = [];
    // foreach ($topics as $topic) {
    for ($i = 0; $i < count($topics); $i++) {
      $current_topic = $topics[$i];
      $next_topic = $topics[$i + 1] ?? null; // null if it's the last one

        $operations[] = [
        ['\\Drupal\\content_generator\\Batch\TutorialBatchGenerator', 'generate'],
        [$current_topic['title'], $current_topic['menu_title'], $tutorial_tid, $assistant_id, $thread_id, $lesson_number,$sub_tutorial_tid,$versions_tid, $next_topic['title'] ?? null,$total_words ?? null,$summary ?? null],
        ];
        $lesson_number++;
      }
    $batch_builder = (new BatchBuilder())
      ->setTitle($this->t('Generating Articles'))
      ->setInitMessage($this->t('Starting...'))
      ->setProgressMessage($this->t('Generating @current of @total'))
      ->setFinishCallback([get_class($this), 'onBatchFinished']);
  
    foreach ($operations as $operation) {
      $batch_builder->addOperation(...$operation);
    }
  
    batch_set($batch_builder->toArray());
  }
  
  public static function onBatchFinished($success, $results, $operations) {
    if ($success && !empty($results)) {
      $message = t('Batch complete. The following topics were created:') . '<ul>';
      $log_lines = [];
  
      foreach ($results as $title) {
        $safe_title = htmlspecialchars($title);
        $message .= '<li>' . $safe_title . '</li>';
        $log_lines[] = $title;
      }
  
      $message .= '</ul>';
      \Drupal::messenger()->addMessage(['#markup' => $message, '#allowed_tags' => ['ul', 'li']]);
  
      // ✅ Add to Drupal log
      \Drupal::logger('tutorial_article_generator')->info('Batch created the following topics: @list', [
        '@list' => implode(', ', $log_lines),
      ]);
  
    } else {
      \Drupal::messenger()->addMessage(t('Batch finished with no topics created.'), 'warning');
      \Drupal::logger('tutorial_article_generator')->warning('Batch completed with no topics created.');
    }
    \Drupal::messenger()->addMessage(t('Batch processing complete.')); 
  }  
}