<?php

require_once './model/text.php';
require_once './model/mysql.php';

class NewsText extends BBCodedText implements IMySQLOperationsAble 
{
  private $_user;
  private $_id;
  private $_showns;
  private $_isLoadedeSQL = false;
  protected $_author;
  protected $_title;
  protected $_date;	

  function IsLoadedSQL()
  {
    return $this->_isLoadedeSQL;
  }
	
  function SaveToMySQL( MySQL $mysql )
  {
    if ( $this->_isLoadedSQL )
      {
	ShowError( 'Nie mo�na dodac newsa do bazy. News ju� istnieje.' );
      }
    else
      {
	//:TODO zapis do mysql
	$this->_isLoadedSQL = true;
      }
  }
	
  function LoadFromMySQL( MySQL $mysql )
  {
    //:TODO �adowanie z mysql
		
    $this->_isLoadedSQL = true;
  }
	
  function UpdateInMySQL( MySQL $mysql )
  {
    if ( $this->_isLoadedSQL == false )
      {
	ShowError( 'Nie mo�na zaktualizowac rekordu. Rekord nie zostal zaladowany z bazy danych.');
      }
    else
      {
	//:TODO aktualizacja news�w do mysql
      }
  }
	
  function SetTitle( $title )
  {
    $this->_title = $title;
  }
	
  function SetDate( $date = NULL )
  {
    $this->_date = $date == NULL ? time() : $date;
  }
	
  function SetUser( $userId )
  {
    if ( !is_numeric( $userId ) )
      {
	ShowError( 'Nie poprawny user id przy dodawaniu news�w. ' );
      }
    else 
      {
	$this->_user = intval( $userId );
      } 
  }

  public function __construct($title, $text, $author, $id, $date = null)
  {
    $this->_title = $title;
    $this->_author = $author;
    $this->_id = $id;
    $this->_date =  $date;
    parent::SetBBCodeText( $text );
  }

  public function ToArray()
  {
    return array(
		 "title"=>$this->_title,
		 "author"=>$this->_author,
		 "id"=>$this->_id,
		 "date"=>$this->_date);
  }

  public function GetHTMLNews()
  {
    $template = new Template(CURRENT_TEMPLATE."single_news.htm");
    $template->Laduj();

    $text = $this->GetHTMLText();
    $text = str_replace("{moore}", "", $text);

    $template->Dodaj("title", $this->_title);
    $template->Dodaj("date", $this->_date);
    $template->Dodaj("author_display_name", $this->_author->GetDisplayName());
    $template->Dodaj("author_id", $this->_author->getid());
    $template->Dodaj("content", $text);

    return $template->Parsuj();
  }

  public function GetShortHTMLNews()
  {
    $template = new Template(CURRENT_TEMPLATE."short_single_news.htm");
    $template->Laduj();

    $text = $this->GetHTMLText();
    $pos = strpos($text, "{moore}");

    if ( $pos != null )
      $text = substr($text, 0, $pos);
    
    $template->Dodaj("title", $this->_title);
    $template->Dodaj("date", $this->_date);
    $template->Dodaj("author_display_name", $this->_author->GetDisplayName());
    $template->Dodaj("author_id", $this->_author->getid());
    $template->Dodaj("content", $text);
    $template->Dodaj("id", $this->_id);
    $template->DodajWarunek("was_moore", $pos != null);

    return $template->Parsuj();
  }
}

?>