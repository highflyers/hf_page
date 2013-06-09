<?php
// arst
error_reporting(E_ALL); 

define('CURRENT_TEMPLATE', "templates/platektemplejt/");
require_once 'model/session.php';

$session = new Session();
$session->StartSession();

require_once 'modules/login.php';
require_once 'model/mysql.php';
$object = new Mysql();

$object->Connect("localhost", "root", "root", "hfdb");
$login = new Login($session, $object);
$login->ValidateLogin();


require_once './modules/main_page.php';

$mp = new MainPage($object, $login);
$mp->ShowPage();
$object->Disconnect();
?>