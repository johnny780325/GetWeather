<?php

header("content-type:text/html;charset=utf-8");

require('General.php');

if (isset($_POST['Mac']) && isset($_POST['FirmwareVersion'])) {
	
	$Mac = myStripslashes($_POST['Mac']);
	$NewFirmwareVersion = myStripslashes($_POST['FirmwareVersion']);
	$NewIP = $_SERVER["REMOTE_ADDR"];
	
	$sql = "SELECT StationID, IPAddress, FirmwareVersion FROM MainStationInfo WHERE MacAddress = '".$Mac."'";
    
    $result = mysql_db_query("web_db",$sql,$link);
    $row = mysql_fetch_array($result);

	$OldIP = $row['IPAddress'];
	$OldFirmwareVersion = $row['FirmwareVersion'];
    
    $sql = "SELECT COUNT(*) FROM MainStationInfo WHERE MacAddress = '".$Mac."'";
    $result = mysql_db_query("web_db",$sql,$link);
    $row2 = mysql_fetch_array($result);
    
    $num = $row2['COUNT(*)'];
	if ($num == 1) {
        
		if ($OldIP != $NewIP || $OldFirmwareVersion != $NewFirmwareVersion) {
            
			$sql = "UPDATE MainStationInfo SET IPAddress = '".$NewIP."', FirmwareVersion = '".$NewFirmwareVersion."' WHERE MacAddress = '".$Mac."'";
            mysql_db_query("web_db",$sql,$link);
         
			if (!mysql_db_query("web_db",$sql,$link)) {
                
				$sql = "UPDATE MainStationInfo SET IPAddress = '".$NewIP."' WHERE MacAddress = '".$Mac."'";
                mysql_db_query("web_db",$sql,$link);
			}
		}
		
		$StationID = $row['StationID'];
		$sql = "SELECT UserInfo.UserID, UserInfo.Account, UserInfo.AccessKey, UserRelateMainStation.PermissionID FROM UserInfo INNER JOIN UserRelateMainStation ON UserInfo.UserID = UserRelateMainStation.UserID WHERE StationID = '".$StationID."' ORDER BY UserRelateMainStation.PermissionID";

        $result = mysql_db_query("web_db",$sql,$link);
        $num = mysql_num_rows($result);
		
		if ($num != 0) {
	
			$Owner = Array();
			$User1 = Array();
			$User2 = Array();
			$User3 = Array();
			$User4 = Array();
			$User5 = Array();

			while ($row = mysql_fetch_array($result)) {
				$PermissionID = $row['PermissionID'];
				
				switch ($PermissionID) {
					case SystemPermission::User1:
						$User1['UserID'] = $row['UserID'];
						$User1['UserAccount'] = $row['Account'];
						$User1['UserAccessKey'] = $row['AccessKey'];
						$User1['PermissionID'] = PermissionID;
						break;
					case SystemPermission::User2:
						$User2['UserID'] = $row['UserID'];
						$User2['UserAccount'] = $row['Account'];
						$User2['UserAccessKey'] = $row['AccessKey'];
						$User2['PermissionID'] = PermissionID;						
						break;
					case SystemPermission::User3:
						$User3['UserID'] = $row['UserID'];
						$User3['UserAccount'] = $row['Account'];
						$User3['UserAccessKey'] = $row['AccessKey'];
						$User3['PermissionID'] = PermissionID;
						break;
					case SystemPermission::User4:
						$User4['UserID'] = $row['UserID'];
						$User4['UserAccount'] = $row['Account'];
						$User4['UserAccessKey'] = $row['AccessKey'];
						$User4['PermissionID'] = PermissionID;						
						break;
					case SystemPermission::User5:
						$User5['UserID'] = $row['UserID'];
						$User5['UserAccount'] = $row['Account'];
						$User5['UserAccessKey'] = $row['AccessKey'];
						$User5['PermissionID'] = PermissionID;
						break;
					default:
						$Owner['UserID'] = $row['UserID'];
						$Owner['UserAccount'] = $row['Account'];
						$Owner['UserAccessKey'] = $row['AccessKey'];
						$Owner['PermissionID'] = PermissionID;
				}
			}
			
			printf("~#%1s#",$Owner['UserID']);
			printf("%1s#",$Owner['UserAccount']);
			printf("%1s#~",$Owner['UserAccessKey']);
			printf("#%1s#",$User1['UserID']);
			printf("%1s#",$User1['UserAccount']);
			printf("%1s#~",$User1['UserAccessKey']);
			printf("#%1s#",$User2['UserID']);
			printf("%1s#",$User2['UserAccount']);
			printf("%1s#~",$User2['UserAccessKey']);
			printf("#%1s#",$User3['UserID']);
			printf("%1s#",$User3['UserAccount']);
			printf("%1s#~",$User3['UserAccessKey']);
			printf("#%1s#",$User4['UserID']);
			printf("%1s#",$User4['UserAccount']);
			printf("%1s#~",$User4['UserAccessKey']);
			printf("#%1s#",$User5['UserID']);
			printf("%1s#",$User5['UserAccount']);
			printf("%1s#~",$User5['UserAccessKey']);
			printf("%1s",$StationID);
		}
		else {
			echo "#~YES~#\n";
		}
	}
	else {
		echo "#~YES~#\n";
	}
}
else {
	echo "#~NO~#\n";
}

?>
