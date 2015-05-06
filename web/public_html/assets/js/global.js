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
var now_mysqlformat = now.getFullYear()+"-"+("0"+(now.getMonth()+1)).toString().slice(-2)+"-"+("0"+now.getDate()).toString().slice(-2)+" "+("0"+now.getHours()).toString().slice(-2)+":"+("0"+now.getMinutes()).toString().slice(-2)+".000";

formatInputs();

function formatInputs() {
    
    $(".percentage, [name^=discount_percentage]").on('keyup change', function(){
        var str 		= $(this).val();
        var init_pos 	= this.selectionStart;
        
        str = str.replace(/[%]*/g, "");
        str = str.replace(/[^0-9\.]*/g, "");
        str = str + "%";
        
        $(this).val(str);
        
        console.log(init_pos + " " + str.length);
        
        if(this.selectionStart == this.selectionEnd)
        {
            this.selectionEnd = this.selectionStart = init_pos;
        }
        
        if(this.selectionStart == str.length)
            this.selectionStart -= 1;
        
        if(this.selectionEnd == str.length)
            this.selectionEnd -= 1;	
    });
}

