<?php
session_start();

if ( isset( $_POST['idp']) ) $_SESSION['idp'] = $_POST['idp'];
if ( isset( $_POST['ido']) ) $_SESSION['ido'] = $_POST['ido'];

if ( isset( $_POST['problad']) ) {
  header( "Location:projectblad.php");
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

  $sql = 'SELECT idleerling FROM llg_project WHERE idproject='.$_SESSION['idp'];
  $q = $conn->query( $sql);
  while ( $r = $q->fetch() )
    $lla[] = $r['idleerling'];
  $sql = 'SELECT project,credits FROM project WHERE idproject='.$_SESSION['idp'];
  $q = $conn->query( $sql);
  if ( $r = $q->fetch() ) {
    $cred = $r['credits'];
    $proj = $r['project'];
  }
}
catch (PDOException $pe) {
  header( "Location:index.php");
  exit;
}

try {
  $dtm = date( "Y-m-d");

  if ( isset( $_POST['beschrijving']) ) {
    $sql = 'UPDATE project SET status=1, dt_beschrijving="'.$dtm.'" WHERE idproject='.$_SESSION['idp'];
    $q = $conn->query( $sql);
    $cred = intval( $cred * 10 / 100);
    for ( $ix = 0; $ix < count( $lla); $ix++ ) {
      $sql = 'INSERT INTO llg_credits VALUES (0,'.$lla[$ix].','.$cred.',"'.$dtm.'","'.$proj.'","Beschrijving",10)';
      $q = $conn->query( $sql);
    }
  }
  else
  if ( isset( $_POST['planning']) ) {
    $sql = 'UPDATE project SET status=2, dt_planning="'.$dtm.'" WHERE idproject='.$_SESSION['idp'];
    $q = $conn->query( $sql);
    $cred = intval( $cred * 5 / 100);
    for ( $ix = 0; $ix < count( $lla); $ix++ ) {
      $sql = 'INSERT INTO llg_credits VALUES (0,'.$lla[$ix].','.$cred.',"'.$dtm.'","'.$proj.'","Planning",10)';
      $q = $conn->query( $sql);
    }
  }
  else
  if ( isset( $_POST['protofase']) ) {
    $sql = 'UPDATE project SET status=3, dt_proto="'.$dtm.'" WHERE idproject='.$_SESSION['idp'];
    $q = $conn->query( $sql);
    $cred = intval( $cred * 30 / 100);
    for ( $ix = 0; $ix < count( $lla); $ix++ ) {
      $sql = 'INSERT INTO llg_credits VALUES (0,'.$lla[$ix].','.$cred.',"'.$dtm.'","'.$proj.'","Protofase",10)';
      $q = $conn->query( $sql);
    }
  }
  else
  if ( isset( $_POST['product']) ) {
    $sql = 'UPDATE project SET status=4, dt_product="'.$dtm.'" WHERE idproject='.$_SESSION['idp'];
    $q = $conn->query( $sql);
    $cred = intval( $cred * 30 / 100);
    for ( $ix = 0; $ix < count( $lla); $ix++ ) {
      $sql = 'INSERT INTO llg_credits VALUES (0,'.$lla[$ix].','.$cred.',"'.$dtm.'","'.$proj.'","Eindproduct",10)';
      $q = $conn->query( $sql);
    }
  }
  else
  if ( isset( $_POST['presentatie']) ) {
    $sql = 'UPDATE project SET status=5, dt_presentatie="'.$dtm.'" WHERE idproject='.$_SESSION['idp'];
    $q = $conn->query( $sql);
    $cred = intval( $cred * 25 / 100);
    for ( $ix = 0; $ix < count( $lla); $ix++ ) {
      $sql = 'INSERT INTO llg_credits VALUES (0,'.$lla[$ix].','.$cred.',"'.$dtm.'","'.$proj.'","Presentatie",10)';
      $q = $conn->query( $sql);
    }
  }
  else
  if ( isset( $_POST['stop']) ) {
    $sql = 'DELETE FROM project WHERE idproject='.$_SESSION['idp'];
    $q = $conn->query( $sql);
    header( "Location:index.php");
    exit;
  }
}
catch ( PDOException $e) {
    die("Error occurred:" . $e->getMessage());
}

  header( "Location:project.php");
  exit;

?>
