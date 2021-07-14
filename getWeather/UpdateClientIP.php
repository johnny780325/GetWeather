<?php 

header("content-type:text/html;charset=utf-8");

require('General.php');

if (isset($_POST['Mac'])) {
	
	$Mac = myStripslashes($_POST['Mac']);
	$NewIPAddress = $_SERVER["REMOTE_ADDR"];
	
	$sql = "SELECT StationID, IPAddress FROM MainStationInfo WHERE MacAddress = '".$Mac."'";

    $result = mysql_db_query("web_db",$sql,$link);
    $row = mysql_fetch_array($result);
    $num = mysql_num_rows($result);
	
	if ($num == 1) {
	
		$OldIPAddress = $row['IPAddress'];
		
		if ($OldIPAddress != $NewIPAddress) {
			$sql = "UPDATE MainStationInfo SET IPAddress = '".$NewIPAddress."' WHERE MacAddress = '".$Mac."'";

			if (mysql_db_query("web_db",$sql,$link)) {
				echo "#YES#\n";
			}
			else {
				echo "#NO#\n";
			}
		}
		else {
			echo "#YES#\n";
		}
	}
	else {
		echo "#NO#\n";
	}
}
else {
	echo "#NO#\n";
}

?>
