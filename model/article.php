<?php

require_once './model/text.php';

class Article extends BBCodedText
{
  public function __construct($text, $author, $date = null)
  {
    $this->_author = $author;
    $this->_date = ( $date == null ) ? date("d-m-Y H:i:s") : $date;
    parent::SetBBCodeText( $text );
  }
}

?>