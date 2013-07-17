<?php
require_once "common.php";
class Session {
	function StartSession() {
		session_start ();
	}
	function RegisterVar($varName, $varValue) {
		$_SESSION [$varName] = $varValue;
	}
	function GetSessionVar($varName) {
		if (isset ( $_SESSION [$varName] )) {
			return $_SESSION [$varName];
		} else {
			return ErrorLevel::WARNING;
		}
	}
	function Clear() {
		$language = $_SESSION ['hf_lang'];
		$_SESSION = array ();
		$_SESSION ['hf_lang'] = $language;
	}
}

?>