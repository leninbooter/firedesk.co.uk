var parent_selected = false;
var items_qty = 0;
var redirect_to = "";

function abandon( contract_id )
{	
	if( confirm('Are you sure you want to abandon this contract? You won\'t be able to activate it or modify it') )
	{
		$.ajax({
			type: "GET",
			url: "abandon_contract",
			data: { contract_id: contract_id },
			dataType: "text"
			})
			.done(function( json ) {
				if( json != 'ko' )
				{	
					$('#alerts .modal-content').html( "<div class=\"modal-body\">The contract was abandoned successfully!</div><div class=\"modal-footer\"><button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button></div>" );
					$('#alerts .modal-content').css('background-color','#D6E9C6');
					redirect_to = "list_all_contracts";
				}else
				{
					$('#alerts .modal-content').html( "<div class=\"modal-body\">There was with the request; please, try again.!</div><div class=\"modal-footer\"><button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button></div>" );	
					$('#alerts .modal-content').css('background-color', '#F2DEDE');
				}
			})
			.fail(function() {
				$('#alerts .modal-content').html( "<div class=\"modal-body\">There was a problem with the request; please, try again.!</div><div class=\"modal-footer\"><button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button></div>" );	
				$('#alerts .modal-content').css('background-color', '#F2DEDE');
			});
	}
}

function activate( contract_id )
{
	if( confirm('Are you sure you want to activate this contract? No further hire items can be added after activation') )
	{
		$.ajax({
			type: "GET",
			url: "activate_contract",
			data: { contract_id: contract_id },
			dataType: "text"
			})
			.done(function( json ) {
				if( json != 'ko' )
				{	
					$('#alerts .modal-content').html( "<div class=\"modal-body\">The contract was activated successfully!</div><div class=\"modal-footer\"><button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button></div>" );
					$('#alerts .modal-content').css('background-color','#D6E9C6');
					redirect_to = "edit?id="+contract_id;
				}else
				{
					$('#alerts .modal-content').html( "<div class=\"modal-body\">There was a problem activating the contract; please, try again.!</div><div class=\"modal-footer\"><button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button></div>" );	
					$('#alerts .modal-content').css('background-color', '#F2DEDE');
				}
			})
			.fail(function() {
				$('#alerts .modal-content').html( "<div class=\"modal-body\">There was a problem activating the contract; please, try again.!</div><div class=\"modal-footer\"><button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button></div>" );	
				$('#alerts .modal-content').css('background-color', '#F2DEDE');
			});
	}
}

function edit( contract_id )
{
	window.location.href = "edit?id="+contract_id;
}

function show_parents_dropdownlist(parent_input)
{	
	$('#dropdown_parents_list').show();
}

function setParentAccount(parent_id, parent_name)
{
	$('#account_reference_id').val(parent_id);
	$('#account_reference').val(parent_name);
	$('#dropdown_parents_list').hide();
	load_customers_addresses();
	parent_selected = true;
}

function get_customers_selec_list()
{
	$.ajax({
		type: "GET",
		url: "list_selectable_customersName_refID",
		data: { account_reference: $('#account_reference').val() },
		dataType: "text"
		})
		.done(function( msg ) {
			if( msg != 'none' )
			{
				$('#dropdown_parents_list').html(msg);
				show_parents_dropdownlist( $('#account_reference') );				
			}else
			{
				$('#dropdown_parents_list').html("");
			}
		});
}

function load_customers_addresses()
{
	$.ajax({
		type: "GET",
		url: "../customers/get_customers_addresses_json",
		data: { pk_id: $('#account_reference_id').val() },
		dataType: "json"
		})
		.done(function( json ) {
			if( json != 'none' )
			{
				$('#saved_addresses').empty();
				$('#saved_addresses').append($('<option>').text("").attr('value', ""));
				$.each(json, function(i, value) {
					$('#saved_addresses').append($('<option>').text(value).attr('value', value));
				});
			}
		});
}

$( document ).ready(function() {
	var parent_input = $('#account_reference');
	var height	= Math.round(parent_input.height() + parseInt(parent_input.css("padding-top").replace("px","")) + parseInt(parent_input.css("padding-bottom").replace("px","")) + 2);
	var width	= Math.round(parent_input.width()  + parseInt(parent_input.css("padding-left").replace("px","")) + parseInt(parent_input.css("padding-right").replace("px","")) + 2);
	$('#dropdown_parents_list').css("position", "absolute");
	$('#dropdown_parents_list').css("top", parent_input.offset().top + height + "px" );
	$('#dropdown_parents_list').css("left", parent_input.offset().left + "px");
	$('#dropdown_parents_list').css("min-width", width + "px");
	$('#account_reference_id').val("");
	$('#account_reference').val("");
	
	$('.datepicker').datepicker()
		.on('changeDate', function(ev){
			$(this).datepicker('hide');
		});
		
});

$('#account_reference').keyup(function()
{
	if( $(this).val().trim().length > 1 && $('#account_reference_id').val()=="")
	{
		get_customers_selec_list();
	}
})
.focus(function()
{
	if( $(this).val().trim().length > 1 && $('#account_reference_id').val()=="")
	{
		get_customers_selec_list();
	}
})
.change(function()
{
	if(! parent_selected && $('#account_reference_id').val()=="")
	{
		$(this).val("");
	}
	
	if( parent_selected )
	{
		$('#account_reference_id').val("");
		$(this).val("");
	}
	
	if( $(this).val()=="" )
	{
		$('#account_reference_id').val("");
	}
	
	
});

$('#save_button').click(function()
{	
	$('#add_items_form').submit();
});

