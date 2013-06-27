function confirmMe(question, url)
{
    var ask = confirm(question);

    if(ask)
    {
	window.location=url;
    }
}
