<?php

session_start();

$_SESSION['pw'] = "";
$_SESSION['idk'] = "";

$host = 'localhost';
$dbname = 'BMTT';
$username = 'gast';
$password = 'welkom';

try {
  $conn = new PDO( "mysql:host=$host;dbname=$dbname", $username, $password);
  }
catch (PDOException $pe) {
  die("Could not connect to the database $dbname :" . $pe->getMessage());
  }
?>

<!DOCTYPE html>
<html>

<head>
<?php
echo '<title>Technologie en Toepassing</title>';
include 'style.css';
?>
</head>

<body>

<a href="index.php"><img src="media/brederomavologo.jpg" alt="LOGO" width=75 /></a>
<h1 id="f24b" class="ca1">Technologie en Toepassing</h1>
<br/>


<form action="indexdo.php" method="post">

  <h1 id="f12" class="cf">Code:
  <input type="password" name="pw" size=28 />
  </h1><br/<br/>

  <h1 id="f16" class="ce">Klas:
  <select id="f12" name="idk" style="width:250px">
<?php
  $sql = 'SELECT DISTINCTROW klas FROM leerling ORDER BY klas';
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  while ( $r = $q->fetch() )
    echo "<option value='".$r['klas']."'>".$r['klas']."</option>";
?>
  </select>
  <input type="submit" name="Klas" value=">>"/>
  </h1>

  <h1 id="f16" class="cd">Project:
  <select id="f12" name="idp" style="width:250px">
<?php
  $sql = 'SELECT idproject,project FROM project WHERE status < 5 ORDER BY project';
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  while ( $r = $q->fetch() )
    echo "<option value='".$r['idproject']."'>".$r['project']."</option>";
?>
  </select>
  <input type="submit" name="Project" value=">>"/>
  </h1>

  <h1 id="f16" class="cc">Opdracht:
  <select id="f12" name="ido" style="width:250px">
<?php
  $sql = 'SELECT opdracht.idopdracht,opdracht FROM opdracht ORDER BY opdracht';
  $q = $conn->query( $sql);
  $q->setFetchMode( PDO::FETCH_ASSOC);
  while ( $r = $q->fetch() )
    echo "<option value='".$r['idopdracht']."'>".$r['opdracht']."</option>";
?>
  </select>
  <input type="submit" name="Opdracht" value=">>"/>
  </h1>

  <br/><br/><h1 id="f12" class="cb">Onderhoud:
  <input type="submit" name="Onderhoud" value=">>"/>
  </h1>

</form>

</body>

</html>

