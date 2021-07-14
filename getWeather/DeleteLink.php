<?php

header("content-type:text/html;charset=utf-8");

require('General.php');

if (isset($_POST['UserID']) && isset($_POST['StationID']) && isset($_POST['PermissionID'])) {

	$UserID = myStripslashes($_POST['UserID']);
	$StationID = myStripslashes($_POST['StationID']);
	$PermissionID = myStripslashes($_POST['PermissionID']);
	$IPAddress = $_SERVER["REMOTE_ADDR"];

	if ($PermissionID >= SystemPermission::Owner && $PermissionID <= SystemPermission::User5) {
	
		$sql = "SELECT UserID FROM UserInfo WHERE UserID = '".$UserID."'";
        
        $result = mysql_db_query("web_db",$sql,$link);
        $num1 = mysql_num_rows($result);
		
		$sql = "SELECT StationID FROM MainStationInfo WHERE StationID = '".$StationID."'";
        
        $result = mysql_db_query("web_db",$sql,$link);
        $num2 = mysql_num_rows($result);

		if ($num1 == 1 && $num2 == 1) {
		
			if ($PermissionID == SystemPermission::Owner) {
				
				$sql = "DELETE FROM UserRelateMainStation WHERE StationID = '".$StationID."'";
                mysql_db_query("web_db",$sql,$link);
				
				$sql = "DELETE FROM MainStationInfo WHERE StationID = '".$StationID."'";
                mysql_db_query("web_db",$sql,$link);
	
				$sql = "SELECT * FROM UserRelateMainStation WHERE StationID = '".$StationID."'";
                $result = mysql_db_query("web_db",$sql,$link);
                $num1 = mysql_num_rows($result);
		
				$sql = "SELECT StationID FROM MainStationInfo WHERE StationID = '".$StationID."'";
                $result = mysql_db_query("web_db",$sql,$link);
                $num2 = mysql_num_rows($result);
	
				if ($num1 == 0 && $num2 == 0) {
				
					$sql = "SELECT COUNT(*) FROM UserInfo INNER JOIN UserRelateMainStation ON UserInfo.UserID = UserRelateMainStation.UserID WHERE UserInfo.UserID = '".$UserID."'";
                    
                    $result = mysql_db_query("web_db",$sql,$link);
                    $row = mysql_fetch_array($result);
                    $num = $row['COUNT(*)'];
					
					if ($num == 0) {
						$DeleteDate = date('Y-m-d', strtotime('+4 week'));
						$sql = "UPDATE UserInfo SET DeleteDate = '$DeleteDate' WHERE UserID = '".$UserID."'";
                        mysql_db_query("web_db",$sql,$link);
						
						if (!mysql_db_query("web_db",$sql,$link)) {
							mysql_db_query("web_db",$sql,$link);
						}
					}

					echo "#YES#\n";
				}
				else {
					echo "#NO#\n";
				}
			}
			else {
				
				$sql = "SELECT IPAddress FROM MainStationInfo WHERE StationID = '".$StationID."'";
                
                $result = mysql_db_query("web_db",$sql,$link);
                $row = mysql_fetch_array($result);

				$OldIPAddress = $row['IPAddress'];
				
				if ($OldIPAddress != $IPAddress) {
					$sql = "UPDATE MainStationInfo SET IPAddress = '".$IPAddress."' WHERE StationID = '".$StationID."'";
                    mysql_db_query("web_db",$sql,$link);
				}
				
				$sql = "DELETE FROM UserRelateMainStation WHERE UserID = '".$UserID."' AND StationID = '".$StationID."' AND PermissionID = '".$PermissionID."'";
                mysql_db_query("web_db",$sql,$link);
                
				
				$sql = "SELECT * FROM UserRelateMainStation WHERE UserID = '".$UserID."' AND StationID = '".$StationID."' AND PermissionID = '".$PermissionID."'";
                $result = mysql_db_query("web_db",$sql,$link);
                $num = mysql_num_rows($result);
				
				if ($num == 0) {
				
					$sql = "SELECT COUNT(*) FROM UserInfo INNER JOIN UserRelateMainStation ON UserInfo.UserID = UserRelateMainStation.UserID WHERE UserInfo.UserID = '".$UserID."'";
                    
                    $result = mysql_db_query("web_db",$sql,$link);
                    $row = mysql_fetch_array($result);
                    $num = $row['COUNT(*)'];
					
					if ($num == 0) {
						$DeleteDate = date('Y-m-d', strtotime('+4 week'));
						$sql = "UPDATE UserInfo SET DeleteDate = '".$DeleteDate."' WHERE UserID = '".$UserID."'";
                        
                        mysql_db_query("web_db",$sql,$link);
						
						if (!mysql_db_query("web_db",$sql,$link)) {
                            mysql_db_query("web_db",$sql,$link);
						}
					}
				
					echo "#YES#\n";
				}
				else {
					echo "#NO#\n";
				}
			}
		}
		else {
			echo "#NO#\n";
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
