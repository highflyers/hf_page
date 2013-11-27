
var newsLoaded = 1;
var newsAutoScroll = 1;
var newsScr = 0;

$(document).ready(function() {
	if(document.getElementById('newsBox') == null) {
		return 0;
	}
	newsLoad(1);
	newsScr = setInterval(function() {newsScroll()}, 5000);
});

function newsScroll() {
	if(newsAutoScroll) {
		newsNext2();
	}
};

function dontGo(evt) {
	evt.preventDefault();
};

function newsLoad(n) {
	//alert('Wczytanie newsa: '+n);
	newsLoaded = n;
	
	var i;
	var circles = $('.newsCircleButton');
	for(i = 0; i < circles.length; i++) {
		circles[i].style.backgroundImage = 'url("gfx/newsNavCircleInactive.png")';
	}
	
	circles[n-1].style.backgroundImage = 'url("gfx/newsNavCircleActive.png")';
	
	var title = document.getElementById('newsBaseTitle' + n).textContent;
	var content = document.getElementById('newsBaseContent' + n).innerHTML;
	var text = document.getElementById('newsBaseContent' + n).textContent;
	var author = document.getElementById('newsBaseAuthor' + n).textContent;
	var date = document.getElementById('newsBaseDate' + n).textContent;
	var banner = document.getElementById('newsBaseBanner' + n);
	var source = banner.getAttribute('src');
	
	var contentTitle = document.createElement('h3');
	contentTitleText = document.createTextNode(title);
	contentTitle.appendChild(contentTitleText);
	contentTitle.setAttribute('id', 'newsContentTitle');
	contentTitle.setAttribute('class', 'contentHeader');
	
	document.getElementById('newsContent').innerHTML = '';
	
	document.getElementById('newsContent').appendChild(contentTitle);
	document.getElementById('newsContentTitle').insertAdjacentHTML('afterend', content);
	document.getElementById('newsInfoAuthor').textContent = author;
	document.getElementById('newsInfoDate').textContent = date;
	
	$( "#newsBanner" ).animate({ opacity: 0 }, 200 );
	$( "#newsTitle" ).animate({ opacity: 0 }, 200 );
	$( "#newsExcerpt" ).animate({ opacity: 0 }, 200 );
	
	setTimeout(function(){
		$( "#newsBanner" ).css('background', 'url('+ source +') left top no-repeat');
	}, 200);
	setTimeout(function(){
		document.getElementById('newsTitle').textContent = title;
	}, 200);
	setTimeout(function(){
		document.getElementById('newsExcerpt').textContent = text.substr(0, text.indexOf(' ', 100)) + '...';
	}, 200);
	
	$( "#newsBanner" ).animate({ opacity: 1 }, 200 );
	$( "#newsTitle" ).animate({ opacity: 1 }, 200 );
	$( "#newsExcerpt" ).animate({ opacity: 1 }, 200 );
	scrollbarInit();
};

function newsExpand() {
	if(newsAutoScroll == 1) {
		//alert('Open');
		newsAutoScroll = 0;
		document.getElementById('newsButtonRead').textContent = "zwiń newsa";
		$('#newsExcerpt').fadeOut(100);
		$('#newsTitle').fadeOut(100);
		$('#newsBoxBody').slideDown(1000);
		scrollbarInit();
	}
	else {
		//alert('Close');
		newsAutoScroll = 1;
		clearInterval(newsScr);
		newsScr = setInterval(function() {newsScroll()}, 5000);
		document.getElementById('newsButtonRead').textContent = "czytaj dalej";
		$('#newsBoxBody').slideUp(500);
		setTimeout(function() {
			$('#newsExcerpt').fadeIn(200);
			$('#newsTitle').fadeIn(200);
		}, 400);
	}
};

function newsNext(evt) {
	evt.preventDefault();
	clearInterval(newsScr);
	newsScr = setInterval(function() {newsScroll()}, 5000);
	if(newsLoaded < 6) newsLoad(newsLoaded + 1);
	else newsLoad(1);
};

function newsNext2() {
	if(newsLoaded < 6) newsLoad(newsLoaded + 1);
	else newsLoad(1);
};

function newsPrev(evt) {
	evt.preventDefault();
	clearInterval(newsScr);
	newsScr = setInterval(function() {newsScroll()}, 5000);
	if(newsLoaded > 1) newsLoad(newsLoaded - 1);
	else newsLoad(6);
};

function scrollbarInit() {
	$("#newsContent").mCustomScrollbar({
				theme:"dark-thick",
				scrollInertia:300,
				mouseWheel:true,
				mouseWheelPixels:116,
				scrollButtons:{
					enable: true,
					scrollSpeed:50
					}
			});
}