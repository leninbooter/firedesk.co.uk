/*$('#save_send_button').click(function(){
	$('#email_invoice').val("1");
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
});*/

window.onbeforeunload = function(){
    
    if( $('#accepted').val() != '1' ){
        
        return 'leaving';
    }
};

$( window ).unload(function() {
   
   if( $('#accepted').val() != '1' ){        
        discard( function(){} );
   }
  return "Bye now!";
});

function discard( callBackFn = function() { 
                                            url = base_url + 'index.php/contracts/edit?id=' + $('#contract_id').val();
                                            location.href = url; 
                                          } ) {
    
    $.ajax(base_url+'index.php/invoices/discard',
            {
            type: 'post',
            data: { iid: $('#invoiceID').val() },
            dataType: "json",
            success: function(r) {
                if ( r.result == "ok") {
                    $('#accepted').val('1');
                    callBackFn();
                }else {
                    alert(r.message);
                }
            },
            statusCode: {                
                400: function(r) {
                    alert(r.responseText);
                }
            }
    });
}

function email( callBackFn = void(0) ) {
    
    iid = $('#invoiceID').val();
    
    url = base_url + 'index.php/invoices/pdf';
    $.get(url, { disk:'' , iid: iid }, function(r) {
        
        $.get( base_url + 'index.php/invoices/email', 
               { iid: iid, pdf: r, to: '' }, 
               function() {                     
                    callBackFn();
        })
        .always( function(r) {
            if ( r != 'ok') {
                alert('Internal error; please, try again');
            }            
        });
    });
    
}

function pdf() {
    
    if( $('#accepted').val() != '1' ){
        save();
    }
    url = base_url + 'index.php/invoices/pdf?iid=' + $('#invoiceID').val();
    window.open( url, '_blank');
}

function removeRow(caller) {
    
    $.ajax(base_url+'index.php/invoices/removeItem',
            { 
            type: 'post',
            data: { rowid: $('#itemID', $(caller).parent().parent()).val()},
            dataType: "text",
            success: function(r) {
                if ( r== "ok") {
                    $(caller).parent().parent().remove();
                }else {
                    alert(r);
                }
            },
            statusCode: {                
                400: function(r) {
                    alert(r.responseText);
                }
            }
    });    
}

function save( then = '' ) {
    
    iid = $('#invoiceID').val();
    $.ajax(base_url+'index.php/invoices/accept',
        { 
        type: 'post',
        data: { iid: iid },
        dataType: "json",
        success: function(r) {
            
            if ( r.result == "ok")  {
                
                if( $('#contract_type').val() == "Cash")
                    $('#payment_invoice').modal();
                
                $('#accepted').val('1');
                if ( then == 'email') {                
                    
                    email( function() {
                       location.reload(true);
                    });
                    
                }else {
                    
                   location.reload(true);
                }
                
            }else {
                alert(r.message);
                $('#accepted').val('1');
                url = base_url + 'index.php/contracts/edit?id=' + $('#contract_id').val();
                location.href = url; 
            }
        },
        statusCode: {                
            400: function(r) {
                alert(r.responseText);
            }
        }
    });
}