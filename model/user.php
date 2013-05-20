<?php

class User
{
  private $id;
  private $nick;
  private $level;
  private $date;
  private $first_name;
  private $second_name;
 
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
  
  public function ToArray()
  {
    return array(
		 "id" => $this->id,
		 "nick" => $this->nick,
		 "level" => $this->level,
		 "date" => $this->date,
		 "firstName" => $this->first_name,
		 "secondName" => $this->second_name);
  }

  public function GetDisplayName()
  {
    return $this->getfirst_name().' '.$this->getsecond_name();
  }

}

?>