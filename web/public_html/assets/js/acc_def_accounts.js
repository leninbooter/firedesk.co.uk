$.get( base_url + "index.php/accounting/getCOAjson", function( data ) {
	
	suppliers = data;
	
	$( "#defNominalAccountsForm input:text" ).autocomplete({
        minLength: 1,
        source: suppliers,
        focus: function( event, ui ) {
                return false;
            } ,
        select: function( event, ui ) {				
                $( this ).val( ui.item.id );
                setDefAccount( this );
                
                return false;
            }
            })
	.autocomplete( "instance" )._renderItem = function( ul, item ) {
        return $( "<li>" )
        .append( "<a>" + item.label + "</a>" )
        .appendTo( ul );
	};
});

$( "#defNominalAccountsForm input:text" ).change( function(e) {
    
    //         
    
});

function setDefAccount( caller ) {
     url  = base_url + 'index.php/accounting/setDefAccount';
    data = {
        defAcc:  $(caller).attr('id'),
        accCode: $(caller).val()
    }; 
    
    ajaxForm( url, data); 
}
