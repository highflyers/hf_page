var $buoop = {vs:{i:7,f:4,o:10.5,s:4,n:9}};
var tplUrl = "templates/platektemplejt/"; 
$buoop.ol = window.onload; 
window.onload = function() { 
	try {if ($buoop.ol) $buoop.ol();}catch (e) {} 
	var e = document.createElement("script"); 
	e.setAttribute("type", "text/javascript"); 
	e.setAttribute("src", "http://browser-update.org/update.js"); 
	document.body.appendChild(e); 
};

$(document).ready(function() {
	
	ddsmoothmenu.init({
		mainmenuid: "mainMenu", //menu DIV id
		orientation: 'h', //Horizontal or vertical menu: Set to "h" or "v"
		classname: 'ddsmoothmenu', //class added to menu's outer DIV
		//customtheme: ["#1c5a80", "#18374a"],
		contentsource: "markup" //"markup" or ["container_id", "path_to_menu_file"]
	});
	
	!window.jQuery && document.write(unescape('%3Cscript src="jquery/minified/jquery-1.9.1.min.js"%3E%3C/script%3E'));
	
	$(".socialButton img").each(function(index){
		var id = $(this).parent().get(0).getAttribute("id");
		$(this).attr("src", tplUrl + "gfx/" + id + ".png");
	});
	
	$(".socialButton img").hover(
    function(){
        var id = $(this).parent().get(0).getAttribute("id");
	    $(this).attr("src", tplUrl + "gfx/" + id + "_over.png");
	}, 
	function() {
	    var id = $(this).parent().get(0).getAttribute("id");
	    $(this).attr("src", tplUrl + "gfx/" + id + ".png");
	});
	
	$("#flagEN").hover(
    function(){
        $(this).attr("src", tplUrl + "gfx/icons/flagEN_1.png");
	}, 
	function() {
	    $(this).attr("src", tplUrl + "gfx/icons/flagEN_0.png");
	});
	$("#flagPL").hover(
    function(){
        $(this).attr("src", tplUrl + "gfx/icons/flagPL_1.png");
	}, 
	function() {
	    $(this).attr("src", tplUrl + "gfx/icons/flagPL_0.png");
	});
});
