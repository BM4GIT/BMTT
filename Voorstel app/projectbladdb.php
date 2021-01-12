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

  $sql = 'SELECT * FROM certificaat ORDER BY certificaat';
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  $incert = array();
  $idcert = array();
  while ( $r = $q->fetch() ) {
    $incert[] = 'I_'.$r['certificaat'];
    $idcert[] = $r['idcertificaat'];
  }

  $sql = 'SELECT idcertificaat,certificaat FROM certificaat'.
         ' INNER JOIN pro_certificaat USING(idcertificaat) WHERE idproject='.$_SESSION['idp'];
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  $curr = array();
  $idcurr = array();
  while ( $r = $q->fetch() ) {
    $curr[] = 'I_'.$r['certificaat'];
    $idcurr[] = $r['idcertificaat'];
  }

  for ( $ix = 0; $ix < count( $incert); $ix++ ) {
    $sql = "";
    if ( isset( $_POST[$incert[$ix]]) ) {
      if ( !in_array( $incert[$ix], $curr) )
        $sql = 'CALL pro_ToeCertificaat("'.$_SESSION['idp'].'","'.$idcert[$ix].'")';
    }
    else {
      if ( in_array( $incert[$ix], $curr) )
        $sql = 'CALL pro_WisCertificaat("'.$_SESSION['idp'].'","'.$idcert[$ix].'")';
    }
    $q = $conn->query( $sql);
  }

  $sql = 'CALL pro_Wijzig("'.$_SESSION['idp'].'","'.$_POST['deadline'].'","'.$_POST['maxcredits'].'","'.$_POST['beschrijving'].'")';
  $q = $conn->query( $sql);

  header( "Location:project.php");
  exit;
?>
