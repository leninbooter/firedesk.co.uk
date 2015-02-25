var no_entries = 0;
$('input[id=qty_in]').focus();
$('#no_entries').html(no_entries);

$('#edit_purchase_order_form').submit( function(e) {
	e.preventDefault();	
});

$('input[id=qty_in]').on('keyup', function(e){
	if( e.which == 13 )
	{
		$('input[id=description_in]').focus();
	}
});

$('input[id=description_in]').on('keyup', function(e){
	if( e.which == 13 )
	{
		$('input[id=suppliers_code_in]').focus();
	};
}).keyup(function(e) {
	if( e.which == 8 || e.which == 46 )
	{
		$('#description_in').popover('destroy');
	}
});

$('input[id=suppliers_code_in]').on('keyup', function(e){
	if( e.which == 13 )
	{
		$('input[id=cost_in]').focus();
	};
});

$('input[id=cost_in]').on('keyup', function(e){
	if( e.which == 13 )
	{
		$('input[id=for_in]').focus();
	};
});
$('input[id=for_in]').on('keyup', function(e){
	if( e.which == 13 )
	{
		add_item();
	}
});

$.get( "../../sales_stock/items_from_supplier_json", { id : $('#supplier_id').val() })
	.done( function( data ) {	
		
		$( "#description_in" ).autocomplete({
			minLength: 0,
			source: data,
			focus: function( event, ui ) {
				$( "#description_in" ).val( ui.item.label );
					return false;
				},
				select: function( event, ui ) {
					$( "#description_in" ).val( ui.item.label );
					$( "#hidden_name" ).val( ui.item.label );
					$( "#cost_in" ).val( ui.item.cost_price );
					$( "#suppliers_code_in" ).val( ui.item.supplier_code );
					$( "#item_id_in" ).val( ui.item.pk_id );

					var options = { placement: 'top',
									html: true,
									template:  	'<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
									title: '',
									content: '<table class="table"><thead><tr><td>Stock</td><td>Rec Level</td><td>On Order</td></tr></thead><tbody><tr><td>'+ui.item.quantity_balance+'</td><td>'+ui.item.quantity_on_order+'</td><td>'+ui.item.quantity_rec_level+'</td></tr></tbody></table>',
									trigger: 'manual' };
					$('#description_in').popover(options);
					$('#description_in').popover('show');										
					return false;
				}
				})
				.autocomplete( "instance" )._renderItem = function( ul, item ) {
					return $( "<li>" )
					.append( "<a>" + item.label + "</a>" )
					.appendTo( ul );
				};
				
				jQuery.each(data, function() {
					  if(Number(this.quantity_balance) < Number(this.quantity_rec_level))
					  {
						  $('#level_items_table tbody').append("<tr><td><input type=\"hidden\" id=\"lvl_modal_item_id_in\" value=\""+Number(this.pk_id)+"\" /><input type=\"hidden\" id=\"lvl_modal_suppliers_code_in\" value=\""+this.supplier_code+"\" />" + this.quantity_balance + "</td><td>"+this.quantity_on_order+"</td><td>"+Number(this.quantity_rec_level)+"</td><td><input class=\"form-control\" id=\"lvl_modal_qty_in\" value=\""+ (Number(this.quantity_rec_level) - Number(this.quantity_balance)) +"\"/></td><td><input type=\"hidden\" id=\"lvl_modal_description_in\" value=\""+this.label+"\"/>"+this.label+"</td><td><input type=\"hidden\" id=\"lvl_modal_cost_in\" vale=\""+this.cost_price+"\" />"+this.cost_price+"</td><td><button type=\"button\" class=\"btn btn-default\" aria-label=\"Left Align\" onclick=\"add_item_with("+this.pk_id+", $('#lvl_modal_qty_in', $(this).parent().parent()).val(), '"+this.label+"', '"+this.supplier_code+"', '"+this.cost_price+"');\" id=\"add_row_btn\" name=\"add_row_btn[]\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span></button></td></tr>");
					  }
				});				
});

