<?php

session_start();

$project = "";
$status = 0;

$host = 'localhost';
$dbname = 'BMTT';
$username = 'gast';
$password = 'welkom';

try {
  $conn = new PDO( "mysql:host=$host;dbname=$dbname", $username, $password);

  $sql = 'SELECT * FROM project WHERE idproject='.$_SESSION['idp'];
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  if ( $r = $q->fetch() ) {
    $project = $r['project'];
    $eindd = $r['einddatum'];
    $status = $r['status'];
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
$title = 'T&T - Projectblad '.$project;
echo '<title>'.$title.'</title>';
include 'style.css';
?>
</head>

<body>

<?php
  echo '<a href="project.php"><img src="media/brederomavologo.jpg" alt="LOGO" width=75 /></a>';
  if ( empty( $_SESSION['pw']) )
    echo '<h1 id="f24b" class="ca1">'.$title.'</h1>';
  else
    echo '<h1 id="f24b" class="ca2">'.$title.'</h1>';
  echo '<br/>';

  echo '<form action="projectbladdb.php" method="post">';

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

//////////////
// DEADLINE //
//////////////

  echo '<h1 id="f16b" class="cf">Deadline</h1>';
  echo '<ul id="f12b"><table>';

  if ( empty( $_SESSION['pw']) ) {
    echo '<tr><td width=200>Inleveren voor:</td><td>'.$eindd.'</td></tr>';
  }
  else {
    if ( $eindd == "0000-00-00" )
      $dtm = "";
    else
      $dtm = $eindd;
    echo '<tr><td width=200>Inleveren voor:</td><td><input type="text" name="deadline" value="'.$dtm.'" /> (jjjj-mm-dd)</td></tr>';
  }

  echo '</table></ul>';

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

//////////////////
// CERTIFICATEN //
//////////////////

  echo '<h1 id="f16b" class="cf">Certificaten</h1>';
  echo '<ul id="f12b">';

  if ( empty( $_SESSION['pw']) ) {

    // ZONDER DOCENTCODE

    $nil = TRUE;
    $sql = 'SELECT certificaat FROM certificaat'.
           ' INNER JOIN pro_certificaat USING(idcertificaat)'.
           ' WHERE idproject='.$_SESSION['idp'].' ORDER BY certificaat';
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

    $sql = 'SELECT idcertificaat FROM pro_certificaat WHERE idproject='.$_SESSION['idp'];
    $q = $conn->query( $sql);
    $q->setFetchMode( PDO::FETCH_ASSOC);
    $idcurr = array();
    while ( $r = $q->fetch() )
      $idcurr[] = $r['idcertificaat'];
    certificatenCheckLijst( FALSE, $idcurr);
  }

  echo '</ul>';

/////////////
// BEWAREN //
/////////////

  if ( !empty( $_SESSION['pw']) )
    echo '<h1 id="f16b" class="cf">Bewaren <input type="submit" name="bewaar" value=">>" /> </h1>';

  echo '</form>';
?>

</body>

</html>
