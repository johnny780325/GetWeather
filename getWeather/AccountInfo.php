<?php

header("content-type:text/html;charset=utf-8");

require('General.php');

if (isset($_POST['Account'])) {

	$Result = array(
		'Result' => '',
		'UserInfo' => array()
	);
	
	$Account = myStripslashes($_POST['Account']);

	$sql = "SELECT * FROM UserInfo WHERE Account = '".$Account."'";
    $result = mysql_db_query("web_db",$sql,$link);
    
    $row = mysql_fetch_array($result);
    
	if ($Account != $row['Account']) {
		$Result = array(
			'Result' => 'NONO',
			'UserInfo' => array()
		);
	}
	else {
		$UserID = $row['UserID'];
		$AccessKey = $row['AccessKey'];
		
		$Result['Result'] = 'YES';
		$Result['UserInfo'][] = array(
			'UserID' => $UserID,
			'Account' => $Account,
			'AccessKey' => $AccessKey
		);
	}
	
	$json = json_encode($Result);
	echo $json;
}
else {

	$Result = array(
		'Result' => 'NONO',
		'UserInfo' => array()
	);

	$json = json_encode($Result);
	echo $json;
}

?>
