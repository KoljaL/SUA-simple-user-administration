<?php

// aktuelle Zeit für das "geaendert" FIELD
$dt = new DateTime("now", new DateTimeZone('Europe/Berlin'));
$local_time =  $dt->format('d.m.Y H:i');


// make connection
$db = new SQLite3('data1.db');
$tablename = 'users';


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


// update the row
if (isset($_POST['update_data'])) {
    $id = $_POST['id'];
    //echo "ID: ".array_values($row)[$id]['id'];
    //print_r($_POST);
    foreach ($_POST as $param_name => $param_val) {
        if ($param_name != 'update_data' && $param_name != 'id' ) {
            $query = "UPDATE $tablename set  $param_name ='$param_val' WHERE id=$id ";
            $db->exec($query);
        }
    }
}

// delete row
if (isset($_GET['delete'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM $tablename WHERE id=$id";
		$db->exec($query);
}


// show only this columns in the index    'erstellt', 'geaendert',
$input2=['id', 'Name', 'Nachname', 'WG', 'eingezogen', 'ausgezogen', 'Telefon', 'Email'];
// get the data
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
  body{color:#333436; }
  a{text-decoration: none;}
  a.blue, a.blue:visited{color: #2d399a;}
  a.blue:hover{color: #1b225c;}
  a.green, a.green:visited{color: #3a9a2d;}
  a.green:hover{color: #173e12;}
  a.red, a.red:visited{color: #9a2d3a;}
  a.red:hover{color: #3e1217;}
	table, tr, td{border-style:none;  border-bottom:0px solid #eee; border-collapse: collapse;}
  tr.pointer{cursor:pointer;}
  tr.hover:hover:nth-child(5n+1) {background: #9fa6e3}
  tr.hover:hover:nth-child(5n+2) {background: #9fe3cd}
  tr.hover:hover:nth-child(5n+3) {background: #e3b29f}
  tr.hover:hover:nth-child(5n+4) {background: #c2e39f}
  tr.hover:hover:nth-child(5n+5) {background: #e39fdd}
  td.logo{padding-left:6px; width:310px; height:110px;}
  td.headline{font-weight: bold;}
	</style>
  <script type="text/javascript"> function DoNav(url){document.location.href = url; }</script>
</head>
<body>
	<table width="100%" cellpadding="5" cellspacing="1" border="1">
    <tr>
      <td class="logo" colspan="5"><a href="/burgis/index.php"><img class="logo" src="files/logo_burgis.png"></a></td>
    </tr>
		<tr>
			<?
			// Schleife macht aus den $fieldnames der DB Überschriften für die Tabelle
			foreach ($fieldnames AS $field){
				echo "<td class=\"headline\">".$field."</td>\n";
			}
			echo "<td class=\"userrow\"><a class=\"green\" href=\"user.php?new\">neue Person</a></td>\n";
			?></tr><?php
			// Schleift durch das multi Array, erst jeder User eine Reihe, dann jeder Item eine Splate
			for ($i = 0;$i < count($rows);$i++) {
			    echo "<tr onclick=\"DoNav('user.php?id=" . $rows[$i][0] . "')\" class=\"hover pointer\" >\n";
			    for ($j = 0;$j < count($rows[$i]);$j++) {
			        echo "<td>" . $rows[$i][$j] . "</td>\n";
			    }
			    // echo "<td><a class=\"blue\" href=\"user.php?id=" . $rows[$i][0] . "\">Details</a></td>\n";
			    echo "</tr>\n";
			} ?>
		</table>
</body>
</html>
