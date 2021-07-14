<?php

header("content-type:text/html;charset=utf-8");

require('General.php');

if (isset($_POST['Account']) && isset($_POST['Password'])) {

	$Result = array(
		'Result' => '',
		'UserInfo' => array(),
	);
	
	$Account = myStripslashes($_POST['Account']);
	$Password = myStripslashes($_POST['Password']);
	$IP = $_SERVER["REMOTE_ADDR"];
	
	$sql = "SELECT COUNT(*) FROM UserInfo WHERE Account = '".$Account."'";
    
    $result = mysql_db_query("web_db",$sql,$link);
    $row = mysql_fetch_array($result);

	$num = $row['COUNT(*)'];
	
	if ($num == 0) {
 
		if (IsValidAccountPassword($Account,$Password)) {
		
			$AccessKey = "";
			$DeleteDate = date('Y-m-d', strtotime('+4 week'));
			$IP_Array = explode(".",$IP);
			foreach($IP_Array as $index => $value) {
				$AccessKey = $AccessKey.$value;
			}
			$AccessKey = rand(100,999).$AccessKey.time().rand(100,999);
	
			$sql = "INSERT INTO UserInfo (Account,Password,AccessKey,DeleteDate) VALUES ('".$Account."','".$Password."','".$AccessKey."','".$DeleteDate."')";
	
			if (mysql_db_query("web_db",$sql,$link)) {

				$sql = "SELECT UserID FROM UserInfo WHERE Account = '".$Account."'";
                
                $result = mysql_db_query("web_db",$sql,$link);
                $row = mysql_fetch_array($result);
                
				$UserID = $row['UserID'];
				
				$Result['Result'] = 'YES';
				$Result['UserInfo'][] = array(
					'UserID' => $UserID,
					'Account' => $Account,
					'Password' => $Password,
					'AccessKey' => $AccessKey
				);
			}
			else {
				$Result['Result'] = 'NONO';
			}
		}
		else {
			$Result['Result'] = 'NONO';
		}
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
		'UserInfo' => array(),
	);

	$json = json_encode($Result);
	echo $json;
}

?>
