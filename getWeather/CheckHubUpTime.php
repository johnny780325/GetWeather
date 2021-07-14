<?php

header("content-type:text/html;charset=utf-8");

require('General.php');

if (isset($_POST['StationID']) && isset($_POST['TimeCount'])) {

	$stationid = myStripslashes($_POST['StationID']);
	$timeCount = myStripslashes($_POST['TimeCount']);

    $sql = "SELECT * FROM HubUpTime WHERE StationID = '".$stationid."' ORDER BY RowID DESC limit 1";
    
    $result = mysql_db_query("web_db",$sql,$link);
    $row = mysql_fetch_array($result);

    $MaxRowID = $row['RowID'];
    $OldTimeCount = $row['TimeCount'];
    $num = mysql_num_rows($result);

    if ($num == 0) {
        
        $sql = "INSERT INTO HubUpTime (RowID,StationID,TimeCount) VALUES (1,'".$stationid."','".$timeCount."')";
        mysql_db_query("web_db",$sql,$link);
        
        echo "#YES#\n";
        
    }
    else if ($timeCount < $OldTimeCount) {
        
        $rowID = $MaxRowID + 1;
        $sql = "INSERT INTO HubUpTime (RowID,StationID,TimeCount) VALUES ('".$rowID."','".$stationid."','".$timeCount."')";
        mysql_db_query("web_db",$sql,$link);
        
        echo "#YES#\n";
        
    }
    else {
        
        $sql = "UPDATE HubUpTime SET TimeCount = '".$timeCount."' WHERE StationID = '".$stationid."' AND RowID = '".$MaxRowID."'";
        mysql_db_query("web_db",$sql,$link);
        
        echo "#YES#\n";
    }
    
}
else {
	echo "#NO#\n";
}

?>
