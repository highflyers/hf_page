<?php 

require_once "./model/article.php";
require_once "./model/user.php";
require_once "./model/mysql.php";

class Controller
{
  private $_action;
  private $_mysql;

  public function __construct(MySQL $mysql)
  {
    $this->_action = isset($_GET['action']) ? $_GET['action'] : null;
    $this->_mysql = $mysql;
  }
  
  public function Decision()
  {
    if ( $this->_action == 'site' )
      return $this->GetSite();
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
}

?>