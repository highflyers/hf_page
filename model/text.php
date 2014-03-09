<?php

require_once "./model/bbcode.php";

class Text
{
  private $_text;
  protected $_author;
  protected $_date;
	
  public function SetText( $text )
  {
    $this->_text = $text;
  }
	
  public function GetText()
  {
    return $this->_text;
  }
}

class BBCodedText extends Text
{
  function GetHTMLText() 
  {
    return BBCodeConverter::BBCodeToHTML( parent::GetText() );
  }
	
  function GetText()
  {
    return parent::GetText();
  }
	
  function SetHTMLText( $text )
  {
    parent::SetText( BBCodeConverter::HTMLToBBCode($text) );
  }

  function SetBBCodeText( $text )
  {
    parent::SetText( BBCodeConverter::BBCodeToHTML($text) );
  }
	
  function SetText( $text )
  {
    parent::SetText( $text );
  }
}

?>