<?php
session_start();

if ( isset( $_POST['idp']) ) $_SESSION['idp'] = $_POST['idp'];
if ( isset( $_POST['ido']) ) $_SESSION['ido'] = $_POST['ido'];

if ( isset( $_POST['opdblad']) ) {
  header( "Location:opdrachtblad.php");
  exit;
}

$host = 'localhost';
$dbname = 'BMTT';
if ( empty ( $_SESSION['pw'] ) ) {
  $username = 'gast';
  $password = 'welkom';
}
else {
  $username = 'docent';
  $password = $_SESSION['pw'];
}

try {

  $lla = array();
  $conn = new PDO( "mysql:host=$host;dbname=$dbname", $username, $password);

  $sql = 'SELECT idleerling FROM llg_opdracht WHERE idopdracht='.$_SESSION['ido'];
  $q = $conn->query( $sql);
  while ( $r = $q->fetch() )
    $lla[] = $r['idleerling'];
  $sql = 'SELECT opdracht,credits FROM opdracht WHERE idopdracht='.$_SESSION['ido'];
  $q = $conn->query( $sql);
  if ( $r = $q->fetch() ) {
    $cred = $r['credits'];
    $opdr = $r['opdracht'];
  }
}
catch (PDOException $pe) {
  header( "Location:index.php");
  exit;
}

  $dtm = date( "Y-m-d");

  header( "Location:opdracht.php");
  exit;

?>
