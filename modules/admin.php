<?php

class AdminController
{
  private $_level;
  private $_mysql;

  public function __construct($level, $mysql)
  {
    $this->_level = $level;
    $this->_mysql = $mysql;
  }

  private function IsLoggedIn()
  {
    return $this->_level >= 0;
  }

  public function Decision()
  {
    if ( !$this->IsLoggedIn() )
      return "Nie masz tu wstępu!";

    $act = ( isset($_GET['admin_act'] ) ) ? $_GET['admin_act'] : "none";

    $add = "";

    if ( $act == 'edit_news' )
      $add = $this->GetEditNews();

    return $this->GetMasterPage().$add;
  }

  public function GetEditNews()
  {
    $template = new Template(CURRENT_TEMPLATE."admin_editnews.htm");
    $template->Laduj();

    $result = $this->_mysql->Query("select id, title, date from news order by date desc");

    $rowCount = $this->_mysql->NumberOfRows();
    $newsArr = array();

    for ( $i = 0; $i < $rowCount; $i++ )
      {
    $result->data_seek($i);
    $row = $result->fetch_assoc();
    array_push($newsArr, $row);
  }
    
    $template->DodajPetle("news", $newsArr);

    return $template->Parsuj();
  }

  public function GetMasterPage()
  {

    $template = new Template(CURRENT_TEMPLATE."admin_main.htm");
    $template->Laduj();

    return $template->Parsuj();
   
  }
}
?>