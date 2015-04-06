if( location.href.indexOf("www.") > -1 )
{
	location.href = location.href.replace("www.", "");
}


var base_url = "";
if(location.href.indexOf("index.php") > -1)
{
	base_url = location.href.substring(0,location.href.lastIndexOf("index.php")).replace("www.", "");
}
else
{
	base_url = location.href.replace("www.", "");
}

// User date & time
var now = new Date();
var now_mysqlformat = now.getFullYear()+"-"+("0"+(now.getMonth()+1)).toString().slice(-2)+"-"+("0"+now.getDate()).toString().slice(-2)+" "+now.getHours()+":"+now.getMinutes()+".000";