<?php

require_once './model/mysql.php';

class NewsProvider
{
  private $_mysql;

  public function __construct(MySQL $mysql)
  {
    $this->_mysql = $mysql;
  }

  public function GetLastHeaders($count)
  {
    $result = $this->_mysql->Query('select id, title from news order by date desc limit 0, '.intval($count));
    $arr = array();

    for ( $i = 0; $i < $this->_mysql->NumberOfRows(); $i++ )
      {
	$result->data_seek($i);
	$row = $result->fetch_assoc();
	array_push($arr, array('id'=>$row['id'], 'title'=>$row['title']));
      }

    return $arr;
  }
}
?>