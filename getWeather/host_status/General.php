<?php 

$dbFile="sqlite:PS500.sqlite";
$link = @mysql_connect("localhost","root","zxcvbn") or die("<br>Oops, Unable to connect to DB!".mysql_error());
abstract class SystemPermission
{
	const Owner=0x00;
	const User1=0x01;
	const User2=0x02;
	const User3=0x03;
	const User4=0x04;
	const User5=0x05;
}

function CheckEmail($email) {
	if (eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,6}$", $email)) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

function CheckAccount($account) {
	if (CheckEmail($account)) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

function CheckPasswordStrength($password) { 
    $strength = 0; 
	
	$len = strlen($password);
	if ($len < 8 || $len > 12) {
		return $strength; 
	}
	
    $patterns = array('#[a-z]#','#[A-Z]#','#[0-9]#','/[¬!"£$%^&*()`{}\[\]:@~;\'#<>?,.\/\\-=_+\|]/'); 
    foreach($patterns as $pattern) 
    { 
        if(preg_match($pattern,$password,$matches)) 
        { 
            $strength++; 
        } 
    } 
    return $strength;
}

function CheckPassword($password) {
	if (CheckPasswordStrength($password) >= 2) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

function myStripslashes($value){
  if (get_magic_quotes_gpc())
    $value = stripslashes($value);
  return $value;
}

function generatorPassword() {
    $password_len = 8;
    $password = '';

    $word = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ0123456789';
    $len = strlen($word);

    for ($i = 0; $i < $password_len; $i++) {
        $password .= $word[rand() % $len];
    }

    return $password;
}

function IsValidAccountPassword($account,$password) {
	
	if ($account !='' && $password !='') {
		if (CheckAccount($account) && CheckPassword($password)) {
			return TRUE;
		}
	}
	return FALSE;
}
?>
