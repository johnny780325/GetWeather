<?php
    
    /*
     WOEID:
     https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20geo.places%20where%20text%3D%22newyork%22&format=json&diagnostics=true&callback=
     
     Weather Info:
     https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20weather.forecast%20where%20woeid%3D2306185&format=json&diagnostics=true&callback=
     */
    
    header("content-type:text/html;charset=utf-8");
    
    require('General.php');
    
    function curl_get_contents($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    
    function getCityTimezone($cityName) {
        
        $url = "https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20geo.places%20where%20text%3D%22".$cityName."%22&format=json&diagnostics=true&callback=";
        
        $json = json_decode(curl_get_contents($url), true); //set the second assoc argument to true so that PHP will return arrays
        
//        echo "<pre>";
//        print_r($json);
        $count = $json[query][count];
        if ($count == 1) {
            $timezone = $json[query][results][place][timezone][content];
        }
        else {
            $timezone = $json[query][results][place][0][timezone][content];
        }
        
        return $timezone;
        
    }
?>