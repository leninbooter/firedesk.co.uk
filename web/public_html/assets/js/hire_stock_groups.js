var grid = new dhtmlXGridObject('gridbox');
grid.setHeader("_group_id, Family Group, Total, Rate, Calc Code, Vat Code, ,");
grid.setInitWidths("100, 200,50,100,120,120,120, 120");
grid.setColAlign("left,left,right, right,left,right,center, center"); 
grid.setColTypes("ron,ro,ro,ro,ro,ron,link,link"); 
grid.setColSorting("int, str, int, str,str,str,str,str");
grid.attachHeader("&nbsp;,#text_search,&nbsp,&nbsp;,&nbsp;,&nbsp;,&nbsp;,&nbsp;");	 
grid.init();
grid.load("groups_json",attachEvents,"json");
grid.setColumnHidden(0,true);

/*grid.getFilterElement(1)._filter = function(){
	var input = this.value; // gets the text of the filter input
	return function(value, id){
		var val=grid.cells(id,5).getValue();
		if (val == input){ 
				return true;
		}
		if (input == "")
			return true;
		
		return false;
	}
};*/

$('#modify_accesory_form').submit(function(event){
	event.preventDefault();
});

$('#new_hire_fleet_family_group').submit(function(evnt){
	
});

$.get( "../hire_stock/get_eligible_items_for_group_json", function( data ) {
	var items = data;
	
	$( "#search_items_for_group" ).autocomplete({
		minLength: 0,
		source: items,
		focus: function( event, ui ) {
			$( "#search_items_for_group" ).val( ui.item.label );
				return false;
			},
			select: function( event, ui ) {
				if(ui.item.origin =="2"){
					$('#accesory_group_ul').append(
					"<li><input type=\"hidden\" id=\"accesory_group\" name=\"accesory_group[]\" value=\""+ui.item.pk_id+","+ui.item.origin+"\"><div class=\"form-inline\" style=\" float:right; width:100%\"><button type=\"button\" class=\"close\" aria-label=\"Close\" onclick=\"$(this).parent().parent().remove();\"><span aria-hidden=\"true\">&times;</span></button><input type=\"text\" class=\"\" style=\"max-width:25px\" id=\"accesory_group_qty\" name=\"accesory_group_qty[]\"/> "+ui.item.label+"</div></li>"
					);
				}else if(ui.item.origin == "1")
				{
					$('#accesory_group_ul').append(
					"<li><input type=\"hidden\" id=\"accesory_group\" name=\"accesory_group[]\" value=\""+ui.item.pk_id+","+ui.item.origin+"\"><div class=\"form-inline\" style=\" float:right; width:100%\"><button type=\"button\" class=\"close\" aria-label=\"Close\" onclick=\"$(this).parent().parent().remove();\"><span aria-hidden=\"true\">&times;</span></button>"+ui.item.label+"</div></li>"
				)	;
				}
			
				$( "#search_items_for_group" ).val("");
				return false;
			}
			})
			.autocomplete( "instance" )._renderItem = function( ul, item ) {
				return $( "<li>" )
				.append( "<a>" + item.label + "</a>" )
				.appendTo( ul );
	};
});

$('#charging_band').change(function(){
	if($(this).val() =="new")
	{
		var rate = parseFloat($('#basic_rate').val()).toFixed(2);
		if( !isNaN(rate))
		{
			$('#new_charging_band_modal').modal('show');
			$('#rate_div').html(rate);
			$('#name', $('#hired_items_form')).focus();
		}else {
			$('#basic_rate').parent().addClass('has-error');
		}
	}
});

$('#_4hr_perc,#_8hr_perc,#_1day_perc,#_2day_perc,#_3day_perc,#_4day_perc,#_5day_perc,#_6day_perc,#_week_perc,#_weekend_perc,#_subsequent_perc').keyup(function(){
	var str 		= $(this).val();
	var init_pos 	= this.selectionStart;
	
	str = str.replace(/[%]*/g, "");
	str = str + "%";
	
	$(this).val(str);
	
	if(this.selectionStart == this.selectionEnd)
	{
		this.selectionEnd = this.selectionStart = init_pos;
	}
	
	if(this.selectionStart == str.length)
		this.selectionStart -= 1;
	
	if(this.selectionEnd == str.length)
		this.selectionEnd -= 1;	
});

