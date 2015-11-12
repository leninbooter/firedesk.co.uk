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
    
    callerID = $(caller).attr('id');
    url      = base_url + 'index.php/accounting/setDefAccount';
    data     = {
        defAcc:  callerID,
        accCode: $(caller).val()
    }; 
    
    callBackFn = function() {
        
        options = {
                trigger:    'manual',
                placement: 'right',
                title:      'Saved!',
                template:   '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
            };
            $('#'+callerID).tooltip(options);
            $('#'+callerID).tooltip('show');            
            setTimeout( function() {
                $('#'+callerID).tooltip('destroy');
            }, 2000);
    }
    
    ajaxForm( url, data, callBackFn); 
}
