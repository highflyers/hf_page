<?php
error_reporting ( E_ALL );

require_once 'model/session.php';

$session = new Session ();
$session->StartSession ();

define ( 'DEFAULT_LANG', 'pl' );

$langID = isset ( $_GET ["hf_lang"] ) ?
	$_GET ['hf_lang'] : 
	(isset($_SESSION['hf_lang']) ? 
		$_SESSION['hf_lang'] : DEFAULT_LANG);

if ($langID == 'pl')
	require_once 'lang/pl.php';
else if ($langID == 'en')
	require_once 'lang/en.php';
else {
	require_once 'lang/pl.php';
	setcookie ( "lang_cookie", 'pl' );
	$langID = DEFAULT_LANG;
}

$_SESSION['hf_lang'] = $langID;

define ( 'CURRENT_TEMPLATE', "templates/platektemplejt/" );

require_once 'modules/login.php';
require_once 'model/mysql.php';
$object = new Mysql ();
require_once 'config.php';
//$object->Connect ( $dbhost, $dbuser, $dbpassword, $dbname );
$object->Connect ( 'localhost', 'root', '', 'hfpage' );
$login = new Login ( $session, $object );
$login->ValidateLogin ();

require_once './modules/main_page.php';

$mp = new MainPage ( $object, $login );
$mp->ShowPage ();
$object->Disconnect ();
?>