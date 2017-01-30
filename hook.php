<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/wiki.php';

function createGenericMessage($recipientId) {
  return [
    'recipient' => [
      'id'    => $recipientId,
    ],
    'message'   => [
      'attachment'  => [
        'type'    => 'template',
        'payload' => [
          'template_type' => 'generic',
          'elements'      => [
            [
              'title'     => 'PHP',
              'subtitle'  => 'back-end разработка и базы данных',
              'item_url'  => 'http://netology.ru/programs/php-sql',
              'image_url' => 'http://netology.ru/program/php-sql.png?v=1474977165',
              'buttons'   => [
                [
                  'type'  => 'web_url',
                  'url'   => 'http://netology.ru/programs/php-sql',
                  'title' => 'Подробности',
                ],
                [
                  'type'    => 'postback',
                  'payload' => 'php',
                  'title'   => 'На PHP',
                ],
              ],
            ],
            [
              'title'     => 'Python',
              'subtitle'  => 'программирование на каждый день и сверхбыстрое прототипирование',
              'item_url'  => 'http://netology.ru/programs/python',
              'image_url' => 'http://netology.ru/program/python.png?v=1474977197',
              'buttons'   => [
                [
                  'type'  => 'web_url',
                  'url'   => 'http://netology.ru/programs/python',
                  'title' => 'Подробности',
                ],
                [
                  'type'    => 'postback',
                  'payload' => 'python',
                  'title'   => 'На Python',
                ],
              ],
            ],
          ],
        ],
      ],
    ],
  ];
}

mb_internal_encoding('UTF-8');
$cities = array_map('mb_strtolower', array_map('trim', file('./data/cities.txt')));

$pics = [
  'http://memesmix.net/media/created/4mq33s.jpg',
  'http://s00.yaplakal.com/pics/pics_original/2/4/1/6478142.jpg',
  'http://risovach.ru/upload/2013/08/mem/dzheki-chan_26370234_big_.jpg',
  'http://risovach.ru/upload/2013/04/mem/golub_15608171_orig_.jpg',
];

function getRandom($list) 
{
  $index = rand(0, count($list) - 1);
  return $list[$index];
}

function getLast($text) 
{
  $badLetters = ['ы', 'ь', 'й'];
  $i = -1;
  
  do {
    $letter = mb_substr($text, $i, $i === -1 ? null : $i + 1);
    --$i;
  } while (check($letter, $badLetters));
  return $letter;
}

function getFirst($text) 
{
  return mb_substr(mb_strtolower($text), 0, 1);
}

function check($item, $list) 
{
  return array_search($item, $list) !== false;
}

function logData($data) 
{
  //file_put_contents('./event.log', $data . PHP_EOL, FILE_APPEND);
}

function mb_ucfirst($str) {
  $fc = mb_strtoupper(mb_substr($str, 0, 1));
  return $fc . mb_substr($str, 1);
}

$app = new Neto\Messenger\Client(getenv('PAGE_TOKEN'), [
  'app' => [
    'id'      => getenv('APP_ID'),
    'secret'  => getenv('APP_SECRET'),
  ],
  'hook' => [
    'subscribe_token' => getenv('HOOK_SUBSCRIBE_TOKEN'),
  ],
]);

$app->on(function ($event) {
  logData(var_export($event, true));
  return true;
});

$app->on(function ($event) {
  $senderId = $event['sender']['id'];
  $pageId = $event['recipient']['id'];
  session_id($senderId);
  session_name('page-' . $pageId);
  session_start();
  
  if (!isset($_SESSION['city'])) {
    $_SESSION['city'] = [];
  }
  return true;
});

$app->on(function ($event) {
  if (isset($_SESSION['last']) && $_SESSION['last'] >= $event['timestamp']) {
    return false;
  }
  $_SESSION['last'] = $event['timestamp'];
  return true;
});

$app->postback('php', function ($event) use ($app) {
  $senderId = $event['sender']['id'];
  $app->sendTextMessage($senderId, 'Угадал. Я написан на PHP. Но меня можно было реализовать и на других языках.');
});

$app->postback('python', function ($event) use ($app) {
  $senderId = $event['sender']['id'];
  $postback = $event['postback'];
  $app->sendTextMessage($senderId, 'Нет. Я написан на PHP. Но мог быть реализова и на других языках. Например на ' . $postback['payload'] . '.');
});

$app->postback('bot/menu/rules', function ($event) use ($app) {
  $senderId = $event['sender']['id'];
  $app->sendTextMessage($senderId, 'Города — игра для одного человека и одного бота, в которой каждый участник в свою очередь называет реально существующий город России, название которого начинается на ту букву, которой оканчивается название предыдущего участника.');
});

