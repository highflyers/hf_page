<?php
require_once './parser_tpl.php';
require_once './model/user.php';
require_once './modules/menu_loader.php';
require_once './main_controller.php';
require_once './model/news_provider.php';
require_once './modules/where_is.php';
require_once './main_controller.php';
require_once './modules/login.php';
class MainPage {
	private $_user;
	private $_menuLoader;
	private $_controller;
	private $_newsProv;
	private $_mysql;
	private $_login;
	public function __construct(MySQL $mysql, Login $login) {
		$this->_login = $login;
		$this->_user = $login->GetUser ();
		$this->_newsProv = new NewsProvider ( $mysql );
		$this->_menuLoader = new MenuLoader ( $mysql );
		$this->_controller = new Controller ( $mysql, $login );
		$this->_mysql = $mysql;
		if (isset ( $_GET ['id'] ))
			$id = intval ( $_GET ['id'] );
		else if (isset ( $_GET ['news_id'] ))
			$id = intval ( $_GET ['news_id'] );
		else
			$id = - 1;
		$this->_whereIs = new WhereIs ( $mysql, $id, $this->_controller->GetAction () == "news_concrete" || $this->_controller->GetAction () == 'news' ? 1 : 0 );
	}
	private function GetHeader() {
		global $langID;
		$template = new Template ( CURRENT_TEMPLATE . "header.htm" );
		$template->Laduj ();
		$template->Dodaj("polsl_website", $langID == 'pl' ? 'http://www.polsl.pl/' : 'http://www.polsl.pl/en');
		if ($this->_user != null)
			$template->Dodaj ( "user", $this->_user->ToAssociatedArray () );
		
		$template->DodajWarunek ( "loggedin", $this->_login->IsLoggedIn () );
		
		if ($this->_login->IsLoggedIn ())
			$template->Dodaj ( "user", $this->_user->ToArray () );
		
		if ( $this->_controller->GetAction () == 'index' ) {
			$template->Dodaj ( "classTop", "topMain" );
			$template->Dodaj ( "classHeader", "headerMain" );
		}
		else {
			$template->Dodaj ( "classTop", "topPage" );
			$template->Dodaj ( "classHeader", "headerPage" );
		}
		$template->Dodaj ( "main_menu", $this->GetMainMenu () );
		
		return $template->Parsuj ();
	}
	private function GetCurrentContentTpl() {
		if ($this->_controller->GetAction () == 'index')
			return $this->GetTotalIndexPage ();
		else
			return $this->GetPage ();
	}
	private function GetPage() {
		$template = new Template ( CURRENT_TEMPLATE . "page_content.htm" );
		$template->Laduj ();
		
		$template->Dodaj ( "main_content", $this->GetCurrentContent () );
		$template->Dodaj ( "where_is", $this->GetWhereIs () );
		$template->Dodaj ( "news_short_list", $this->GetNewsShortList () );
		
		return $template->Parsuj ();
	}
	private function GetTotalIndexPage() {
		$template = new Template ( CURRENT_TEMPLATE . "index_content.htm" );
		$template->Laduj ();
		
		$provider = new NewsProvider ( $this->_mysql );
		
		$tmp = $provider->GetLastNewsList ( 6 );
		
		$content = array ();
		$i = 1;
		foreach ( $tmp as $val )
			array_push ( $content, $val->GetNewsFromIndex ( $i ++ ) );
		
		$template->DodajPetle ( "news_expand_list", $content );
		
		$dynList = array ();
		
		$result = $this->_mysql->Query ( 'select ' . $this->_mysql->GetImprovedLang ( 'news.title' ) . 'title from news order by date desc limit 0, 6' );
		$i=1;
		while ($row = mysql_fetch_array($result)) {
			array_push ( $dynList, array (
					'title' => $row ['title'],
					'iterator' => $i ++ 
			) );
		}
		
		$template->DodajPetle ( "quick_dynamic_list", $dynList );
		
		return $template->Parsuj ();
	}
	private function GetFooter() {
		$template = new Template ( CURRENT_TEMPLATE . "footer.htm" );
		$template->Laduj ();
		
		return $template->Parsuj ();
	}
	private function GetWhereIs() {
		return $this->_whereIs->GetWhereIsPanel ();
	}
	private function GetHeadSection() {
		$template = new Template ( CURRENT_TEMPLATE . "head_section.htm" );
		$template->Laduj ();
		
		$template->Dodaj ( "tpl_url", CURRENT_TEMPLATE );
		return $template->Parsuj ();
	}
	private function GetCurrentContent() {
		return $this->_controller->Decision ();
	}
	private function GetBodySection() {
		$template = new Template ( CURRENT_TEMPLATE . "body_section.htm" );
		$template->Laduj ();
		
		$template->Dodaj ( "tpl_url", CURRENT_TEMPLATE );
		$template->Dodaj ( "header", $this->GetHeader () );
		$template->Dodaj ( "footer", $this->GetFooter () );
		$template->Dodaj ( "current_content_tpl", $this->GetCurrentContentTpl () );
		return $template->Parsuj ();
	}
	private function GetNewsShortList() {
		$template = new Template ( CURRENT_TEMPLATE . "news_short_list.htm" );
		$template->Laduj ();
		$template->DodajPetle ( "news_short", $this->_newsProv->GetLastHeaders ( 4 ) );
		
		return $template->Parsuj ();
	}
	private function GetMainMenu() {
		return $this->_menuLoader->GetMenu ();
	}
	public function ShowPage() {
		$template = new Template ( CURRENT_TEMPLATE . "main_page.htm" );
		$template->Laduj ();
		$template->DodajWarunek ( "USER_EXISTS", $this->_user != null );
		$template->Dodaj ( "head_section", $this->GetHeadSection () );
		$template->Dodaj ( "body_section", $this->GetBodySection () );
		
		echo $template->Parsuj ();
	}
}

?>