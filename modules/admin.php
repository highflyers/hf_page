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
    $template->DodajPetle('news', array());  // because of warnings
    if ( !isset($_GET['news_edit_id']) )
      $this->GetEditNewsList($template);
    else
      $this->GetNewsEditor($template);
    
    return $template->Parsuj();
  }

  private function GetEditNewsList(&$template)
  {
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
    $template->DodajWarunek("listmode", 1);
  }

  private function GetNewsEditor(&$template)
  {
    $id = intval($_GET['news_edit_id']);

    if ( isset($_POST['editNewsP']) )
      {
	$this->_mysql->Query("update news set content='".str_replace("'", "\'", $_POST['bbcodeText'])."' where id=".$id);

	$template->Dodaj("MSG", "Zapisano zmiany!");
      }
    else
      $template->Dodaj("MSG", "");

    $result = $this->_mysql->Query('select title, content, date from news where id='.$id);

    if ( $this->_mysql->NumberOfRows() != 1 )
      return;

    require_once './modules/wysiwyg_editor.php';
    $row = $result->fetch_assoc();
    $editor = new WysiwygEditor('/admin/news/edit/'.$id, 'editNewsP', 'Zakończ edycję', $row['content']);
    $editor->Additional('Nagłówek: <input type=text name=title value=\''.$row['title'].'\'><br />');
    $template->DodajWarunek('listmode', 0);
    $template->Dodaj('bbcode_editor', $editor->GetEditor());
  }

  public function GetMasterPage()
  {

    $template = new Template(CURRENT_TEMPLATE."admin_main.htm");
    $template->Laduj();

    return $template->Parsuj();
   
  }
}
?>