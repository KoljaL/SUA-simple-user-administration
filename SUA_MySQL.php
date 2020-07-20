<?php
// https://www.php-kurs.com/loesung-einlogg-script.htm
// session_start();
// $_SESSION['last_visit'] = time();
// $session_timeout = 1800;
// if((time() - $_SESSION['last_visit']) > $session_timeout) {session_destroy();}
// if ( isset($_GET['logout'])){ session_destroy(); header('Location: index.php'); exit;}
// if ( isset($_POST['benutzername']) and $_POST['benutzername'] != "" and isset($_POST['kennwort']) and $_POST['kennwort'] != ""  )
// {if ($_POST['benutzername'] == "admin" AND $_POST['kennwort'] == "admin" or $_POST['benutzername'] == "finanzen" AND $_POST['kennwort'] == "finanzen" or $_POST['benutzername'] == "verwaltung" AND $_POST['kennwort'] == "verwaltung" )
// {$_SESSION['benutzername'] = $_POST['benutzername']; $_SESSION['eingeloggt'] = true;} //echo "<b>einloggen erfolgreich</b>";}
// else{echo "<b>ungültige Eingabe</b>"; $_SESSION['eingeloggt'] = false;}}
// if ( isset($_SESSION['eingeloggt']) and $_SESSION['eingeloggt'] == true ); //{echo "<h1>Hallo ". $_SESSION['benutzername'] . "</h1>";}
// else{echo '<form action="'. $_SERVER['SCRIPT_NAME'] .'" method="POST"><p>Benutzername:<br><input type="text" name="benutzername" value=""><p>Kennwort:<br><input type="password" name="kennwort" value=""><p><input type="Submit" value="einloggen"></form>';exit;}


$app_name = 'Simple User Administration';
// aktuelle Zeit für das "geaendert" FIELD
$dt = new DateTime("now", new DateTimeZone('Europe/Berlin'));
$local_time =  $dt->format('d.m.Y H:i');
$local_time_sort =  $dt->format('Y_m_d_H_i_');


// make connection
$tablename = 'user';
$db = new PDO('mysql:host=localhost;dbname=d032596b;charset=utf8', 'd032596b', 'TkHvthJ7qRsUr25z' );
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );


//////////////////// JUST FOR THE FIRST RUN

$sql= "CREATE TABLE IF NOT EXISTS $tablename(
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  erstellt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  geaendert TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  Name VARCHAR(30) NOT NULL)";
$db->exec($sql);

// first data
try{
  $db->exec("ALTER TABLE $tablename ADD COLUMN Nachname VARCHAR(30)  NOT NULL");
  $db->exec("ALTER TABLE $tablename ADD COLUMN WG VARCHAR(30)  NOT NULL");
  $db->exec("ALTER TABLE $tablename ADD COLUMN eingezogen VARCHAR(30)  NOT NULL");
  $db->exec("ALTER TABLE $tablename ADD COLUMN ausgezogen  VARCHAR(30)  NOT NULL ");
  $db->exec("ALTER TABLE $tablename ADD COLUMN Telefon VARCHAR(30)  NOT NULL ");
  $db->exec("ALTER TABLE $tablename ADD COLUMN Email  VARCHAR(30)  NOT NULL ");
  $db->exec("ALTER TABLE $tablename ADD COLUMN Freifeld_P  VARCHAR(3500)  NOT NULL ");

  $db-> exec("INSERT INTO $tablename(Name, Nachname, WG, eingezogen, ausgezogen, Telefon, Email ,Freifeld_P )VALUES('Paul', 'Panther', 'EG links', '18.03.2005', '', '0800 2566 5214', 'paulchen@panther.de', 'Text und so...')");
  $db-> exec("INSERT INTO $tablename(Name, Nachname, WG, eingezogen, ausgezogen, Telefon, Email ,Freifeld_P )VALUES('Elke', 'Eichhörnchen', 'EG rechts', '15.11.2014', '', '0800 4579 2548', 'elke@eichhörnchen.de', 'Text und so...')");
  $db-> exec("INSERT INTO $tablename(Name, Nachname, WG, eingezogen, ausgezogen, Telefon, Email ,Freifeld_P )VALUES('Heinz', 'Hummel', '1 OG links', '19.01.2014', '11.03.2020', '0800 2566 9547', 'heinz@hummel.de', 'Text und so...')");
  $db-> exec("INSERT INTO $tablename(Name, Nachname, WG, eingezogen, ausgezogen, Telefon, Email ,Freifeld_P )VALUES('Karin', 'Känguru', '2 OG mitte', '25.09.2008', '', '0800 5863 2478', 'karin@kaenguru.de', 'Text und so...')");
  $db-> exec("INSERT INTO $tablename(Name, Nachname, WG, eingezogen, ausgezogen, Telefon, Email ,Freifeld_P )VALUES('Moritz', 'Marder', '2 OG rechts', '11.03.2020', '11.03.2020', '0800 8956 2478', 'moritz@marder.de', 'Text und so...')");
  $db-> exec("INSERT INTO $tablename(Name, Nachname, WG, eingezogen, ausgezogen, Telefon, Email ,Freifeld_P )VALUES('Anne', 'Aal', '1 OG mitte', '16.04.2017', '', '0800 8569 8569', 'anne@aal.de', 'Text und so...')");
  $db-> exec("INSERT INTO $tablename(Name, Nachname, WG, eingezogen, ausgezogen, Telefon, Email ,Freifeld_P )VALUES('Ute', 'Unke', '2 OG mitte', '10.06.2017', '', '0800 7425 8547', 'unte@unke.de', 'Text und so...')");
} catch (Exception $e) {}
//////////////////// JUST FOR THE FIRST RUN


