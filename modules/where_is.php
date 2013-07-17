<?php

require_once './model/mysql.php';
require_once './parser_tpl.php';

class QuickLink
{
  private $_id;
  private $_title;

  public function __construct($id, $title)
  {
    $this->_id = $id;
    $this->_title = $title;
  }

  public function GetId()
  {
    return $this->_id;
  }

  public function GetTitle()
  {
    return $this->_title;
  }
}

class WhereIs
{
  private $_mysql;
  private $_article_id;
  private $_linksArray;
  private $_kind;

  public function __construct(MySQL $mysql, $art_id, $kind)
  {
    $this->_mysql = $mysql;
    $this->_article_id = $art_id;
    $this->_kind = $kind;
    $this->_linksArray = array();

    if ( $art_id != -1 )
      $this->FillArray($art_id);
  }

  private function FillArray($id)
  {
    $result = $this->_mysql->Query('select id, '.$this->_mysql->GetImprovedLang("menu.title").'title, parent from menu where id = '.intval($id).' limit 0, 1');

    if ( $this->_mysql->NumberOfRows() == 0 )
      return ""; // TODO !!

    $row = $result->fetch_assoc();

    if ( $row['parent'] != -1 )
      $this->FillArray($row['parent']);

    array_push($this->_linksArray, new QuickLink($row['id'], $row['title']));
  }

  public function GetWhereIsPanel()
  {
    $template = new Template(CURRENT_TEMPLATE."where_is.htm");
    $template->Laduj();

    $template->Dodaj("content", $this->_kind == 0 ? $this->GetWhereIsArticle() : $this->GetWhereIsNews());

    return $template->Parsuj();
  }

  private function GetWhereIsArticle()
  {
    $template = new Template(CURRENT_TEMPLATE."where_is_article.htm");
    $template->Laduj();
    $cnt = count($this->_linksArray);
    
    $med_exists =  $cnt - 2 >= 0;
    $first_exists = $cnt - 3 >= 0;
    $template->DodajWarunek("medium_exists", $med_exists);
    $template->DodajWarunek("first_exists", $first_exists );
    $template->DodajWarunek("article", $this->_article_id != -1);

    if ( $this->_article_id != -1 )
      $template->Dodaj("last_level_title", $this->_linksArray[$cnt - 1]->GetTitle());

    if ( $first_exists )
      {
	$template->Dodaj("first_t", $this->_linksArray[0]->GetTitle());
	$template->Dodaj("first_id", $this->_linksArray[0]->GetId());
      }
    if ( $med_exists )
      {
	$template->Dodaj("med_t", $this->_linksArray[$cnt - 2]->GetTitle());
	$template->Dodaj("med_id", $this->_linksArray[$cnt - 2]->GetId());
      }
    return $template->Parsuj();
  }

  private function GetWhereIsNews()
  {
    $template = new Template(CURRENT_TEMPLATE."where_is_news.htm");
    $template->Laduj();
    $result = $this->_mysql->Query('select '.$this->_mysql->GetImprovedLang("news.title").'title from news where id = '.intval($this->_article_id).' limit 0, 1');
    $row = $result->fetch_assoc();
    $template->Dodaj('news_t', $row['title']);


    return $template->Parsuj();
    
  }
}

?>