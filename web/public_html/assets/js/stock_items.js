$('#add_row').click(function()
{
	$('#rows').append($('#first_row').html());
	$("button[name^=remove_row_btn]").click( function() {
		if( $(this).parent().parent().parent().parent().attr("id") != "first_row")
			$(this).parent().parent().parent().remove();
	});
	
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
	
	/*$('#customers_pk_id').change( function(){ 
		var types = [];
		alert("pabblo");
		$('select[name^=customers_pk_id]').each(function(index,value) {			
			types.push($(this).val());
			
			if( $(this).val() == 0 )
			{
				if( $('#price_type').get(index).val() == "2")
					$('#price_type').get(index).val("1");
				$('#price_type option[value="2"]').get(index).attr('disabled', true);
				$('#price_type option[value="0"]').get(index).attr('disabled', false);
				$('#price_type option[value="1"]').get(index).attr('disabled', false);		
			}else {
				$('#price_type option[value="2"]').get(index).attr('disabled', false);
				$('#price_type option[value="0"]').get(index).attr('disabled', true);
				$('#price_type option[value="1"]').get(index).attr('disabled', true);
				$('#price_type').get(index).val("2");
			}
		});	
		
		$.each(types, function(index, value) {
				alert(index + ": " + value);
		});
	});*/
	
	

});

$('#customers_pk_id').change( function(){
	if( $('#customers_pk_id').val() == 0 )
	{
		if( $('#price_type').val() == "2")
			$('#price_type').val("1");
		$('#price_type option[value="2"]').attr('disabled', true);
		$('#price_type option[value="0"]').attr('disabled', false);
		$('#price_type option[value="1"]').attr('disabled', false);		
	}else {
		$('#price_type option[value="2"]').attr('disabled', false);
		$('#price_type option[value="0"]').attr('disabled', true);
		$('#price_type option[value="1"]').attr('disabled', true);
		$('#price_type').val("2");
	}
});

$('#prices_form').submit(function(event) {
	event.preventDefault();	
	$("input[name^=price]").each(function() {
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
	
	$("input[name^=min_qty]").each(function() {
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
	});
	
	var dataForm = $('#prices_form').serializeArray();
	$.ajax(
	{
		type: "POST",
		url: "../save_item_prices",
		data: dataForm,
		dataType: "text"
	}).done(function(response){
		if(response == "ko-validation")
		{
			$('#special_prices .modal-body').append("<br/><div class=\"alert alert-warning\" role=\"alert\">Please, verify the data sent and try again.</div>");
		}
		if(response == "ko-db")
		{
			$('#special_prices .modal-body').append("<br/><div class=\"alert alert-danger\" role=\"alert\">There was an error updating the database; please , try again.</div>");
		}else{
			window.location = response;
		}		
	}).fail(function(response){
			$('#special_prices .modal-body').append("<br/><div class=\"alert alert-danger\" role=\"alert\">There was a problem processing the request; please, try again.</div>");
	});	
});