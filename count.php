<?php

define('DRUPAL_MODULE_INSTALLATIONS_PROFILE_URL', 'https://www.drupal.org/user/531118');

function file_get_contents_curl($url) {
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.73 Safari/537.36');

  $data = curl_exec($ch);
  curl_close($ch);

  return $data;
}

if (filemtime('count.txt') < time() - 86400 || isset($_GET['force'])) {

  require_once('lib/simplehtmldom/simple_html_dom.php');

  $drupal = DRUPAL_MODULE_INSTALLATIONS_PROFILE_URL;
  $drupal_html = file_get_contents_curl($drupal);
  $drupal_html = str_get_html($drupal_html);
  $drupal_commits = $drupal_html->find('dl');

  $foo = $drupal_commits[0];

  $projects = array();

  foreach ($foo->find('dd a[href^="http://drupal.org/project/"]') as $key => $element) {
    $usages = str_replace('/project', '/project/usage', $element->href);

    $usages_html_raw = file_get_contents_curl($usages);
    $usages_html = str_get_html($usages_html_raw);
    $count = str_replace(',', '', $usages_html->find('table#project-usage-project-api tbody tr td.project-usage-numbers', 0)->text());

    $projects[] = array('title' => $element->text(), 'url' => $element->href, 'count' => $count);
  }

  $bla = json_encode($projects);
  $fp = fopen('count.txt', 'w');
  fwrite($fp, $bla);
  fclose($fp);

  print $bla;

}
else {
  print file_get_contents('count.txt');
}
