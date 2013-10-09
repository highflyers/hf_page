<?php
require_once './model/mysql.php';
require_once './model/news_text.php';
class NewsProvider {
	private $_mysql;
	public function __construct(MySQL $mysql) {
		$this->_mysql = $mysql;
	}
	public function GetNewsCount() {
		$result = $this->_mysql->Query ( 'select count(*) as cnt from news' );
		$tmp = $this->_mysql->FetchAssoc();
		return $tmp['cnt'];
	}
	public function GetLastHeaders($count) {
		global $langID;
		$result = $this->_mysql->Query ( 'select id, ' . $this->_mysql->GetImprovedLang ( "news.title" ) . 'title from news order by date desc limit 0, ' . intval ( $count ) );
		$arr = array ();
		
		while ($row = mysql_fetch_array($result)) {
			array_push ( $arr, array (
					'id' => $row ['id'],
					'title' => $row ['title'] 
			) );
		}
		
		return $arr;
	}
	public function GetLastNewsList($count) {
		$result = $this->_mysql->Query ( 'select id, author, date, baner_url, ' . $this->_mysql->GetImprovedLang ( "news.title" ) . 'title, ' . $this->_mysql->GetImprovedLang ( "news.content" ) . 'content from news order by date desc limit 0, ' . intval ( $count ) );
		$newsList = array ();

		while ($row = mysql_fetch_array($result)) {
			$this->_mysql->Query ( 'select first_name, second_name, id from user where id = ' . intval ( $row ['author'] ) );
			
			if ($this->_mysql->NumberOfRows () == 0) {
				echo "ni ma usera";
				continue;
			}
			
			$user = new User ( $this->_mysql->FetchAssoc () );
			
			array_push ( $newsList, new NewsText ( $row ['title'], $row ['content'], $user, $row ['id'], $row ['date'], $row ['baner_url'] ) );
		}
		
		return $newsList;
	}
	public function GetNews($id) {
		$result = $this->_mysql->Query ( 'select id, author, date, baner_url, ' . $this->_mysql->GetImprovedLang ( "news.title" ) . 'title, ' . $this->_mysql->GetImprovedLang ( "news.content" ) . 'content from news where id = ' . intval ( $id ) );
		if ($this->_mysql->NumberOfRows () == 0) {
			return null; // TODO what about exception ?
		}
		return mysql_fetch_array ( $result );
	}
	public function GetShortNewsList($newsPerList, $startNews) {
		$newsList = array ();
		
		$result = $this->_mysql->Query ( 'select id, author, date, baner_url, ' . $this->_mysql->GetImprovedLang ( "news.title" ) . 'title, ' . $this->_mysql->GetImprovedLang ( "news.content" ) . 'content from news order by date desc limit ' . intval ( $startNews ) . ', ' . intval ( $newsPerList ) );
		$numCnt = $this->_mysql->NumberOfRows ();
		while ($row = mysql_fetch_array($result)) {
			$this->_mysql->Query ( 'select first_name, second_name, id from user where id = ' . intval ( $row ['author'] ) );
			
			if ($this->_mysql->NumberOfRows () == 0) {
				echo "ni ma usera";
				continue;
			}
			
			$user = new User ( $this->_mysql->FetchAssoc () );
			
			array_push ( $newsList, new NewsText ( $row ['title'], $row ['content'], $user, $row ['id'], $row ['date'] ) );
		}
		
		return $newsList;
	}
}
?>