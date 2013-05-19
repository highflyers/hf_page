<?php

require_once 'common.php';
require_once 'mysql.php';

class Equipment
{
	private $_fields = array();
	private $_isInMySQL = false;
	
	function __construct( MySQL $mysql, $id )
	{
		$mysql->Query('select * from equipment where equipment_id = '.intval($id));
		
		if ( $mysql->NumberOfRows() != 1 )
			ShowError('Nie mozna utworzyc instancji obiektu Equipment');
		
		$this->_fields = $mysql->FetchAssoc();
		$this->_isInMySQL = true;
	}
	
	function __construct( array $fields )
	{
		$this->SetID( $fields['equipment_id'] );
		$this->SetName( $fields['equipment_name'] );
		$this->SetResponsible( $fields['equipment_responsible'] );
		$this->SetQuantity( $fields['equipment_quantity'] );
	}
	
	function GetID()
	{
		return intval( $this->_fields['equipment_id'] );
	}
	
	function GetResponsible()
	{
		return intval( $this->_fields['equipment_responsible'] );
	}
	
	function GetQuantity()
	{
		return intval( $this->_fields['equipment_quantity'] );
	}
	
	private function SetID( $id )
	{
		$this->_fields['equipment_id'] = intval( $id );
	}
	
	function SetName( $name )
	{
		$this->_fields['equipment_name'] = $mysql->LastID();
	}
	
	
	function SetResponsible( $responsible )
	{
		$this->_fields['equipment_responsible'] = intval( $responsible );
	}
	
	
	function SetQuantity( $quantity )
	{
		$this->_fields['equipment_quantity'] = intval( $quantity );
	}
	
	function UpdateMySQL( MySQL $mysql )
	{
		if ( $this->_isInMySQL )
		{
			$mysql->Query( 'update equipment set 
					equipment_responsible = '.$this->GetResponsible().',
					equipment_name = "'.$this->GetName().'", 
					equipment_quantity = '.$this->GetQuantity().'
					where equipment_id = '.$this->GetID() );
			
			$this->_fields['equipment_id'] = $mysql->LastID();
		}
		else 
		{
			ShowError( 'Nie mo¿na zaktualizowa bazy danych - obiekt nie zostal jeszcze dodany.' ); 
		}
	}
	
	function SaveToMySQL( MySQL $mysql)
	{
		if ( $this->_isInMySQL == false )
		{
			$mysql->Query( 'insert into equipment( 
					equipment_id, 
					equipment_name, 
					equipment_quantity, 
					equipment_responsible 
					values(
					NULL,
					"'.$this->GetName().'",
					'.$this->GetQuantity().',
					'.$this->GetResponsible().')' );
			$this->_isInMySQL = true;
		}
		else
		{
			ShowError( 'Nie mo¿na dodac obiektu do bazy - obiekt ju¿ istnieje w bazie.' );
		}
	}
	
	function RemoveFromBase( MySQL $mysql)
	{
		$mysql->Query( 'select equipment_id from equipment where equipment_id = '.$this->GetID() );
		
		if ( $mysql->NumberOfRows() != 1 )
		{
			ShowError( 'Nie mo¿na usun¹c obiektu z bazy - obiekt nie istnieje w bazie, lub kryterium jest niejednoznaczne.' );
		}
		else 
		{
			$mysql->Query( 'delete from equipment where equipment_id = '.$this->GetID() );
			$this->_isInMySQL = false;
		}
	}
	
}

?>