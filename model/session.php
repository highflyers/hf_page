<?php

require_once "common.php";

class Session
{	
  function SessionStarted()
  {
    return ( session_status() == PHP_SESSION_ACTIVE );			
  }
  function StartSession()
  {
    if ( !$this->SessionStarted() )
      session_start();
  }
	
  function RegisterVar($varName, $varValue)
  {
    if ( $this->SessionStarted() )
      $_SESSION[$varName] = $varValue;
    else
      {
	ShowError("Sesja nie zostala rozpoczeta.");
	return ErrorLevel::ERROR;
      }
  }
	
  function GetSessionVar($varName)
  {
    if ( $this->SessionStarted() )
      {
	if ( isset( $_SESSION[$varName] ) )
	  {
	    return $_SESSION[$varName];
	  }
	else 
	  {
	    return ErrorLevel::WARNING;
	  }
      }
    else
      {
	ShowError("Sesja nie zostala rozpoczeta.");
	return ErrorLevel::ERROR; 
      }
  }
  function Clear()
  {
    $_SESSION = array();
  }
	
}

?>