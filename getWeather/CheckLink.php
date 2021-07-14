<?php

header("content-type:text/html;charset=utf-8");

require('General.php');

if (isset($_POST['StationID']) && isset($_POST['PermissionID'])) {

	$StationID = myStripslashes($_POST['StationID']);
	$PermissionID = myStripslashes($_POST['PermissionID']);
	$IPAddress = $_SERVER["REMOTE_ADDR"];

	if ($PermissionID >= SystemPermission::User1 && $PermissionID <= SystemPermission::User5) {
	
		$sql = "SELECT StationID, IPAddress FROM MainStationInfo WHERE StationID = '".$StationID."'";
        
        $result = mysql_db_query("web_db",$sql,$link);
        $row = mysql_fetch_array($result);

		$OldIPAddress = $row['IPAddress'];
				
		if ($OldIPAddress != $IPAddress) {
			
            $sql = "UPDATE MainStationInfo SET IPAddress = '".$IPAddress."' WHERE StationID = '".$StationID."'";
            
            mysql_db_query("web_db",$sql,$link);
		}
		
		$sql = "SELECT * FROM UserRelateMainStation WHERE StationID = '".$StationID."' AND PermissionID = '".$PermissionID."'";
        
        $result = mysql_db_query("web_db",$sql,$link);
        $num = mysql_num_rows($result);

		if ($num == 0) {
			echo "#NO#\n";
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
