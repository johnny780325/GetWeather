<?php
    
    require('QueryWOEID.php');
    $link = @mysql_connect("localhost","root","zxcvbn") or die("<br>Oops, Unable to connect to DB!".mysql_error());
    
    $updateTime = $argv[1];

//    $sql = "SELECT WOEID FROM City WHERE (UpdateTime >= '".$updateTime."' AND UpdateTime < ('".$updateTime."' +100))";
    $sql = "SELECT WOEID FROM City WHERE (UpdateFlag = 0 AND UpdateTime < ('".$updateTime."' +100))";

	$result_for_update = mysql_db_query("web_db",$sql,$link);
	$num = mysql_num_rows($result_for_update);
	$today = date('Y-m-d');

	if ($num != 0) {

		while ($row = mysql_fetch_array($result_for_update)) {

			$woeid = $row['WOEID'];

			$url = "https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20weather.forecast%20where%20woeid%3D".$woeid."&format=json&diagnostics=true&callback=";

			$json = json_decode(curl_get_contents($url), true);
			if ($json) {
            
				//fetch weather info
				$chill = $json[query][results][channel][wind][chill];
				$direction = $json[query][results][channel][wind][direction];
				$speed = $json[query][results][channel][wind][speed];
				$humidity = $json[query][results][channel][atmosphere][humidity];
				$pressure = $json[query][results][channel][atmosphere][pressure];
				$rising = $json[query][results][channel][atmosphere][rising];
				$visibility = $json[query][results][channel][atmosphere][visibility];
				$sunrise = (int)str_replace(":","",date("H:i",strtotime($json[query][results][channel][astronomy][sunrise])));
				$sunset = (int)str_replace(":","",date("H:i",strtotime($json[query][results][channel][astronomy][sunset])));
                $temp = $json[query][results][channel][item][condition][temp];

				//Check WeatherConditionID already exists or not
				for ($i = 0; $i < 5; $i++) {

					$weatherCondition = $json[query][results][channel][item][forecast][$i][text];
					$sql = "SELECT Content FROM WeatherCondition WHERE Content = '".$weatherCondition."'";
					$result = mysql_db_query("web_db",$sql,$link);
					$num = mysql_num_rows($result);

					if ($num == 0) {

						$sql = "SELECT COUNT(*) FROM WeatherCondition";
						$result = mysql_db_query("web_db",$sql,$link);
						$row = mysql_fetch_array($result);

						$weatherConditionID = $row['COUNT(*)'] + 1;
						$sql = "INSERT INTO WeatherCondition (WeatherConditionID,Content) VALUES ('".$weatherConditionID."','".$weatherCondition."')";
						mysql_db_query("web_db",$sql,$link);

					}

				}

				for ($i = 0; $i < 5; $i++) {

					$day = date("Y-m-d", strtotime($today."+".$i." day"));
					$dateid = $i+1;

					$sql = "UPDATE DateIndex SET Date = '".$day."' WHERE WOEID = '".$woeid."' AND DateID = '".$dateid."'";
					mysql_db_query("web_db",$sql,$link);

				}

				//Today
				$sql = "UPDATE Today SET Chill = '".$chill."',Direction = '".$direction."',Speed = '".$speed."',Humidity = '".$humidity."',Pressure = '".$pressure."',Rising = '".$rising."',Visibility = '".$visibility."',Sunrise = '".$sunrise."',Sunset = '".$sunset."',Temperature = '".$temp."' WHERE WOEID = '".$woeid."'";
				mysql_db_query("web_db",$sql,$link);

				//Recently five days
				for ($i = 0; $i < 5; $i++) {

					//get WeatherConditionID
					$weatherCondition = $json[query][results][channel][item][forecast][$i][text];
					$sql = "SELECT WeatherConditionID FROM WeatherCondition WHERE Content = '".$weatherCondition."'";
					$result = mysql_db_query("web_db",$sql,$link);
					$num = mysql_num_rows($result);

					if ($num != 0) {
						$row = mysql_fetch_array($result);
					}

					//insert Recently five days weather information
					$high = $json[query][results][channel][item][forecast][$i][high];
					$low = $json[query][results][channel][item][forecast][$i][low];
					$dateid = $i+1;
					$weatherConditionID = $row['WeatherConditionID'];

					$sql = "UPDATE RecentlyFiveDays SET High = '".$high."',Low = '".$low."',WeatherConditionID = '".$weatherConditionID."' WHERE WOEID = '".$woeid."' AND DateID = '".$dateid."'";
					mysql_db_query("web_db",$sql,$link);

				}

				//Confirm UpdateFlag
				$sql = "UPDATE City SET UpdateFlag = 1 WHERE WOEID = '".$woeid."'";
				mysql_db_query("web_db",$sql,$link);

			}
			else {
				echo "#NONO#\n"; //fail to fetch data
			}            
		}

	}

//Reset all UpdateFlag
if ($updateTime == 2300) {

	$sql = "UPDATE City SET UpdateFlag = 0";
	mysql_db_query("web_db",$sql,$link);

}

?>
