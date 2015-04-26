$('#new_discount_group').submit(function(event) {	
	$("[name^=description]").each(function(index,value)
	{
		if( $(this).prop('readonly') )
		{
			$(this).prop('disabled', true);
			$("[name^=pk_id]").eq(index).prop('disabled', true);
			$("[name^=discount_percentage]").eq(index).prop('disabled', true);
		}		
	});
});

$('[name^=remove_description_btn]').click(function()
{
	var input = $("[name^=description]:first", $(this).parent().parent().parent());
	var disc = $("[name^=discount_percentage]:first", $(this).parent().parent().parent());
	
	input.prop('readonly', false);	
	input.val("");
	
	disc.prop('readonly', false);
	
	$('#new_discount_group').submit();
});

