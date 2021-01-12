<?php

session_start();

$opdracht = "";
$credits = 0;

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

  $sql = 'SELECT opdracht,credits FROM opdracht WHERE idopdracht='.$_SESSION['ido'];
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  if ( $r = $q->fetch() ) {
    $opdracht = $r['opdracht'];
    $credits = $r['credits'];
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
$title = 'T&T - Opdracht '.$opdracht;
echo '<title>'.$title.'</title>';
include 'style.css';
?>
</head>

<body>

<?php
  if ( empty( $_SESSION['idk']) )
    echo '<a href="index.php">';
  else
    echo '<a href="klas.php">';
  echo '<img src="media/brederomavologo.jpg" alt="LOGO" width=75 /></a>';

  if ( $_SESSION['pw'] == "" )
    echo '<h1 id="f24b" class="ca1">'.$title.'</h1>';
  else
    echo '<h1 id="f24b" class="ca2">'.$title.'</h1>';

  echo '<br/>';

  echo '<form action="opdrachtdb.php" method="post">';

/////////////////////
// OPDRACHT VOORAF //
/////////////////////

  echo '<h1 id="f16b" class="cf">Voorbereidende opdrachten</h1>';
  echo '<ul id="f12b">';

  $sql = 'SELECT certificaat FROM certificaat INNER JOIN opd_certificaat_i USING (idcertificaat) WHERE idopdracht='.$_SESSION['ido'];
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  $cert = "";
  $nil = TRUE;
  if ( $r = $q->fetch() )
    $nil = FALSE;

  if ( $nil )
    echo "Geen<br/>";
  else {

    echo '<table><tr><td width=250><u>Opdracht</u></td><td><u>Afhankelijk van</u></td></tr>';

    $cid = array();
    $copdr = array();
    $pid = array();
    $popdr = array();
    $klaar = array();

    $sql = 'CALL opd_OpdrachtenVooraf( "'.$_SESSION['ido'].'")';
    $q = $conn->query( $sql);
    $q->setFetchMode( PDO::FETCH_ASSOC);
    while ( $r = $q->fetch() ) {
      $cid[] = $r['idopdracht'];
      $copdr[] = $r['opdracht'];
      $pid[] = $_SESSION['ido'];
      $popdr[] = $opdracht;
    }
    $ix = 0;
    while ( $ix < count( $cid) ) {
      $sql = 'CALL opd_OpdrachtenVooraf( "'.$cid[$ix].'")';
      $q = $conn->query( $sql);
      $q->setFetchMode( PDO::FETCH_ASSOC);
      while ( $r = $q->fetch() ) {
        if ( !in_array( $r['idopdracht'], $cid) ) { // voorkom een cirkelgang
          $cid[] = $r['idopdracht'];
          $copdr[] = $r['opdracht'];
          $pid[] = $cid[$ix];
          $popdr[] = $copdr[$ix];
        }
      }
      $ix++;
    }

    $max = count( $pid);
    $ix = 0;
    echo '<tr>';
    while ( $ix < $max ) {
      if ( in_array( $pid[$ix], $klaar) ) {
        $ix++;
        continue;
      }
      $klaar[] = $pid[$ix];
      echo '<td></td></tr><tr><td>'.$popdr[$ix].'</td>';

      $jx = $ix;
      while ( $jx < $max ) {
        if ( $pid[$jx] == $pid[$ix] ) {
          echo '<td>'.$copdr[$jx];
          if ( in_array( $cid[$jx], $pid) )
            echo " *";
          echo '</td></tr><tr>';
        }
        $jx++;
        if ( $jx < $max )
          echo '<td></td>';
      }

      $ix++;
    }
    echo '</tr></table><br/>(* heeft zelf opdachten vooraf)';
  }

  echo '</ul>';

/////////////
// CREDITS //
/////////////

  echo '<h1 id="f16b" class="cf">Credits</h1>';
  echo '<ul id="f12b">';

  $cr = intval($credits) / 5;
  echo 'Maximaal aantal credits: '.intval($credits).'<br/><br/>';
  echo 'Het werkelijk aantal credits wordt als volgt berekend:<br/>';
  echo '<ul>maximaal &times; cijfer &div; 10</ul>';

  echo '</ul>';

////////////////
// LEERLINGEN //
////////////////

  echo '<h1 id="f16b" class="cf">Leerlingen</h1>';
  echo '<ul id="f12b">';

  echo '<input type="submit" name="letter_a" value="A" />';
  echo '<input type="submit" name="letter_b" value="B" />';
  echo '<input type="submit" name="letter_c" value="C" />';
  echo '<input type="submit" name="letter_d" value="D" />';
  echo '<input type="submit" name="letter_e" value="E" />';
  echo '<input type="submit" name="letter_f" value="F" />';
  echo '<input type="submit" name="letter_g" value="G" />';
  echo '<input type="submit" name="letter_h" value="H" />';
  echo '<input type="submit" name="letter_i" value="I" />';
  echo '<input type="submit" name="letter_j" value="J" />';
  echo '<input type="submit" name="letter_k" value="K" />';
  echo '<input type="submit" name="letter_l" value="L" />';
  echo '<input type="submit" name="letter_m" value="M" />';
  echo '<input type="submit" name="letter_n" value="N" />';
  echo '<input type="submit" name="letter_o" value="O" />';
  echo '<input type="submit" name="letter_p" value="P" />';
  echo '<input type="submit" name="letter_q" value="Q" />';
  echo '<input type="submit" name="letter_r" value="R" />';
  echo '<input type="submit" name="letter_s" value="S" />';
  echo '<input type="submit" name="letter_t" value="T" />';
  echo '<input type="submit" name="letter_u" value="U" />';
  echo '<input type="submit" name="letter_v" value="V" />';
  echo '<input type="submit" name="letter_w" value="W" />';
  echo '<input type="submit" name="letter_x" value="X" />';
  echo '<input type="submit" name="letter_y" value="Y" />';
  echo '<input type="submit" name="letter_z" value="Z" />';

  echo '<br/><br/><br/>';

  $sql = 'SELECT * FROM leerling'.
         ' INNER JOIN llg_opdracht USING (idleerling) WHERE idopdracht='.$_SESSION['ido'];

  if ( isset( $_POST['letter_a']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "A"';
  if ( isset( $_POST['letter_b']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "B"';
  if ( isset( $_POST['letter_c']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "C"';
  if ( isset( $_POST['letter_d']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "D"';
  if ( isset( $_POST['letter_e']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "E"';
  if ( isset( $_POST['letter_f']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "F"';
  if ( isset( $_POST['letter_g']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "G"';
  if ( isset( $_POST['letter_h']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "H"';
  if ( isset( $_POST['letter_i']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "I"';
  if ( isset( $_POST['letter_j']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "J"';
  if ( isset( $_POST['letter_k']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "K"';
  if ( isset( $_POST['letter_l']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "L"';
  if ( isset( $_POST['letter_m']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "M"';
  if ( isset( $_POST['letter_n']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "N"';
  if ( isset( $_POST['letter_o']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "O"';
  if ( isset( $_POST['letter_p']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "P"';
  if ( isset( $_POST['letter_q']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "Q"';
  if ( isset( $_POST['letter_r']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "R"';
  if ( isset( $_POST['letter_s']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "S"';
  if ( isset( $_POST['letter_t']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "T"';
  if ( isset( $_POST['letter_u']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "U"';
  if ( isset( $_POST['letter_v']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "V"';
  if ( isset( $_POST['letter_w']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "W"';
  if ( isset( $_POST['letter_x']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "X"';
  if ( isset( $_POST['letter_y']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "Y"';
  if ( isset( $_POST['letter_z']) ) $sql = $sql.' AND LEFT( voornaam, 1) = "Z"';

  $sql = $sql.' ORDER BY klas,voornaam';
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  $nil = TRUE;
  while ( $r = $q->fetch() ) {
    if ( empty( $r['tussenvoegsel']) )
      echo $r['voornaam'].' '.$r['achternaam'];
    else
      echo $r['voornaam'].' '.$r['tussenvoegsel'].' '.$r['achternaam'];
    echo ' ('.$r['klas'].')';
    if ( $r['ingeleverd'] <> "0000-00-00" )
      echo " *";
    echo '<br/>';
    $nil = FALSE;
  }
  if ( $nil ) echo "Geen<br/>";
  else echo "</br>(* is klaar met de opdracht)";

  echo '</ul>';

//////////////////////////
// OPDRACHTBESCHRIJVING //
//////////////////////////

  echo '<h1 id="f16b" class="cf">Opdrachtblad ';
  echo '<input type="SUBMIT" name="opdblad" value=">>" /></h1>';

  echo '</form>';
?>

</body>

</html>