$('#_4hr_perc,#_8hr_perc,#_1day_perc,#_2day_perc,#_3day_perc,#_4day_perc,#_5day_perc,#_6day_perc,#_week_perc,#_weekend_perc,#_subsequent_perc').change(function(){
	var rate_discounted = (Number($('#rate_div').html())*Number($(this).val().replace('%','')))/100;
	$('div:nth-child(3) > input',$(this).parent().parent()).val(rate_discounted);
});

$('#new_charging_band_form').submit(function(event){
	event.preventDefault();
	$.ajax({
		type: "POST",
		url: 'ins_charging_band',
		data: $(this).serialize(),
		dataType: "json",
        statusCode: {
            400: function(r) {
                
                alert(r.responseText);
            }
        }
	}).done(function( json ) {
		if(json.result == "ok")
		{
			$('#charging_band').append($('<option>').text(json.name).attr('value', json.id));
			$('#charging_band option[value='+json.id+']').attr('selected', true);
			$('#new_charging_band_modal').modal('hide');
			
		}else{
			alert("Request error");
		}
		
	}).fail(function(){
		alert("Request error");
	});		
});




function submit_new_hire_fleet_family_group()
{
	$.post( "../hire_stock/save_group", $('#new_hire_fleet_family_group').serialize(),  function(result) {
		
		if(result == "exists")
		{
			alert("There is already a group with that name; please, choose another one.");
		}else if(result =="ok"){
			location.href="../hire_stock/groups";
		}else if(result == "ko")
		{
			alert( "Request error" );
		}else{
			alert(result);
		}
		
	}, "text")
	.fail(function() {
		alert( "error" );
	});
}

function attachEvents()
{
	grid.attachEvent("onRowDblClicked", function(rId,cInd){
		if($('#new_hire_fleet_family_group #group_id').val() == "")
		{			
			$('#new_hire_fleet_family_group .panel-body .row:nth-child(3)').prepend("<div class=\"col-md-6\"><button type=\"button\" class=\"btn btn-default  btn-block\"onclick=\"restore_family_group_form()\">Cancel</button></div>");
			$('#new_hire_fleet_family_group .panel-heading').text("Edit family group");
		}		
		$('#new_hire_fleet_family_group #group_id').val(grid.cells(rId,0).getValue());
		$('#new_hire_fleet_family_group #name').val(grid.cells(rId,1).getValue());
		$('#new_hire_fleet_family_group #basic_rate').val(grid.cells(rId,3).getValue());
		$('#new_hire_fleet_family_group #charging_band option').filter(function() { 
			return ($(this).text() == grid.cells(rId,4).getValue());
		}).prop('selected', true);
		$('#new_hire_fleet_family_group #vat_code option').filter(function() { 
			return ($(this).text() == grid.cells(rId,5).getValue());
		}).prop('selected', true);
		$('#new_hire_fleet_family_group .panel-body .row:nth-child(2)').hide("fast");	
	});

	grid.attachEvent("onEnter", function(id,ind){
		alert(id+" "+ind);
	});
}

function edit_qtys(group_id)
{
	$.get("../hire_stock/group_accesories", { "id": group_id}, function(result){
		$('#edit_qtys_form_modal #accesories_group').html(result);
		set_accesory_searcher($('#edit_qtys_form_modal .modal-body #search_items_for_group'));
	}, "text" );
	
	$('#edit_qtys_form_modal').modal('show');
}

function remove_accesory(btn, group_id, accesory_id)
{
	$.post( "../hire_stock/remove_accesory", {"id": accesory_id},  function(result) {
		
		if(result =="ok"){
			
			var recursion = function removeMe(item){
				if( $('td', item).css('padding-left') == "30px" )
				{
					removeMe($(item).next());
					$(item).remove();
				}
			}
			
			recursion($(btn).parent().parent().next());
			$(btn).parent().parent().remove();
			
		}else if(result == "ko")
		{
			alert( "Request error" );
		}else{
			alert(result);
		}
		
	}, "text")
	.fail(function() {
		alert( "error" );
	});
}

