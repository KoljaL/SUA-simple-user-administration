<?php
if( isset($_GET['id']) ){$id = $_GET['id'];}


// aktuelle Zeit für das "geaendert" FIELD
$dt = new DateTime("now", new DateTimeZone('Europe/Berlin'));
$local_time =  $dt->format('d.m.Y H:i');


// make connection
$db = new SQLite3('data1.db');
$tablename = 'users';


// create a new user
if (isset($_POST['new_user'])) {
  $name = [];
  $value = [];
  foreach ($_POST as $param_name => $param_val) {
    if ($param_name != 'new_user' && $param_name != 'id') {
      $name[] = $param_name;
      $value[] = $param_val;
    }
  }
  $query = "INSERT INTO $tablename (" . implode(",", $name) . ") VALUES ('" . implode("','", $value) . "')";
  $db->exec($query);
  // get the id of the new user to show him
  $id = $db->querySingle("SELECT id FROM $tablename WHERE id = (SELECT MAX(id) FROM $tablename) order by id desc limit 1");
}

// update the user data
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

// get the data
if(!isset($_GET['new'])){
    $result = $db->query("SELECT * FROM $tablename WHERE id = $id");
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

    // turn nummeric array $rows to associate array
    for ($i=0; $i<count($rows); $i++) {
        for ($j=0; $j<count($rows[$i]); $j++) {
            $rows[$i][$fieldnames[$j]] = $rows[$i][$j];
            unset($rows[$i][$j]);
        }
        function get_val($arr,$nr){
          return current(array_slice($arr, $nr, 1));
        }
        $key = array_search($id, array_column($rows, 'id'));
    }
}



