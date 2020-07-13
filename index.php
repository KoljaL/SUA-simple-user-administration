<?php

$app_name = 'Simple User Administration';
// aktuelle Zeit für das "geaendert" FIELD
$dt = new DateTime("now", new DateTimeZone('Europe/Berlin'));
$local_time =  $dt->format('d.m.Y H:i');
$local_time_sort =  $dt->format('Y_m_d_H_i_');


// make connection
$filename = 'files/data.db';
$tablename = 'users';
$db = new SQLite3($filename);


//////////////////// JUST FOR THE FIRST RUN
// check if the table exists, if not we will insert first data after creating ist in the next step
$tableCheck =$db->query("SELECT name FROM sqlite_master WHERE name='$tablename'");
if ($tableCheck->fetchArray() === false){$first_run = true;}

 $db-> exec("CREATE TABLE IF NOT EXISTS $tablename(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    erstellt TEXT NOT NULL DEFAULT '$local_time',
    geaendert TEXT NOT NULL DEFAULT '$local_time',
    Name TEXT NOT NULL DEFAULT '')");

// first data
if ($first_run){
  $db-> exec("INSERT INTO $tablename(Name)VALUES('Niemand')");

  $db->exec("ALTER TABLE $tablename ADD COLUMN Nachname TEXT NOT NULL DEFAULT '' ");
  $db->exec("ALTER TABLE $tablename ADD COLUMN WG TEXT NOT NULL DEFAULT '' ");
  $db->exec("ALTER TABLE $tablename ADD COLUMN eingezogen TEXT NOT NULL DEFAULT '' ");
  $db->exec("ALTER TABLE $tablename ADD COLUMN ausgezogen TEXT NOT NULL DEFAULT '' ");
  $db->exec("ALTER TABLE $tablename ADD COLUMN Telefon TEXT NOT NULL DEFAULT '' ");
  $db->exec("ALTER TABLE $tablename ADD COLUMN Email TEXT NOT NULL DEFAULT '' ");
  $db->exec("ALTER TABLE $tablename ADD COLUMN Freifeld_P TEXT NOT NULL DEFAULT '' ");

  $db-> exec("INSERT INTO $tablename(Name, Nachname, WG, eingezogen, ausgezogen, Telefon, Email ,Freifeld_P )VALUES('Paul', 'Panther', 'EG links', '18.03.2005', '', '0800 2566 5214', 'paulchen@panther.de', 'Text und so...')");
  $db-> exec("INSERT INTO $tablename(Name, Nachname, WG, eingezogen, ausgezogen, Telefon, Email ,Freifeld_P )VALUES('Elke', 'Eichhörnchen', 'EG rechts', '15.11.2014', '', '0800 4579 2548', 'elke@eichhörnchen.de', 'Text und so...')");
  $db-> exec("INSERT INTO $tablename(Name, Nachname, WG, eingezogen, ausgezogen, Telefon, Email ,Freifeld_P )VALUES('Heinz', 'Hummel', '1 OG links', '19.01.2014', '11.03.2020', '0800 2566 9547', 'heinz@hummel.de', 'Text und so...')");
  $db-> exec("INSERT INTO $tablename(Name, Nachname, WG, eingezogen, ausgezogen, Telefon, Email ,Freifeld_P )VALUES('Karin', 'Känguru', '2 OG mitte', '25.09.2008', '', '0800 5863 2478', 'karin@kaenguru.de', 'Text und so...')");
  $db-> exec("INSERT INTO $tablename(Name, Nachname, WG, eingezogen, ausgezogen, Telefon, Email ,Freifeld_P )VALUES('Moritz', 'Marder', '2 OG rechts', '11.03.2020', '11.03.2020', '0800 8956 2478', 'moritz@marder.de', 'Text und so...')");
  $db-> exec("INSERT INTO $tablename(Name, Nachname, WG, eingezogen, ausgezogen, Telefon, Email ,Freifeld_P )VALUES('Anne', 'Aal', '1 OG mitte', '16.04.2017', '', '0800 8569 8569', 'anne@aal.de', 'Text und so...')");
  $db-> exec("INSERT INTO $tablename(Name, Nachname, WG, eingezogen, ausgezogen, Telefon, Email ,Freifeld_P )VALUES('Ute', 'Unke', '2 OG mitte', '10.06.2017', '', '0800 7425 8547', 'unte@unke.de', 'Text und so...')");
}
//////////////////// JUST FOR THE FIRST RUN


