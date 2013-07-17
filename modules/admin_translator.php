<?php
class AdminTranslator {
	private $_mysql;
	public function __construct($mysql) {
		$this->_mysql = $mysql;
	}
	public function ShowTranslator() {
		$template = new Template ( CURRENT_TEMPLATE . "admin_translator.htm" );
		$template->Laduj ();
		
		$langList = $this->GetLangList ();
		$template->DodajPetle ( 'jezyki', $langList );
		
		$elements = $this->GetElements ();
		$template->DodajPetle ( 'elements', $elements );
		
		return $template->Parsuj ();
	}
	private function GetLangList() {
		$langs = array ();
		
		$result = $this->_mysql->Query ( 'show columns from translable_element' );
		while ($row = mysql_fetch_array($result)) {
			$row = mysql_fetch_array ( $result );
			
			array_push ( $langs, $row ['Field'] );
		}
		
		return $langs;
	}
	private function GetElements() {
		global $langID;
		$elements = array ();
		$result = $this->_mysql->Query ( 'select * from translable_element' );
		$rowCount = $this->_mysql->NumberOfRows ();
		$untr = isset ( $_GET ['untranslated'] );
		
		for($i = 0; $i < $rowCount; $i ++) {
			$result->data_seek ( $i );
			$row = mysql_fetch_array ( $result );
			$template = new Template ( CURRENT_TEMPLATE . "admin_trans_element.htm" );
			$template->Laduj ();
			$tmpF = strlen ( $row [$langID] ) > 45;
			$row [$langID] = substr ( $row [$langID], 0, 45 );
			if ($tmpF)
				$row [$langID] .= ' ...';
			$affected = false;
			foreach ( $row as $key => $e )
				if ($key != $langID && $key != 'id') {
					if (strlen ( $e ) > 0)
						$img = 'tak';
					else {
						$img = 'nie';
						$affected = true;
					}
					$row [$key] = "<img src='/images/" . $img . ".png'>";
				}
			if (! $affected && $untr)
				continue;
			$template->DodajPetle ( 'single_element', $row );
			array_push ( $elements, $template->Parsuj () );
		}
		
		return $elements;
	}
}

?>