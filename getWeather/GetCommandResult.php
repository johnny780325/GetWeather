<?php

header("content-type:text/html;charset=utf-8");

require('General.php');

if (isset($_POST['StationID']) && isset($_POST['DeviceType']) && isset($_POST['SuccessCount']) && isset($_POST['FailCount'])) {
    
    $stationid = myStripslashes($_POST['StationID']);
    $deviceType = myStripslashes($_POST['DeviceType']);
    $successCount = myStripslashes($_POST['SuccessCount']);
    $failCount = myStripslashes($_POST['FailCount']);
    
    $sql = "SELECT * FROM CommandResult WHERE StationID = '".$stationid."' AND DeviceType = '".$deviceType."'";
    $result = mysql_db_query("web_db",$sql,$link);
    
    $row = mysql_fetch_array($result);
    $num = mysql_num_rows($result);
    
    if ($num == 0) {
        
        $sql = "INSERT INTO CommandResult (StationID,DeviceType,SuccessCount,FailCount) VALUES ('".$stationid."','".$deviceType."','".$successCount."','".$failCount."')";
        mysql_db_query("web_db",$sql,$link);

    }
    else {
        
        $NewSuccessCount = $row['SuccessCount'] + $successCount;
        $NewFailCount = $row['FailCount'] + $failCount;
        
        $sql = "UPDATE CommandResult SET SuccessCount = '".$NewSuccessCount."',FailCount = '".$NewFailCount."' WHERE StationID = '".$stationid."' AND DeviceType = '".$deviceType."'";
        mysql_db_query("web_db",$sql,$link);
        
    }
    
    echo "#YES#\n";
    
}
else {
    echo "#NO#\n";
}

?>