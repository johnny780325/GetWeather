<?php

header("content-type:text/html;charset=utf-8");

require('General.php');

if (isset($_GET['Mac']) && isset($_GET['StationVersion']) && isset($_GET['CompanyID']) && isset($_GET['FirmwareVersion'])) {
	
	$Result = array(
		'Result' => '',
		'StationInfo' => array(),
		'FirmwareInfo' => array()
	);
	
	$MacAddress = myStripslashes($_GET['Mac']);
	$StationVersion = myStripslashes($_GET['StationVersion']);
	$CompanyID = myStripslashes($_GET['CompanyID']);
	$StationFirmwareVersion = myStripslashes($_GET['FirmwareVersion']);
	
	$sql = "SELECT COUNT(*) FROM BlackList WHERE MacAddress = '".$MacAddress."'";

    $result = mysql_db_query("web_db",$sql,$link);
    $row = mysql_fetch_array($result);

	$BlackList = $row[0]['COUNT(*)'];
	
	if ($BlackList == 1) {
        
        $Result['Result'] = 'YES';
        
		$Result['StationInfo'][] = array(
			'MAC_Address' => $MacAddress,
			'StationVersion' => $StationVersion,
			'CompanyID' => $CompanyID,
			'Black' => 1
		);
		$Result['FirmwareInfo'][] = array(
			'FirmwareVersion' => "Black"
		);
	}
	else {
		$sql = "SELECT FirmwareVersion FROM FirmwareUpdate WHERE StationVersion = '".$StationVersion."' AND CompanyID = '".$CompanyID."'";
        
        $result = mysql_db_query("web_db",$sql,$link);
        $row = mysql_fetch_array($result);

		$NewFirmwareVersion = $row['FirmwareVersion'];

		if ($StationFirmwareVersion != $NewFirmwareVersion) {
            
            $Result['Result'] = 'YES';
            
			$Result['StationInfo'][] = array(
				'MAC_Address' => $MacAddress,
				'StationVersion' => $StationVersion,
				'CompanyID' => $CompanyID,
				'Black' => 0
			);
			$Result['FirmwareInfo'][] = array(
				'FirmwareVersion' => $StationVersion.$CompanyID.$NewFirmwareVersion
			);
		}
		else {
            
            $Result['Result'] = 'YES';
            
			$Result['StationInfo'][] = array(
				'MAC_Address' => $MacAddress,
				'StationVersion' => $StationVersion,
				'CompanyID' => $CompanyID,
				'Black' => 0
			);
		}
	}
	
	$json = json_encode($Result);
	echo $json;
}
else {

	$Result = array(
		'Result' => 'NONO',
		'StationInfo' => array(),
		'FirmwareInfo' => array()
	);

	$json = json_encode($Result);
	echo $json;
}

?>
