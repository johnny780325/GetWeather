<?php 

header("content-type:text/html;charset=utf-8");

require('General.php');

if (isset($_POST['TimeDifference'])) {
	
	$TimeDifference = myStripslashes($_POST['TimeDifference']);
	$MaxTimeDifference = 24 * 60 * 60;
	
	//時差只在+-24小時內
	if ($TimeDifference > 0) {
		
		if (abs($MaxTimeDifference - $TimeDifference) > 0) {
			$myTime = date("Y#m#d#H#i#s",time() + $TimeDifference);
			echo "#$myTime#\n";
		}
	}
	else if ($TimeDifference <= 0) {
	
		if (abs($MaxTimeDifference + $TimeDifference) > 0) {
			$myTime = date("Y#m#d#H#i#s",time() + $TimeDifference);
			echo "#$myTime#\n";
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
