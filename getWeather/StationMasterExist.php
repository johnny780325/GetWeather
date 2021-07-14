<?php

header("content-type:text/html;charset=utf-8");

require('General.php');

if (isset($_POST['Mac'])) {

	$Mac = myStripslashes($_POST['Mac']);
	
	$sql = "SELECT COUNT(*) FROM MainStationInfo INNER JOIN UserRelateMainStation ON MainStationInfo.StationID = UserRelateMainStation.StationID WHERE MacAddress = '".$Mac."' AND PermissionID = '0'";
    
    $result = mysql_db_query("web_db",$sql,$link);
    $row = mysql_fetch_array($result);

	$num = $row['COUNT(*)'];
	
	if ($num == 0) {
		echo "NONO\n";
	}
	else {
		echo "YES\n";
	}
    
}
else if(isset($_POST['MacAddress'])) {
		
	$MacAddress = myStripslashes($_POST['MacAddress']);
	
	$sql = "SELECT COUNT(*) FROM MainStationInfo INNER JOIN UserRelateMainStation ON MainStationInfo.StationID = UserRelateMainStation.StationID WHERE MacAddress = '".$MacAddress."' AND PermissionID = '0'";
    
    $result = mysql_db_query("web_db",$sql,$link);
    $row = mysql_fetch_array($result);

	$num = $row['COUNT(*)'];
	
	if ($num == 0) {
		echo "#NO#\n";
	}
	else {
		echo "#YES#\n";
	}
	
}
else {
	echo "NONO\n";
}

?>
