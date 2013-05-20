<?php

error_reporting(E_ALL); 

define('CURRENT_TEMPLATE', "templates/platektemplejt/");
require_once 'model/session.php';

$session = new Session();

$session->StartSession();

require_once 'modules/login.php';
require_once 'model/mysql.php';
$object = new Mysql();
$object->Connect("localhost", "root", "root", "hfdb");
require_once "model/user.php";
require_once "modules/main_page.php";

$uss = new User();
$uss->set_id(123);
$uss->set_nick("loganek");

$mp = new MainPage($object, $uss);
$mp->ShowPage();
$object->Disconnect();
?>