$('#save_send_button').click(function(){
	$('#email_invoice').val("1");
	$('#payment_invoice').modal();
});

$('#save_button').click(function(){
	$('#email_invoice').val("0");
	$('#payment_invoice').modal();
});
