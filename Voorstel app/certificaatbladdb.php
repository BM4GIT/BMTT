<?php
session_start();

$host = 'localhost';
$dbname = 'BMTT';
$username = 'docent';
$password = $_SESSION['pw'];

try {
  $conn = new PDO( "mysql:host=$host;dbname=$dbname", $username, $password);
}
catch (PDOException $pe) {
  header( "Location:index.php");
  exit;
}

  if ( isset( $_POST['catNieuw']) )
  {
    // Nieuwe categorie aangevraagd

    $sql = 'INSERT INTO categorie (categorie) VALUES ("'.$_POST['catNaam'].'")';
    $q = $conn->query( $sql);
    header( "Location:certificaatblad.php");
    exit;
  }

  // Wijzig de instellingen van het certificaat
		
  $sql = 'UPDATE opdracht SET credits='.$_POST['maxcredits'].',beschrijving="'.$_POST['beschrijving'].'"'.
         ' WHERE idopdracht='.$_SESSION['ido'];
  $q = $conn->query( $sql);

echo $_POST['return'];
//  header( "Location:".$_POST['return']);
//  exit;
?>
