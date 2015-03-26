$( document ).ready(function() {
	/*
	* Date 
	* ====
	*/
	var d = new Date();
	var weekday = new Array(7);
	weekday[0]=  "Sunday";
	weekday[1] = "Monday";
	weekday[2] = "Tuesday";
	weekday[3] = "Wednesday";
	weekday[4] = "Thursday";
	weekday[5] = "Friday";
	weekday[6] = "Saturday";
	var month = new Array(12);
	month[0] = "january";
	month[1] = "february";
	month[2] = "march";
	month[3] = "april";
	month[4] = "may";
	month[5] = "june";
	month[6] = "july";
	month[7] = "august";
	month[8] = "september";
	month[9] = "october";
	month[10] = "november";
	month[11] = "december";

	$('#date_string').datepicker({format: 'yyyy-mm-dd'})
		.on('changeDate', function(ev){
			$(this).datepicker('hide');
			$('#purchase_date').val($('#date_string').val());
			var new_date = new Date($('#date_string').val());
			var nw = weekday[new_date.getDay()];
			$('#date_string').val(nw + ", " + new_date.getDate() + " of " + month[new_date.getMonth()] + " of " +  new_date.getFullYear());
		});

	$('#new_hire_item_form').submit(function(event){
		event.preventDefault();
		$.ajax({
			type: "POST",
			url: 'save_item',
			data: $(this).serialize(),
			dataType: "json"
		}).done(function( json ) {
			if(json.result == "ok")
			{
				if(json.type != "1")
				{	
					set_searcher(json.type);
					$('#components_form #parent_item').val(json.new_item_id);
					var options = {backdrop: 'static', keyboard: false, show:true};
					$('#components_modal').modal(options);
				}else{
					location.href="../hire_stock/fleet_records";
				}
							
			}else{
				alert("Request error");
			}
			
		}).fail(function(){
			alert("There is already an item with that description or fleet number.");
		});		
	});	
	
	$('#components_form').submit(function(event){
		event.preventDefault();
		$.ajax({
			type: "POST",
			url: 'save_components_members',
			data: $(this).serialize(),
			dataType: "json"
		}).done(function( json ) {
			if(json.result == "ok")
			{				
				$('#components_modal').modal('hide');
				location.href="../hire_stock/fleet_records";			
			}else{
				alert("Request error");
			}
			
		}).fail(function(){
			alert("Technical error");
		});		
	});

});
	
function set_searcher( type )	
{
	var func = "";
	
	if( type == 2)
	{
			func = "get_single_items_json";
	}else if(type == 3)
	{
		func = "get_multiple_items_json";
	}else if( type == 4)
	{
		func = "groups_json";
	}		
		
	$.get( "../hire_stock/"+func, function( data ) {
			var items = data;
			$( '#search_hire_item' ).autocomplete({
				minLength: 0,
				source: items,
				focus: function( event, ui ) {
					$('#search_hire_item').val( ui.item.label );
						return false;
					},
					select: function( event, ui ) {
						
							$('#items_table > tbody').append(
							"<tr>\
								<td><input type=\"hidden\" id=\"new_item_id_in\" name=\"new_item_id_in[]\" value=\""+ui.item.pk_id+"\">"+ui.item.fleet_number+"</td>\
								<td>"+ui.item.label+"</td>\
								<td>"+ui.item.qty+"</td>\
								<td><input type=\"text\" id=\"new_item_qty_in\" name=\"new_item_qty_in[]\" class=\"form-control input-sm\"/></td>\
							</tr>");													
					
						$( '#search_hire_item' ).val("");
						return false;
					}
					})
					.autocomplete( "instance" )._renderItem = function( ul, item ) {
						return $( "<li>" )
						.append( "<a>" + item.label + "</a>" )
						.appendTo( ul );
			};
		});
}