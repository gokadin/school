<?php
header('Cache-control: private');

if (isset($_POST['lang'])) {
	$lang = $_POST['lang'];
	
	$_SESSION['lang'] = $lang;
	setcookie('lang', $lang, time() + (3600 * 24 * 30));
} else if (isset($_SESSION['lang'])) {
	$lang = $_SESSION['lang'];
} else if (isset($_COOKIE['lang'])) {
	$lang = $_COOKIE['lang'];
} else {
	$lang = 'en';
}

switch ($lang) {
	case 'en' :
		$lang_file = 'lang.en.php';
		break;
	case 'fr' :
		$lang_file = 'lang.fr.php';
		break;
		
	default :
		$lang_file = 'lang.en.php';
}

include_once $lang_file;
?>