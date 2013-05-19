<?php 

$modules = array("login", "arstdar");

if ( $id >=0 && $id < count($modules) )
{
	require_once 'modules/'.$modules[$id].'php';
}

?>