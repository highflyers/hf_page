<?php
require_once './modules/menu_loader.php';
require_once './modules/wysiwyg_editor.php';
class AdminMenu extends MenuLoader {
	public function __construct($mysql) {
		parent::__construct ( $mysql );
	}
	public function ShowMenuMod() {
		$template = new Template ( CURRENT_TEMPLATE . "admin_menu.htm" );
		$template->Laduj ();
		$template->DodajPetle ( 'menuList', array () );
		$case = 3;
		
		if (isset ( $_GET ['move'] )) {
			$pos = intval ( $_GET ['position'] );
			if ($_GET ['move'] == 'up')
				$this->MoveUp ( $pos );
			else
				$this->MoveDown ( $pos );
		}
		
		$this->LoadStructure ();
		
		if (isset ( $_POST ['newMenuElement'] )) {print_r($_POST);
			$this->_mysql_ob->Query ( 'insert into translable_element(' . DEFAULT_LANG . ') values("' . $_POST ['menuTitle'] . '")' );
			$titleId = $this->_mysql_ob->LastID ();
			$this->_mysql_ob->Query ( 'insert into translable_element(' . DEFAULT_LANG . ') values("")' );
			$result = $this->_mysql_ob->Query ( 'SELECT position from menu ORDER BY position DESC LIMIT 1' );
			$tmpOut = mysql_fetch_array($result);
			$this->_mysql_ob->Query ( 'insert into menu values(NULL, ' . $titleId . ', ' . $_POST ['menuVal'] . ', ' . ($tmpOut['position'] + 1) . ', ' . $this->_mysql_ob->LastID () . ', 1, NOW())' );
			$template->Dodaj ( 'menu_id', $this->_mysql_ob->LastID () );
			$case = 5;
		} else if (isset ( $_GET ['edit'] )) {
			$id = intval ( $_GET ['edit'] );
			$result = $this->_mysql_ob->Query ( 'select title, content from menu where id=' . $id );
			
			if ($this->_mysql_ob->NumberOfRows () != 1)
				$case = 6;
			else {
				$case = 4;
				$row = mysql_fetch_array($result);
				
				if (isset ( $_POST ['editMenuElement'] )) {
					$this->_mysql_ob->Query ( 'update translable_element set ' . DEFAULT_LANG . '="' . addslashes($_POST ['menuElementTitle']) . '" where id=' . $row ['title'] );
					$this->_mysql_ob->Query ( 'update translable_element set ' . DEFAULT_LANG . '="' . addslashes($_POST ['bbcodeText']) . '" where id=' . $row ['content'] );
					$template->DodajWarunek ( "was_edited", 1 );
				}

				$result = $this->_mysql_ob->Query ( 'select ' . $this->_mysql_ob->GetImprovedLang ( "menu.title", true ) . 'langtitle, title, ' . $this->_mysql_ob->GetLangStr ( 'menu.content' ) . 'langcontent, content from menu where id=' . $id );
				$row = mysql_fetch_array($result);
					
				$editor = new WysiwygEditor ( "action=admin&admin_act=menu_mod&edit=" . $id, 'editMenuElement', "Zapisz zmiany", $row ['langcontent'] );
				$editor->Additional ( "Tytuł: <input name='menuElementTitle' value='" . $row ['langtitle'] . "'><br />" );
				$template->Dodaj ( 'editor', $editor->GetEditor () );
			}
		} else if (isset ( $_GET ['rm'] )) {
			$okRem = $this->RemoveElement ( intval ( $_GET ['rm'] ) );
			$case = $okRem ? 2 : 1;
		} else {
			$template->Dodaj ( "menu_content", $this->GetItems () );
			$template->DodajPetle ( 'menuList', $this->GenerateMenuList () );
		}
		
		$template->DodajWarunek ( 'case', $case );
		
		return $template->Parsuj ();
	}
	private function MoveUp($position) {
		$result = $this->_mysql_ob->Query ( 'select id from menu where position=' . $position );
		$row = mysql_fetch_array($result);
		
		$this->_mysql_ob->Query ( 'update menu set position = position + 1 where position = ' . ($position - 1) );
		$this->_mysql_ob->Query ( 'update menu set position = position - 1 where id = ' . $row ['id'] );
	}
	private function MoveDown($position) {
		$result = $this->_mysql_ob->Query ( 'select id from menu where position=' . $position );
		$row = mysql_fetch_array($result);
		
		$this->_mysql_ob->Query ( 'update menu set position = position - 1 where position = ' . ($position + 1) );
		$this->_mysql_ob->Query ( 'update menu set position = position + 1 where id = ' . $row ['id'] );
	}
	private function RegenerateMenuOrder($parent) {
		$result = $this->_mysql_ob->Query ( 'select id from menu where parent=' . $parent );
		$i = 0;
		while ($row = mysql_fetch_array($result)) {
			$this->_mysql_ob->Query ( 'update menu set position=' . $i . ' where id=' . $row ['id'] );
			$i++;
		}
	}
	private function GenerateMenuList() {
		$arr = array ();
		$result = $this->_mysql_ob->Query ( 'select id, ' . $this->_mysql_ob->GetImprovedLang ( "menu.title", true ) . 'title from menu' );
		
		while ($row = mysql_fetch_array($result)) {
			array_push ( $arr, $row );
		}
		return $arr;
	}
	private function RemoveElement($id) {
		$this->_mysql_ob->Query ( 'select id from menu where parent=' . $id );
		
		if ($this->_mysql_ob->NumberOfRows () > 0)
			return false;
		
		$result = $this->_mysql_ob->Query ( 'select title, content, parent from menu where id=' . $id );
		
		if ($this->_mysql_ob->NumberOfRows () == 0)
			return true;
		
		$row = mysql_fetch_array($result);
		$this->_mysql_ob->Query ( 'delete from translable_element where id=' . $row ['title'] );
		$this->_mysql_ob->Query ( 'delete from translable_element where id=' . $row ['content'] );
		$this->_mysql_ob->Query ( 'delete from menu where id=' . $id );
		$this->RegenerateMenuOrder ( $row ['parent'] );
		
		return true;
	}
	private function GetItems() {
		$str = "<ul>";
		
		for($i = 0; $i < count ( $this->_structure ); $i ++) {
			$curLink = "/?hf_lang=" . DEFAULT_LANG . "&action=admin&admin_act=menu_mod&";
			$str .= "<li>";
			$str .= $this->GenerateUpDownButton ( $i, $this->_structure );
			$str .= $this->_structure [$i]->GetTitle () . " - <a href='" . $curLink . "edit=" . $this->_structure [$i]->GetSite ()."'>Edytuj</a> ";
			$str .= " - <a onclick='confirmMe(\"Czy na pewno chcesz to zrobic? Nie ma odwrotu!\", \"" . $curLink . "&rm=" . $this->_structure [$i]->GetSite () ."\")' >Usuń</a>";
			if ($this->_structure [$i]->GetChildren () != null)
				$str .= $this->GetSubItems ( $this->_structure [$i]->GetChildren () );
			$str .= "</li>";
		}
		
		$str .= "</ul>";
		return $str;
	}
	private function GetSubItems($struct) {
		$str = "<ul>";
		
		for($i = 0; $i < count ( $struct ); $i ++) {
			$curLink = "/?hf_lang=" . DEFAULT_LANG . "&action=admin&admin_act=menu_mod&";
			$str .= "<li>";
			$str .= $this->GenerateUpDownButton ( $i, $struct );
			$str .= $struct [$i]->GetTitle () . " - <a  href='" . $curLink . "edit=".$struct [$i]->GetSite ()."'>Edytuj</a>";
			$str .= " - <a onclick='confirmMe(\"Czy na pewno chcesz to zrobic? Nie ma odwrotu!\", \"" . $curLink . "rm=".$struct [$i]->GetSite ()."\")' >Usuń</a>";
			if ($struct [$i]->GetChildren () != null)
				$str .= $this->MediumLevelMenu ( $struct [$i]->GetChildren () );
			$str .= "</li>";
		}
		
		$str .= "</ul>";
		return $str;
	}
	private function GenerateUpDownButton($i, $struct) {
		$str = '';
		$i ++;
		if ($i != 1)
			$str .= " <a href='/?hf_page" . DEFAULT_LANG . "&action=admin&admin_act=menu_mod&move=up&position=" . $i . "'>";
		$str .= "<img src='/images/up.png'>";
		if ($i != 1)
			$str .= "</a> ";
		if ($i < count ( $struct ))
			$str .= " <a href='/?hf_page" . DEFAULT_LANG . "&action=admin&admin_act=menu_mod&move=down&position=" . $i . "'>";
		$str .= "<img src='/images/down.png'>";
		if ($i < count ( $struct ))
			$str .= "</a> ";
		
		return $str;
	}
}

?>