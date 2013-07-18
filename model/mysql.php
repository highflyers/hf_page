<?php
require_once "common.php";
interface IMySQLOperationsAble {
	public function IsLoadedSQL();
	public function SaveToMySQL(MySQL $mysql);
	public function LoadFromMySQL(MySQL $mysql);
	public function UpdateInMySQL(MySQL $mysql);
}
class MySQL {
	private $_mysqli;
	private $_lastQuery;
	private $_lastResult;
	function __construct() {
		$this->_mysqli = null;
	}
	function __destruct() {
		$this->Disconnect ();
	}
	function GetLangStr($id) {
		global $langID;
		return "(select " . $langID . " from translable_element where id=" . $id . ") as ";
	}
	function GetImprovedLang($id, $forceDefaultLang = false) {
		global $langID;
		
		if ($forceDefaultLang)
			return '(select '.DEFAULT_LANG.' from translable_element where id='.$id.') as '; 
		
		// SOO UGLY!
		return '(case
					(select length('.$langID.') from translable_element where id='.$id.') 
    					when 0 then (select '.DEFAULT_LANG.' from translable_element where id='.$id.') 
    					else (select '.$langID.' from translable_element where id='.$id.')
    			end) as ';
	}
	private function MysqlError($errLvl) {
		if ($this->_mysqli == null)
			ShowError ( "Obiekt mysqli nie zostal utworzony(a powinien!)" );
		else
			ShowError ( "Blad w module mysql: " . $this->_mysqli->error );
		
		return $errLvl;
	}
	function Connect($host, $login, $password, $database) {
		mysql_connect( $host, $login, $password);
		mysql_select_db( $database );
		
		if (false) {
			$this->_mysqli = null;
			$this->MysqlError ( ErrorLevel::CRITICAL_ERROR );
		}
	}
	function Query($query) {
		$this->_lastQuery = $query;
		
		if (($this->_lastResult = mysql_query ( $query )) == false) {
			$this->MysqlError ( ErrorLevel::ERROR );
			return null;
		}
		
		return $this->_lastResult;
	}
	function FetchAssoc($result = null) {
		return mysql_fetch_array($this->_lastResult);
	}
	function LastID() {
		return mysql_insert_id();
	}
	function NumberOfRows() {
		return mysql_num_rows($this->_lastResult);
	}
	function Disconnect() {
		if ($this->_mysqli != null) {
			$this->_mysqli->close ();
			$this->_mysqli = null;
		}
	}
}

?>