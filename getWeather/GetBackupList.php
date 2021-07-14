<?php

header("content-type:text/html;charset=utf-8");

require('General.php');

if (isset($_POST['StationID'])) {
    
    $stationid = myStripslashes($_POST['StationID']);
        
    $sql = "SELECT * FROM MainStationBackupList WHERE StationID = '".$stationid."'";
    $result = mysql_db_query("web_db",$sql,$link);
    
    $row = mysql_fetch_array($result);
    
    if ($stationid != $row['StationID']) {

        $Result = array(
                        'Result' => 'NONO',
                        'BackupInfo' => array()
                        );

    } else {
        
        $backupDate = $row['BackupDate'];
        $firmwareVersion = $row['FirmwareVersion'];
        
        $Result['Result'] = 'YES';
        $Result['BackupInfo'][] = array(
                                      'StationID' => $stationid,
                                      'BackupDate' => $backupDate,
                                      'FirmwareVersion' => $firmwareVersion
                                      );
        
    }
    
    $json = json_encode($Result);
    echo $json;
        
} else {
    
    $Result = array(
                    'Result' => 'NONO',
                    'BackupInfo' => array()
                    );
    
    $json = json_encode($Result);
    echo $json;

}

?>