var parent_selected = false;

function show_parents_dropdownlist(parent_input)
{	
	$('#dropdown_parents_list').show();
}

function setParentAccount(parent_id, parent_name)
{
	$('#parent_account_id').val(parent_id);
	$('#parent_account').val(parent_name);
	$('#dropdown_parents_list').hide();
	parent_selected = true;
}

function get_customers_selec_list()
{
	$.ajax({
		type: "GET",
		url: "list_selectable_customers",
		data: { parent_account: $('#parent_account').val() },
		dataType: "text"
		})
		.done(function( msg ) {
			if( msg != 'none' )
			{
				$('#dropdown_parents_list').html(msg);
				show_parents_dropdownlist( $('#parent_account') );
			}else
			{
				$('#dropdown_parents_list').html("");
			}
		});
}

$( document ).ready(function() {
	var parent_input = $('#parent_account');
	var height	= Math.round(parent_input.height() + parseInt(parent_input.css("padding-top").replace("px","")) + parseInt(parent_input.css("padding-bottom").replace("px","")) + 2);
	var width	= Math.round(parent_input.width()  + parseInt(parent_input.css("padding-left").replace("px","")) + parseInt(parent_input.css("padding-right").replace("px","")) + 2);
	$('#dropdown_parents_list').css("position", "absolute");
	$('#dropdown_parents_list').css("top", parent_input.offset().top + height + "px" );
	$('#dropdown_parents_list').css("left", parent_input.offset().left + "px");
	$('#dropdown_parents_list').css("min-width", width + "px");
	$('#parent_account_id').val("");
	$('#parent_account').val("");
});

$('#parent_account').keyup(function()
{
	if( $(this).val().trim().length > 1 && $('#parent_account_id').val()=="")
	{
		get_customers_selec_list();
	}
})
.focus(function()
{
	if( $(this).val().trim().length > 1 && $('#parent_account_id').val()=="")
	{
		get_customers_selec_list();
	}
})
.change(function()
{
	if(! parent_selected && $('#parent_account_id').val()=="")
	{
		$(this).val("");
	}
	
	if( parent_selected )
	{
		$('#parent_account_id').val("");
		$(this).val("");
	}
	
	if( $(this).val()=="" )
	{
		$('#parent_account_id').val("");
	}
	
	
});


$('#parent_account').focusout(function() {
	//$('#dropdown_parents_list').hide();
});

