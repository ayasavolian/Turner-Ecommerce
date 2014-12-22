<?php
$str_browser_language = !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? strtok(strip_tags($_SERVER['HTTP_ACCEPT_LANGUAGE']), ',') : '';
	$str_browser_language = !empty($_GET['language']) ? $_GET['language'] : $str_browser_language;
	switch (substr($str_browser_language, 0,2))
	{
		case 'de':
			$str_language = 'de';
			break;
		case 'en':
			$str_language = 'en';
			break;
		default:
			$str_language = 'en';
	}
    
	$arr_available_languages = array();
	$arr_available_languages[] = array('str_name' => 'English', 'str_token' => 'en');
	$arr_available_languages[] = array('str_name' => 'Deutsch', 'str_token' => 'de');
    
	$str_available_languages = (string) '';
	foreach ($arr_available_languages as $arr_language)
	{
		if ($arr_language['str_token'] !== $str_language)
		{
			$str_available_languages .= '<a href="'.strip_tags($_SERVER['PHP_SELF']).'?language='.$arr_language['str_token'].'" lang="'.$arr_language['str_token'].'" xml:lang="'.$arr_language['str_token'].'" hreflang="'.$arr_language['str_token'].'">'.$arr_language['str_name'].'</a> | ';
		}
	}
	$str_available_languages = substr($str_available_languages, 0, -3);
/*
 DEFINE('DB_USERNAME', 'marketos_simoni2');
 DEFINE('DB_PASSWORD', 'U8nobney');
 DEFINE('DB_HOST', 'localhost');
 DEFINE('DB_DATABASE', 'marketos_ecommerce');
*/

 DEFINE('DB_USERNAME', 'root');
 DEFINE('DB_PASSWORD', 'root');
 DEFINE('DB_HOST', 'localhost');
 DEFINE('DB_DATABASE', 'ecommerce');

 // http://www.php.net/manual/en/mysqli.connect.php
$mysqli = new mysqli(DB_HOST, DB_USERNAME, 
DB_PASSWORD, DB_DATABASE);
 if (mysqli_connect_error()) {
 die('Connect Error (' . mysqli_connect_errno() 
. ') ' . mysqli_connect_error());
 }
 //$mysqli->close();
?>