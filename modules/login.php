<?php 
require_once 'model/session.php';
require_once 'model/user.php';
require_once 'parser_tpl.php';

class Login
{
	private $_session;
	private $_user;
	
	function __construct(Session $sesObject, User $user = null)
	{
		$this->session = $sesObject;
		$this->_user = $user;
	}
	
	function SetUser(User $user)
	{
		$this->_user = $user;
	}
	
	function GetUser()
	{
		return $this->_user;
	}
	
	function IsLoggedIn()
	{
		return $this->_user != null;
	}
	
	function ShowLoginForm($fileName)
	{
		$template = new Template(CURRENT_TEMPLATE.$fileName);
		$template->Laduj();
		$template->DodajWarunek("IS_LOGGED_IN", $this->IsLoggedIn());
		
		if ( $this->IsLoggedIn() ) 
		{
			$template->Dodaj("login", $this->_user->get_nick());
		}
		
		echo $template->Parsuj();
	}
}

?>