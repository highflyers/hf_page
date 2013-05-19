<?php

require_once 'Text.php';
require_once 'mysql.php';

class NewsText extends BBCodedText implements IMySQLOperationsAble 
{
	private $_user;
	private $_id;
	private $_date;
	private $_showns;
	private $_title;
	private $_isLoadedeSQL = false;
	
	function IsLoadedSQL()
	{
		return $this->_isLoadedeSQL;
	}
	
	function SaveToMySQL( MySQL $mysql )
	{
		if ( $this->_isLoadedSQL )
		{
			ShowError( 'Nie mo¿na dodac newsa do bazy. News ju¿ istnieje.' );
		}
		else
		{
			//:TODO zapis do mysql
			$this->_isLoadedSQL = true;
		}
	}
	
	function LoadFromMySQL( MySQL $mysql )
	{
		//:TODO ³adowanie z mysql
		
		$this->_isLoadedSQL = true;
	}
	
	function UpdateInMySQL( MySQL $mysql )
	{
		if ( $this->_isLoadedSQL == false )
		{
			ShowError( 'Nie mo¿na zaktualizowac rekordu. Rekord nie zostal zaladowany z bazy danych.');
		}
		else
		{
			//:TODO aktualizacja newsów do mysql
		}
	}
	
	function SetTitle( $title )
	{
		$this->_title = $title;
	}
	
	function SetDate( $date = NULL )
	{
		$this->_date = $date == NULL ? time() : $date;
	}
	
	function SetUser( $userId )
	{
		if ( !is_numeric( $userId ) )
		{
			ShowError( 'Nie poprawny user id przy dodawaniu newsów. ' );
		}
		else 
		{
			$this->_user = intval( $userId );
		} 
	}
}

?>