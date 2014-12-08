<?php

//Warnung! Die Funktion in php von xmlrpc sind EXPERIMENTELL !!!!

include ("ticket.php");

echo '<form action="index.php" method="post">
 <p>Benutzername: <input type="text" name="user" /></p>
 <p>Passwort: <input type="text" name="kennwort" /></p>
 <p><input type="submit" /></p>
</form>';

if (isset($_POST["user"]))
	$user = $_POST["user"];

if (isset($_POST["kennwort"]))
	$kennwort = $_POST["kennwort"];

// Ticket mit Usernamen und Userpasswort holen

// Abhängig davon ob es ein Paket-Admin, ein Domain-Admin oder ein Mail-User ist 
// stehen ggf nur eingeschränkte Information zur Verfügung.


$loginTicket = getLoginTicket($user, $kennwort);
if (strlen($loginTicket) > 5)
	echo 'Login Ticket:<br>'.$loginTicket.'<br><br>';
else
{
	echo 'Fehler login Ticket!<br>';
	exit;
}	


// Einmal Ticket zum bearbeiten anfordern

$aktionTicket = getTicket($loginTicket);
if (strlen($aktionTicket) > 5)
	echo 'Aktion Ticket:<br>'.$aktionTicket.'<br><br>';
else
{
	echo 'Fehler Aktion Ticket!<br>';
	exit;
}	


// nun hsadmin

echo 'Es gibt die Module:<br>';
echo 'user, domain, emailaddress, mysqluser, mysqldb, postgresqluser, postgresqldb<br>';
echo 'Mit den jeweiligen Funktionen:<br>';
echo 'search, add, update, delete<br><br>';


// Es folgen zwei kleine Beispiele

// Aktuelle User ausgeben user.search
echo 'Bsp.: Aktuelle User Daten anzeigen user.search<br>';
 
$request = xmlrpc_encode_request("user.search", array($user, $aktionTicket));
$context = stream_context_create(array('http' => array(
    'method' => "POST",
    'header' => "Content-Type: text/xml\r\nUser-Agent: PHPRPC/1.0\r\n",
    'content' => $request
)));
$server = 'https://config.hostsharing.net:443/hsar/xmlrpc/hsadmin';

$file = file_get_contents($server, false, $context);
$response = xmlrpc_decode($file);
print_r ($response);
echo '<br><br>';

// Bei search kann ein 3 Value beim Array übergeben werden um die suche darauf einzuschränken

$whereMap = array(
'name' => 'xyz00-name'
);

// $request = xmlrpc_encode_request("user.search", array($user, $aktionTicket, $whereMap));



// Bsp. eine email Weiterleitung ändern

// Bitte example.com durch einen gültigen Namen ersetzten.

// Erst wieder ein neues einmal Aktion Ticket holen
$aktionTicket = getTicket($loginTicket);

echo 'Bsp.: Mail Weiterleitung ändern emailaddress.update<br>';

$setMap = array(
'target' => 'muster@example.com'
);
$whereMap = array(
'localpart' => 'info',
'domain' => "example.com"
);

$request = xmlrpc_encode_request("emailaddress.update", array($user, $aktionTicket, $setMap, $whereMap));
$context = stream_context_create(array('http' => array(
    'method' => "POST",
    'header' => "Content-Type: text/xml\r\nUser-Agent: PHPRPC/1.0\r\n",
    'content' => $request
)));
$server = 'https://config.hostsharing.net:443/hsar/xmlrpc/hsadmin';

$file = file_get_contents($server, false, $context);
$response = xmlrpc_decode($file);
var_dump($response);

?>
