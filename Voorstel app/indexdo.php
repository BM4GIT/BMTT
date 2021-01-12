<?php
session_start();


if ( isset( $_POST['Klas']) )
  $_SESSION['idk'] = $_POST['idk']; // KLAS
else
  $_SESSION['idk'] = "";

$_SESSION['idl'] = $_POST['idl'];   // LEERLING
$_SESSION['idp'] = $_POST['idp'];   // PROJECT
$_SESSION['ido'] = $_POST['ido'];   // OPDRACHT
$_SESSION['idc'] = "";              // CERTIFICAAT

if ( isset( $_POST['pw']) )
  $_SESSION['pw'] = $_POST['pw'];   // PASSWORD

if ( isset( $_POST['Klas']) ) $phpfile = "klas.php";
if ( isset( $_POST['Leerling']) ) $phpfile = "leerling.php";
if ( isset( $_POST['Project']) ) $phpfile = "project.php";
if ( isset( $_POST['Opdracht']) ) $phpfile = "opdracht.php";
if ( isset( $_POST['Onderhoud']) ) $phpfile = "onderhoud.php";

header( "Location:".$phpfile);
exit;
?>
