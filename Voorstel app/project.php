<?php

session_start();

$opdready = TRUE;
$project = "";
$status = 0;

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

  $sql = 'SELECT * FROM project WHERE idproject='.$_SESSION['idp'];
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  if ( $r = $q->fetch() ) {
    $project = $r['project'];
    $startd = $r['startdatum'];
    $eindd = $r['einddatum'];
    $verslagd = $r['verslagdatum'];
    $status = $r['status'];
    $credits = $r['credits'];
    $blad = $r['beschrijving'];
  }
}
catch (PDOException $pe) {
  header( "Location:index.php");
  exit;
}

////////////////////////////
// FUNCTION OPDRACHTLIJST //
////////////////////////////

function opdrachtLijst( $idopdr, $opdr, $level)
{
  global $opdready;
  global $conn;

  // welke leerlingen hebben de opdracht gedaan?

  $llg = array();
  $sql = 'SELECT voornaam, tussenvoegsel, achternaam FROM leerling'.
         ' INNER JOIN llg_project USING (idleerling)'.
         ' INNER JOIN llg_opdracht USING (idleerling)'.
         ' WHERE idproject='.$_SESSION['idp'].' AND idopdracht='.$idopdr.
         ' ORDER BY voornaam, achternaam';
  $q = $conn->prepare( $sql);
  $q->execute();
  while ( $r = $q->fetch() )
    $llg[] = $r['voornaam'].' '.$r['tussenvoegsel'].' '.$r['achternaam'];
  $llgcnt = count( $llg);

  // druk huidige opdracht level af

  echo '<table><tr>';
  for ( $ix = 0; $ix < $level + 1; $ix++ )
    echo '<td width=30></td>';
  echo '<td>-</td><td>'.$opdr;

  if ( $llgcnt ) {
    echo ' (';
    for ( $ix = 0; $ix < $llgcnt; $ix++ ) {
      echo $llg[$ix];
      if ( $ix < $llgcnt - 1 )
        echo ",";
    }
    echo ')';
  }
  echo '</td></tr></table>';

  if ( !$llgcnt ) {
    if ( !$level )
      $opdready = FALSE;

    // vraag volgende level uit database

    $aidOpdr = array();
    $aOpdr = array();
    $sql = 'CALL opd_OpdrachtenVooraf( "'.$idopdr.'")';
    $q = $conn->prepare( $sql);
    $q->execute();

    // verzamel eerst alle rijen
    // daarna pas recursief aanroepen
    // anders raakt de recordset gecorrumpeerd

    while ( $r = $q->fetch() ) {
      $aidOpdr[] = $r['idopdracht'];
      $aOpdr[] = $r['opdracht'];
    }
    for ( $ix = 0; $ix < count( $aidOpdr); $ix++ )
      opdrachtLijst( $aidOpdr[$ix], $aOpdr[$ix], $level+1);
  }
}

////////////////////
// FUNCTION EINDE //
////////////////////
?>

<!DOCTYPE html>
<html>

<head>
<?php
$title = 'T&T - Project '.$project;
echo '<title>'.$title.'</title>';
include 'style.css';
?>
</head>

<body>

<?php
  if ( empty( $_SESSION['idk']) )
    echo '<a href="index.php"><img src="media/brederomavologo.jpg" alt="LOGO" width=75 /></a>';
  else
    echo '<a href="klas.php"><img src="media/brederomavologo.jpg" alt="LOGO" width=75 /></a>';

  if ( $_SESSION['pw'] == "" )
    echo '<h1 id="f24b" class="ca1">'.$title.'</h1>';
  else
    echo '<h1 id="f24b" class="ca2">'.$title.'</h1>';

  echo '<br/>';

  echo '<form action="projectdb.php" method="post">';

////////////////
// DEELNEMERS //
////////////////

  echo '<h1 id="f16b" class="cf">Deelnemers</h1>';
  echo '<ul id="f12b">';

  $sql = 'SELECT * FROM leerling'.
         ' INNER JOIN llg_project USING (idleerling) WHERE idproject='.$_SESSION['idp'].
         ' ORDER BY klas,voornaam';
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  $nil = TRUE;
  while ( $r = $q->fetch() ) {
    if ( empty( $r['tussenvoegsel']) )
      echo $r['voornaam'].' '.$r['achternaam'].' ('.$r['klas'].')<br/>';
    else
      echo $r['voornaam'].' '.$r['tussenvoegsel'].' '.$r['achternaam'].' ('.$r['klas'].')<br/>';
    $nil = FALSE;
  }
  if ( $nil ) echo "Geen<br/>";

  echo '</ul>';

