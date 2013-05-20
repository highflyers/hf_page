<?php 

require_once "./model/article.php";
require_once "./model/user.php";
require_once "./model/mysql.php";
require_once "./model/news_text.php";

class Controller
{
  private $_action;
  private $_mysql;

  public function __construct(MySQL $mysql)
  {
    $this->_action = isset($_GET['action']) ? $_GET['action'] : "index";
    $this->_mysql = $mysql;
  }
  
  public function Decision()
  {
    if ( $this->_action == 'site' )
      return $this->GetSite();
    if ( $this->_action == 'news' )
      return $this->GetNews();
    if ( $this->_action == 'index' )
      return $this->GetShortNewsList();
  }
  
  private function GetSite()
  {
    $id = intval($_GET['id']);
    
    $this->_mysql->Query('select * from menu where id='.$id);

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
    
    $news = new NewsText($row['title'], $row['content'], $user, $row['date']);
    return $news->GetHTMLNews();
  }

  private function GetShortNewsList()
  {
    $template = new Template(CURRENT_TEMPLATE."news_short_index.htm");
    $template->Laduj();
    $provider = new NewsProvider($this->_mysql);
    $tmp = $provider->GetShortNewsList(5);
    
    $content = array();

    foreach ( $tmp as $val )
      array_push($content, $val->GetShortHTMLNews());
    
    $nums = array();
    $cnt = count($content) / 5;
    for ( $i = 0; $i < $cnt; $i++ )
      {
	array_push($nums, $i + 1);
      }

    $template->DodajPetle("news_short", $content);
    $template->DodajPetle("numbers", $nums);

    return $template->Parsuj();
  }
}

?>