function restore_family_group_form()
{
		$('#new_hire_fleet_family_group #group_id').val("");
		$('#new_hire_fleet_family_group #name').val("");
		$('#new_hire_fleet_family_group #basic_rate').val("");
		$('#new_hire_fleet_family_group #charging_band option').filter(function() { 
			return ($(this).text() == "");
		}).prop('selected', true);
		$('#new_hire_fleet_family_group #vat_code option').filter(function() { 
			return ($(this).text() == "");
		}).prop('selected', true);
		$('#new_hire_fleet_family_group .panel-body .row:nth-child(2)').show("fast");
		$('#new_hire_fleet_family_group .panel-body .row:nth-child(3) div:nth-child(1)').remove();
		$('#new_hire_fleet_family_group .panel-heading').text("Add family group");
}

function send_modify_accesory_form()
{
	$.post( "../hire_stock/save_accesory_group", $('#modify_accesory_form').serialize(),  function(result) {
		
		if(result =="ok"){
			$('#edit_qtys_form_modal').modal('hide');
		}else if(result == "ko")
		{
			alert( "Request error" );
		}else{
			alert(result);
		}
		
	}, "text")
	.fail(function() {
		alert( "error" );
	});
}

function set_accesory_searcher(input)
{
$.get( "../hire_stock/get_eligible_items_for_group_json", function( data ) {
	var items = data;
	$( input ).autocomplete({
		minLength: 0,
		source: items,
		focus: function( event, ui ) {
			$( input ).val( ui.item.label );
				return false;
			},
			select: function( event, ui ) {
				if(ui.item.origin =="2" || ui.item.item_type == "1" ){
					$('table > tbody', $(input).parent().parent().parent().parent().parent().parent()).append(
					"<tr><td><input type=\"hidden\" id=\"new_item_id_in\" name=\"new_item_id_in[]\" value=\""+ui.item.pk_id+"\"><input type=\"hidden\" id=\"new_item_origin_in\" name=\"new_item_origin_in[]\" value=\""+ui.item.origin+"\"><input type=\"hidden\" id=\"new_hire_item_type_in\" name=\"new_hire_item_type_in[]\" value=\""+ui.item.item_type+"\">"+ui.item.number+"</td><td>"+ui.item.label+"</td><td><input type=\"text\" id=\"new_item_qty_in\" name=\"new_item_qty_in[]\" class=\"form-control input-sm\"/></td><td><button type=\"button\" class=\"btn btn-default\" aria-label=\"Left Align\" onclick=\"$(this).parent().parent().remove();\"><span class=\"glyphicon glyphicon-minus-sign\" aria-hidden=\"true\"></span></button></td></tr>"
					);
				}else if(ui.item.origin == "1")
				{
					$('table > tbody', $(input).parent().parent().parent().parent().parent().parent()).append(
					"<tr><td><input type=\"hidden\" id=\"new_item_id_in\" name=\"new_item_id_in[]\" value=\""+ui.item.pk_id+"\"><input type=\"hidden\" id=\"new_item_origin_in\" name=\"new_item_origin_in[]\" value=\""+ui.item.origin+"\"><input type=\"hidden\" id=\"new_hire_item_type_in\" name=\"new_hire_item_type_in[]\" value=\""+ui.item.item_type+"\">"+ui.item.number+"</td><td>"+ui.item.label+"</td><td><input type=\"hidden\" value=\"NULL\" id=\"new_item_qty_in\" name=\"new_item_qty_in[]\" /></td><td><button type=\"button\" class=\"btn btn-default\" aria-label=\"Left Align\" onclick=\"$(this).parent().parent().remove();\"><span class=\"glyphicon glyphicon-minus-sign\" aria-hidden=\"true\"></span></button></td></tr>"
				)	;
				}
			
				$( input ).val("");
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