///////////////////////
// OPDRACHTEN VOORAF //
///////////////////////

  echo '<h1 id="f16b" class="cf">Voorbereidende opdrachten</h1>';
  echo '<ul id="f12b">';

  $idc = array();
  $crt = array();

  $sql = 'SELECT idcertificaat,certificaat FROM pro_certificaat'.
         ' INNER JOIN certificaat USING(idcertificaat)'.
         ' WHERE idproject='.$_SESSION['idp'];
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  while ( $r = $q->fetch() ) {
      $idc[] = $r['idcertificaat'];
      $crt[] = $r['certificaat'];
  }

  for ( $ix = 0; $ix < count( $idc); $ix++ ) {
    $ido = array();
    $opd = array();

    echo 'Certificaat: <u>'.$crt[$ix].'</u><br/>';
    $sql = 'CALL crt_OpdrachtenVooraf( "'.$idc[$ix].'")';
    $q = $conn->query( $sql);
    $q->setFetchMode( PDO::FETCH_ASSOC);
    while ( $r = $q->fetch() ) {
        $ido[] = $r['idopdracht'];
        $opd[] = $r['opdracht'];
    }

    for ( $jx = 0; $jx < count( $ido); $jx++ )
      opdrachtLijst( $ido[$jx], $opd[$jx], 0);

    echo '<br/>';
  }

  echo '</ul>';

////////////
// STATUS //
////////////

  // Variabele $status is bij het openen van de database ingesteld

  echo '<h1 id="f16b" class="cf">Status</h1>';
  echo '<ul id="f12b">';

  $stts = "Geen status bekend";
  $fase = "aanvraag";
  switch ( $status ) {
    case 0 : $stts = "Het project is aangevraagd";
             if ( !$nil ) $stts = $stts." en wordt nu beschreven";
             $fase = "beschrijving";
             break;
    case 1 : $stts = "Het project is beschreven<br/>";
             $stts = $stts."Er wordt een takenverdeling en een planning gemaakt";
             $fase = "planning";
             break;
    case 2 : $stts = "Er is een takenverdeling en een planning<br/>";
             $stts = $stts."Er wordt aan de onderdelen van het product gewerkt";
             $fase = "protofase";
             break;
    case 3 : $stts = "De onderdelen van het product werken<br/>";
             $stts = $stts."Nu wordt het eindproduct gemaakt";
             $fase = "product";
             break;
    case 4 : $stts = "Het eindproduct is ingeleverd<br/>";
             $stts = $stts."De deelnemers werken aan hun presentatie";
             $fase = "presentatie";
             break;
    case 5 : $stts = "<u>Het project is afgesloten</u>";
             break;
  }
  echo $stts.'<br/><br/>';

  // LET OP! $fase WORDT IN projectdb.php GEBRUIKT VOOR $_POST

  if ( !$opdready ) // is eerder via routine 'opdrachtLijst' ingesteld
    echo "Let op! Nog niet alle certificaten zijn behaald<br/><br/>";

  if ( !empty( $_SESSION['pw']) && ($status < 5) ) {
    if ( $nil )
      echo '<input type="SUBMIT" name="stop" value="Stop dit project" />';
    else
      echo '<input type="SUBMIT" name="'.$fase.'" value="Accepteer '.$fase.'" />';
    echo '<br/><br/>';
  }

  echo 'Startdatum: '.$startd.'<br/>';
  if ( $status ) {
    if ( $eindd <> "0000-00-00" )
      echo 'Einddatum : '.$eindd.'<br/>';
    else
      echo 'Einddatum : niet gepland<br/>';
  }

  if ( !$status ) {
    echo '</ul><h1 id="f16b" class="cf">Projectbeschrijving</h1><ul id="f12b">';
    echo '<input type="SUBMIT" name="problad" value="Vul het projectblad in" />';
    echo '<br/>';
    exit;
  }

  echo '</ul>';

/////////////
// CREDITS //
/////////////

  echo '<h1 id="f16b" class="cf">Credits</h1>';
  echo '<ul id="f12b">';

  $cr = intval($credits) / 5;
  echo 'Het project levert in totaal '.intval($credits).' credits op<br/><br/>Verdeeld over:<br/><br/>';
  echo '<table>';
  echo '<tr><td width=200>Beschrijving:</td><td>'.intval($credits * 10 / 100).' credits</td></tr>';
  echo '<tr><td width=200>Planning:</td><td>'.intval($credits * 5 / 100).' credits</td></tr>';
  echo '<tr><td width=200>Protofase:</td><td>'.intval($credits * 30 / 100).' credits</td></tr>';
  echo '<tr><td width=200>Eindproduct:</td><td>'.intval($credits * 30 / 100).' credits</td></tr>';
  echo '<tr><td width=200>Presentatie:</td><td>'.intval($credits * 25 / 100).' credits</td></tr></table>';

  echo '</ul>';

/////////////////////////
// PROJECTBESCHRIJVING //
/////////////////////////

  echo '<h1 id="f16b" class="cf">Projectblad ';
  echo '<input type="SUBMIT" name="problad" value=">>" /></h1>';

  echo '</form>';
?>

</body>

</html>
