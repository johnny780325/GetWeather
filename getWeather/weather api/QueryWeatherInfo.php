<?php
    
    require('QueryWOEID.php');

    if (isset($_GET['WOEID'])) {
        
        $woeid = myStripslashes($_GET['WOEID']);
        $link = @mysql_connect("localhost","root","zxcvbn") or die("<br>Oops, Unable to connect to DB!".mysql_error());
        
		//check whether the input WOEID already exists or not
		$sql = "SELECT WOEID,Date FROM DateIndex WHERE WOEID = '".$woeid."' AND DateID = 1";
		$result = mysql_db_query("web_db",$sql,$link);

		$num = mysql_num_rows($result);
		$today = date('Y-m-d');
		$row = mysql_fetch_array($result);

		if (strcmp($today, $row['Date']) == 0) {
			$check_date = True;
		}
		else {
			$check_date = False;
		}

		if ($num == 0) {

			$url = "https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20weather.forecast%20where%20woeid%3D".$woeid."&format=json&diagnostics=true&callback=";

			$json = json_decode(curl_get_contents($url), true);
            
            $city_exist = True;
            if ($json[query][results][channel][item][title] == 'City not found') {
                $city_exist = False;
            }
            
			if ($json && $city_exist) {
            
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

				$cityName = str_replace("City","",str_replace(" ","",$json[query][results][channel][location][city]));

				//convert the timezone of the city to GMT standard
				$timezone = getCityTimezone($cityName);
				date_default_timezone_set($timezone);
				$default_datetime = '00:00';
				$update_time = (int)str_replace(":","",date('H:i',strtotime("default_datetime GMT")));

				//insert new WOEID to DB
				$sql = "INSERT INTO City (WOEID,Name,UpdateTime,UpdateFlag) VALUES ('".$woeid."','".$cityName."','".$update_time."',0)";
				mysql_db_query("web_db",$sql,$link);

				//DateIndex
				//   $today = date('Y-m-d');
				//   echo "<br>Today:".$today."<br>";
				for ($i = 0; $i < 5; $i++) {

					$day = date("Y-m-d", strtotime($today."+".$i." day"));
					$dateid = $i+1;
					$sql = "INSERT INTO DateIndex (WOEID,DateID,Date) VALUES ('".$woeid."','".$dateid."','".$day."')";
					mysql_db_query("web_db",$sql,$link);

				}

				//Today
				$sql = "INSERT INTO Today (WOEID,Chill,Direction,Speed,Humidity,Pressure,Rising,Visibility,Sunrise,Sunset,Temperature) VALUES ('".$woeid."','".$chill."','".$direction."','".$speed."','".$humidity."','".$pressure."','".$rising."','".$visibility."','".$sunrise."','".$sunset."','".$temp."')";
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
					else {
						//echo "#NO#";
					}

					//insert Recently five days weather information
					$high = $json[query][results][channel][item][forecast][$i][high];
					$low = $json[query][results][channel][item][forecast][$i][low];
					$dateid = $i+1;
					$weatherConditionID = $row['WeatherConditionID'];
					$sql = "INSERT INTO RecentlyFiveDays (WOEID,DateID,High,Low,WeatherConditionID) VALUES ('".$woeid."','".$dateid."','".$high."','".$low."','".$weatherConditionID."')";
					mysql_db_query("web_db",$sql,$link);

				}
			}
			else {
				//fail to fetch data
			}
		}
		else if (!$check_date) { //need to update

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
			}
			else {
				//fail to fetch data
			}        
		}

        // output result
        $Result = array(
                        'Result' => '',
                        'City' => '',
                        'TODAY' => array(),
                        'RECENTLY FIVE DAYS' => array(),
                        );
        
        $sql = "SELECT Name FROM City WHERE WOEID = '".$woeid."'";
        $result = mysql_db_query("web_db",$sql,$link);
        $num = mysql_num_rows($result);
        
        if ($num != 0) {
            
            $row = mysql_fetch_array($result);
            
            $Result['Result'] = 'YES';
            $Result['City'] = $row['Name'];
            
            $sql = "SELECT * FROM Today WHERE WOEID = '".$woeid."'";
            $result = mysql_db_query("web_db",$sql,$link);
            $row = mysql_fetch_array($result);

            $todayInfo_arr = array(
                                    'wind' => '',
                                    'atmosphere' => '',
                                    'astronomy' => '',
                                   );
            $todayInfo_count = 0;
            foreach ($row as $key => $value) {
                
                if (is_string($key) && $key != 'WOEID') {
                
                    switch($todayInfo_count) {
                            
                        case 0:
                        case 1:
                        case 2:
                            $todayInfo_arr['wind'][$key] = $value;
                            break;
                        case 3:
                        case 4:
                        case 5:
                        case 6:
                        case 9:
                            $todayInfo_arr['atmosphere'][$key] = $value;
                            break;
                        case 7:
                        case 8:
                            $todayInfo_arr['astronomy'][$key] = $value;
                            break;
                        default:
                            break;
                            
                    }
                    $todayInfo_count++;
                }
            }
            $Result['TODAY'][] = $todayInfo_arr;
            
            $sql = "SELECT DateID,Date FROM DateIndex WHERE WOEID = '".$woeid."'";
            $result = mysql_db_query("web_db",$sql,$link);
  
            while ($row = mysql_fetch_array($result)) {
                
                $dateid = $row['DateID'];

                $sql = "SELECT * FROM RecentlyFiveDays WHERE WOEID = '".$woeid."' AND DateID = '".$dateid."'";
                $result_RFD = mysql_db_query("web_db",$sql,$link);
                $row_RFD = mysql_fetch_array($result_RFD);
                
                $weatherConditionid = $row_RFD['WeatherConditionID'];

                $sql = "SELECT Content FROM WeatherCondition WHERE WeatherConditionID = '".$weatherConditionid."'";
                $result_RFD2 = mysql_db_query("web_db",$sql,$link);
                $row_RFD2 = mysql_fetch_array($result_RFD2);
            
                $Result['RECENTLY FIVE DAYS'][] = array(
                                                        'Date' => $row['Date'],
                                                        'High' => $row_RFD['High'],
                                                        'Low' => $row_RFD['Low'],
                                                        'Weather Condition' => $row_RFD2['Content'],
                                                        );
            }
            
        }
        else {
            $Result['Result'] = 'NO';
        }
        
        $json = json_encode($Result);
        echo $json;
        
    }
    else {
        $Result['Result'] = 'NONO';
    }

?>
