<?php

session_start();

$host = 'localhost';
$dbname = 'BMTT';
if ( $_SESSION['pw'] == "" ) {
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
?>

<!DOCTYPE html>
<html>

<head>
<?php
$title = 'T&T - Klas '.$_SESSION['idk'];
echo '<title>'.$title.'</title>';
include 'style.css';
?>
</head>

<body>

  <a href="index.php"><img src="media/brederomavologo.jpg" alt="LOGO" width=75 /></a>
<?php
  if ( $_SESSION['pw'] == "" )
    echo '<h1 id="f24b" class="ca1">'.$title.'</h1>';
  else
    echo '<h1 id="f24b" class="ca2">'.$title.'</h1>';

  echo '<br/>';

  echo '<form action="klasdb.php" method="post">';

/////////////
// PERIODE //
/////////////

  echo '<h1 id="f12" class="cf">Periode: ';
  echo '<select id="f12" name="idr" style="width:250px">';

  $sql = 'SELECT * FROM rapport ORDER BY begin DESC';
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  $first = TRUE;
  while ( $r = $q->fetch() ) {
    echo "<option value='".$r['idrapport']."'";
    if ( $first ) {
      echo " selected='selected'";
      $first = FALSE;
    }
    echo ">".$r['rapport']."</option>";
  }

  echo '</select>';
  echo '</h1><br/<br/>';

//////////////
// LEERLING //
//////////////

  echo '<h1 id="f16" class="cf">Leerling: ';
  echo '<select id="f12" name="idl" style="width:250px">';

  $sql = 'SELECT idleerling,voornaam,tussenvoegsel,achternaam FROM leerling WHERE klas="'.$_SESSION['idk'].'" ORDER BY achternaam,voornaam';
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  while ( $r = $q->fetch() )
    echo "<option value='".$r['idleerling']."'>".$r['voornaam']." ".$r['tussenvoegsel']." ".$r['achternaam']."</option>";
  echo '</select>';
  echo '<input type="submit" name="Leerling" value=">>"/>';
  echo '</h1>';

/////////////
// PROJECT //
/////////////

  echo '<h1 id="f16" class="ce">Project: ';
  echo '<select id="f12" name="idp" style="width:250px">';

//  $sql = 'CALL kls_Projecten( "'.$_SESSION['idk'].'")';
  $sql = 'SELECT project,idproject FROM project WHERE status < 5';
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  while ( $r = $q->fetch() )
    echo "<option value='".$r['idproject']."'>".$r['project']."</option>";
  echo '</select>';
  echo '<input type="submit" name="Project" value=">>"/>';
  echo '</h1>';

  echo '<h1 id="f16" class="cd">Opdracht: ';
  echo '<select id="f12" name="ido" style="width:250px">';

//////////////
// OPDRACHT //
//////////////

  $sql = 'SELECT opdracht.idopdracht,opdracht FROM opdracht ORDER BY opdracht';
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  while ( $r = $q->fetch() )
    echo "<option value='".$r['idopdracht']."'>".$r['opdracht']."</option>";

  echo '</select>';
  echo '<input type="submit" name="Opdracht" value=">>"/>';
  echo '</h1>';

///////////////////
// NIEUW PROJECT //
///////////////////

  if ( !empty( $_SESSION['pw']) ) {

    echo '<br/><br/><h1 id="f12" class="cb">Start project: ';
    echo '<input type="Text" name="proNaam" value="<naam>" style="width:200px"/>';
    echo '<input type="submit" name="proNieuw" value=">>"/>';
    echo '</h1>';

    // on success 'indexdo.php' returns idp as -1 and pronaam with the project name
    if ( $_SESSION['idp'] < 0 )
      echo '<ul id="f12">Project "'.$_SESSION['pronaam'].'" is toegevoegd</ul>';
  }

  echo '</form>';
?>

</body>

</html>

