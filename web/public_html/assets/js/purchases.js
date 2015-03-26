$(document).ready(function(){
	$('#supplier_name').focus();
});
				
$.get( "../suppliers/get_suppliers_json", function( data ) {
	
	suppliers = data;
	
	$( "#supplier_name" ).autocomplete({
		minLength: 0,
		source: suppliers,
		focus: function( event, ui ) {
			$( "#supplier_name" ).val( ui.item.label );
				return false;
			},
			select: function( event, ui ) {				
				$( "#supplier_pk_id" ).val( ui.item.pk_id );
				return false;
			}
			})
			.autocomplete( "instance" )._renderItem = function( ul, item ) {
				return $( "<li>" )
				.append( "<a>" + item.label + "</a>" )
				.appendTo( ul );
	};
});
				
 