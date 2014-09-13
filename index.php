<?php

define('DRUPAL_MODULE_INSTALLATIONS_USER_NAME', 'yannickoo');
define('DRUPAL_MODULE_INSTALLATIONS_PROFILE_URL', 'https://www.drupal.org/user/531118');


$count = file_get_contents('count.txt');
$count = json_decode($count, TRUE);

$output = '<ul>';

// Comparison function
function cmp($a, $b) {
    if ($a['count'] == $b['count']) {
        return 0;
    }
    return ($a['count'] < $b['count']) ? -1 : 1;
}

uasort($count, 'cmp');
$count = array_reverse($count);

foreach ($count as $c) {
  $output .= '<li><a href="' . $c['url'] . '"><span>' . $c['count'] . '</span> ' . $c['title'] . '</a></li>';
}

$output .= '</ul>';

?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Modules by <?php print DRUPAL_MODULE_INSTALLATIONS_USER_NAME; ?></title>
    <style>body,html{height:100%;text-align:center;white-space:nowrap;margin:0}#page{display:inline-block;vertical-align:middle;white-space:normal}body:before{content:'';display:inline-block;height:100%;width:0;vertical-align:middle;margin-right:-.3em}body{font-family: 'HelveticaNeue-Light', 'Helvetica Neue Light', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;}ul{padding-left:0;list-style-type:none}a{color:#000;text-decoration:none;padding-bottom:3px;border-bottom:2px solid #000;}a:active{position:relative;top:1px;}</style>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script>
    google.load('visualization', '1', {packages: ['corechart']});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Task', 'link', 'Hours per Day'],
<?php foreach ($count as $c): ?>
          ['<?php print $c['title']; ?>', '<?php print $c['url']; ?>', <?php print $c['count']; ?>],
<?php endforeach; ?>
        ]);

        var view = new google.visualization.DataView(data);
        view.setColumns([0, 2]);

        var options = {
          pieSliceText: 'label',
          width: 700,
          height: 400
        };

        var chart = new google.visualization.PieChart(
          document.getElementById('chart_div'));
        chart.draw(view, options);

        var selectHandler = function(e) {
         window.location = data.getValue(chart.getSelection()[0]['row'], 1 );
        }

        google.visualization.events.addListener(chart, 'select', selectHandler);
      }
    </script>
  </head>
  <body>
    <div id="page">
      <h1>Modules by <a href="<? print DRUPAL_MODULE_INSTALLATIONS_PROFILE_URL; ?>"><?php print DRUPAL_MODULE_INSTALLATIONS_USER_NAME; ?></a></h1>
      <div id="chart_div"></div>
    </div>
  </body>
</html>
