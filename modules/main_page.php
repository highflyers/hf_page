<?php
require_once './parser_tpl.php';
require_once './model/user.php';
require_once './modules/menu_loader.php';
require_once './main_controller.php';
require_once './model/news_provider.php';

class MainPage
{
  private $_user;
  private $_menuLoader;
  private $_controller;
  private $newsProv;

  public function __construct(Controller $controller, MenuLoader $loader, NewsProvider $newsProv, $user = null)
  {
    $this->_user = $user;
    $this->_newsProv = $newsProv;
    $this->_menuLoader = $loader;
    $this->_controller = $controller;
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

  private function GetWhereIs()
  {
    $template = new Template(CURRENT_TEMPLATE."where_is.htm");
    $template->Laduj();
    $template->Dodaj("tpl_url", CURRENT_TEMPLATE);
    return $template->Parsuj();
  }

  private function GetHeadSection()
  {
    $template = new Template(CURRENT_TEMPLATE."head_section.htm");
    $template->Laduj();

    $template->Dodaj("tpl_url", CURRENT_TEMPLATE);
    return $template->Parsuj();
  }

  private function GetCurrentContent()
  {
    return $this->_controller->Decision();
  }

  private function GetBodySection()
  {
    $template = new Template(CURRENT_TEMPLATE."body_section.htm");
    $template->Laduj();

    $template->Dodaj("tpl_url", CURRENT_TEMPLATE);
    $template->Dodaj("header", $this->GetHeader());
    $template->Dodaj("footer", $this->GetFooter());
    $template->Dodaj("main_content", $this->GetCurrentContent());
    $template->Dodaj("where_is", $this->GetWhereIs());
    $template->Dodaj("news_short_list", $this->GetNewsShortList());
    return $template->Parsuj();
  }

  private function GetNewsShortList()
  {
    $template = new Template(CURRENT_TEMPLATE."news_short_list.htm");
    $template->Laduj();
    $template->DodajPetle("news_short", $this->_newsProv->GetLastHeaders(4));

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

    
    echo $template->Parsuj();
    
  }
}

?>