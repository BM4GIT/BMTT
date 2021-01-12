<?php

session_start();

$opdracht = "";
$status = 0;

$host = 'localhost';
$dbname = 'BMTT';
$username = 'gast';
$password = 'welkom';

try {
  $conn = new PDO( "mysql:host=$host;dbname=$dbname", $username, $password);

  $sql = 'SELECT * FROM opdracht WHERE idopdracht='.$_SESSION['ido'];
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  if ( $r = $q->fetch() ) {
    $opdracht = $r['opdracht'];
    $credits = $r['credits'];
    $blad = $r['beschrijving'];
  }
}
catch (PDOException $pe) {
  header( "Location:index.php");
  exit;
}

include 'certificatenCheckLijst.php';
?>

<!DOCTYPE html>
<html>

<head>
<?php
$title = 'T&T - Opdrachtblad '.$opdracht;
echo '<title>'.$title.'</title>';
include 'style.css';
?>
</head>

<body>

<?php
  echo '<a href="opdracht.php"><img src="media/brederomavologo.jpg" alt="LOGO" width=75 /></a>';
  if ( $_SESSION['pw'] == "" )
    echo '<h1 id="f24b" class="ca1">'.$title.'</h1>';
  else
    echo '<h1 id="f24b" class="ca2">'.$title.'</h1>';
  echo '<br/>';

  echo '<form action="opdrachtbladdb.php" method="post">';

//////////////////
// BESCHRIJVING //
//////////////////

  echo '<h1 id="f16b" class="cf">Beschrijving</h1>';
  echo '<ul id="f12b">';

  if ( empty( $_SESSION['pw']) )
    echo $blad;
  else
    echo '<textarea name="beschrijving" cols=60 rows=10>'.$blad.'</textarea>';

  echo '</ul>';

/////////////
// CREDITS //
/////////////

  echo '<h1 id="f16b" class="cf">Credits</h1>';
  echo '<ul id="f12b"><table>';

  if ( empty( $_SESSION['pw']) )
    echo '<tr><td width=200>Maximum:</td><td>'.$credits.'</td></tr>';
  else
    echo '<tr><td width=200>Maximum:</td><td><input type="text" name="maxcredits" value="'.$credits.'" /></td></tr>';

  echo '</table></ul>';

/////////////////////
// CERTIFICATEN IN //
/////////////////////

  echo '<h1 id="f16b" class="cf">Benodigde certificaten</h1>';
  echo '<ul id="f12b">';

  if ( empty( $_SESSION['pw']) ) {

    // ZONDER DOCENTCODE

    $nil = TRUE;
    $sql = 'SELECT certificaat FROM certificaat'.
           ' INNER JOIN opd_certificaat_i USING(idcertificaat)'.
           ' WHERE idopdracht='.$_SESSION['ido'].' ORDER BY certificaat';
    $q = $conn->query( $sql);
    $q->setFetchMode( PDO::FETCH_ASSOC);
    while ( $r = $q->fetch() ) {
      echo $r['certificaat'].'<br/>';
      $nil = FALSE;
    }
    if ( $nil )
      echo "Geen";
  }
  else {

    // MET DOCENTCODE

    // Stel de lijst met huidige certificaten samen
    // Deze worden in de checkbox aangevinkt

    $sql = 'SELECT idcertificaat FROM opd_certificaat_i WHERE idopdracht='.$_SESSION['ido'];
    $q = $conn->query( $sql);
    $q->setFetchMode( PDO::FETCH_ASSOC);
    $idcurr = array();
    while ( $r = $q->fetch() )
      $idcurr[] = $r['idcertificaat'];

    // Toon de lijst op het scherm

    certificatenCheckLijst( FALSE, $idcurr);
  }

  echo '</ul>';

//////////////////////
// CERTIFICATEN UIT //
//////////////////////

  echo '<h1 id="f16b" class="cf">Uit te geven certificaten</h1>';
  echo '<ul id="f12b">';

  if ( empty( $_SESSION['pw']) ) {

    // ZONDER DOCENTCODE

    $nil = TRUE;
    $sql = 'SELECT certificaat FROM certificaat'.
           ' INNER JOIN opd_certificaat_u USING(idcertificaat)'.
           ' WHERE idopdracht='.$_SESSION['ido'].' ORDER BY certificaat';
    $q = $conn->query( $sql);
    $q->setFetchMode( PDO::FETCH_ASSOC);
    while ( $r = $q->fetch() ) {
      echo $r['certificaat'].'<br/>';
      $nil = FALSE;
    }
    if ( $nil )
      echo "Geen";
  }
  else {

    // MET DOCENTCODE

    // Stel de lijst met huidige certificaten samen
    // Deze worden in de checkbox aangevinkt

    $sql = 'SELECT idcertificaat FROM opd_certificaat_u WHERE idopdracht='.$_SESSION['ido'];
    $q = $conn->query( $sql);
    $q->setFetchMode( PDO::FETCH_ASSOC);
    $idcurr = array();
    while ( $r = $q->fetch() )
      $idcurr[] = $r['idcertificaat'];

    // Toon de lijst op het scherm

    certificatenCheckLijst( TRUE, $idcurr);
  }

  echo '</ul>';

  if ( !empty( $_SESSION['pw']) ) {

///////////////////////
// NIEUW CERTIFICAAT //
///////////////////////

    echo '<h1 id="f12" class="cb">Nieuw certificaat:';
    echo '<input type="Text" name="cerNaam" value="<naam>" style="width:200px"/>';
    echo '<input type="submit" name="cerNieuw" value=">>"/>';
    echo '</h1>';
    echo '<input type="hidden" name="return" value="'.$_POST['opdrachtblad.php'].'" />';

/////////////
// BEWAREN //
/////////////

    echo '<h1 id="f16b" class="cf">Bewaren <input type="submit" name="bewaar" value=">>" /> </h1>';
  }

  echo '</form>';
?>

</body>

</html>
