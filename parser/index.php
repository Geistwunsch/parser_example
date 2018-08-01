<?php

require_once('parser.php');
require_once('show.php');

$parsedPage = file_get_contents('http://dailyillini.com/category/news');

$queryData = parse($parsedPage, '<div class="sno-animate">', '<div class="postmeta2"></div>' );
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>TEST TASK PARSER</title>
    <link rel="stylesheet" href="css/main.css">
  </head>
  <body>

<?php
showFreshThree();
?>
  </body>
</html>
