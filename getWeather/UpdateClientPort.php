<?php 

header("content-type:text/html;charset=utf-8");

require('General.php');

if (isset($_POST['Mac']) && isset($_POST['Port']))
{
	$Mac = myStripslashes($_POST['Mac']);
	$NewPortNumber = myStripslashes($_POST['Port']);
	$NewConnectionType = 0;
	if (isset($_POST['ConnectionType'])) {
		$NewConnectionType = myStripslashes($_POST['ConnectionType']);
	}

	$sql = "SELECT StationID, PortNumber, ConnectionType FROM MainStationInfo WHERE MacAddress = '".$Mac."'";
    
    $result = mysql_db_query("web_db",$sql,$link);
    $row = mysql_fetch_array($result);
    $num = mysql_num_rows($result);

	if ($num == 1) 
	{

		$OldPortNumber = $row['PortNumber'];
		$OldConnectionType = $row['ConnectionType'];

		if (($OldPortNumber != $NewPortNumber) || ($OldConnectionType != $NewConnectionType))
		{
			$sql = "UPDATE MainStationInfo SET PortNumber = '".$NewPortNumber."' , ConnectionType = '".$NewConnectionType."' WHERE MacAddress = '".$Mac."'";

			if (mysql_db_query("web_db",$sql,$link))
			{
				echo "#YES#\n";
			}
			else 
			{
				echo "#NO#\n";
			}
		}
		else 
		{
			echo "#YES#\n";
		}
	}
	else 
	{
		echo "#NO#\n";
	}
}
else {
	echo "#NO#\n";
}

?>

