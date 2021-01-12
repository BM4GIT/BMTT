<?php

session_start();

$host = 'localhost';
$dbname = 'BMTT';
$username = 'gast';
$password = 'welkom';

try {
  $conn = new PDO( "mysql:host=$host;dbname=$dbname", $username, $password);
  }
catch (PDOException $pe) {
  header( "Location:index.php");
  exit;
  }
?>

<!DOCTYPE html>
<html>

<head>
<?php
$title = 'T&T - Onderhoud';
echo '<title>'.$title.'</title>';
include 'style.css';
?>
</head>

<body>

  <a href="index.php"><img src="media/brederomavologo.jpg" alt="LOGO" width=75 /></a>
<?php
  if ( $_SESSION['pw'] == "" )
    echo '<h1 id="f24b" class="ca1">'.$title.'</h1>';
  else
    echo '<h1 id="f24b" class="ca2">'.$title.'</h1>';
?>
  <br/>



</body>

</html>

