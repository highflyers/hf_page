<?php

require_once './parser_tpl.php';

class WysiwygEditor
{
  private $_actionUrl;
  private $_submitName;
  private $_submitValue;
  private $_initText;
  private $_template;
  private $_additional;

  public function __construct($actionUrl, $submitName, $submitValue, $initText = "")
  {
    $this->_actionUrl = $actionUrl;
    $this->_submitName = $submitName;
    $this->_submitValue = $submitValue;
    $this->_initText = $initText;
    $this->_template = new Template(CURRENT_TEMPLATE.'bbcode_editor.htm');
    $this->_template->Laduj();
    $this->_additional = "";

  }

  public function Additional($add)
  {
    $this->_additional .= $add;
  }

  public function GetEditor()
  {
    $this->_template->Dodaj('action_url', $this->_actionUrl);
    $this->_template->Dodaj('init_text', $this->_initText);
    $this->_template->Dodaj('submit_value', $this->_submitValue);
    $this->_template->Dodaj('submit_name', $this->_submitName);
    $this->_template->Dodaj('additional', $this->_additional);

    return $this->_template->Parsuj();
  }
}

?>