$('#family_group_items_form').submit( function(e){
	e.preventDefault();
	add_items_from_family();
});

function add_item()
{
	if( validate_item_ins() ) {
		$('#description_in').popover('destroy');
	
		$('#description_in').on('hidden.bs.popover', function () {
			var total =  parseFloat($('#qty_in').val() * $('#cost_in').val()).toFixed(2);
			$('#items tr').last().before(
			"<tr><td><input type=\"hidden\" id=\"item_id\" name=\"item_id[]\" value=\""+$( "#item_id_in" ).val()+"\"/><input class=\"form-control\" id=\"qty\" name=\"qty[]\" value=\""+$('#qty_in').val()+"\" readonly/></td><td><input class=\"form-control\" id=\"description\" name=\"description[]\" value=\""+$('#description_in').val()+"\" readonly/><br/><div class=\"form-inline\"><div class=\"form-group\"><label for=\"for\">For </label> <input type=\"text\" class=\"form-control input-sm\" id=\"for\" name=\"for[]\" value=\""+$('#for_in').val()+"\" readonly/></div></div></td><td><input class=\"form-control\" id=\"suppliers_code\" name=\"suppliers_code[]\" value=\""+$('#suppliers_code_in').val()+"\" readonly/></td><td><input class=\"form-control\" id=\"cost\" name=\"cost[]\" value=\""+$('#cost_in').val()+"\" readonly/></td><td><input class=\"form-control\" id=\"total\" name=\"total[]\" value=\""+total+"\" readonly/></td><td><button type=\"button\" class=\"btn btn-default\" aria-label=\"Left Align\" onclick=\"$(this).parent().parent().remove();no_entries--;$('#no_entries').html(no_entries);$('#qty_in').focus();\" id=\"remove_row_btn\" name=\"remove_row_btn[]\"><span class=\"glyphicon glyphicon-minus-sign\" aria-hidden=\"true\"></span></button></td></tr>"
			);
			
			$('#qty_in').val("");
			$('#qty_in').parent().removeClass("has-error");
			$('#description_in').val("");	
			$('#description_in').parent().removeClass("has-error");		
			$('#suppliers_code_in').val("");
			$('#suppliers_code_in').parent().removeClass("has-error");
			$('#cost_in').val("");
			$('#cost_in').parent().removeClass("has-error");
			$('#for_in').val("");
		
			$('input[id=qty_in]').focus();
			no_entries++;
			$('#no_entries').html(no_entries);
		});		
	}
}

function add_items_from_family()
{
	var arrayData = $('#family_group_items_form').serializeArray();
	var j = 0;
	for( var i=1; i <= (arrayData.length / 5); i++ )
	{	
		var qty = Number(arrayData[j+2].value);
		if(!isNaN(qty) && qty > 0 )
			add_item_with(arrayData[j].value, qty, arrayData[j+3].value, arrayData[j+1].value, Number(arrayData[j+4].value));
		
		j += 5;			
	}
}

function add_item_with(item_id, qty, description, suppliers_code, cost)
{
	var total =  parseFloat(qty * cost).toFixed(2);
	suppliers_code = suppliers_code == "null" ? "": suppliers_code;
	$('#items tr').last().before(
			"<tr><td><input type=\"hidden\" id=\"item_id\" name=\"item_id[]\" value=\""+item_id+"\"/><input class=\"form-control\" id=\"qty\" name=\"qty[]\" value=\""+qty+"\" readonly/></td><td><input class=\"form-control\" id=\"description\" name=\"description[]\" value=\""+description+"\" readonly/><br/><div class=\"form-inline\"><div class=\"form-group\"><label for=\"for\">For </label> <input type=\"text\" class=\"form-control input-sm\" id=\"for\" name=\"for[]\" value=\"\" readonly/></div></div></td><td><input class=\"form-control\" id=\"suppliers_code\" name=\"suppliers_code[]\" value=\""+suppliers_code+"\" readonly/></td><td><input class=\"form-control\" id=\"cost\" name=\"cost[]\" value=\""+cost+"\" readonly/></td><td><input class=\"form-control\" id=\"total\" name=\"total[]\" value=\""+total+"\" readonly/></td><td><button type=\"button\" class=\"btn btn-default\" aria-label=\"Left Align\" onclick=\"$(this).parent().parent().remove();no_entries--;$('#no_entries').html(no_entries);$('#qty_in').focus();\" id=\"remove_row_btn\" name=\"remove_row_btn[]\"><span class=\"glyphicon glyphicon-minus-sign\" aria-hidden=\"true\"></span></button></td></tr>"
			);
	no_entries++;
	$('#no_entries').html(no_entries);
}

