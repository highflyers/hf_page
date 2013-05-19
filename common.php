<?php

class ErrorLevel
{
	const __default = self::NO_ERROR;

	const WARNING = 1;
	const ERROR = 2;
	const CRITICAL_ERROR = 3;
}


function ShowError($msg)
{
	echo $msg;
}

?>