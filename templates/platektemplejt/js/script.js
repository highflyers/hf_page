var tplUrl = "templates/platektemplejt/";

$(document).ready(function() {
	ddsmoothmenu.init({
		mainmenuid: "mainMenu", //menu DIV id
		orientation: 'h', //Horizontal or vertical menu: Set to "h" or "v"
		classname: 'ddsmoothmenu', //class added to menu's outer DIV
		//customtheme: ["#1c5a80", "#18374a"],
		contentsource: "markup" //"markup" or ["container_id", "path_to_menu_file"]
	});
	
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
});