function submit_edit_purchase_order_form()
{
	$.ajax({
		type: "POST",
		url: '../save_order',
		data: $(this).serialize()
	}).done(function( response ) {
		if(response == "ok")
			window.location = '../../purchases_orders/edit/' + $('#order_id').val() ;
	}).fail(function(){
		alert("Request error");
	});
}

function fill_family_modal( member_id )
{
	$('#description_in').popover('destroy');
	$('#family_group_table tbody').html("");
	$('#family_group_modal').modal('show');
	
	$.get( "../../sales_stock/items_from_family_from_json/", { id : member_id })
	.done( function( data ) {	
		jQuery.each(data, function() {
			$('#family_group_table tbody').append("<tr><td><input type=\"hidden\" id=\"family_group_modal_item_id_in\" name=\"family_group_modal_item_id_in[]\" value=\""+Number(this.pk_id)+"\" /><input type=\"hidden\" id=\"family_group_modal_suppliers_code_in\" name=\"family_group_modal_suppliers_code_in[]\" value=\""+this.supplier_code+"\" />" + this.quantity_balance + "</td><td>"+this.quantity_on_order+"</td><td>"+Number(this.quantity_rec_level)+"</td><td><input type=\"text\" class=\"form-control\" id=\"family_group_modal_qty_in\" name=\"family_group_modal_qty_in[]\" value=\"\"/></td><td><input type=\"hidden\" id=\"family_group_modal_description_in\" name=\"family_group_modal_description_in[]\" value=\""+this.label+"\"/>"+this.label+"</td><td><input type=\"hidden\" id=\"family_group_modal_cost_in\" name=\"family_group_modal_cost_in[]\" value=\""+this.cost_price+"\" />"+this.cost_price+"</td><td></td></tr>");
		});		
		$('input[name^=family_group_modal_qty_in]:first').focus();
		
		$('input[name^=family_group_modal_qty_in]').keydown(function(e){
			if(e.which == 40)
			{
				$('input[name^=family_group_modal_qty_in]:first', $(this).parent().parent().next() ).focus();
			}else {
				if( e.which == 38)
					$('input[name^=family_group_modal_qty_in]:first', $(this).parent().parent().prev() ).focus();
			}
		});
	});
}

function validate_item_ins()
{
	var result = true;
	
	if( isNaN($('#qty_in').val()) || $('#qty_in').val() == "" || $('#qty_in').val() == "0" )
	{
		$('#qty_in').parent().addClass("has-error");
		$('#qty_in').focus();
		result = false;
	}else{
		$('#qty_in').parent().removeClass("has-error");
	}
	
	if( $('#hidden_name').val() != $('#description_in').val() || ($('#description_in').val() == "" && $('#description_in').val() == "")  )
	{
		$('#description_in').parent().addClass("has-error");
		$('#description_in').focus();
		result = false;
	}else{
		$('#description_in').parent().removeClass("has-error");
	}
	
	if( isNaN($('#cost_in').val()) || $('#cost_in').val() == "" )
	{
		$('#cost_in').parent().addClass("has-error");
		$('#cost_in').focus();
		result = false;
	}else {
		$('#cost_in').parent().removeClass("has-error");
	}
	
	return result;
}