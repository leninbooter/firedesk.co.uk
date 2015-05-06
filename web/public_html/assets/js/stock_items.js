function removePrice(caller) {
    
    var priceIDinputVal = $('#price_id', $(caller).parent().parent().parent()).val();
    if (  priceIDinputVal != "" ) {
        
        if (confirm('Are you sure you want to delete this customed price? You won\'t be able to restore it')) {
                        
            $.post(base_url+'index.php/sales_stock/removeItemPrice',
                    {price_id: priceIDinputVal},
                    function(r) {
                        
                        if ( r == "ok" ) {
                            
                            $(caller).parent().parent().parent().remove();
                        }else {
                            
                            alert('Please, reload the page and try again');
                        }
                    });
        }
        
    }else {
        
        $(caller).parent().parent().parent().remove();
    }
}

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
    $.get(base_url+'index.php/sales_stock/getNewCustomedPriceRow', function(r) {
        $('#pricesRow').append(r);   
        type_checker();        
    });
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
	
	$.ajax(
	{
		type: "POST",
		url: "../save_item_prices",
		data: $(this).serializeArray(),
		dataType: "text"
	}).success(function(response){
		switch(response)
		{
			case "ko-validation":
				$('#special_prices .modal-body').append("<br/><div class=\"alert alert-warning\" role=\"alert\">Please, verify the data sent and try again.</div>");
			break;
			
			case "ko-db":
				$('#special_prices .modal-body').append("<br/><div class=\"alert alert-danger\" role=\"alert\">There was an error updating the database; please , try again.</div>");
			break;
			
			case "ok":
                location.reload();
            break;
            
            default:
				alert(response);
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