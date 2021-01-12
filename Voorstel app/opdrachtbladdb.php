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

  ///////////////////////////////////
  // NIEUW CERTIFICAAT AANGEVRAAGD //
  ///////////////////////////////////

  if ( isset( $_POST['cerNieuw']) )
  {
    $sql = 'INSERT INTO certificaat (certificaat) VALUES ("'.$_POST['cerNaam'].'")';
    $q = $conn->query( $sql);
    $sql = 'SELECT idcertificaat FROM certificaat WHERE certificaat = "'.$_POST['cerNaam'].'"';
    $q = $conn->query( $sql);
    $q->setFetchMode( PDO::FETCH_ASSOC);
    if ( $r = $q->fetch() )
      $_SESSION['idc'] = $r['idcertificaat'];
    header( "Location:certificaatblad.php");
    exit;
  }

  ////////////////////////////////
  // Verzamel alle certificaten //
  ////////////////////////////////

  $sql = 'SELECT * FROM certificaat ORDER BY certificaat';
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  $incert = array();
  $uitcert = array();
  $idcert = array();
  while ( $r = $q->fetch() ) {
    $incert[] = 'I_'.$r['certificaat'];
    $uitcert[] = 'U_'.$r['certificaat'];
    $idcert[] = $r['idcertificaat'];
  }

  /////////////////////////////////////////
  // Wijzig de uit te geven certificaten //
  /////////////////////////////////////////

  $sql = 'SELECT idcertificaat,certificaat FROM certificaat'.
         ' INNER JOIN opd_certificaat_u USING(idcertificaat) WHERE idopdracht='.$_SESSION['ido'];
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  $curr = array();
  $idcurr = array();
  while ( $r = $q->fetch() ) {
    $curr[] = 'U_'.$r['certificaat'];
    $idcurr[] = $r['idcertificaat'];
  }

  for ( $ix = 0; $ix < count( $uitcert); $ix++ ) {

    $sql = "";
    if ( isset( $_POST[$uitcert[$ix]]) ) {
      if ( !in_array( $uitcert[$ix], $curr) )
        $sql = 'INSERT INTO opd_certificaat_u (idopdracht, idcertificaat)'.
               ' VALUES ('.$_SESSION['ido'].','.$idcert[$ix].')';
    }
    else {
      if ( in_array( $uitcert[$ix], $curr) )
        $sql = 'DELETE FROM opd_certificaat_u'.
               ' WHERE idopdracht='.$_SESSION['ido'].' AND idcertificaat='.$idcert[$ix];
    }
    $q = $conn->query( $sql);
  }

  unset( $curr);
  unset( $idcurr);

  //////////////////////////////////////
  // Wijzig de benodigde certificaten //
  //////////////////////////////////////

  $sql = 'SELECT idcertificaat,certificaat FROM certificaat'.
         ' INNER JOIN opd_certificaat_i USING(idcertificaat) WHERE idopdracht='.$_SESSION['ido'];
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
        $sql = 'INSERT INTO opd_certificaat_i (idopdracht, idcertificaat)'.
               ' VALUES ('.$_SESSION['ido'].','.$idcert[$ix].')';
    }
    else {
      if ( in_array( $incert[$ix], $curr) )
        $sql = 'DELETE FROM opd_certificaat_i'.
               ' WHERE idopdracht='.$_SESSION['ido'].' AND idcertificaat='.$idcert[$ix];
    }
    $q = $conn->query( $sql);
  }

  ///////////////////////////////////////////////////
  // Wijzig de andere instellingen van de opdracht //
  ///////////////////////////////////////////////////
		
  $sql = 'UPDATE opdracht SET credits='.$_POST['maxcredits'].',beschrijving="'.$_POST['beschrijving'].'"'.
         ' WHERE idopdracht='.$_SESSION['ido'];
  $q = $conn->query( $sql);

  header( "Location:opdracht.php");
  exit;
?>