// delete row
if (isset($_GET['delete'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM $tablename WHERE id=$id";
		$db->exec($query);
    copy($filename, "files/archive/".$local_time_sort."delete_".$id.".db");
}

// get the data
// show only this columns in the index    'erstellt', 'geaendert',
$input2=['id', 'Name', 'Nachname', 'WG', 'eingezogen', 'ausgezogen', 'Telefon', 'Email'];
$result = $db->query("SELECT ".implode(', ', $input2)." FROM $tablename");
//$result = $db->query("SELECT * FROM $tablename");
$result->fetchArray(SQLITE3_NUM);
$fieldnames = []; // all colum-names
$fieldtypes = [];
for ($colnum = 0;$colnum < $result->numColumns();$colnum++) {
    $fieldnames[] = $result->columnName($colnum);
    $fieldtypes[] = $result->columnType($colnum);
}
$result->reset();
while ($row = $result->fetchArray(SQLITE3_NUM)) {
    $rows[] = $row; // all data in $row
}
?>

<!-- HTML & CSS  -->
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
	<title>Data List</title>
	<style>
  /* table > tbody > tr > td{border: 1px solid red; border-collapse: collapse;}
  table > tbody > tr > td > table > tbody > tr > td{border: 1px solid blue; border-collapse: collapse;}
  table > tbody > tr > td > table > tbody > tr > td > table > tbody > tr > td{border: 1px solid green; border-collapse: collapse;} */

  body{color:#333436; }
  a{text-decoration: none;}
  a.blue, a.blue:visited{color: #2d399a;}
  a.blue:hover{color: #1b225c;}
  a.green, a.green:visited{color: #3a9a2d;}
  a.green:hover{color: #173e12;}
  a.red, a.red:visited{color: #9a2d3a;}
  a.red:hover{color: #3e1217;}
  td>h1{display: inline;font-size: 2em;font-weight: bold; }
  td>h2{display: inline;font-size: 1.5em;font-weight: bold; }
  td>h3{display: inline;font-size: 1.2em;font-weight: bold; }
	table, tr, td{border-style:none;  border-bottom:0px solid #eee; border-collapse: collapse;}
  tr.pointer{cursor:pointer;}
  tr.hover{transition: 0.3s;}
  tr.hover:hover:nth-child(5n+1) {background: #9fa6e3}
  tr.hover:hover:nth-child(5n+2) {background: #9fe3cd}
  tr.hover:hover:nth-child(5n+3) {background: #e3b29f}
  tr.hover:hover:nth-child(5n+4) {background: #c2e39f}
  tr.hover:hover:nth-child(5n+5) {background: #e39fdd}
  td.logo{width:310px; height:110px;}
  td.headline{padding-left: 5px;padding-right: 25px;}
  td.user_cell{padding: 5px;}
  td{padding: 1px;}
  td{vertical-align: top; text-align: left;}
  td.header{width: 220px;}
  td.inter_heading{vertical-align: bottom;  height:33px;}
  td.freifeld{display: block; width:400px; height: 200px; overflow-y: scroll}
  td.key{height:25px; width: 100px;}
  td.value{width: 200px;}
  td.links{text-align: right; width: 180px;}
  td.small{font-size: 70%;}
  input[type=text], input[type=password], input[type=email], input[type=number]{border: 1px solid grey;}
  textarea{border-style: none;}

</style>
<script type="text/javascript"> function tr_click(url){document.location.href = url; }</script>
</head>



<body>
<!-- PAGE -->
<table>
  <tr>
    <td colspan="8">
    <!-- HEADER -->
    <table>
      <tr>
        <td class="logo"><a href="/burgis/index.php"><img class="logo" src="files/Stina_2_100.png"></a></td>
        <td class="header">
        <!-- DATENSATZ -->
          <table>
            <tr><td colspan="2"><h1><? echo $app_name; ?></h1></td></tr>
            <!-- <tr><td class="small">&nbsp;</td><td class="small">&nbsp;</td></tr> -->
            <!-- <tr><td class="small">&nbsp;</td><td class="small">&nbsp;</td></tr> -->
            <tr><td class="small">&nbsp;</td><td class="small">&nbsp;</td></tr>
          </table>
        <!-- DATENSATZ -->
        </td>
        <td class="header">
        <!-- HYPERLINKS -->
          <table>
            <tr><td class="links"><a class="green" href="user.php?new">Person anlegen</a></td></tr>
            <tr><td class="links">&nbsp;</td></tr>
            <tr><td class="links">&nbsp;</td></tr>
            <tr><td class="links"><a class="red" href="index.php?logout">abmelden</a></td></tr>
          </table>
        <!-- HYPERLINKS -->
        </td>
      </tr>
    </table>
    <!-- HEADER -->
    </td>
  </tr>
  <tr>
    <td>
    <table>
      <tr>
    <?
    // Schleife macht aus den $fieldnames der DB Überschriften für die Tabelle
    foreach ($fieldnames AS $field){
      echo "<td class=\"headline\"><h3>".$field."</h3></td>\n";
    }

    ?></tr><?php
    // Schleift durch das multi Array, erst jeder User eine Reihe, dann jeder Item eine Splate
    for ($i = 0;$i < count($rows);$i++) {
        echo "<tr onclick=\"tr_click('user.php?id=" . $rows[$i][0] . "')\" class=\"hover pointer\" >\n";
        echo "<td class=\"user_cell\"><a class=\"blue\" href=\"user.php?id=" . $rows[$i][0] . "\">" . $rows[$i][0] . "</a></td>\n";
        for ($j = 1;$j < count($rows[$i]);$j++) {
            echo "<td  class=\"user_cell\" >" . $rows[$i][$j] . "</td>\n";
        }
        echo "</tr>\n";
    } ?>


  </table>
</body>
</html>
