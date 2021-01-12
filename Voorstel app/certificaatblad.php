<?php

session_start();

$opdracht = "";
$status = 0;

$host = 'localhost';
$dbname = 'BMTT';
$username = 'docent';
$password = $_SESSION['pw'];

try {
  $conn = new PDO( "mysql:host=$host;dbname=$dbname", $username, $password);

  $sql = 'SELECT * FROM certificaat WHERE idcertificaat='.$_SESSION['idc'];
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  if ( $r = $q->fetch() ) {
    $certifcaat = $r['opdracht'];
    $idcat = $r['idcategorie'];
    $blad = $r['beschrijving'];
  }
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
$title = 'T&T - Certificaatblad '.$certifcaat;
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

  echo '<form action="certificaatbladdb.php" method="post">';
//  echo '<input type="hidden" name="return" value="'.$_POST['return'].'" />';

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

///////////////
// CATEGORIE //
///////////////

  echo '<h1 id="f16b" class="cf">Categorie</h1>';
  echo '<ul id="f12b"><table>';

  echo '<br/><select id="f12" name="idcat" style="width:250px" >';
  $sql = 'SELECT * FROM categorie';
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  while ( $r = $q->fetch() )
    echo '<option value="'.$r['idcategorie'].'">'.$r['categorie'].'</option>';
  echo '</select>';

//////////////////////
// NIEUWE CATEGORIE //
//////////////////////

    echo '<h1 id="f12" class="cb">Nieuwe categorie:';
    echo '<input type="Text" name="catNaam" value="<naam>" style="width:200px"/>';
    echo '<input type="submit" name="catNieuw" value=">>"/>';
    echo '</h1>';

/////////////
// BEWAREN //
/////////////

  echo '<h1 id="f16b" class="cf">Bewaren <input type="submit" name="bewaar" value=">>" /> </h1>';

  echo '</form>';
?>

</body>

</html>
