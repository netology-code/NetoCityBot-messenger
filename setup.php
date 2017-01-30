<?php

require_once __DIR__ . '/vendor/autoload.php';

define('APP_ID', '1284279858301198');
define('APP_SECRET', '8a4477e921449cbbc8103ae8f0b74e9d');
define('HOOK_TOKEN', 'EAASQC7SZCCQ4BAEgg9zbJUBnjIKzKk9G3Eunsga8Y6ZAGCUpUiglnA8MuhZCED4XipKyZAACwAlInC4wNLhbbDoVBedmZASPoEFBn2bJLIZBM310ZBz68u8CIuboM8gaeCKKHgoQES5GiR83Lgl0Xi8xkLhYxwTKSZCdRBxu89jZCXwZDZD');


$fb = new Facebook\Facebook([
  'app_id' => APP_ID,
  'app_secret' => APP_SECRET,
  'default_graph_version' => 'v2.6',
]);
$fb->setDefaultAccessToken(HOOK_TOKEN);

$config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);

if (isset($config['greeting']) && $config['greeting']) {
  $data = [
    'setting_type'  => 'greeting',
    'greeting'      => [
      'text'  => $config['greeting'],
    ],
  ];
  $result = $fb->post('/me/thread_settings', $data);
  var_export($result);
} else {
  $data = [
    'setting_type'  => 'greeting',
  ];
  $result = $fb->delete('/me/thread_settings', $data);
  var_export($result);
}

if (isset($config['start']) && $config['start']) {
  $data = [
    'setting_type'          => 'call_to_actions',
    'thread_state'          => 'new_thread',
    'call_to_actions'       => [
      [ 'payload'  => $config['start'] ],
    ],
  ];
  $result = $fb->post('/me/thread_settings', $data);
  var_export($result);
} else {
  $data = [
    'setting_type'  => 'call_to_actions',
    'thread_state'  => 'new_thread',
  ];
  $result = $fb->delete('/me/thread_settings');
  var_export($result);
}

if (isset($config['menu']) && $config['menu']) {
  $data = [
    'setting_type'          => 'call_to_actions',
    'thread_state'          => 'existing_thread',
    'call_to_actions'       => $config['menu'],
  ];
  $result = $fb->post('/me/thread_settings', $data);
  var_export($result);
} else {
  $data = [
    'setting_type'  => 'call_to_actions',
    'thread_state'  => 'existing_thread',
  ];
  $result = $fb->delete('/me/thread_settings');
  var_export($result);
}
