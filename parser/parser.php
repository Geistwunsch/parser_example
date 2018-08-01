<?php

DEFINE ('DB_USER', 'root');
DEFINE ('DB_PASSWORD', 'root');
DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_NAME', 'news');

function parse ($str1, $str2, $str3) {
  $num1 = strpos($str1, $str2);
  $i = 0;
  $stepVar = [];
  if ($num1 === false) return 0;
  while ($num1 == true) {
    $num2 = substr($str1, $num1);
    // добавляю 35 потому, что нет достаточно универсального идентификатора, через который можно было бы
    // грабить целиком HTML-разметку каждого div-элемента, отвечающего за отдельный новостной блок,
    // но при этом есть постоянное количество символов, которые можно к подходящей позиции кода добавлять,
    // а при парсинге через позицию </div><div class="snow-animate"> не захватывается последний новостной блок
    // на страничке
    $stepVar[$i] = substr($num2, 0, strpos($num2, $str3)+35);
    $str1 = str_replace("$stepVar[$i]", "", $str1);
    $num1 = strpos($str1, $str2);
    $i++;
  }
  $titles = [];
  $dates = [];
  $textes = [];
  $pics = [];
  $imgStorePath = 'img/';
  $dbConnection = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
  OR die('Could not connect to MySQL: ' . mysqli_connect_error());
  for ($i=0; $i<sizeof($stepVar); $i++) {

    $titlePos = strpos($stepVar[$i], '<h2 class="searchheadline">');
    $dataPos = strpos($stepVar[$i], '<span class="time-wrapper">');
    $textesPos = strpos($stepVar[$i], '</a></p><p>')+8;
    $picPos = strpos($stepVar[$i], '<img src="');

    $substrTitle = substr($stepVar[$i], $titlePos);
    $substrDate = substr($stepVar[$i], $dataPos);
    $substrText = substr($stepVar[$i], $textesPos);
    $substrPic = substr($stepVar[$i], $picPos+10);

    $titles[$i] = substr($substrTitle, 0, strpos($substrTitle, '</h2>')+5);
    $dates[$i] = substr($substrDate, 0, strpos($substrDate, '</span>')+7);
    $textes[$i] = str_replace("'", "&quot;", substr($substrText, 0, strpos($substrText, '</p><div class="clear">')));
    $imgUrl = substr($substrPic, 0, strpos($substrPic, '" class="categoryimage"'));
    $imgFileName   = basename($imgUrl);
    $completeSaveLoc = $imgStorePath . $imgFileName;
    file_put_contents($completeSaveLoc, file_get_contents($imgUrl));
    $pics[$i] = $completeSaveLoc;
    $sql = "INSERT INTO `news`(`article_title`, `article_date`, `article_text`, `article_pic`)
    VALUES ('$titles[$i]', '$dates[$i]', '$textes[$i]', '$pics[$i]')";

    if ($dbConnection->query($sql) === TRUE) {
        echo nl2br("<p>Data from the website for article block number " . ($i+1) . " was parsed successfuly!</p>");
    } else {
        echo nl2br("\nError: " . $sql . "<br>" . $dbConnection->error);
    }

  }
  mysqli_close($dbConnection);
}



?>
