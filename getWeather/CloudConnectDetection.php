<?php

header("content-type:text/html;charset=utf-8");

require('General.php');


function catchDataFromClient($port)
{
	$ip = $_SERVER["REMOTE_ADDR"];

	$url = "http://admin:12345@".$ip.":".$port."/MainStationInfo.json";
	$contents = "";

	$fp = fopen($url, "r");
	if (!$fp)
	 {
		echo "#NO#\n";
	}
	else
	{
		fclose($fp);
		echo "#YES#\n";
	}
}

if (isset($_POST['Port']))
{
	$port = myStripslashes($_POST['Port']);
	catchDataFromClient($port);
}
else
{
	echo "#NO#\n";
}

?>