$('#parent_account').focusout(function() {
	//$('#dropdown_parents_list').hide();
});

/* New customer form */
$( '#new_contract_form' ).submit( function( event ) {
		$('#payment_ammount').val( parseFloat( $('#payment_ammount').val() ).toFixed(2) );
		$('#delivery_charge').val( parseFloat( $('#delivery_charge').val() ).toFixed(2) );
		return true;
});

$('#desc_in').on('keyup', function(e) {
	if( e.which == 13 )
	{
		if( $('#qty_in').val() != "" &&  $('#description_in').val() != "" )
		{				
			var qty = $('#qty_in').val();
			var rate = $('#rate_in').val();
			var disc = $('#desc_in').val() == "" ? 0 : $('#desc_in').val();
			
			if( isNaN(qty) )
			{
				alert('Quantity must contain only numbers.');
			}else {			
			
				if( isNaN(rate) )
				{
					alert('Rate must contain only numbers.');
				}else 
				{
					if( isNaN(disc) )
					{
						alert('Discount must contain only numbers.');
					}else
					{
						if( typeof $('input[name=options]:checked' ).val() != 'undefined' )
						{
							var value = qty * ( rate - ( (rate * disc) / 100) );
							items_qty += 1;
							var item_type = $('input[name=options]:checked' ).val() == "sale" ? "1" : "2";
							item_type = $('input[name=options]:checked' ).val() == "hire" ? "2" : "1";
							
							if( $('input[name=options]:checked' ).val() == "hire" )
							{
								if( $('#regularity_in').val() == "" )
								{
									alert('Select the regularity');
								}else {								
									$('#items tr').last().before(
									"<tr><td><input type=\"hidden\" id=\"item_type\" name=\"item_type[]\" value=\""+item_type+"\"><input id=\"item_no\" name=\"item_no[]\" type=\"text\" class=\"form-control\" value=\""+ $('#item_no_in').val() +"\" readonly></td><td><input id=\"qty\" name=\"qty[]\" type=\"text\" class=\"form-control\" value=\""+ parseInt(qty) +"\" readonly></td><td><input id=\"description\" name=\"description[]\" type=\"text\" class=\"form-control\" value=\""+ $('#description_in').val() +"\" readonly></td><td><input id=\"no_entries\" name=\"no_entries[]\" type=\"text\" class=\"form-control\" value=\""+ $('#entry_in').val() +"\" readonly></td><td><input id=\"rate_per\" name=\"rate_per[]\" type=\"text\" class=\"form-control\" value=\""+ parseFloat(rate).toFixed(2) +"\" readonly><input id=\"regularity\" name=\"regularity[]\" type=\"text\" class=\"form-control\" value=\""+ $('#regularity_in option:selected').text() +"\" readonly></td><td><input id=\"disc\" name=\"disc[]\" type=\"text\" class=\"form-control\" value=\""+ parseFloat(disc).toFixed(2) +"\" readonly></td><td><input id=\"value\" name=\"value[]\" type=\"text\" class=\"form-control last-field\" value=\""+ parseFloat(value).toFixed(2) +"\" readonly></td></tr>"
									);
									$('#item_no_in').val("");
									$('#qty_in').val("");
									$('#description_in').val("");
									$('#entry_in').val("");
									$('#rate_in').val("");
									$('#regularity_in').val(1);
									$('#desc_in').val("");
									$('#item_no_in').focus();
								}
							}else
							{
								if( $('input[name=options]:checked' ).val() == "sale" )
								{
									$('#items tr').last().before(
									"<tr><td><input type=\"hidden\" id=\"item_type\" name=\"item_type[]\" value=\""+item_type+"\"><input id=\"item_no\" name=\"item_no[]\" type=\"text\" class=\"form-control\" value=\""+ $('#item_no_in').val() +"\" readonly></td><td><input id=\"qty\" name=\"qty[]\" type=\"text\" class=\"form-control\" value=\""+ parseInt(qty) +"\" readonly></td><td><input id=\"description\" name=\"description[]\" type=\"text\" class=\"form-control\" value=\""+ $('#description_in').val() +"\" readonly></td><td><input id=\"no_entries\" name=\"no_entries[]\" type=\"text\" class=\"form-control\" value=\""+ $('#entry_in').val() +"\" readonly></td><td><input id=\"rate_per\" name=\"rate_per[]\" type=\"text\" class=\"form-control\" value=\""+ parseFloat(rate).toFixed(2) +"\" readonly></td><td><input id=\"disc\" name=\"disc[]\" type=\"text\" class=\"form-control\" value=\""+ parseFloat(disc).toFixed(2) +"\" readonly></td><td><input id=\"value\" name=\"value[]\" type=\"text\" class=\"form-control last-field\" value=\""+ parseFloat(value).toFixed(2) +"\" readonly></td></tr>"
									);
									$('#item_no_in').val("");
									$('#qty_in').val("");
									$('#description_in').val("");
									$('#entry_in').val("");
									$('#rate_in').val("");
									$('#regularity_in').val(1);
									$('#desc_in').val("");
									$('#item_no_in').focus();
								}
							}
							
						}else{
							alert('Please, select the type of item, Sale or Hire');
						}
					}
				}
			}
		}
	}
});

$('#alerts ').on('hide.bs.modal', function (e) {
	if( redirect_to != "" )
	{
		window.location.href = redirect_to;
	}
})

function contract_details_pdf(contract_id)
{
	$('#contract_details_content_iframe').attr("src", 'contract_details_pdf?contract_id=' + contract_id);
}
