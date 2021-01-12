<?php
session_start();


if ( isset( $_POST['idr']) )
  $_SESSION['idr'] = $_POST['idr']; // PERIODE

$_SESSION['idl'] = $_POST['idl'];   // LEERLING
$_SESSION['ido'] = $_POST['ido'];   // OPDRACHT
$_SESSION['idp'] = $_POST['idp'];   // PROJECT

if ( isset( $_POST['Leerling']) ) $phpfile = "leerling.php";
if ( isset( $_POST['Project']) ) $phpfile = "project.php";
if ( isset( $_POST['Opdracht']) ) $phpfile = "opdracht.php";

if ( isset( $_POST['proNieuw']) ) {
  $phpfile = "klas.php";
  $host = 'localhost';
  $dbname = 'BMTT';
  $username = 'docent';
  $password = $_SESSION['pw'];
try {
  $conn = new PDO( "mysql:host=$host;dbname=$dbname", $username, $password);
  $sql = 'INSERT INTO project (project, startdatum, einddatum,status) VALUES ("'.
                              $_POST['proNaam'].'","'.date( "Y-m-d").'","0000-00-00",0)';
  $q = $conn->query( $sql);

  // on success 'klas.php' expects idp to be 0 and idk to contain the new project name
  $_SESSION['idp'] = -1;
  $_SESSION['pronaam'] = $_POST['proNaam'];
}
catch (PDOException $pe) {
  header( "Location:klas.php");
  exit;
}
}

header( "Location:".$phpfile);
exit;
?>
