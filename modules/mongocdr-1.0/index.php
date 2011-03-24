<?php

//check external IPs

if(sizeof($_POST) == 0)
{
	die('No direct access');
}

include('MongoCdr.php');

$mongo = new MongoCdr();

$mongo->connect();

$xml = $_POST['cdr'];

$mongo->addXMLCDR($xml);


$fp = fopen('/tmp/lame.txt', 'w+');

fwrite($fp, $xml);

fclose($fp);



while(false)
{
	$mongo->addXMLCDR(file_get_contents('/home/mphill/cdr.txt'));
}


