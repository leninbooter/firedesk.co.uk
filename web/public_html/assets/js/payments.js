$('#cash, #cheque, #card').keyup( function() { calculateTotal(); });

function calculateTotal() {
    
    cash    = parseFloat($('#cash').val());
    cash    = isNaN(cash) ? 0:cash;
    cheque  = parseFloat($('#cheque').val());
    cheque  = isNaN(cheque) ? 0:cheque;
    card    = parseFloat($('#card').val());
    card    = isNaN(card) ? 0:card;
    total   = cash + card + cheque;
    total   = parseFloat(total).toFixed(2);
    if ( !isNaN(total))
        $('#totalPaid').html(total);
}

function submitPay() {
    
   url = base_url + 'index.php/invoices/pay';
   data = $('#payment_form').serialize();
   cbfn = function() {
     
        $('#multipurposeModal').modal('hide');
       
   };
   
   ajaxForm( url, data, cbfn );
    
}