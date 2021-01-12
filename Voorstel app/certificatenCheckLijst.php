<?php

function certificatenCheckLijst( $lossen, array $idvink)

// $lossen: TRUE  lijst met 'lossen' ten behoeve van de uit te geven certificaten
//          FALSE lijst zonder 'lossen' ten behoeve van de benodigde certificaten
//          Losse certificaten kunnen nog door geen enkele opdracht worden behaald

// $idvink: Dit zijn alle certificaten die in de lijst moeten worden aangevinkt

// $idniek: Deze certificaten mogen niet in de lijst voorkomen

{
  global $conn;

  $cert = array();
  $idcert = array();
  $chcert = array();

  // Verzamel alle certificaten

  $sql = 'SELECT * FROM certificaat ORDER BY certificaat';
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);

  while ( $r = $q->fetch() ) {
    $cert[] = $r['certificaat'];
    $idcert[] = $r['idcertificaat'];
    if ( in_array( $r['idcertificaat'], $idvink) )
      $chcert[] = TRUE;
    else
      $chcert[] = FALSE;
  }

  // Losse certificaten:
  // Als $lossen TRUE is worden ze gemarkeerd met *
  // Anders worden ze uit de lijst verwijderd

  for ( $ix = 0; $ix < count( $idcert); $ix++ ) {
    $sql = 'CALL crt_OpdrachtenVooraf( '.$idcert[$ix].')';
    $q = $conn->query( $sql);
    $q->setFetchMode( PDO::FETCH_ASSOC);
    $nil = TRUE;
    if ( $r = $q->fetch() )
      $nil = FALSE;
    if ( $nil ) {
      if ( $lossen )
        $cert[$ix] = $cert[$ix].' *';
      else {
        unset( $cert[$ix]);
        unset( $idcert[$ix]);
        unset( $chcert[$ix]);
      }
    }
  }
  if ( !$lossen ) {
    $cert = array_values( $cert);
    $idcert = array_values( $idcert);
    $chcert = array_values( $chcert);
  }

  // Maak de lijst aan

  echo '<table><tr><td width=250></td><td width=250></td><td></td></tr>';

  $max = count( $cert);
  $rows = intval( $max / 3 + 1);
  if ( $lossen ) $pref = "U_";
  else $pref = "I_";

  for ( $ix = 0; $ix < $rows; $ix++ ) {
    $col1 = $ix;
    $col2 = $rows + $ix;
    $col3 = 2*$rows + $ix;
    echo '<tr>';
    if ( $chcert[$col1] )
      echo '<td><input type="checkbox" name="'.$pref.$cert[$col1].'" value="'.$idcert[$col1].'" checked />'.$cert[$col1].'</td>';
    else
      echo '<td><input type="checkbox" name="'.$pref.$cert[$col1].'" value="'.$idcert[$col1].'" />'.$cert[$col1].'</td>';

    if ( $chcert[$col2] )
      echo '<td><input type="checkbox" name="'.$pref.$cert[$col2].'" value="'.$idcert[$col2].'" checked />'.$cert[$col2].'</td>';
    else
      echo '<td><input type="checkbox" name="'.$pref.$cert[$col2].'" value="'.$idcert[$col2].'" />'.$cert[$col2].'</td>';

    if ( $col3 < $max ) {
      if ( $chcert[$col3] )
        echo '<td><input type="checkbox" name="'.$pref.$cert[$col3].'" value="'.$idcert[$col3].'" checked />'.$cert[$col3].'</td>';
      else
        echo '<td><input type="checkbox" name="'.$pref.$cert[$col3].'" value="'.$idcert[$col3].'" />'.$cert[$col3].'</td>';
    }
    echo '</tr>';
  }

  echo '</table>';

  if ( !$lossen )
    echo '<br/>NB. Certificaten, die niet door een opdracht kunnen worden behaald, zijn uit de lijst weggelaten.<br/>';
  else
    echo '<br/>* Deze certificaten kunnen nog niet door een opdracht worden behaald.<br/>';
}

?>
