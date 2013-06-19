<?php

class Stack
{
  private $_tab = array();
    
  public function Push($wartosc)
  {
    array_push($this->_tab, $wartosc);
  }
    
  public function Pop()
  {
    return array_pop($this->_tab);
  }
    
  public function Put()
  {
    print_r($this->_tab);
  }
}

class Template
{
  private $_stos = array();
  private $_plik;
  private $_tresc;
  private $_zmiany = array();
  private $_warunki = array();
    
  private function _NaStos($filtr_start, $filtr_koniec)
  {
    $start = 0;
    $tekst = substr($this->_tresc, $start);
    $stos_tmp = new Stack();
        
    for($i = $start; $i<strlen($tekst); $i++)
      {
	if(substr($tekst, $i, strlen($filtr_start)) == $filtr_start)
	  $stos_tmp->Push($i);
           
	else if(substr($tekst, $i, strlen($filtr_koniec)) == $filtr_koniec)
	  {
	    $poczatek = $stos_tmp->Pop();
	    array_push($this->_stos, substr($tekst, $poczatek, $i+strlen($filtr_koniec)-$poczatek));
	  }                  
      }
  }
    
  private function _Warunki()
  {
    $this->_NaStos('{IF', '{/IF}');
        
    for($i=count($this->_stos)-1; $i>=0; $i--)
      {
	$tmp = $this->_stos[$i];
	$gdzie_rowna = strpos($tmp, '=');
	$nazwa = substr($tmp, 4, $gdzie_rowna-4);
	$wartosc = substr($tmp, $gdzie_rowna+1, strpos($tmp, '}')-$gdzie_rowna-1);
        
	if( isset($this->_warunki[$nazwa]) && $this->_warunki[$nazwa] == $wartosc)
	  $this->_tresc = str_replace('{IF '.$nazwa.'='.$wartosc.'}', '', $this->_tresc);
            
	else
	  $this->_tresc = str_replace($tmp, '', $this->_tresc);
      }
    $this->_tresc = str_replace('{/IF}', '', $this->_tresc);
  }
    
  private function _Petle()
  {
    $this->_NaStos('{LOOP=', '{/LOOP}');
        
    for($i=count($this->_stos)-1; $i>=0; $i--)
      {
	$tekst = '';
	$tmp = $this->_stos[$i];
	$gdzie_rowna = strpos($tmp, '=');
	$id = substr($tmp, 6, strpos($tmp, '}')-6);
            
	$tekst_w_petli = substr($tmp, strlen('{LOOP='.$id.'}'), strlen($tmp)-strlen('{LOOP='.$id.'}')-7);
                
	foreach($this->_petle[$id] as $klucz => $wartosc)
	  {
	    $s_tmp = new Template($tekst_w_petli);
	    $s_tmp->Laduj();
	    $s_tmp->Dodaj($id, $wartosc);
	    $tekst .= $s_tmp->Parsuj();
	  }
	$this->_tresc = str_replace($tmp, $tekst, $this->_tresc);
                
      }
        
        
        
        
  }
    
  public function __construct($plik)
  {
    $this->_plik = $plik;
  }
    
  public function Laduj()
  {
    if(file_exists($this->_plik))
      $this->_tresc = @file_get_contents($this->_plik);
    else
      $this->_tresc = $this->_plik;
    
    global $lang;

    if ( isset($lang) )
      {
	$this->Dodaj("lang", $lang);
      }
  }
    
  public function Dodaj($nazwa, $zmienna)
  {
    if(is_array($zmienna))
      foreach($zmienna as $klucz => $wartosc)
	$this->Dodaj($nazwa.'['.$klucz.']', $zmienna[$klucz]);
        
    else
      $this->_zmiany[$nazwa] = $zmienna;
  }
    
  public function DodajPetle($id, $zmienna)
  {
    $this->_petle[$id] = $zmienna;
  }
    
  public function DodajWarunek($nazwa, $zmienna)
  {
    $this->_warunki[$nazwa] = $zmienna;
  }
    
  public function Parsuj()
  {
    $this->_Petle();
    $this->_Warunki();
        
    $to_return = $this->_tresc;

    foreach($this->_zmiany as $klucz => $wartosc)
      $to_return = str_replace('{'.$klucz.'}', $wartosc, $to_return);

    return $to_return;
  }
    
  public function pritn()
  {
    $this->_tresc;
  }
}
?>
