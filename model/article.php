<?php

require_once './parser_tpl.php';
require_once './model/text.php';
require_once './model/user.php';

class Article extends BBCodedText
{
  protected $_author;
  protected $_title;
  protected $_date;

  public function __construct($title, $text, $author, $date = null)
  {
    $this->_title = $title;
    $this->_author = $author;
    $this->_date = ( $date == null ) ? date("d-m-Y H:i:s") : $date;
    parent::SetBBCodeText( $text );
  }

  public function GetHTMLArticle()
  {
    $template = new Template(CURRENT_TEMPLATE."article.htm");
    $template->Laduj();

    $template->Dodaj("title", $this->_title);
    $template->Dodaj("date", $this->_date);
    $template->Dodaj("author_display_name", $this->_author->GetDisplayName());
    $template->Dodaj("content", $this->GetHTMLText());

    return $template->Parsuj();
  }

}

?>