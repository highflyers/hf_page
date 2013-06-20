<?php

require_once './modules/wysiwyg_editor.php';

class AdminController
{
  private $_user;
  private $_mysql;

  public function __construct($user, $mysql)
  {
    $this->_user = $user;
    $this->_mysql = $mysql;
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
      $add = $this->GetEditNews();
    else if ( $act == 'add_news' )
      $add = $this->GetAddNews();

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
      $this->GetNewsEditorEdit($template);
    
    return $template->Parsuj();
  }

  public function GetAddNews()
  {
    $template = new Template(CURRENT_TEMPLATE."admin_addnews.htm");
    $template->Laduj();
    $template->DodajPetle('news', array()); 

    $this->GetNewsEditorAdd($template);
    
    return $template->Parsuj();
  }


  private function GetEditNewsList(&$template)
  {
    global $langID;

    $result = $this->_mysql->Query("select id, (select ".$langID." from translable_element where id=news.title) as title, date from news order by date desc");
    
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

  private function GetNewsEditorEdit(&$template)
  {
    global $langID;
    $id = intval($_GET['news_edit_id']);

    if ( isset($_POST['editNewsP']) )
      {
	$this->_mysql->Query("update news set title='".str_replace("'", "\'", $_POST['title'])."', content='".str_replace("'", "\'", $_POST['bbcodeText'])."', baner_url='".str_replace("'", "\'", $_POST['baner'])."' where id=".$id);

	$template->Dodaj("MSG", "Zapisano zmiany!");
      }
    else
      $template->Dodaj("MSG", "");

    $result = $this->_mysql->Query('select (select '.$langID.' from translable_element where id=news.title) as title,baner_url,  (select '.$langID.' from translable_element where id=news.content) as content, date from news where id='.$id);

    if ( $this->_mysql->NumberOfRows() != 1 )
      return;

    $row = $result->fetch_assoc();
    $editor = new WysiwygEditor('/admin/news/edit/'.$id, 'editNewsP', 'Zakończ edycję', $row['content']);
    $this->GetTextEditor($template, $editor, $row['title'], $row['baner_url']);
  }

  private function GetNewsEditorAdd(&$template)
  {
    if ( isset($_POST['addNewsP']) )
      {
	global $langID;
	$this->_mysql->Query("insert into translable_element(id, ".$langID.") values(NULL, '".str_replace("'", "\'", $_POST['title'])."')");
	$idTit = $this->_mysql->LastID();
	$this->_mysql->Query("insert into translable_element(id, ".$langID.") values(NULL, '".str_replace("'", "\'", $_POST['bbcodeText'])."')");
	$idTex = $this->_mysql->LastID();
	$this->_mysql->Query("insert into news values(NULL, ".$idTit.", ".$idTex.", ".intval($this->_user->getid()).", NOW(), '".str_replace("'", "\'", $_POST['banner'])."')");

	$template->Dodaj("MSG", "Zapisano zmiany!");
      }
    else
      $template->Dodaj("MSG", "");

    $editor = new WysiwygEditor('/admin/news/add', 'addNewsP', 'Dodaj newsa');
    $this->GetTextEditor($template, $editor);
  }


  private function GetTextEditor(&$template, WysiwygEditor $editor, $headerDefault = '', $banerDefault='')
  {

    $editor->Additional('Nagłówek: <input type=text name=title value=\''.$headerDefault.'\'><br />
<fieldset><legend>Baner</legend>'.($banerDefault != '' ? '
<img src="'.$banerDefault.'" width=400><br />' : '').'
<input id=cbaner type=checkbox name=changeBaner onclick="document.getElementById(\'hbaner1\').disabled=document.getElementById(\'hbaner2\').disabled=!document.getElementById(\'cbaner\').checked">Zastosuj baner<br />
<input type="radio" checked disabled=true id=hbaner1 name="banerFromWhere" value="local">Z dysku: <input id=nbaner disabled=true type=file name=fbaner ><br />
<input type="radio" id=hbaner2 disabled=true name="banerFromWhere" value="extern">Link z zewnętrznego serwera: <input name=sbaner><br /><span id=ibaner style="font-size:9px">Zmiana banera spowoduje usunięcie z dysku starego banera.</span><br /></fieldset><br />
');
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