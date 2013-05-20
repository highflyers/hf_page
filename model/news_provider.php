<?php

require_once './model/mysql.php';
require_once './model/news_text.php';

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

  public function GetNews($id)
  {
    $result = $this->_mysql->Query('select * from news where id = '.intval($id));
    if ($this->_mysql->NumberOfRows() == 0)
      {
	return null; // TODO what about exception ?
      }
    return $result->fetch_assoc();
  }

  public function GetShortNewsList($newsPerList, $startNews)
  {
    $newsList = array();

    $result = $this->_mysql->Query('select * from news order by date desc limit '.intval($startNews).', '.intval($newsPerList));
    $numCnt = $this->_mysql->NumberOfRows();
    for ( $i = 0; $i < $numCnt; $i++ )
      {
	$result->data_seek($i);
	$row = $result->fetch_assoc();
	$this->_mysql->Query('select first_name, second_name, id from user where id = '.intval($row['author']));
	
	if ( $this->_mysql->NumberOfRows() == 0 )
	  {
	    echo "ni ma usera";
	    continue;
	  }

	$user = new User($this->_mysql->FetchAssoc());

	array_push($newsList, new NewsText($row['title'], $row['content'], $user, $row['id'], $row['date']));
      }

    return $newsList;
  }
}
?>