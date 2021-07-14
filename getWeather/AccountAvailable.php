<?php

header("content-type:text/html;charset=utf-8");

require('General.php');

if (isset($_POST['Account'])) {
    
    $Account = myStripslashes($_POST['Account']);
    
    if (CheckAccount($Account)) {
        
        $sql = "SELECT COUNT(*) FROM UserInfo WHERE Account = '".$Account."'";
        $result = mysql_db_query("web_db",$sql,$link);
        
        $row = mysql_fetch_array($result);
        $num = $row['COUNT(*)'];
    
        if ($num == 0) {
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
