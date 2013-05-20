<?php 
require_once 'model/session.php';
require_once 'model/user.php';
require_once 'parser_tpl.php';

class Login
{
  private $_sess;
  private $_user;
  private $_mysql;
	
  function __construct(Session $sesObject, MySQL $mysql)
  {
    $this->_mysql = $mysql;
    $this->_sess = $sesObject;
  }
	
  function LoginProcess($nick, $password)
  {
    $result = $this->_mysql->Query("select * from user where nick='".htmlspecialchars($nick)."' and password='".htmlspecialchars($password)."'");

    if ( $this->_mysql->NumberOfRows() == 0 )
      {
	$this->_user = null;
	$this->_sess->Clear();
	return;
      }

    $this->_user = new User($result->fetch_assoc());
    $this->_sess->Clear();
    $this->_sess->RegisterVar("nick", $nick);
    $this->_sess->RegisterVar("pass", $password);
  }

  function ValidateLogin()
  {
    $this->LoginProcess(htmlspecialchars($this->_sess->GetSessionVar('nick')), htmlspecialchars($this->_sess->GetSessionVar('pass')));
  }
	
  function Logout()
  {
    $this->_sess->Clear();
  }

  function GetUser()
  {
    return $this->_user;
  }
	
  function IsLoggedIn()
  {
    return $this->_user != null;
  }
	
  function GetLoginForm($error = "")
  {
    $template = new Template(CURRENT_TEMPLATE."login.htm");
    $template->Laduj();
    $template->DodajWarunek("IS_LOGGED_IN", $this->IsLoggedIn());
		
    if ( $this->IsLoggedIn() ) 
      {
	$template->Dodaj("login", $this->_user->getnick());
      }
    else
      $template->Dodaj("error", $error);

    return $template->Parsuj();
  }
}

?>