if( isset($_GET['id']) ){$id = $_GET['id'];}
if( isset($_POST['id']) ){$id = $_POST['id'];}


// // // // // // // delete user // // // // // // //
if (isset($_GET['delete'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM $tablename WHERE id=$id";
		$db->exec($query);
    $titel = "gelöscht";
}


// // // // // // // create a new user // // // // // // //
if (isset($_POST['new_user'])) {
    foreach ($_POST as $param_name => $param_val) {
      if ($param_name != 'new_user' && $param_name != 'id') {
        $name[] = $param_name;
        $value[] = $param_val;
      }
    }
    $db->exec("INSERT INTO $tablename (" . implode(",", $name) . ") VALUES ('" . implode("','", $value) . "')");
    // get the id of the new user to show him
    $stmt = $db->prepare("SELECT MAX(id) AS max_id FROM $tablename");
    $stmt -> execute();
    $invNum = $stmt -> fetch(PDO::FETCH_ASSOC);
    $id = $invNum['max_id'];
    $titel = $_POST['Name']."angelegt";

}


// // // // // // // update the user data // // // // // // //
if (isset($_POST['update_user'])) {
    $id = $_POST['id'];
    //echo "ID: ".array_values($row)[$id]['id'];
    //print_r($_POST);
    foreach ($_POST as $param_name => $param_val) {
        if ($param_name != 'update_user' && $param_name != 'id' ) {
            $query = "UPDATE $tablename set  $param_name ='$param_val' WHERE id=$id ";
            $db->exec($query);
        }
    }
}


// // // // // // // get the data for user // // // // // // //
if(!isset($_GET['new']) AND isset($_GET['user'])){
    $result = $db->query("SELECT * FROM $tablename WHERE id = $id");
    while( $row = $result->fetch(PDO::FETCH_ASSOC) ) {
      $rows = $row;
    }
  //  print_r($rows);
}

// // // // // // // get the data for index // // // // // // //
if(!isset($_GET['user'])){
    // show only this columns in the index    'erstellt', 'geaendert',
    $input2=['id', 'Name', 'Nachname', 'WG', 'eingezogen', 'ausgezogen', 'Telefon', 'Email'];
    $query = "SELECT ".implode(', ', $input2)." FROM $tablename";
    $sth = $db->query($query);
    while( $row = $sth->fetch(PDO::FETCH_ASSOC) ) {
      $rows[] = $row; // appends each row to the array
    }
    if (isset($_GET['sort'])) {$sort = $_GET['sort'];} else {$sort = 'id';}
    array_multisort( array_column($rows, $sort), SORT_ASC, $rows );
    //print_r($rows);
    $fieldnames = array_keys($rows[0]);
}


?>

<!-- // // // // // // HTML & CSS // // // // // // // // // //  -->
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
	<title><?php echo $titel; ?></title>
	<style>
  /* table > tbody > tr > td{border: 1px solid red; border-collapse: collapse;}table > tbody > tr > td > table > tbody > tr > td{border: 1px solid blue; border-collapse: collapse;}table > tbody > tr > td > table > tbody > tr > td > table > tbody > tr > td{border: 1px solid green; border-collapse: collapse;} */
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
  td>h3>a{color:#333436;}
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

<?php
// // // // // // // HTML for index // // // // // // //
if(!isset($_GET['user'])){
?>
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
                <tr><td class="links"><a class="green" href="index.php?user&new">Person anlegen</a></td></tr>
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
        echo "\t";
        foreach ($fieldnames AS $field){
          echo "<td class=\"headline\"><h3><a href=\"index.php?sort=".$field."\">".$field."</a></h3></td>\n\t\t\t";
        }
        echo "</tr>\n\t\t";
        // Schleift durch das multi Array, erst jeder User eine Reihe, dann jeder Item eine Splate
        foreach ($rows as $key_row=>$val_row) {
            echo "\t<tr onclick=\"tr_click('index.php?user&id=" . $val_row['id'] . "')\" class=\"hover pointer\" >\n";
            echo "\t\t\t<td class=\"user_cell\"><a class=\"blue\" href=\"index.php?user&id=" . $val_row['id'] . "\">" . $val_row['id'] . "</a></td>\n";
            foreach ($val_row as $key_field=>$val_field) {
              if ($key_field == 'id') continue;
                echo "\t\t\t<td class=\"user_cell\" >" . $val_field . "</td>\n";
            }
            echo "\t</tr>\n";
        } ?>
      </table>
  </body>
</html>
<?php
}
// // // // // // // HTML for index // // // // // // //
?>


<?php if(isset($_GET['user'])){
  // case allocation
  if(isset($_GET['update'])){
    $id           = $rows['id'];
    $textarea_css = "style=\"border: 1px solid grey; \"";
    $form_action  = "index.php?user&id=".$rows['id'];
    $update_link  = "&nbsp;";
    $delete_link  = "&nbsp;";

    // PERSON
    $erstellt    = $rows['erstellt'];
    $geaendert   = $rows['geaendert'];
    $name        = "<input type=\"text\" name=\"Name\" size=\"8\" value=\"".$rows['Name']."\">";
    $name_header = "<input name=\"update_user\" type=\"submit\" form=\"formular\" value=\"Änderungen für ".$rows['Name']." speichern\"> ";
    $nachname    = "<input type=\"text\" name=\"Nachname\" size=\"8\" value=\"".$rows['Nachname']."\">";
    $wg          = "<input type=\"text\" name=\"WG\" size=\"21\" value=\"".$rows['WG']."\">";
    $eingezogen  = "<input type=\"text\" name=\"eingezogen\" size=\"21\" value=\"".$rows['eingezogen']."\">";
    $ausgezogen  = "<input type=\"text\" name=\"ausgezogen\" size=\"21\" value=\"".$rows['ausgezogen']."\">";
    $telefon     = "<input type=\"text\" name=\"Telefon\" size=\"21\" value=\"".$rows['Telefon']."\">";
    $email       = "<input type=\"text\" name=\"Email\" size=\"21\" value=\"".$rows['Email']."\">";
    $freifeld_p  = "<textarea name=\"Freifeld_P\" cols=\"45\" rows=\"30\">".$rows['Freifeld_P']."</textarea>";
    // PERSON

  }
  elseif(isset($_GET['new'])){
    $id           = $rows['id'];
    $textarea_css = "style=\"border: 1px solid grey; \"";
    $form_action  = "index.php";
    $update_link  = "&nbsp;";
    $delete_link  = "&nbsp;";

    // PERSON
    $erstellt    = "<input type=\"text\" name=\"erstellt\" size=\"18\" readonly value=\"".$local_time."\">";
    $geaendert   = "<input type=\"text\" name=\"geaendert\" size=\"18\" readonly value=\"".$local_time."\">";
    $name        = "<input type=\"text\" name=\"Name\" size=\"8\"  >";
    $name_header = "<input name=\"new_user\" type=\"submit\" form=\"formular\" value=\"neue Person anlegen\"> ";
    $nachname    = "<input type=\"text\" name=\"Nachname\" size=\"8\" value=\"".$rows['Nachname']."\">";
    $wg          = "<input type=\"text\" name=\"WG\" size=\"21\" value=\"".$rows['WG']."\">";
    $eingezogen  = "<input type=\"text\" name=\"eingezogen\" size=\"21\" value=\"".$rows['eingezogen']."\">";
    $ausgezogen  = "<input type=\"text\" name=\"ausgezogen\" size=\"21\" value=\"".$rows['ausgezogen']."\">";
    $telefon     = "<input type=\"text\" name=\"Telefon\" size=\"21\" value=\"".$rows['Telefon']."\">";
    $email       = "<input type=\"text\" name=\"Email\" size=\"21\" value=\"".$rows['Email']."\">";
    $freifeld_p  = "<textarea name=\"Freifeld_P\" cols=\"45\" rows=\"30\">".$rows['Freifeld_P']."</textarea>";
    // PERSON

  } else {
    $id           = $rows['id'];
    $textarea_css = "style=\"white-space: pre-line\"";
    $form_action  = "index.php?user&id=".$rows['id'];
    $update_link  = "<a class=\"blue\" href=\"index.php?user&update&amp;id=".$id."\">bearbeiten</a>";
    $delete_link  = "<a class=\"red\" href=\"index.php?delete&amp;id=".$id."\" onclick=\"return confirm('Soll ".strtoupper($rows['Name'] ." ". $rows['Nachname'])." wirklich gelöscht werden?');\">löschen</a>";

    // PERSON
    $erstellt    = $rows['erstellt'];
    $geaendert   = $rows['geaendert'];
    $name        = $rows['Name'];
    $name_header = $rows['Name'];
    $nachname    = $rows['Nachname'];
    $wg          = $rows['WG'];
    $eingezogen  = $rows['eingezogen'];
    $ausgezogen  = $rows['ausgezogen'];
    $telefon     = $rows['Telefon'];
    $email       = $rows['Email'];
    $freifeld_p  = $rows['Freifeld_P'];
    // PERSON
}
?>

<body>
<!-- PAGE -->
<table>
  <tr>
    <td>
    <!-- HEADER -->
    <table>
      <tr>
        <td class="logo"><a href="/burgis/index.php"><img class="logo" src="files/Stina_2_100.png"></a></td>
        <td class="header">
        <!-- DATENSATZ -->
          <table>
            <tr><td colspan="2"><h1><? echo $name_header; ?></h1></td></tr>
            <tr><td class="small">&nbsp;</td><td class="small">&nbsp;</td></tr>
            <tr><td class="small">erstellt: </td><td class="small"> <? echo $erstellt; ?></td></tr>
            <tr><td class="small">geändert: </td><td class="small"> <? echo $geaendert; ?></td></tr>
          </table>
        <!-- DATENSATZ -->
        </td>
        <td class="header">
        <!-- HYPERLINKS -->
          <table>
            <tr><td class="links"><a class="green" href="index.php">Übersicht</a></td></tr>
            <tr><td class="links"><a class="red" href="index.php?logout">abmelden</a></td></tr>
            <tr><td class="links"><? echo $update_link ?></td></tr>
            <tr><td class="links"><? echo $delete_link; ?></td></tr>
          </table>
        <!-- HYPERLINKS -->
        </td>
      </tr>
    </table>
    <!-- HEADER -->
    </td>
  </tr>
  <form action="<?php echo $form_action; ?>" method="post" id="formular">
  <input type="hidden" name="id" value="<?php echo $id; ?>">
  <input type="hidden" name="geaendert" value="<?php echo $local_time; ?>">


  <!-- BLOCK PERSON -->
  <tr>
    <td>
    <table>
      <tr>
        <td>
        <!-- LEFT TABLE -->
          <table>
            <tr><td class="inter_heading" colspan="2"><h2>Person</h2></td></tr>
            <tr><td class="key">Name: </td><td class="value"><? echo $name; ?> <? echo $nachname; ?></td></tr>
            <tr><td class="key">eingezogen: </td><td class="value"><? echo $eingezogen; ?></td></tr>
            <tr><td class="key">Wohnung: </td><td class="value"><? echo $wg; ?></td></tr>
            <tr><td class="key">ausgezogen: </td><td class="value"><? echo $ausgezogen; ?></td></tr>
            <tr><td class="key">Telefon: </td><td class="value"><? echo $telefon; ?></td></tr>
            <tr><td class="key">E-Mail: </td><td class="value"><? echo $email; ?></td></tr>
          </table>
        <!-- LEFT TABLE -->
        </td>
        <td>
        <!-- RIGHT FIELD -->
          <table>
            <tr><td class="inter_heading"><h3>Freifeld Person</h3></td></tr>
            <tr><td class="freifeld" <? echo $textarea_css ?> > <? echo $freifeld_p ?></td></tr>
          </table>
        <!-- RIGHT FIELD -->
        </td>
      </tr>
    </table>
    </td>
  </tr>
  <!-- BLOCK PERSON -->

  <!-- BLOCK FINANZEN -->
  <tr>
    <td>
    <table>
      <tr>
        <td>
        <!-- LEFT TABLE -->
          <table>
            <tr><td class="inter_heading" colspan="2"><h2>Finanzen</h2></td></tr>
          </table>
        <!-- LEFT TABLE -->
        </td>
        <td>
        <!-- RIGHT FIELD -->
          <table>
            <tr><td class="inter_heading"><h3>Freifeld Finanzen</h3></td></tr>
          </table>
        <!-- RIGHT FIELD -->
        </td>
      </tr>
    </table>
    </td>
  </tr>
  <!-- BLOCK FINANZEN -->


</form>
</table>
<!-- PAGE -->
</body>
</html>

<?php } //  index.php?user ?>

























<?php

//
// function get_val($arr,$nr1, $nr2){
//   return current(array_slice($arr, $nr, 1));
// }
//
// foreach ($rows as $key=>$val) {
//   echo $val['id'][0];
//   echo " <br>";
//     foreach ($val as $key=>$val) {
//       echo $val;
//     }
// }

 ?>
