var parent_selected = false;

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


$('#parent_account').focusout(function() {
	//$('#dropdown_parents_list').hide();
});

