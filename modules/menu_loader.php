<?php 

require_once "./model/mysql.php";

class MenuItem
{
  private $_title;
  private $_children;
  private $_site;

  public function __construct($title, $children, $site)
  {
    $this->_title = $title;
    $this->_children = $children;
    $this->_site = $site;
  }

  public function GetTitle() { return $this->_title; }
  public function GetChildren() { return $this->_children; }
  public function GetSite() { return $this->_site; }
}

class MenuLoader
{
  protected $_mysql_ob;
  protected $_structure;

  public function __construct(MySQL $mysql_ob)
  {
    $this->_mysql_ob = $mysql_ob;

    $this->LoadStructure();
  }

  private function LoadLayer($parent)
  {
    $arr = array();
    global $langID;
    $result = $this->_mysql_ob->Query("select id, (select ".$langID." from translable_element where id=menu.title) as title, parent from menu where parent = ".intval($parent)." order by position");

    if ( $result == null )
      throw new Exception("Nie mozna zaladowac menu");

    $rowCount = $this->_mysql_ob->NumberOfRows();
        
    if ( $rowCount == 0 )
      return null;

    for ( $i = 0; $i < $rowCount; $i++ )
      {
	$result->data_seek($i);
	$row = $result->fetch_assoc();
	
	$tmp = $this->LoadLayer($row['id']);
	
	array_push($arr, new MenuItem($row['title'], $tmp, $row['id']));
      }

    return $arr;
  }

  public function LoadStructure()
  {
    global $langID;
    $this->_structure = array();

    $result = $this->_mysql_ob->Query("select id, (select ".$langID." from translable_element where id=menu.title) as title, parent from menu where parent = -1 order by position");

    if ( $result == null )
      throw new Exception("Nie mozna zaladowac menu");

    $rowCount = $this->_mysql_ob->NumberOfRows();
    for ( $i = 0; $i < $rowCount; $i++ )
      {
	$result->data_seek($i);
	$row = $result->fetch_assoc();
	$arr = $this->LoadLayer($row['id']);
	array_push($this->_structure, new MenuItem($row['title'], $arr, $row['id']));
      }
  }

  public function MediumLevelMenu($struct)
  {
    $str = "<ul>";
    global $langID;

    for ( $i = 0; $i < count($struct); $i++ )
      {
	$str .= "<li><a  href='/".$langID."/page/".$struct[$i]->GetSite()."'>".$struct[$i]->GetTitle()."</a>";
	if ( $struct[$i]->GetChildren() != null )
	  $str .= $this->MediumLevelMenu($struct[$i]->GetChildren());
	$str .= "</li>";

      }

    $str .= "</ul>";
    return $str;
  }

  public function GetMenu()
  {
    $str = "<ul>";
    global $langID;

    for ( $i = 0; $i < count($this->_structure); $i++ )
      {
	$str .= "<li><a class=menuItemTopLevel href='/".$langID."/page/".$this->_structure[$i]->GetSite()."'>".$this->_structure[$i]->GetTitle()."</a>";
	if ( $this->_structure[$i]->GetChildren() != null )
	  $str .= $this->MediumLevelMenu($this->_structure[$i]->GetChildren());
	$str .= "</li>";

      }

    $str .= "</ul>";
    return $str;
  }
}

?>