$app->postback('bot/menu/stop', function ($event) use ($app) {
  $senderId = $event['sender']['id'];
  if (isset($_SESSION['city']['isOn']) && $_SESSION['city']['isOn']) {
    $_SESSION['city'] = [];
    $app->sendTextMessage($senderId, 'Хорошо. Закончим игру.');
  } else {
    $_SESSION['city']['isOn'] = true;
    $app->sendTextMessage($senderId, 'Ура. Начнем новую игру. Назови любой город России чтобы начать.');
  }
});

$app->postback('bot/menu/state', function ($event) use ($app) {
  $senderId = $event['sender']['id'];
  if (!isset($_SESSION['city']['isOn']) || !$_SESSION['city']['isOn']) {
    $app->sendTextMessage($senderId, 'Сейчас мы не играем.');
    return;
  }
  
  if (!isset($_SESSION['city']['letter'])) {
    $app->sendTextMessage($senderId, 'Это первый раунд. Я жду любой город России, чтобы сделать свой ход.');  
  } else {
    $app->sendTextMessage($senderId, 'Это ' . count($_SESSION['city']['used']) . ' раунд. Я назвал город ' . $_SESSION['city']['current'] . ' и жду город на букву «' . $_SESSION['city']['letter'] . '».');  
  }
  
  if (isset($_SESSION['city']['used']) && $_SESSION['city']['used']) {
    $app->sendTextMessage($senderId, 'Уже названны города: ' . implode(', ', $_SESSION['city']['used']));  
  }
});

$app->postback('bot/start', function ($event) use ($app) {
  $senderId = $event['sender']['id'];
  $_SESSION['city']['isOn'] = true;
  $app->sendTextMessage($senderId, 'Ура. Начнем новую игру. Назови любой город России чтобы начать.');
});

$app->message(function ($event) use ($app, $cities, $pics) {
  $senderId = $event['sender']['id'];
  $message = $event['message'];
  $city = mb_strtolower($message['text']);
  logData('Город: ' . $city);
  
  if (isset($_SESSION['city']['isOn']) && $_SESSION['city']['isOn']) {
    if (!check($city, $cities)) {
      $app->sendTextMessage($senderId, mb_ucfirst($city) . '? Такого города нет. Давай не будем выдумывать свои города. Попробуй еще раз.');
      return;
    }
    if (isset($_SESSION['city']['letter'])) {
      $letter = getFirst($city);
      if ($letter !== $_SESSION['city']['letter']) {
        $app->sendTextMessage($senderId, mb_ucfirst($city) . ' — хорошая попытка! Но нужен город который начинается на «' . mb_strtoupper($_SESSION['city']['letter']) . '». А этот начинается на «' . mb_strtoupper($letter) . '». Только не говори, что мне придется учить тебя азбуке. Попробуй еще раз.');
        return;
      }
    }
    if (isset($_SESSION['city']['used']) && check($city, $_SESSION['city']['used'])) {
      $app->sendTextMessage($senderId, mb_ucfirst($city) . ' — серьёзно? Как хорошо что у меня отличная память. Такой город уже называли. Соберись, и не пытайся меня надуть!');
      return;
    }
    
    if (!isset($_SESSION['city']['used'])) {
      $_SESSION['city']['used'] = [];
    }
    
    if (rand(1, 100) > 66) {
      $app->sendImage($senderId, getRandom($pics));
    }
    
    $_SESSION['city']['used'][] = $city;
    $letter = getLast($city);
    logData('Последняя буква: (' . $letter . ')');
    $available = array_filter($cities, function ($city) use ($letter) {
      return !check($city, $_SESSION['city']['used']) && getFirst($city) === $letter;
    });
    
    if (!$available) {
      $_SESSION['city'] = [];
      $app->sendTextMessage($senderId, 'Сдаюсь! Невероятно! Такого не было и никогда больше не будет.');
      return;
    }
    
    $city = getRandom(array_values($available));
    $letter = getLast($city);
    $_SESSION['city']['current'] = $city;
    $_SESSION['city']['used'][] = $city;
    $_SESSION['city']['letter'] = $letter;
    $info = getCityInfo($city);
    if ($info) {
      $app->sendTextMessage($senderId, $info['descr']);
    }
    
    $app->sendTextMessage($senderId, mb_ucfirst($city) . '! Тебе на «' . mb_strtoupper($letter) . '»');
  } else {
    $app->sendTextMessage($senderId, 'Угадай, на каком языке программирования я написан?');
    $app->sendMessage(createGenericMessage($senderId));
  }
});

$app->run();
