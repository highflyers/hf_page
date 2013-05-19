<?php

class User
{
  private $_id;
  private $_nick;
  private $_level;
  private $_date;
  private $_firstName;
  private $_secondName;
	
  public function __construct($associated_array = null)
  {
    if ( $associated_array == null )
      return;
		
    foreach ( $associated_array as $key=>$val )
      {
	$this->$key = $val;
      }
  }
	
  public function __call($name, $args)
  {
    $tmpArray = get_class_vars(__CLASS__);
    $endName = substr($name, 3);
    	
    foreach ( $tmpArray as $key=>$val)
      {
	if ( $endName == $key )
	  {
	    if (substr($name, 0, 3) == "get")
	      {
		return $this->$endName;
	      }	
	    else 
	      {
		$this->$endName = $args[0];
	      }
	  }
      }
  }
  
  public function ToAssociatedArray()
  {
    return array(
		 "id" => $this->_id,
		 "nick" => $this->_nick,
		 "level" => $this->_level,
		 "date" => $this->_date,
		 "firstName" => $this->_firstName,
		 "secondName" => $this->_secondName);
  }
}

?>