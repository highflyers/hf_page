<?php
require_once './modules/wysiwyg_editor.php';
class AdminNews {
	private $_mysql;
	private $_user;
	public function __construct($mysql, $user) {
		$this->_mysql = $mysql;
		$this->_user = $user;
	}
	private function GetNewsEditorAdd(&$template) {
		if (isset ( $_POST ['addNewsP'] )) {
			$this->_mysql->Query ( "insert into translable_element(id, " . DEFAULT_LANG . ") values(NULL, '" . str_replace ( "'", "\'", $_POST ['title'] ) . "')" );
			$idTit = $this->_mysql->LastID ();
			$this->_mysql->Query ( "insert into translable_element(id, " . DEFAULT_LANG . ") values(NULL, '" . str_replace ( "'", "\'", $_POST ['bbcodeText'] ) . "')" );
			$idTex = $this->_mysql->LastID ();
			$this->_mysql->Query ( "insert into news values(NULL, " . $idTit . ", " . $idTex . ", " . intval ( $this->_user->getid () ) . ", NOW(), '" . str_replace ( "'", "\'", $_POST ['banner'] ) . "')" );
			
			$template->Dodaj ( "MSG", "Zapisano zmiany!" );
		} else
			$template->Dodaj ( "MSG", "" );
		
		$editor = new WysiwygEditor ( 'action=admin&admin_act=add_news', 'addNewsP', 'Dodaj newsa' );
		$this->GetTextEditor ( $template, $editor );
	}
	private function GetTextEditor(&$template, WysiwygEditor $editor, $headerDefault = '', $banerDefault = '') {
		$editor->Additional ( 'Nagłówek: <input type=text name=title value=\'' . $headerDefault . '\'><br />
<fieldset><legend>Baner</legend>' . ($banerDefault != '' ? '
<img src="' . $banerDefault . '" width=400><br />' : '') . '
<input id=cbaner type=checkbox name=changeBaner onclick="document.getElementById(\'hbaner1\').disabled=document.getElementById(\'hbaner2\').disabled=!document.getElementById(\'cbaner\').checked">Zastosuj baner<br />
<input type="radio" checked disabled=true id=hbaner1 name="banerFromWhere" value="local">Z dysku: <input id=nbaner disabled=true type=file name=fbaner ><br />
<input type="radio" id=hbaner2 disabled=true name="banerFromWhere" value="extern">Link z zewnętrznego serwera: <input name=sbaner><br /><span id=ibaner style="font-size:9px">Zmiana banera spowoduje usunięcie z dysku starego banera.</span><br /></fieldset><br />
' );
		$template->DodajWarunek ( 'listmode', 0 );
		$template->Dodaj ( 'bbcode_editor', $editor->GetEditor () );
	}
	public function GetEditNews() {
		$template = new Template ( CURRENT_TEMPLATE . "admin_editnews.htm" );
		$template->Laduj ();
		$template->DodajPetle ( 'news', array () ); // because of warnings
		if (! isset ( $_GET ['news_edit_id'] ))
			$this->GetEditNewsList ( $template );
		else
			$this->GetNewsEditorEdit ( $template );
		
		return $template->Parsuj ();
	}
	public function GetAddNews() {
		$template = new Template ( CURRENT_TEMPLATE . "admin_addnews.htm" );
		$template->Laduj ();
		$template->DodajPetle ( 'news', array () );
		
		$this->GetNewsEditorAdd ( $template );
		
		return $template->Parsuj ();
	}
	private function GetEditNewsList(&$template) {
		
		$result = $this->_mysql->Query ( "select id, (select " . DEFAULT_LANG . " from translable_element where id=news.title) as title, date from news order by date desc" );
		
		$newsArr = array ();
		
		while ($row = mysql_fetch_array($result)) {
			array_push ( $newsArr, $row );
		}
		
		$template->DodajPetle ( "news", $newsArr );
		$template->DodajWarunek ( "listmode", 1 );
	}
	private function GetNewsEditorEdit(&$template) {
		
		$id = intval ( $_GET ['news_edit_id'] );
		
		if (isset ( $_POST ['editNewsP'] )) {
			$result = $this->_mysql->Query ( "select title, content from news where id = " . $id );
			$row = mysql_fetch_array($result);
			
			$baner = isset($_POST['baner']) ? $_POST['baner'] : '';
			$this->_mysql->Query ( "update news set baner_url='" . str_replace ( "'", "\'", $baner ) . "' where id=" . $id );
			$this->_mysql->Query ( "update translable_element set " . DEFAULT_LANG . "='" . str_replace ( "'", "\'", $_POST ['bbcodeText'] ) . "' where id=" . $row ['content'] );
			
			$this->_mysql->Query ( "update translable_element set " . DEFAULT_LANG . "='" . str_replace ( "'", "\'", $_POST ['title'] ) . "' where id=" . $row ['title'] );
			
			$template->Dodaj ( "MSG", "Zapisano zmiany!" );
		} else
			$template->Dodaj ( "MSG", "" );
		
		$result = $this->_mysql->Query ( 'select (select ' . DEFAULT_LANG . ' from translable_element where id=news.title) as title,baner_url,  (select ' . DEFAULT_LANG . ' from translable_element where id=news.content) as content, date from news where id=' . $id );
		
		if ($this->_mysql->NumberOfRows () != 1)
			return;
		
		$row = mysql_fetch_array($result);
		$editor = new WysiwygEditor ( 'action=admin&admin_act=edit_news&news_edit_id=' . $id, 'editNewsP', 'Zakończ edycję', $row ['content'] );
		$this->GetTextEditor ( $template, $editor, $row ['title'], $row ['baner_url'] );
	}
}
?>