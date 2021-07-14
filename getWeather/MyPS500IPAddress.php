<?php

header("content-type:text/html;charset=utf-8");

require('General.php');

if (isset($_POST['Account']) && isset($_POST['Password'])) {

	$Result = array(
		'Result' => '',
		'UserInfo' => array(),
		'StationInfo' => array()
	);
	
	$Account = myStripslashes($_POST['Account']);
	$Password = myStripslashes($_POST['Password']);
	
	$sql = "SELECT * FROM UserInfo WHERE Account = '".$Account."' AND Password = '".$Password."'";
    
    $result = mysql_db_query("web_db",$sql,$link);
    $row = mysql_fetch_array($result);
	
	if ($Account != $row['Account']) {	//no such account
		$Result['Result'] = 'NONO';
	}
	else {

		$UserID = $row['UserID'];
		$AccessKey = $row['AccessKey'];
		
		$Result['Result'] = 'YES';
		$Result['UserInfo'][] = array(
			'UserID' => $UserID,
			'Account' => $Account,
			'Password' => $Password,
			'AccessKey' => $AccessKey
		);
		
		$sql = "SELECT * FROM MainStationInfo INNER JOIN UserRelateMainStation ON MainStationInfo.StationID = UserRelateMainStation.StationID WHERE UserRelateMainStation.UserID = '".$UserID."'";
        
        $result = mysql_db_query("web_db",$sql,$link);

        while ($row = mysql_fetch_array($result)) {
            $StationID = $row['StationID'];
            $IP = $row['IPAddress'];
            $PortNumber = $row['PortNumber'];
            $ConnectionType = $row['ConnectionType'];
            $MacAddress = $row['MacAddress'];
            $PermissionID = $row['PermissionID'];
            
            $Result['StationInfo'][] = array(
                'StationID' => $StationID,
                'IP_Address' => $IP,
                'Port_Number' => $PortNumber,
                'ConnectionType' => $ConnectionType,
                'Mac_Address' => $MacAddress,
                'PermissionID' => $PermissionID
                                             );
        }
	}
	
	$json = json_encode($Result);
	echo $json;
}
else {

	$Result = array(
		'Result' => 'NONO',
		'UserInfo' => array(),
		'DeviceInfo' => array()
	);

	$json = json_encode($Result);
	echo $json;
}

?>

