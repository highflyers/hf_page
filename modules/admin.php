<?php

require_once './modules/wysiwyg_editor.php';
require_once './modules/admin_news.php';
require_once './modules/admin_menu.php';

class AdminController
{
  private $_user;
  private $_newsManager;
  private $_menuManager;

  public function __construct($user, $mysql)
  {
    $this->_user = $user;
    $this->_newsManager = new AdminNews($mysql);
    $this->_menuManager = new AdminMenu($mysql);
  }

  private function IsLoggedIn()
  {
    return $this->_user->getlevel() >= 0;
  }

  public function Decision()
  {
    if ( !$this->IsLoggedIn() )
      return "Nie masz tu wstępu!";

    $act = ( isset($_GET['admin_act'] ) ) ? $_GET['admin_act'] : "none";

    $add = "";

    if ( $act == 'edit_news' )
      $add = $this->_newsManager->GetEditNews();
    else if ( $act == 'add_news' )
      $add = $this->_newsManager->GetAddNews();
    else if ( $act == 'menu_mod' )
      $add = $this->_menuManager->ShowMenuMod();

    return $this->GetMasterPage().$add;
  }



  public function GetMasterPage()
  {

    $template = new Template(CURRENT_TEMPLATE."admin_main.htm");
    $template->Laduj();

    return $template->Parsuj();
   
  }
}
?>