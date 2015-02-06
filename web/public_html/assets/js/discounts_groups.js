$('#new_discount_group').submit(function(event) {	
	$("[name^=description]").each(function(index,value)
	{
		if( $(this).prop('readonly') )
		{
			$(this).prop('disabled', true);
			$("[name^=pk_id]").eq(index).prop('disabled', true);
		}		
	});
});