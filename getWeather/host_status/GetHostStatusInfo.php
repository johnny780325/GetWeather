<?php

header("content-type:text/html;charset=utf-8");

require('General.php');

if (isset($_GET['StationID'])) {

	$Result = array(
		'Result' => '',
		'StatusInfo' => array(),
	);
    
    $link = @mysql_connect("localhost","root","zxcvbn") or die("<br>Oops, Unable to connect to DB!".mysql_error());
    
	$stationid = myStripslashes($_GET['StationID']);
	
	$sql = "SELECT station_id,status,lastlogin FROM host_status WHERE station_id = '".$stationid."'";
    
    $result = mysql_db_query("pscmd_db",$sql,$link);
    $row = mysql_fetch_array($result);

    $num = mysql_num_rows($result);
    
    if ($num != 0) {
        
        $status = $row['status'];
        $lastlogin = $row['lastlogin'];
        
        $time1 = "2015-01-01 00:00:00";
        $time2 = date("Y-m-d H:i:s");
        $systemTime = (strtotime($time2) - strtotime($time1));
        
        $Result['Result'] = 'YES';
        $Result['StatusInfo'][] = array(
            'Status' => $status,
            'LastLogin' => $lastlogin,
            'SystemTime' => $systemTime
        );
        
    }
    else {
        $Result['Result'] = 'NONO';
    }
    
    $json = json_encode($Result);
    echo $json;
    
}
else {

	$Result = array(
		'Result' => 'NONO',
		'StatusInfo' => array(),
	);

	$json = json_encode($Result);
	echo $json;
    
}

?>
