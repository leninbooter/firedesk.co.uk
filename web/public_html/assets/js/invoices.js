$('#save_send_button').click(function(){
	$('#email_invoice').val("1");
	if( $('#contract_type').val() == "Cash")
		$('#payment_invoice').modal();
	else
		$('#generate_invoice_form').submit();
});

$('#save_button').click(function(){
	$('#email_invoice').val("0");
	if( $('#contract_type').val() == "Cash")
		$('#payment_invoice').modal();
	else
		$('#generate_invoice_form').submit();
});

$('#ok_button').click(function()
{
	var total_paid = parseFloat( $('#cash').val() ).toFixed(2) + parseFloat( $('#card').val() ).toFixed(2) + parseFloat( $('#cheque').val() ).toFixed(2);
	
	if(	(total_paid == 0.00 &&
		confirm("The invoice will remain unpaid as you didn't especify any payment."))
		||
		total_paid > 0.00 )
	{		
		$('#generate_invoice_form').submit();
	}
});
