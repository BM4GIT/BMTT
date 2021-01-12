<?php
session_start();

$leerling = "";

$host = 'localhost';
$dbname = 'BMTT';
if ( empty( $_SESSION['pw']) ) {
  $username = 'gast';
  $password = 'welkom';
}
else {
  $username = 'docent';
  $password = $_SESSION['pw'];
}

try {
  $conn = new PDO( "mysql:host=$host;dbname=$dbname", $username, $password);

//////////////////////
// LEERLINGGEGEVENS //
//////////////////////

  $sql = 'SELECT voornaam,tussenvoegsel,achternaam FROM leerling WHERE idleerling='.$_SESSION['idl'];
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  if ( $r = $q->fetch() ) {
    if ( $r['tussenvoegsel'] == "" )
      $leerling = $r['voornaam'].' '.$r['achternaam'];
    else
      $leerling = $r['voornaam'].' '.$r['tussenvoegsel'].' '.$r['achternaam'];
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
$title = 'T&T - '.$leerling.', '.$_SESSION['idk'];
echo '<title>'.$title.'</title>';
include 'style.css';
?>
</head>

<body>

<?php
  echo '<a href="klas.php"><img src="media/brederomavologo.jpg" alt="LOGO" width=75 /></a>';

  if ( $_SESSION['pw'] == "" )
    echo '<h1 id="f24b" class="ca1">'.$title.'</h1>';
  else
    echo '<h1 id="f24b" class="ca2">'.$title.'</h1>';

  echo '<br/>';

  echo '<form action="leerlingdb.php" method="post">';

///////////////
// PROJECTEN //
///////////////

  echo '<h1 id="f16b" class="cf">Projecten</h1>';
  echo '<ul id="f12b">';

  // Toon ALLE projecten waar de leerling aan deelgenomen heeft
  // (ook die uit voorgaande periodes)
  // Het laatste project kan eventueel nog gaande zijn

  $status = -1;
  $proklaar = TRUE;
  $proverslag = TRUE;
  $sql = 'CALL llg_Projecten( "'.$_SESSION['idl'].'")';
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  while ( $r = $q->fetch() ) {
    echo $r['project'];
    $status = $r['status'];
    switch ( $r['status'] ) {
      case 0 : echo " (beschrijft het project)"; 
               break;
      case 1 : echo " (plannen het project)";
               break;
      case 2 : echo " (in de protofase)";
               break;
      case 3 : echo " (werkt aan het eindproduct)";
               break;
      case 4 : echo " (bereidt de presentatie voor)";
               break;
      case 5 : // project is afgesloten maar de leerling
               // heeft mogelijk nog geen verslag ingeleverd
               if ( $r['ingeleverd'] == "0000-00-00" )
                 $proverslag = FALSE;
               break;
    }
    echo '<br/>';

    // Met status < 5 is er een project gaande
    // Met status -1 is er geen project geweest

    if ( $status >= 0 && $status < 5 ) {
      $proklaar = FALSE;
      // de volgende regel stelt het open project van de leerling op het huidige in
      // zodat 'leerlingdb.php' het juist project updatet
      $_SESSION['idp'] = $r['idproject'];
    }
  }

  if ( $status < 0 || $status == 5 ) {

      // Bij status -1 heeft de leerling nog niet aan een project deelgenomen
      // Bij status  5 zijn alle projecten van de leerling afgesloten

      if ( $status < 0 )
        echo "Geen<br/>";

      // Wanneer een leerling niet aan een project deelneemt
      // en het verslag van het laatste project heeft ingeleverd
      // kan deze een lopend project bekijken
      // (en hieronder met docentcode bij het project aansluiten)

      if ( ($status < 0) || $proverslag ) {
        echo '<br/><select id="f12" name="idp" style="width:250px" >';
        $sql = 'SELECT idproject,project FROM project WHERE status < 5 ORDER BY project';
        $q = $conn->query( $sql);
        $q->setFetchMode( PDO::FETCH_ASSOC);
        while ( $r = $q->fetch() )
          echo '<option value="'.$r['idproject'].'">'.$r['project'].'</option>';
        echo '</select>';

        if ( empty( $_SESSION['pw']) )
          echo '<input type="submit" name="proBekijk" value="Bekijken"/></ul>';
      }
  }

  if ( !empty( $_SESSION['pw']) ) {

    // Knoppen voor bewerking zijn alleen met docentcode toegestaan

    if ( !$proklaar ) {
      echo '<br/><input type="submit" name="proStop" value="Project verlaten"/></ul>';
    }
    else
    if ( !$proverslag ) {
      echo '<br/><input type="submit" name="proVerslag" value="Verslag inleveren"/>';
      echo '<input type="submit" name="proGeenVerslag" value="Zonder verslag afsluiten"/></ul>';
    }
    else {
      echo '<input type="submit" name="proNieuw" value="Aanmelden"/></ul>';
    }
  }

  echo "</ul>";

////////////////
// OPDRACHTEN //
////////////////

  echo '<h1 id="f16b" class="ce">Opdrachten</h1>';
  echo '<ul id="f12b">';

  $opdklaar = TRUE;

  // Toon ALLE opdrachten die de leerling heeft afgerond
  // (ook die uit voorgaande periodes)
  // De laatste opdracht kan eventueel nog gaande zijn

  $sql = 'CALL llg_Opdrachten( "'.$_SESSION['idl'].'")';
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  $nil = TRUE;
  while ( $r = $q->fetch() ) {
    echo $r['opdracht'];
    if ( $r['ingeleverd'] == "0000-00-00" ) {
      echo " (bezig)";
      $opdklaar = FALSE;
      $_SESSION['ido'] = $r['idopdracht'];
    }
    echo '<br/>';
    $nil = FALSE;
  }
  if ( $nil )
    echo "Geen<br/>";

  echo '<br/>';

  if ( $opdklaar ) {

    // Als er geen lopende opdracht gaande is
    // laat dan een keuzelijst met opdrachten zien
    // die aansluiten bij de eerder gemaakte opdrachten

    echo '<select id="f12" name="ido" style="width:250px">';

    if ( $proklaar ) {

      // Als een leerling niet aan een project deelneemt,
      // heeft deze de mogelijkheid om een vrije opdracht te kiezen

      $sql = 'CALL llg_VrijeOpdrachten( "'.$_SESSION['idl'].'")';
      $q = $conn->query( $sql);
      $q->setFetchMode( PDO::FETCH_ASSOC);
      while ( $r = $q->fetch() )
        echo '<option value="'.$r['idopdracht'].'">'.$r['opdracht'].'</option>';
    }
    else {

      // Als een leerling aan een project deelneemt
      // worden alleen opdrachten gekozen om project-certificaten te halen
      // Deze worden eerst verzameld en dan in de keuzelijst geplaatst

      $lijst = array();
      $lijstix = 0;
      $opdr = array();

      // Begin met het verzamelen van alle opdrachten die direct leiden tot project-certificaten

      $sql = 'CALL llgpro_OpdrachtenVooraf( "'.$_SESSION['idp'].'","'.$_SESSION['idl'].'")';
      $q = $conn->query( $sql);
      $q->setFetchMode( PDO::FETCH_ASSOC);
      while ( $r = $q->fetch() ) {
        // Verzamel alleen nieuwe opdrachten
        if ( !in_array( $r['idopdracht'], $lijst) ) {
          $lijst[] = $r['idopdracht'];
          $opdr[] = $r['opdracht'];
        }
      }

      // Verzamel vervolgens alle opdrachten die in een keten vooraf tot de benodigde certificaten leiden
      // De variabele $afhankelijk registreert of een parent-opdracht nog child-opdrachten heeft
      // Alleen opdrachten zonder child-opdrachten mogen in de keuzelijst verschijnen
      // omdat de leerling daarvoor de juiste certificaten heeft behaald

      while ( $lijstix < count( $lijst) ) {
        $sql = 'CALL llgopd_OpdrachtenVooraf( "'.$lijst[$lijstix].'","'.$_SESSION['idl'].'")';
        $q = $conn->query( $sql);
        $q->setFetchMode( PDO::FETCH_ASSOC);
        $afhankelijk = FALSE; // stel in eerste instantie in op geen child-opdrachten 
        while ( $r = $q->fetch() ) {
          // Verzamel alleen nieuwe opdrachten
          if ( !in_array( $r['idopdracht'], $lijst) ) {
            $lijst[] = $r['idopdracht'];
            $opdr[] = $r['opdracht'];
          }
          $afhankelijk = TRUE; // er blijken wel child-opdrachten te zijn
        }

        if ( !$afhankelijk )
          echo '<option value="'.$lijst[$lijstix].'">'.$opdr[$lijstix].'</option>';

        $lijstix++;
      }
    }
    echo '</select>';

    // Met de docentcode kan een nieuwe opdracht worden toegewezen
    // en zonder kan deze alleen worden bekeken

    if ( !empty( $_SESSION['pw']) )
      echo '<input type="submit" name="opdNieuw" value="Toewijzen"/></ul>';
    else
      echo '<input type="submit" name="opdBekijk" value="Bekijken"/></ul>';
  }
  else {

    // Als er een lopende opdracht gaande is
    // kan deze met de docentcode worden afgesloten

    if ( !empty( $_SESSION['pw']) ) {
        echo '<ul><input type="submit" name="opdSluit" value="Accepteren"/>';
        echo '<input type="submit" name="opdStop" value="Stoppen"/></ul>';
        echo '<ul id="f12b">Cijfer: <input type="text" name="cijfer" pattern="[0-9]{2}" size=2 /></ul>';
    }
  }

  echo '</ul>';

///////////////
// VOORTGANG //
///////////////

  echo '<h1 id="f16b" class="cd">Voortgang';

  $begin = "";
  $eind = "";
  $sql = 'SELECT * FROM rapport WHERE idrapport='.$_SESSION['idr'];
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  if ( $r = $q->fetch() ) {
    echo ' '.$r['rapport'];
    $rbegin = $r['begin'];
    $reind = $r['eind'];
    $maxcr = $r['maxcredits'];
    $d1 = new DateTime( $rbegin);
    $d2 = new DateTime( $reind);
    $d3 = new DateTime( 'now');
    $span = $d2->diff( $d1)->days;
    $diff = $d3->diff( $d1)->days;
    $fact = $span / $diff;
  }

  echo '</h1>';

  echo '<ul id="f12b"><table>';
  echo '<tr><td width=200><u>Project/Opdracht</u></td><td width=150><u>Reden</u></td>'.
       '<td width=150><u>Geaccepteerd</u></td><td><u>Credits (Cijfer)</u></td></tr>';

  $totaal = 0;
  $nil = TRUE;
  $proj = "";
  $sql = 'SELECT idcredits,credits,datum,onderwerp,reden,cijfer FROM llg_credits WHERE idleerling='.$_SESSION['idl'].
         ' AND datum >= "'.$rbegin.'" AND datum <= "'.$reind.'"';;
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  while ( $r = $q->fetch() ) {
    $cred = round( $r['credits'] * $r['cijfer'] / 10);
    echo '<tr><td>'.$r['onderwerp'].'</td><td>'.$r['reden'].'</td><td>'.$r['datum'].'</td><td>';

    if ( $r['reden'] == "Ingeleverd" ) {
      // Credits voor een opdracht met cijfer
      if ( empty( $_SESSION[ 'pw']) )
        echo $cred.' ('.$r['cijfer'].')</td></tr>';
      else
        echo $cred.' (<input type="text" name="cfr['.$r['idcredits'].']" value="'.$r['cijfer'].'" size=2 >)</td></tr>';
    }
    else
      // Credits voor een afgesloten project fase
      echo $cred.'</td></tr>';

    $totaal = $totaal + $cred;
    $nil = FALSE;
  }
  if ( $nil )
    echo '<tr><td>Geen</td><td>-</td><td>-</td><td>-</td></tr>';
  if ( !empty( $_SESSION[ 'pw']) ) {
    echo '<tr><td></td><td></td><td></td><td>';
    echo '<input type="submit" name="llgCijfers" value="Cijfers opslaan" /></td></tr>';
  }

  echo '</table><br/>';
  echo 'Totaal '.$totaal.' credits, Voorlopig rapportcijfer '.round($fact * $totaal * 10 / $maxcr, 1);

  echo '</ul>';

//////////////////
// CERTIFICATEN //
//////////////////

  echo '<h1 id="f16b" class="cd">Certificaten</h1>';
  echo '<ul id="f12b">';

  $nil = TRUE;
  $sql = 'CALL llg_Certificaten( "'.$_SESSION['idl'].'")';
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  while ( $r = $q->fetch() ) {
    echo $r['certificaat'].'<br/>';
    $nil = FALSE;
  }
  if ( $nil )
    echo 'Geen';

  echo '</ul>';

  echo '</form>';
?>
</body>

</html>
