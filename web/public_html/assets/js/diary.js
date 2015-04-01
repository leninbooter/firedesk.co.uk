$(document).ready(function(){
    $('#celndar_placeholder').datepicker({
    todayBtn: "linked"
    });
	
	
	$('#start_date, #end_date').datepicker({format: 'dd/mm/yyyy'})
		.on('changeDate', function(ev){
			$(this).datepicker('hide');
			var new_date = new Date();
		});
	
	//Contract time
	//$('#time').val(d.getHours() + ":" + d.getMinutes());
	
	$.get( location.href.substring(0,location.href.lastIndexOf("index.php")) +"index.php/users/get_all_users_json", function( data ) {
		var items = data;
		var validate = function(selected) {
			var matches = $.grep(items, function(n, i) {
				return selected.toLowerCase() == n.label.toLowerCase();
			});
			
			if(!matches.length)
			{	$('#searcher').parent().addClass("has-error");
				$('#searcher').val('');}
			else
			{
				$('#searcher').parent().removeClass("has-error");
				
			}
		};
		$( '#searcher' ).autocomplete({
			minLength: 0,
			source: items,
			focus: function( event, ui ) {
				//$('#messenger_new_message_form #to_string').val( ui.item.label );
					return false;
				},
				select: function( event, ui ) {
					$('#searcher').val( ui.item.label );
					$('#searcher').val(ui.item.pk_id);
					validate(ui.item.label);
					return false;
				}
			})
			.keyup(function() {
				var $parentContext = $(this);				
				validate($parentContext.val());
			})
			.autocomplete( "instance" )._renderItem = function( ul, item ) {
				return $( "<li>" )
				.append( "<a>" + item.label + "</a>" )
				.appendTo( ul );
			};
	});	
	
});