<?php 
class BBCodeConverter
{
	
  public static function BBCodeToHTML( $text )
  {
    //$text = nl2br($text);
	
    $text = preg_replace("#\[code\](.*?)\[/code\]#si", '<div class=kod>\\1</div>', $text);
    $text = preg_replace("#\[b\](.*?)\[/b\]#si", '<b>\\1</b>', $text);
    $text = preg_replace("#\[center\](.*?)\[/center\]#si", '<center>\\1</center>', $text);
    $text = preg_replace("#\[i\](.*?)\[/i\]#si", '<i>\\1</i>', $text);
    $text = preg_replace("#\[u\](.*?)\[/u\]#si", '<u>\\1</u>', $text);
    $text = preg_replace("#\[img\](.*?)\[/img\]#si", '<img src="\\1">', $text);
    $text = preg_replace("#\[img=(.*?)\]#si", '<img src="\\1">', $text);
    $text = preg_replace("#\[url\](.*?)\[/url\]#si", '<a href="\\1" target=_blank>\\1</a>', $text);
    $text = preg_replace("#\[quote\](.*?)\[/quote\]#si", '<div class=quote>\\1</div>', $text);
    $text = preg_replace("#\[url=(.*?)?(.*?)\](.*?)\[/url\]#si", '<a href="\\2" target=_blank>\\3</a>', $text);
    $text = preg_replace("#\[size=(.*?)?(.*?)\](.*?)\[/size\]#si", '<span style="font-size:\\2px">\\3</span>', $text);
    $text = preg_replace("#\[color=(.*?)?(.*?)\](.*?)\[/color\]#si", '<span style="color:\\2">\\3</span>', $text);
    $text = preg_replace("#\[list\](.*?)\[/list\]#si", '<ul>\\1</ul>', $text);
    $text=str_replace("[hr]", "<hr>", $text);
    $text=str_replace("[*]", "<li>", $text);
    //$text=str_replace(" ", "&nbsp;", $text);
    return $text;
  }
	
  public static function HTMLToBBCode( $text )
  {
    $text=stripslashes($text);
    $text=str_replace(" &nbsp; ", " ", $text);
    $text=str_replace("&nbsp;", " ", $text);
    $text=str_replace("<br />", "", $text);
    $text = preg_replace("#<div class=kod>(.*?)</div>#si", "[code]\\1[/code]", $text);
    $text = preg_replace("#<b>(.*?)</b>#si", "[b]\\1[/b]", $text);
    $text = preg_replace("#<center>(.*?)</center>#si", "[center]\\1[/center]", $text);
    $text = preg_replace("#<i>(.*?)</i>#si", "[i]\\1[/i]", $text);
    $text = preg_replace("#<u>(.*?)</u>#si", "[u]\\1[/u]", $text);
    $text = preg_replace("#<img src=\"(.*?)\">#si", "[img]\\1[/img]", $text);
    $text = preg_replace("#<a href=\"(.*?)?(.*?)\" target=_blank>(.*?)</a>#si", "[url=\\2]\\3[/url]", $text);
    $text = preg_replace("#<span style=\"font-size:(.*?)?(.*?)px\">(.*?)</span>#si", "[size=\\2]\\3[/size]", $text);
    $text = preg_replace("#<span style=\"color:(.*?)?(.*?)\">(.*?)</span>#si", "[color=\\2]\\3[/color]", $text);
    $text = preg_replace("#<ul>(.*?)</ul>#si", "[list]\\1[/list]", $text);
    $text = preg_replace("#<div class=kod>(.*?)</div>#si", "[code]\\1[/code]", $text);
    $text=str_replace("<hr>", "[hr]", $text);
    $text=str_replace("<li>", "[*]", $text);
    return $text;
  }
}
?>