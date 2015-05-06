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



function add_item_to_grid(item_type, qty, description, rate, regularity, disc, total_row)
{
	$('#items tr').last().before(
		"<tr><td><input type=\"hidden\" id=\"item_type\" name=\"item_type[]\" value=\""+item_type+"\"><input id=\"item_no\" name=\"item_no[]\" type=\"text\" class=\"form-control\" value=\""+ $('#item_no_in').val() +"\" readonly></td><td><input id=\"qty\" name=\"qty[]\" type=\"text\" class=\"form-control\" value=\""+ parseInt(qty) +"\" readonly></td><td><input id=\"description\" name=\"description[]\" type=\"text\" class=\"form-control\" value=\""+ description +"\" readonly></td><td><input id=\"rate_per\" name=\"rate_per[]\" type=\"text\" class=\"form-control\" value=\""+ parseFloat(rate).toFixed(2) +"\" readonly><input id=\"regularity\" name=\"regularity[]\" type=\"text\" class=\"form-control\" value=\""+ regularity +"\" readonly></td><td><input id=\"disc\" name=\"disc[]\" type=\"text\" class=\"form-control\" value=\""+ disc +"\" readonly></td><td><input id=\"value\" name=\"value[]\" type=\"text\" class=\"form-control last-field\" value=\""+ total_row +"\" readonly></td></tr>");
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
	//Contract date
	var d = new Date();
	var weekday = new Array(7);
	weekday[0]=  "Sunday";
	weekday[1] = "Monday";
	weekday[2] = "Tuesday";
	weekday[3] = "Wednesday";
	weekday[4] = "Thursday";
	weekday[5] = "Friday";
	weekday[6] = "Saturday";
	var month = new Array(12);
	month[0] = "january";
	month[1] = "february";
	month[2] = "march";
	month[3] = "april";
	month[4] = "may";
	month[5] = "june";
	month[6] = "july";
	month[7] = "august";
	month[8] = "september";
	month[9] = "october";
	month[10] = "november";
	month[11] = "december";

	var nw = weekday[d.getDay()];
	$('#date_string').val(nw + ", " + d.getDate() + " of " + month[d.getMonth()] + " of " +  d.getFullYear());
	$('#date').val(d.getFullYear() +"-"+ (d.getMonth()+1) + "-" + d.getDate());
	
	$('#date_string').datepicker({format: 'yyyy-mm-dd'})
		.on('changeDate', function(ev){
			$(this).datepicker('hide');
			$('#date').val($('#date_string').val());
			var new_date = new Date($('#date_string').val());
			var nw = weekday[new_date.getDay()];
			$('#date_string').val(nw + ", " + new_date.getDate() + " of " + month[new_date.getMonth()] + " of " +  new_date.getFullYear());
		});
	
	//Contract time
	$('#time').val(d.getHours() + ":" + d.getMinutes());
});


/*$('#hired_items_form').submit(function(e){
	e.preventDefault();
	
	var fields = $(this).serializeArray();
	var iter = 0;
	 jQuery.each( fields, function( i, field ) {
		iter ++;
		switch(iter)
		{
			case 1:
				item_type = 3;
			break;
			
			case 2:
				rate = field.value;
			break;
			
			case 3: 
				qty = field.value;
			break;
			
			case 4:
				description = field.value;
				iter = 0;
				add_item_to_grid(item_type, qty, description, rate, 1, 0, qty*rate);
			break;
		}
	});
	
})*/;

$('#save_button').click(function()
{	
	$('#add_items_form').submit();
});

$('#parent_account').focusout(function() {
	//$('#dropdown_parents_list').hide();
});

/* New customer form */
$( '#new_contract_form' ).submit( function( event ) {
		if( $('#delivery_charge').val() == "")
			$('#delivery_charge').val(0);
		
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
									"<tr><td><input type=\"hidden\" id=\"item_type\" name=\"item_type[]\" value=\""+item_type+"\"><input id=\"item_no\" name=\"item_no[]\" type=\"text\" class=\"form-control\" value=\""+ $('#item_no_in').val() +"\" readonly></td><td><input id=\"qty\" name=\"qty[]\" type=\"text\" class=\"form-control\" value=\""+ parseInt(qty) +"\" readonly></td><td><input id=\"description\" name=\"description[]\" type=\"text\" class=\"form-control\" value=\""+ $('#description_in').val() +"\" readonly></td><td><input id=\"rate_per\" name=\"rate_per[]\" type=\"text\" class=\"form-control\" value=\""+ parseFloat(rate).toFixed(2) +"\" readonly><input id=\"regularity\" name=\"regularity[]\" type=\"text\" class=\"form-control\" value=\""+ $('#regularity_in option:selected').text() +"\" readonly></td><td><input id=\"disc\" name=\"disc[]\" type=\"text\" class=\"form-control\" value=\""+ parseFloat(disc).toFixed(2) +"\" readonly></td><td><input id=\"value\" name=\"value[]\" type=\"text\" class=\"form-control last-field\" value=\""+ parseFloat(value).toFixed(2) +"\" readonly></td></tr>"
									);
									$('#item_no_in').val("");
									$('#qty_in').val("");
									$('#description_in').val("");
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
									"<tr><td><input type=\"hidden\" id=\"item_type\" name=\"item_type[]\" value=\""+item_type+"\"><input id=\"item_no\" name=\"item_no[]\" type=\"text\" class=\"form-control\" value=\""+ $('#item_no_in').val() +"\" readonly></td><td><input id=\"qty\" name=\"qty[]\" type=\"text\" class=\"form-control\" value=\""+ parseInt(qty) +"\" readonly></td><td><input id=\"description\" name=\"description[]\" type=\"text\" class=\"form-control\" value=\""+ $('#description_in').val() +"\" readonly></td><td><input id=\"rate_per\" name=\"rate_per[]\" type=\"text\" class=\"form-control\" value=\""+ parseFloat(rate).toFixed(2) +"\" readonly></td><td><input id=\"disc\" name=\"disc[]\" type=\"text\" class=\"form-control\" value=\""+ parseFloat(disc).toFixed(2) +"\" readonly></td><td><input id=\"value\" name=\"value[]\" type=\"text\" class=\"form-control last-field\" value=\""+ parseFloat(value).toFixed(2) +"\" readonly></td></tr>"
									);
									$('#item_no_in').val("");
									$('#qty_in').val("");
									$('#description_in').val("");
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

$.get( base_url + "index.php/customers/get_customers_json", function( data ) {
	
	suppliers = data;
	
	$( "#account_reference" ).autocomplete({
		minLength: 0,
		source: suppliers,
		focus: function( event, ui ) {
			$( "#account_reference" ).val( ui.item.label );
				return false;
			},
			select: function( event, ui ) {				
				$( "#account_reference_id" ).val( ui.item.id );
				return false;
			}
			})
			.autocomplete( "instance" )._renderItem = function( ul, item ) {
				return $( "<li>" )
				.append( "<a>" + item.label + "</a>" )
				.appendTo( ul );
	};
});