// case allocation
if(isset($_GET['update'])){
  $id           = $rows[$key]['id'];
  $textarea_css = "style=\"border: 1px solid grey; \"";
  $form_action  = "user.php?id=".$rows[$key]['id'];
  $update_link  = "&nbsp;";
  $delete_link  = "&nbsp;";

  // PERSON
  $erstellt    = $rows[$key]['erstellt'];
  $geaendert   = $rows[$key]['geaendert'];
  $name        = "<input type=\"text\" name=\"Name\" size=\"8\" value=\"".$rows[$key]['Name']."\">";
  $name_header = "<input name=\"update_user\" type=\"submit\" form=\"formular\" value=\"Änderungen für ".$rows[$key]['Name']." speichern\"> ";
  $nachname    = "<input type=\"text\" name=\"Nachname\" size=\"8\" value=\"".$rows[$key]['Nachname']."\">";
  $wg          = "<input type=\"text\" name=\"WG\" size=\"21\" value=\"".$rows[$key]['WG']."\">";
  $eingezogen  = "<input type=\"text\" name=\"eingezogen\" size=\"21\" value=\"".$rows[$key]['eingezogen']."\">";
  $ausgezogen  = "<input type=\"text\" name=\"ausgezogen\" size=\"21\" value=\"".$rows[$key]['ausgezogen']."\">";
  $telefon     = "<input type=\"text\" name=\"Telefon\" size=\"21\" value=\"".$rows[$key]['Telefon']."\">";
  $email       = "<input type=\"text\" name=\"Email\" size=\"21\" value=\"".$rows[$key]['Email']."\">";
  $freifeld_p  = "<textarea name=\"Freifeld_P\" cols=\"45\" rows=\"30\">".$rows[$key]['Freifeld_P']."</textarea>";
  // PERSON

}
elseif(isset($_GET['new'])){
  $id           = $rows[$key]['id'];
  $textarea_css = "style=\"border: 1px solid grey; \"";
  $form_action  = "user.php";
  $update_link  = "&nbsp;";
  $delete_link  = "&nbsp;";

  // PERSON
  $erstellt    = "<input type=\"text\" name=\"erstellt\" size=\"18\" readonly value=\"".$local_time."\">";
  $geaendert   = "<input type=\"text\" name=\"geaendert\" size=\"18\" readonly value=\"".$local_time."\">";
  $name        = "<input type=\"text\" name=\"Name\" size=\"8\"  >";
  $name_header = "<input name=\"new_user\" type=\"submit\" form=\"formular\" value=\"neue Person anlegen\"> ";
  $nachname    = "<input type=\"text\" name=\"Nachname\" size=\"8\" value=\"".$rows[$key]['Nachname']."\">";
  $wg          = "<input type=\"text\" name=\"WG\" size=\"21\" value=\"".$rows[$key]['WG']."\">";
  $eingezogen  = "<input type=\"text\" name=\"eingezogen\" size=\"21\" value=\"".$rows[$key]['eingezogen']."\">";
  $ausgezogen  = "<input type=\"text\" name=\"ausgezogen\" size=\"21\" value=\"".$rows[$key]['ausgezogen']."\">";
  $telefon     = "<input type=\"text\" name=\"Telefon\" size=\"21\" value=\"".$rows[$key]['Telefon']."\">";
  $email       = "<input type=\"text\" name=\"Email\" size=\"21\" value=\"".$rows[$key]['Email']."\">";
  $freifeld_p  = "<textarea name=\"Freifeld_P\" cols=\"45\" rows=\"30\">".$rows[$key]['Freifeld_P']."</textarea>";
  // PERSON

} else {
  $id           = $rows[$key]['id'];
  $textarea_css = "style=\"white-space: pre-line\"";
  $form_action  = "user.php?id=".$rows[$key]['id'];
  $update_link  = "<a class=\"blue\" href=\"user.php?update&amp;id=".$id."\">bearbeiten</a>";
  $delete_link  = "<a class=\"red\" href=\"index.php?delete&amp;id=".$id."\" onclick=\"return confirm('Soll ".strtoupper($rows[$key]['Name'] ." ". $rows[$key]['Nachname'])." wirklich gelöscht werden?');\">löschen</a>";

  // PERSON
  $erstellt    = $rows[$key]['erstellt'];
  $geaendert   = $rows[$key]['geaendert'];
  $name        = $rows[$key]['Name'];
  $name_header = $rows[$key]['Name'];
  $nachname    = $rows[$key]['Nachname'];
  $wg          = $rows[$key]['WG'];
  $eingezogen  = $rows[$key]['eingezogen'];
  $ausgezogen  = $rows[$key]['ausgezogen'];
  $telefon     = $rows[$key]['Telefon'];
  $email       = $rows[$key]['Email'];
  $freifeld_p  = $rows[$key]['Freifeld_P'];
  // PERSON

}
?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Data User</title>
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
    td{padding: 1px;}
    td{vertical-align: top; text-align: left;}
    td>h1{display: inline;font-size: 2em;font-weight: bold; }
    td>h2{display: inline;font-size: 1.5em;font-weight: bold; }
    td>h3{display: inline;font-size: 1.2em;font-weight: bold; }
    td.logo{width:310px; height:110px;}
    td.header{width: 220px;}
    td.inter_heading{vertical-align: bottom;  height:33px;}
    td.freifeld{display: block; width:400px; height: 200px; overflow-y: scroll}
    td.key{height:25px; width: 100px;}
    td.value{width: 200px;}
    td.links{text-align: right; width: 180px;}
    td.red > a{color:red;}
    .colspan2{flex: 2;}
    input[type=text], input[type=password], input[type=email], input[type=number]{border: 1px solid grey;}
    textarea{border-style: none;}
  </style>
</head>
<body>
<div class="page">
<!-- PAGE -->
<table>
  <tr>
    <td>
    <!-- HEADER -->
    <table>
      <tr>
        <td class="logo"><a href="/burgis/index.php"><img class="logo" src="files/logo_burgis.png"></a></td>
        <td class="header">
        <!-- DATENSATZ -->
          <table>
            <tr><td colspan="2"><h1><? echo $name_header; ?></h1></td></tr>
            <tr><td>erstellt: </td><td> <? echo $erstellt; ?></td></tr>
            <tr><td>geändert: </td><td> <? echo $geaendert; ?></td></tr>
          </table>
        <!-- DATENSATZ -->
        </td>
        <td class="header">
        <!-- HYPERLINKS -->
          <table>
            <tr><td class="links"><a class="green" href="index.php">Übersicht</a></td></tr>
            <tr><td class="links">&nbsp; </td></tr>
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

  <!-- BLOCK PERSON -->
  <tr>
    <td>
    <table>
      <form action="<?php echo $form_action; ?>" method="post" id="formular">
      <input type="hidden" name="id" value="<?php echo $id; ?>">
      <input type="hidden" name="geaendert" value="<?php echo $local_time; ?>">
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
      </form>
    </table>
    </td>
  </tr>
  <!-- BLOCK PERSON -->

</table>
<!-- PAGE -->
</div>
