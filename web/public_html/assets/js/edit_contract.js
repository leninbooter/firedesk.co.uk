$( document ).ready(function() {		
	var uriArray = window.location.pathname.split( '/' );
	if(uriArray[4] == "edit_outstanding_items")
	{
		$('#outstanding_items').modal();
	}
});

$( '#items_supplied_form' ).submit(function(event)
{
	event.preventDefault();
	var dataForm = $('#items_supplied_form').serializeArray();
	$.ajax(
	{
		type: "POST",
		url: "save_items_supplied",
		data: dataForm,
		dataType: "text"
	}).done(function(response){
		if(response == "ok")
		{
			$('#outstanding_items .modal-body').append("<div class=\"alert alert-success\" role=\"alert\">The contract was successfully updated.</div>");
		}
		if(response == "ko")
		{
			$('#outstanding_items .modal-body').append("<div class=\"alert alert-warning\" role=\"alert\">Please, verify the data sent and try again.</div>");
		}
	}).fail(function(response){
			$('#outstanding_items .modal-body').append("<div class=\"alert alert-danger\" role=\"alert\">There was a problem processing the request; please, try again.</div>");
	});	
});