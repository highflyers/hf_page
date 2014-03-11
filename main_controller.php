<?php 

require_once "./model/article.php";
require_once "./model/user.php";
require_once "./model/mysql.php";
require_once "./model/news_text.php";
require_once './modules/login.php';
require_once './modules/admin.php';

class Controller
{
  private $_action;
  private $_mysql;
  private $_login;

  public function __construct(MySQL $mysql, Login $login)
  {
    $this->_action = isset($_GET['action']) ? $_GET['action'] : "index";
    $this->_mysql = $mysql;
    $this->_login = $login;
  }
  
  public function GetAction()
  {
    return $this->_action == "news" && isset($_GET['news_id']) ? "news_concrete" : $this->_action;
  }

  public function Decision()
  {
    if ( $this->_action == 'site' )
      return $this->GetSite();
    if ( $this->_action == 'news' || $this->_action == 'index' )
      return $this->GetNews();
    if ( $this->_action == 'login' )
      return $this->GetLoginForm();
    if ( $this->_action == 'logout' )
      {
	$this->_login->Logout();
	Header("Location: /");
      }
    if ( $this->_action == 'show_user' )
      return $this->ShowUser();
    if ( $this->_action == 'admin' )
      return $this->AdminMode();
  }
  
  private function GetSite()
  {
    $id = intval($_GET['id']);
    
    $this->_mysql->Query('select *, '.$this->_mysql->GetImprovedLang('menu.title').'title, '.$this->_mysql->GetImprovedLang('menu.content').'content from menu where id='.$id);

    if ( $this->_mysql->NumberOfRows() == 0 )
      return "ni ma artykulu"; // TODO ??

    $siteInfo = $this->_mysql->FetchAssoc();
    $user_id = $siteInfo['author'];

    $this->_mysql->Query('select first_name, second_name, id from user where id = '.$user_id);

    if ( $this->_mysql->NumberOfRows() == 0 )
      return "ni ma usera";
    
    $user = new User($this->_mysql->FetchAssoc());
    
    $art = new Article($siteInfo['title'], $siteInfo['content'], $user, $siteInfo['date']);
    
    return $art->GetHTMLArticle();
  }

  private function GetNews()
  {
    if ( isset($_GET['news_id'] ) )
      return $this->GetSingleNews(intval($_GET['news_id']));
    else
      return $this->GetShortNewsList();
  }

  private function GetSingleNews($id)
  {
    $provider = new NewsProvider($this->_mysql);
    $row = $provider->GetNews($id);

    if ( $row == null )
      {
	echo "Ni ma takigo newsa! Wylyz stond!";
	return ;
      }

    $this->_mysql->Query('select first_name, second_name, id from user where id = '.intval($row['author']));

    if ( $this->_mysql->NumberOfRows() == 0 )
      return "ni ma usera";
    
    $user = new User($this->_mysql->FetchAssoc());
    
	$date = $provider -> formatDate($row['date']);
	$news = new NewsText($row['title'], $row['content'], $user, $id, $date);
    return $news->GetHTMLNews();
  }

  private function GetShortNewsList()
  {
    $template = new Template(CURRENT_TEMPLATE."news_short_index.htm"); //tutaj
    $template->Laduj();
    $provider = new NewsProvider($this->_mysql);

    $current = isset($_GET['news_list']) ? intval($_GET['news_list']) - 1 : 0;
    $tmp = $provider->GetShortNewsList(5, $current * 5);
    
    $content = array();

    foreach ( $tmp as $val )
      array_push($content, $val->GetShortHTMLNews());

    $displayedNums = 18;
    $dnPerTwo = $displayedNums / 2;
    $nums = array();
    $cnt = $provider->GetNewsCount() / 5;
    
    $maxCur = ($current > $dnPerTwo ) ? $current : $dnPerTwo;
    $right = ($maxCur + $dnPerTwo < $cnt ) ? $maxCur + $dnPerTwo : $cnt;
    $left = ($right - $displayedNums < 0 ) ? 0 : $right - $displayedNums;

    for ( $i = $left; $i < $right; $i++ )
      {
	array_push($nums, $i + 1);
      }
    
    $template->Dodaj("max_number", ceil($cnt));
    $template->Dodaj("prev", max($current - 1, 1));
    $template->Dodaj("next", min($current + 2, ceil($cnt)));
    $template->DodajPetle("news_short", $content);
    $template->DodajPetle("numbers", $nums);

    return $template->Parsuj();
  }

  private function GetLoginForm()
  {
    $err = "";

    if ( isset($_POST['logmenow']) )
      {
	$username = htmlspecialchars($_POST['nick']);
	$password = htmlspecialchars($_POST['pass']);

	$this->_login->LoginProcess($username, md5($password));

	if ( $this->_login->IsLoggedIn() )
	  Header("Location: /index.php");
	else
	  $err = "Niepoprawny login lub hasło.";
      }

    return $this->_login->GetLoginForm($err);
  }

  private function ShowUser()
  {
    if ( !isset($_GET['user_id']) )
      return "ni ma takigo usera";

    $id = intval($_GET['user_id']);

    $this->_mysql->Query('select * from user where id = '.$id);

    if ( $this->_mysql->NumberOfRows() == 0 )
      return "ni ma usera";
    
    $user = new User($this->_mysql->FetchAssoc());

    return $user->ShowUserPage($this->_login->IsLoggedIn());
  }
  
  private function AdminMode()
  {
    $admin = new AdminController($this->_login->GetUser(), $this->_mysql);

    return $admin->Decision();
  }
}

?>