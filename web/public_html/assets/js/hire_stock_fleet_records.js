var grid = new dhtmlXGridObject('gridbox');
var options = { backdrop: 'static', keyboard: true, show:true };

$( document ).ready(function() {
	grid.setHeader("_item_id,Fleet Number,Description,Qty,Family Group,Type");
	grid.setInitWidths("100,150,200,50,200,100");
	grid.setColAlign("left,left,left,center,left,left"); 
	grid.setColTypes("ron,ro,ro,ron,ro,ro"); 
	grid.setColSorting("int,str,str,int,str,str");
	grid.attachHeader("&nbsp;,#text_search,#text_search,&nbsp;,#select_filter,#select_filter");	 
	grid.init();
	grid.load("fleet_records_json", attachEvents,"json");
	grid.setColumnHidden(0,true);

	grid.attachEvent("onRowDblClicked", function(rId,cInd){		
		$('#items_menu_modal').modal('show');
		
		$('[name=options] option').filter(function() { 
			return ($(this).text() == 'Blue'); //To select Blue
		}).prop('selected', true);


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
			}else if(json.result =="nothing")
			{
				if(confirm("There are no components added; please confirm to continue."))
				{
					$('#components_modal').modal('hide');
				}
			}else{
				alert("Request error");
			}
			
		}).fail(function(){
			alert("Technical error");
		});
});

$('#print_prices_list_pdf_btn').click(function(){
	$('#multiporpuses_modal').on('show.bs.modal', 
								function() { 
									$('#multiporpuses_modal .modal-content').html("<div class=\"modal-body\"><iframe style=\"width:100%; height:500px\" src=\""+base_url+"index.php/hire_stock/print_prices_list\"></iframe></div>"); 
								} 
							);
});

function attachEvents()
{
	
}

function go_to(url)
{
	if(url == 'add_remove')
	{
		var selected_item_type = grid.cells(grid.getSelectedRowId(),5).getValue();
		if(selected_item_type == "Kit" || selected_item_type == "Bundle")
		{
            $('#parent_item').val(grid.cells(grid.getSelectedRowId(),0).getValue());
			if(selected_item_type == "Kit")
			{
				set_searcher(3);
			}else if(selected_item_type == "Bundle")
			{
				set_searcher(4);
			}
			$('#items_menu_modal').modal('hide');
			$('#components_modal #items_table tbody').load("get_components/"+grid.cells(grid.getSelectedRowId(),0).getValue());
			$('#components_modal').modal(options);
			
		}else if(selected_item_type == "Multiple")
		{
			location.href="../hire_stock/"+url+"/"+grid.cells(grid.getSelectedRowId(),0).getValue();
		}
	}
	else if(url == 'activity')
	{
		var from = (now.getMonth()+1) + "/" + (now.getFullYear()-1);
		var to = (now.getMonth()+1) + "/" + now.getFullYear();			
		
		$('#items_menu_modal').modal('hide');
		
		location.href=base_url+"index.php/hire_stock/activity?from="+from+"&to="+to+"&item="+grid.cells(grid.getSelectedRowId(),0).getValue();
	}
}

function mark_to_delete(obj)
{
	if( $('input[id=delete]', $(obj).parent().parent()).val() == "no")
	{
		$(obj).parent().parent().addClass('danger');
		$('input[id=delete]', $(obj).parent().parent()).val('yes');
	}else{
		$(obj).parent().parent().removeClass('danger');
		$('input[id=delete]', $(obj).parent().parent()).val('no');
	}
	
}

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
		func = "get_groups_json";
	}		
		
	$.get( "../hire_stock/"+func, function( data ) {
			var items = data;
			$( '#search_hire_item' ).autocomplete({
				minLength: 0,
				source: items,
				focus: function( event, ui ) {
					$('#components_form #search_hire_item').val( ui.item.label );
						return false;
					},
					select: function( event, ui ) {
							var html = "<tr>\
							<td style=\"display:none\">\
							<input type=\"hidden\" id=\"new_item\" name=\"new_item[]\" value=\"yes\">\
							<input type=\"hidden\" id=\"delete\" name=\"delete[]\" value=\"no\">\
							<input type=\"hidden\" id=\"new_item_id_in\" name=\"new_item_id_in[]\" value=\""+ui.item.pk_id+"\"></td>\
							<td>"+ui.item.fleet_number+"</td>\
							<td>"+ui.item.label+"</td>";
							
							if(type < 4){
								html = html + "<td>"+ui.item.qty+"</td><td><input type=\"text\" id=\"new_item_qty_in\" name=\"new_item_qty_in[]\" class=\"form-control input-sm\"/></td>\
								<td><button type=\"button\" class=\"btn btn-default btn-sm\" aria-label=\"Left Align\" onclick=\"$(this).parent().parent().remove();\"><span class=\"glyphicon glyphicon-minus-sign\" aria-hidden=\"true\"></span></button>\
								</tr>";
							}else{
								html = html + "<td></td>\
								<td></td>\
								<td><button type=\"button\" class=\"btn btn-default btn-sm\" aria-label=\"Left Align\" onclick=\"$(this).parent().parent().remove();\"><span class=\"glyphicon glyphicon-minus-sign\" aria-hidden=\"true\"></span></button>\
								</tr>";
							}
							
							$('#items_table > tbody').append(html);													
					
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