<?php
function getLoginTicket($user, $kennwort)
{
	//set POST variables
	$url = 'https://login.hostsharing.net/cas/v1/tickets';
	$fields = array(
	'username' => urlencode($user),
	'password' => urlencode($kennwort)
	);

	//url-ify the data for the POST
	foreach($fields as $key=>$value) 
	{ 
		$fields_string .= $key.'='.$value.'&'; 
	}
	rtrim($fields_string, '&');

	$ch = curl_init();

	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

	//execute post
	$result = curl_exec($ch);

	$line = explode("\n", $result);
	$ticket = $line[3];
	//$ticket = substr(strrchr ($ticket, "/"), 1);
	// Ticket mit führendem / Zeichen

	$ticket = strrchr ($ticket, "/");

	// close curl resource to free up system resources
	curl_close($ch); 
	return $ticket;
}

function getTicket($ticket)
{
	$serviceURL = 'https://config.hostsharing.net:443/hsar/backend';
	$url = 'https://login.hostsharing.net/cas/v1/tickets'.$ticket;
	$fields = array('service' => urlencode($serviceURL));

	//url-ify the data for the POST
	foreach($fields as $key=>$value) 
	{ 
		$fields_string .= $key.'='.$value.'&'; 
	}
	rtrim($fields_string, '&');

	$ch = curl_init();

	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

	//execute post
	$result = curl_exec($ch);
	$line= curl_multi_getcontent($result);

	$line = explode("\n", $result);
	$aktTicket = $line[8];
	return $aktTicket;
}
?>
