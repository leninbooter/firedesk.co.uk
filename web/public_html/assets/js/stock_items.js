function type_checker()
{	
	$('[name^=customers_pk_id]').change( function(){ 
		var types = [];	
		
		$('[name^=customers_pk_id]').each(function(){
			types.push($(this).val());
		});
		
		$('[name^=price_type]').each(function(index, value) {
			if( types[index] == "0" )
			{
				if( $(this).val() == "2")
					$(this).val("1");
					
				$('option[value="2"]', this).attr('disabled', true);
				$('option[value="0"]', this).attr('disabled', false);
				$('option[value="1"]', this).attr('disabled', false);	
			}else{
				$('option[value="2"]', this).attr('disabled', false);
				$('option[value="0"]', this).attr('disabled', true);
				$('option[value="1"]', this).attr('disabled', true);
				$(this).val("2");
			}			
		});

	});
}

type_checker();

$('#add_row').click(function()
{
	$('#rows').append($('#first_row').html());
	$("button[name^=remove_row_btn]").click( function() {
		if( $(this).parent().parent().parent().parent().attr("id") != "first_row")
			$(this).parent().parent().parent().remove();
	});
	
	type_checker();

});

$('#prices_form').submit(function(event) {
	event.preventDefault();	
	$("input[name^=price], input[name^=min_qty], input[name^=max_qty]").each(function() {
		 if( $(this).val() != "")
		 {
			if( isNaN($(this).val()) )
			{
				$(this).val("");
			}else{
				$(this).val( parseFloat( $(this).val() ).toFixed(2) );
			}
		 }
	});
	
	$('#first_row input, #first_row select').attr('disabled',true);
	
	/*$("input[name^=min_qty]").each(function() {
		 if( $(this).val() != "")
		 {
			if( isNaN($(this).val()) )
			{
				$(this).val("");
			}
		 }
	});
	
	$("input[name^=max_qty]").each(function() {
		 if( $(this).val() != "")
		 {
			if( isNaN($(this).val()) )
			{
				$(this).val("");
			}
		 }
	});*/
	
	var dataForm = $('#prices_form').serializeArray();
	$.ajax(
	{
		type: "POST",
		url: "../save_item_prices",
		data: dataForm,
		dataType: "text"
	}).done(function(response){
		switch(response)
		{
			case "ko-validation":
				$('#special_prices .modal-body').append("<br/><div class=\"alert alert-warning\" role=\"alert\">Please, verify the data sent and try again.</div>");
			break;
			
			case "ko-db":
				$('#special_prices .modal-body').append("<br/><div class=\"alert alert-danger\" role=\"alert\">There was an error updating the database; please , try again.</div>");
			break;
			
			default:
				window.location = response;
				break;
		}					
	}).fail(function(response){
			$('#special_prices .modal-body').append("<br/><div class=\"alert alert-danger\" role=\"alert\">There was a problem processing the request; please, try again.</div>");
	});	
});

$('#update_balances_massive_form').submit(function(e){
	e.preventDefault();	
	
	var dataForm = $('#update_balances_massive_form').serializeArray();
	
	$.ajax(
	{
		type: "POST",
		url: "update_balances_massive",
		data: dataForm,
		dataType: "text"
	}).done(function(response){
		switch(response)
		{
			case "ko-validation":
				$('#working_modal .modal-body').html("<br/><div class=\"alert alert-warning\" role=\"alert\">Please, verify the data sent and try again.</div>");
			break;
			
			case "ko-db":
				$('#working_modal .modal-body').html("<br/><div class=\"alert alert-danger\" role=\"alert\">There was an error updating the database; please , try again.</div>");
			break;
			
			default:
				$('#working_modal .modal-body').html("<br/><div class=\"alert alert-success\" role=\"alert\">"+response+" records modified.</div>");
				break;
		}					
	}).fail(function(response){
			$('#working_modal .modal-body').html("<br/><div class=\"alert alert-danger\" role=\"alert\">There was a problem processing the request; please, try again.</div>");
	});
	
});

$('#update_location_massive_form').submit(function(e){
	e.preventDefault();	
	
	var dataForm = $('#update_location_massive_form').serializeArray();
	
	$.ajax(
	{
		type: "POST",
		url: "update_locations_massive",
		data: dataForm,
		dataType: "text"
	}).done(function(response){
		switch(response)
		{
			case "ko-validation":
				$('#working_modal .modal-body').html("<br/><div class=\"alert alert-warning\" role=\"alert\">Please, verify the data sent and try again.</div>");
			break;
			
			case "ko-db":
				$('#working_modal .modal-body').html("<br/><div class=\"alert alert-danger\" role=\"alert\">There was an error updating the database; please , try again.</div>");
			break;
			
			default:
				$('#working_modal .modal-body').html("<br/><div class=\"alert alert-success\" role=\"alert\">"+response+" records modified.</div>");
				break;
		}					
	}).fail(function(response){
			$('#working_modal .modal-body').html("<br/><div class=\"alert alert-danger\" role=\"alert\">There was a problem processing the request; please, try again.</div>");
	});
	
});

$('#update_prices_massive_form').submit(function(e){
	e.preventDefault();	
	
	var dataForm = $('#update_prices_massive_form').serializeArray();
	
	$.ajax(
	{
		type: "POST",
		url: "update_prices_massive",
		data: dataForm,
		dataType: "text"
	}).done(function(response){
		switch(response)
		{
			case "ko-validation":
				$('#working_modal .modal-body').html("<br/><div class=\"alert alert-warning\" role=\"alert\">Please, verify the data sent and try again.</div>");
			break;
			
			case "ko-db":
				$('#working_modal .modal-body').html("<br/><div class=\"alert alert-danger\" role=\"alert\">There was an error updating the database; please , try again.</div>");
			break;
			
			default:
				$('#working_modal .modal-body').html("<br/><div class=\"alert alert-success\" role=\"alert\">"+response+" records modified.</div>");
				break;
		}					
	}).fail(function(response){
			$('#working_modal .modal-body').html("<br/><div class=\"alert alert-danger\" role=\"alert\">There was a problem processing the request; please, try again.</div>");
	});
	
});

$('#update_vats_massive_form').submit(function(e){
	e.preventDefault();	
	
	var dataForm = $('#update_vats_massive_form').serializeArray();
	
	$.ajax(
	{
		type: "POST",
		url: "update_vats_massive",
		data: dataForm,
		dataType: "text"
	}).done(function(response){
		switch(response)
		{
			case "ko-validation":
				$('#working_modal .modal-body').html("<br/><div class=\"alert alert-warning\" role=\"alert\">Please, verify the data sent and try again.</div>");
			break;
			
			case "ko-db":
				$('#working_modal .modal-body').html("<br/><div class=\"alert alert-danger\" role=\"alert\">There was an error updating the database; please , try again.</div>");
			break;
			
			default:
				$('#working_modal .modal-body').html("<br/><div class=\"alert alert-success\" role=\"alert\">"+response+" records modified.</div>");
				break;
		}					
	}).fail(function(response){
			$('#working_modal .modal-body').html("<br/><div class=\"alert alert-danger\" role=\"alert\">There was a problem processing the request; please, try again.</div>");
	});
	
});

$('[name=apply_to]').change(function(){
	var select = $('[name^=family_groups]:first', $(this).parent().parent() );
	if( $(this).val() != "Family group" )
	{
		select.prop('disabled', true);
	}else
	{
		select.prop('disabled', false);
	}
		
		
});