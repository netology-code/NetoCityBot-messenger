<?php

mb_internal_encoding('UTF-8');

function getCityInfo($name) 
{
  $data = json_decode(file_get_contents('https://ru.wikipedia.org/w/api.php?action=opensearch&search=' . urlencode($name)), true);
  $result = null;
  foreach ($data[2] as $i => $descr) {
    if (array_search('город', preg_split('(\s)', mb_strtolower($descr)))) {
      $result = [
        'title' => $data[1][$i],
        'descr' => $descr,
        'link'  => $data[3][$i],
      ];
      break;
    }
  }
  
  return $result;
}

