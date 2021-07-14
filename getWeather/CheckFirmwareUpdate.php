<?php

header("content-type:text/html;charset=utf-8");

require('General.php');

if (isset($_POST['Mac']) && isset($_POST['StationVersion']) && isset($_POST['CompanyID']) && isset($_POST['FirmwareVersion'])) {
	
	$MacAddress = myStripslashes($_POST['Mac']);
	$StationVersion = myStripslashes($_POST['StationVersion']);
	$CompanyID = myStripslashes($_POST['CompanyID']);
	$StationFirmwareVersion = myStripslashes($_POST['FirmwareVersion']);
	
	$sql = "SELECT COUNT(*) FROM BlackList WHERE MacAddress = '".$MacAddress."'";

    $result = mysql_db_query("web_db",$sql,$link);
    $row = mysql_fetch_array($result);
    
    $BlackList = $row['COUNT(*)'];
	
	if ($BlackList == 1) {
		echo "#Black#\n";
	}
	else {
		$sql = "SELECT FirmwareVersion FROM FirmwareUpdate WHERE StationVersion = '".$StationVersion."' AND CompanyID = '".$CompanyID."'";
        
        $result = mysql_db_query("web_db",$sql,$link);
        $row = mysql_fetch_array($result);
        
        $NewFirmwareVersion = $row['FirmwareVersion'];
        
		if ($StationFirmwareVersion != $NewFirmwareVersion) {
			echo "#".$StationVersion.$CompanyID.$NewFirmwareVersion."#\n";	
		}
		else {
			echo "#NO#\n";
		}
	}
}
else {
	echo "#NO#\n";
}

?>
