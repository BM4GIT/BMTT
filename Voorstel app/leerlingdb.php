<?php
session_start();

if ( isset( $_POST['idp']) ) $_SESSION['idp'] = $_POST['idp'];
if ( isset( $_POST['ido']) ) $_SESSION['ido'] = $_POST['ido'];

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
  $conn = new PDO( "mysql:host=$host;dbname=$dbname", $username, $password);
}
catch (PDOException $pe) {
  header( "Location:index.php");
  exit;
}

if ( isset( $_POST['proBekijk']) ) {
  header( "Location:probladtoon.php");
  exit;
}
else
if ( isset( $_POST['opdBekijk']) ) {
  header( "Location:opdracht.php");
  exit;
}

try {
  if ( isset( $_POST['proNieuw']) ) {
    $sql = 'INSERT INTO llg_project (idleerling, idproject, ingeleverd) VALUES ('.
           $_SESSION['idl'].','.$_SESSION['idp'].',"0000-00-00")';
    $q = $conn->query( $sql);
  }
  else
  if ( isset( $_POST['proVerslag']) ) {
    $sql = 'UPDATE llg_project SET ingeleverd="'.date( "Y-m-d").
           '" WHERE idleerling='.$_SESSION['idl'].' AND idproject='.$_SESSION['idp'];
    $q = $conn->query( $sql);
    $sql = 'SELECT project FROM project WHERE idproject='.$_SESSION['idp'];
    $q = $conn->query( $sql);
    if ( $r = $q->fetch() ) {
      $proj = $r['project'];
      $sql = 'INSERT INTO llg_credits VALUES (0,'.$_SESSION['idl'].',20,"'.
              date( "Y-m-d").'","'.$proj.'","Verslag",'.$_POST['cijfer'].')';
      $q = $conn->query( $sql);
    }
  }
  else
  if ( isset( $_POST['proGeenVerslag']) ) {
    $sql = 'UPDATE llg_project SET ingeleverd="1111-11-11"'.
           ' WHERE idleerling='.$_SESSION['idl'].' AND idproject='.$_SESSION['idp'];
    $q = $conn->query( $sql);
  }
  else
  if ( isset( $_POST['proStop']) ) {
    $sql = 'DELETE FROM llg_project WHERE idleerling='.$_SESSION['idl'].' AND idproject='.$_SESSION['idp'];
    $q = $conn->query( $sql);
  }
  else
  if ( isset( $_POST['opdNieuw']) ) {
    $sql = 'INSERT INTO llg_opdracht (idleerling, idopdracht, gestart, ingeleverd) VALUES ('.
           $_SESSION['idl'].','.$_SESSION['ido'].',"'.date( "Y-m-d").'","0000-00-00")';
    $q = $conn->query( $sql);
  }
  else
  if ( isset( $_POST['opdSluit']) ) {
    $dtm = date( "Y-m-d");
    $sql = 'UPDATE llg_opdracht SET ingeleverd="'.$dtm.
           '" WHERE idleerling='.$_SESSION['idl'].' AND idopdracht='.$_SESSION['ido'];
    $q = $conn->query( $sql);
    $sql = 'SELECT opdracht,credits FROM opdracht WHERE idopdracht='.$_SESSION['ido'];
    $q = $conn->query( $sql);
    if ( $r = $q->fetch() ) {
      $opdr = $r['opdracht'];
      $cred = $r['credits'];
      $sql = 'INSERT INTO llg_credits VALUES (0,'.$_SESSION['idl'].','.$cred.',"'.
              $dtm.'","'.$opdr.'","Ingeleverd",'.$_POST['cijfer'].')';
      $q = $conn->query( $sql);
    }
  }
  else
  if ( isset( $_POST['opdStop']) ) {
    $sql = 'DELETE FROM llg_opdracht WHERE idleerling='.$_SESSION['idl'].' AND idopdracht='.$_SESSION['ido'];
    $q = $conn->query( $sql);
  }
  else
  if ( isset( $_POST['llgCijfers']) ) {
    $cijfer = $_POST['cfr'];
    foreach ( $cijfer as $id=>$value ) {
      echo 'idcredits = '.$id.', cijfer = '.$cijfer[$id].'<br/>';
      $sql = 'UPDATE llg_credits SET cijfer='.$cijfer[$id].' WHERE idcredits='.$id;
      $q = $conn->query( $sql);
    }
  }
}
catch ( PDOException $e) {
    die("Error occurred:" . $e->getMessage());
}

  header( "Location:leerling.php");
  exit;
?>
