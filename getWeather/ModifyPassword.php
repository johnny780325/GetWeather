<?php

header("content-type:text/html;charset=utf-8");

require('General.php');

if (isset($_POST['UserID']) && isset($_POST['NewPassword'])) {

	$UserID = myStripslashes($_POST['UserID']);
	$NewPassword = myStripslashes($_POST['NewPassword']);

	if (CheckPassword($NewPassword)) {
		
		$sql = "UPDATE UserInfo SET Password = '".$NewPassword."' WHERE UserID = '".$UserID."'";

        mysql_db_query("web_db",$sql,$link);
        
		if (mysql_db_query("web_db",$sql,$link)) {
			echo "YES\n";
		}
		else {
			echo "NONO\n";
		}
	}
	else {
		echo "NONO\n";
	}
}
else {
	echo "NONO\n";	
}

?>
