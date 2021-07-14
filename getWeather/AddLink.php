<?php

header("content-type:text/html;charset=utf-8");

require('General.php');

if (isset($_POST['UserID']) && isset($_POST['StationID']) && isset($_POST['PermissionID']) && isset($_POST['Mac']) && isset($_POST['StationVersion']) && isset($_POST['CompanyID']) && isset($_POST['FirmwareVersion'])) {
    
    $UserID = myStripslashes($_POST['UserID']);
    $StationID = myStripslashes($_POST['StationID']);
    $PermissionID = myStripslashes($_POST['PermissionID']);
    $Mac = myStripslashes($_POST['Mac']);
    $StationVersion = myStripslashes($_POST['StationVersion']);
    $CompanyID = myStripslashes($_POST['CompanyID']);
    $FirmwareVersion = myStripslashes($_POST['FirmwareVersion']);
    $IPAddress = $_SERVER["REMOTE_ADDR"];
    
    if ($PermissionID >= SystemPermission::Owner && $PermissionID <= SystemPermission::User5) {
        
        $sql = "SELECT UserID FROM UserInfo WHERE UserID = '".$UserID."'";
        
        $result = mysql_db_query("web_db",$sql,$link);
        $row = mysql_fetch_array($result);
        
        if ($UserID == $row['UserID']) {
            
            if ($PermissionID == SystemPermission::Owner) {

                $sql = "SELECT StationID FROM MainStationInfo WHERE MacAddress = '".$Mac."'";
                
                $result = mysql_db_query("web_db",$sql,$link);
                $num = mysql_num_rows($result);
                
                if ($num == 0) {
                    $sql = "INSERT INTO MainStationInfo (MacAddress,IPAddress,PortNumber,ConnectionType,StationVersion,CompanyID,FirmwareVersion) VALUES ('$Mac','$IPAddress','8080','0','$StationVersion','$CompanyID','$FirmwareVersion')";
                    
                    if (mysql_db_query("web_db",$sql,$link)) {
                        $sql = "SELECT StationID FROM MainStationInfo WHERE MacAddress = '".$Mac."'";
                        
                        $result = mysql_db_query("web_db",$sql,$link);
                        $row = mysql_fetch_array($result);
                        
                        $StationID = $row['StationID'];
                        
                        $sql = "INSERT INTO UserRelateMainStation (UserID,StationID,PermissionID) VALUES ('".$UserID."','".$StationID."','".$PermissionID."')";
                        
                        if (mysql_db_query("web_db",$sql,$link)) {
                            $DeleteDate = 0;
                            $sql = "UPDATE UserInfo SET DeleteDate = '".$DeleteDate."' WHERE UserID = '".$UserID."'";
                            mysql_db_query("web_db",$sql,$link);
                            
                            if (!mysql_db_query("web_db",$sql,$link)) {
                                mysql_db_query("web_db",$sql,$link);
                            }
                            
                            echo "#YES#\n";
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
            }
            else {
                
                $sql = "SELECT StationID, IPAddress FROM MainStationInfo WHERE StationID = '".$StationID."'";
                
                $result = mysql_db_query("web_db",$sql,$link);
                $row = mysql_fetch_array($result);
                
                $OldIPAddress = $row['IPAddress'];
                
                if ($OldIPAddress != $IPAddress) {
                    
                    $sql = "UPDATE MainStationInfo SET IPAddress = '".$IPAddress."' WHERE StationID = '".$StationID."'";
                    mysql_db_query("web_db",$sql,$link);
                }
                
                $num = mysql_num_rows($result);
                
                if ($num == 1) {
                    
                    $sql = "SELECT * FROM UserRelateMainStation WHERE UserID = '".$UserID."' AND StationID = '".$StationID."' AND PermissionID = '".$PermissionID."'";
                    
                    $result = mysql_db_query("web_db",$sql,$link);
                    $num = mysql_num_rows($result);
                    
                    if ($num == 0) {
                        
                        $sql = "SELECT UserID FROM UserRelateMainStation WHERE StationID = '".$StationID."'";
                        $result = mysql_db_query("web_db",$sql,$link);
                        $num = mysql_num_rows($result);
                        
                        $IsValid = TRUE;
                        while ($row = mysql_fetch_array($result))
                        {
                            $ExistUserID = $row['UserID'];
                            if ($ExistUserID == $UserID) {
                                $IsValid = FALSE;
                            }
                        }
                        
						if ($IsValid) {
                       
							$sql = "INSERT INTO UserRelateMainStation (UserID,StationID,PermissionID) VALUES ('".$UserID."','".$StationID."','".$PermissionID."')";
							
							if (mysql_db_query("web_db",$sql,$link)) {
					
								$DeleteDate = 0;
								$sql = "UPDATE UserInfo SET DeleteDate = '".$DeleteDate."' WHERE UserID = '".$UserID."'";
									
                                mysql_db_query("web_db",$sql,$link);
                                
                                if (!mysql_db_query("web_db",$sql,$link)) {
                                    mysql_db_query("web_db",$sql,$link);
                                }
							
								echo "#YES#\n";
                                
							} else {
                                
								echo "#NO#\n";
                                
							}
                            
						} else {
                            
							echo "#NO#\n";
                            
						}
                        
					} else {
                        
						echo "#NO#\n";
                        
					}
				}
                
				else {
                    
					echo "#NO#\n";
                    
				}
			}
            
		} else {
            
			echo "#NO#\n";
            
		}
        
	} else {
        
		echo "#NO#\n";
        
	}
}
else {
    
	echo "#NO#\n";
    
}

?>
