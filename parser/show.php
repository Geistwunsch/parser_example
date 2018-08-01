<?php

require_once('parser.php');


function showFreshThree () {
  $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  // На самом сайте уже проихводится фильтр по дате, поэтому
  // можно просто всегда выводить самые первые три ряда из базы данных, которые спарсил скрипт
  $sql = "SELECT * FROM news LIMIT 3";
  $result = $conn->query($sql);
  echo "<h2>THESE ARE THE TOP 3 FRESH NEWS FROM THE PARSING SOURCE:</h2>";
  if ($result->num_rows > 0) {
      echo "<table><tr><th>Article Name</th><th>Article Date</th><th>Article Picture</th><th>Article Text</th></tr>";
      // вывод блоков по соответствующим столбцам для наглядности
      while($row = $result->fetch_assoc()) {
          echo "<tr><td>".$row["article_title"]."</td><td>".$row["article_date"]."</td><td><img src='"
          .$row["article_pic"]."'></td><td>".$row["article_text"]."</td></tr>";
      }
      echo "</table>";
  } else {
      echo "0 results";
  }
  $conn->close();

}

?>
