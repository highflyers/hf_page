<?php

error_reporting(E_ALL); 

define('CURRENT_TEMPLATE', "templates/platektemplejt/");
require_once 'model/session.php';

$session = new Session();

$session->StartSession();
require_once 'model/bbcode.php';
require_once 'modules/login.php';
require_once 'model/mysql.php';
require_once 'modules/menu_loader.php';
$object = new Mysql();
$object->Connect("localhost", "root", "root", "hfdb");
require_once "model/user.php";
require_once "modules/main_page.php";

require_once "./main_controller.php";

$ctrl = new Controller($object);
$uss = new User();
$uss->set_id(123);
$uss->set_nick("loganek");

$mainMenu = new MenuLoader($object);
$mp = new MainPage($ctrl, $mainMenu, $uss);
$mp->ShowPage();
$object->Disconnect();
?>