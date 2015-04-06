$('#login_form').submit(function(event){
	event.preventDefault();
	$.ajax({
		type: "POST",
		url: base_url+"index.php/sessions/start",
		data: $(this).serialize(),
		dataType: "json"
	}).done(function( json ) {
		if(json.result == "ok")
		{			
			location.href=base_url+"index.php/desk";
		}
		else if(json.result = "ko"){
			alert(json.error);
		}
		
	}).fail(function(){
		alert("Technical error");
	});
});