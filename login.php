<?php
// https://www.php-kurs.com/loesung-einlogg-script.htm
session_start();
$_SESSION['last_visit'] = time();
$session_timeout = 1800;
if((time() - $_SESSION['last_visit']) > $session_timeout) {
  session_destroy();
}



if ( isset($_GET['logout'])){
  session_destroy();
  header('Location: login.php');
  exit;}

if ( isset($_POST['benutzername']) and $_POST['benutzername'] != ""
     and isset($_POST['kennwort']) and $_POST['kennwort'] != ""  )
{
    // Kontrolle, ob Benutzername und Kennwort korrekt
    // diese werden i.d.R. aus Datenbank ausgelesen
    if ($_POST['benutzername'] == "admin" AND $_POST['kennwort'] == "admin" or
    $_POST['benutzername'] == "finanzen" AND $_POST['kennwort'] == "finanzen" or
    $_POST['benutzername'] == "verwaltung" AND $_POST['kennwort'] == "verwaltung" )
    {
        $_SESSION['benutzername'] = $_POST['benutzername'];
        $_SESSION['eingeloggt'] = true;
        echo "<b>einloggen erfolgreich</b>";
    }
    else
    {
        echo "<b>ungültige Eingabe</b>";
        $_SESSION['eingeloggt'] = false;
    }
}

if ( isset($_SESSION['eingeloggt']) and $_SESSION['eingeloggt'] == true )
{
    // Benutzer begruessen
    echo "<h1>Hallo ". $_SESSION['benutzername'] . "</h1>";
}
else
{
    // Einloggformular anzeigen
    echo "<h1>Bitte loggen Sie sich ein";

    $url = $_SERVER['SCRIPT_NAME'];
    echo '<form action="'. $url .'" method="POST">';
    echo '<p>Benutzername:<br>';
    echo '<input type="text" name="benutzername" value="">';
    echo '<p>Kennwort:<br>';
    echo '<input type="password" name="kennwort" value="">';
    echo '<p><input type="Submit" value="einloggen">';
    echo '</form>';

    // Programm wird hier beendet, denn Benutzer ist noch nicht
    // eingeloggt
    exit;
}

echo $_SESSION['benutzername'];
echo "<a href=\"login.php?logout\">logout</a>";
?>
