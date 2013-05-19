<?php
require_once './parser_tpl.php';
require_once './model/user.php';
require_once './modules/menu_loader.php';
class MainPage
{
  private $_user;
  private $_menuLoader;

  public function __construct(MenuLoader $loader, $user = null)
  {
    $this->_user = $user;
    $this->_menuLoader = $loader;
  }	
 
  private function GetHeader()
  {
    $template = new Template(CURRENT_TEMPLATE."header.htm");
    $template->Laduj();

    if ( $this->_user != null )
      $template->Dodaj("user", $this->_user->ToAssociatedArray());

    $template->Dodaj("main_menu", $this->GetMainMenu());

    return $template->Parsuj();
  }

  private function GetFooter()
  {
    $template = new Template(CURRENT_TEMPLATE."footer.htm");
    $template->Laduj();

    return $template->Parsuj();
  }

  private function GetHeadSection()
  {
    $template = new Template(CURRENT_TEMPLATE."head_section.htm");
    $template->Dodaj("tpl_url", CURRENT_TEMPLATE);
    $template->Laduj();

    return $template->Parsuj();
  }

  private function GetBodySection()
  {
    $template = new Template(CURRENT_TEMPLATE."body_section.htm");
    $template->Laduj();

    return $template->Parsuj();
  }
  
  private function GetMainMenu()
  {
    return $this->_menuLoader->GetMenu();
  }

  public function ShowPage()
  {
    $template = new Template(CURRENT_TEMPLATE."main_page.htm");
    $template->Laduj();
    $template->DodajWarunek("USER_EXISTS", $this->_user != null);
    $template->Dodaj("head_section", $this->GetHeadSection());
    $template->Dodaj("body_section", $this->GetBodySection());
    $template->Dodaj("header", $this->GetHeader());
    $template->Dodaj("footer", $this->GetFooter());
    
    echo $template->Parsuj();
    
  }
}

?>