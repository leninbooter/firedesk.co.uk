$('input[id=qty_in]').focus();
$('#no_entries').html(no_entries);


$('#edit_purchase_order_form, #receptions_form').submit( function(e) {
	e.preventDefault();	
});

$('input[id=qty_in]').on('keyup', function(e){
	if( e.which == 13 )
	{
		$('input[id=description_in]').focus();
	}
}).change(function(){
	total_row();
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
		$('input[id=rate_in]').focus();
	};
});

$('input[id=rate_in]').on('keyup', function(e){
	if( e.which == 13 )
	{
		$('input[id=disc_in]').focus();
	};
}).change(function(){
	total_row();
});

$('input[id=disc_in]').on('keyup', function(e){
	if( e.which == 13 )
	{
		$('input[id=min_hire_days_in]').focus();
	}
}).change(function(){
	total_row();
});

$('input[id=min_hire_days_in]').on('keyup', function(e){
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
					$( "#rate_in" ).val( ui.item.cost_price );
					$( "#suppliers_code_in" ).val( ui.item.supplier_code );
					$( "#item_id_in" ).val( ui.item.pk_id );

					var options = { placement: 'top',
									html: true,
									template:  	'<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
									title: '',
									content: '<table class="table"><thead><tr><td>Stock</td><td>Rec Level</td><td>On Order</td></tr></thead><tbody><tr><td>'+ui.item.quantity_balance+'</td><td>'+ui.item.quantity_rec_level+'</td><td>'+ui.item.quantity_on_order+'</td></tr></tbody></table>',
									trigger: 'manual' };
					$('#description_in').popover(options);
					$('#description_in').popover('show');										
					total_row();
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
						  $('#level_items_table tbody').append("<tr><td><input type=\"hidden\" id=\"lvl_modal_item_id_in\" value=\""+Number(this.pk_id)+"\" /><input type=\"hidden\" id=\"lvl_modal_suppliers_code_in\" value=\""+this.supplier_code+"\" />" + this.quantity_balance + "</td><td>"+this.quantity_on_order+"</td><td>"+Number(this.quantity_rec_level)+"</td><td><input type=\"text\" class=\"form-control\" id=\"lvl_modal_qty_in\" value=\""+ (Number(this.quantity_rec_level) - Number(this.quantity_balance)) +"\"/></td><td><input type=\"hidden\" id=\"lvl_modal_description_in\" value=\""+this.label+"\"/>"+this.label+"</td><td><input type=\"hidden\" id=\"lvl_modal_rate_in\" vale=\""+this.cost_price+"\" />"+this.cost_price+"</td><td><button type=\"button\" class=\"btn btn-default\" aria-label=\"Left Align\" onclick=\"add_item_with("+this.pk_id+", $('#lvl_modal_qty_in', $(this).parent().parent()).val(), '"+this.label+"', '"+this.supplier_code+"', '"+this.cost_price+"');\" id=\"add_row_btn\" name=\"add_row_btn[]\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span></button></td></tr>");
					  }
				});				
});

$('#family_group_items_form').submit( function(e){
	e.preventDefault();
	add_items_from_family();
});


function abandon( order_id )
{
	if(confirm('Once you set this order as abandoned, you will not be able to re-use it; please, confirm the action.'))
	{
		$.ajax({
			type: "GET",
			url: '../abandon/'+order_id,
		}).done(function( response ) {
			if(response == "ok")
				window.location = '../../cross_hire/edit_order/' + order_id ;
			else
				alert(response);
		}).fail(function(){
			alert("Request error");
		});
	}
}

function add_item()
{
	if( validate_item_ins() ) {
		$('input[id=qty_in]').focus();
		
		$('#description_in').popover('destroy');
	
		$('#description_in').on('hidden.bs.popover', function () {
			
			var disc = parseFloat($('#disc_in').val()).toFixed(2);
			var min = parseInt($('#min_hire_days_in').val());			
			var rate = parseFloat($('#rate_in').val()).toFixed(2);
			var total;
			if( !isNaN(disc) )
			{
				var rate_reduced = parseFloat(rate - (rate*disc)/100).toFixed(2);
				total =  parseFloat($('#qty_in').val() * rate_reduced).toFixed(2);
			}else{
				total =  parseFloat($('#qty_in').val() * rate).toFixed(2);
			}
							
			//total_amount = Number(total_amount) + Number(total);			
			
			$('#items > tr').last().after(
			"<tr onclick=\"set_popover_to_tr(this, this, this,this)\"><input type=\"hidden\" id=\"delete\" name=\"delete[]\" value=\"no\"/><td style=\"width:10%\"\><input type=\"hidden\" id=\"item_id\" name=\"item_id[]\" value=\""+$( "#item_id_in" ).val()+"\"/><input class=\"form-control\" id=\"qty\" name=\"qty[]\" value=\""+$('#qty_in').val()+"\" readonly/></td><td style=\"width:30%\"><input class=\"form-control\" id=\"description\" name=\"description[]\" value=\""+$('#description_in').val()+"\" readonly/></td><td style=\"width:15%\"><input class=\"form-control\" id=\"suppliers_code\" name=\"suppliers_code[]\" value=\""+$('#suppliers_code_in').val()+"\" readonly/></td><td style=\"width:10%\"><input type=\"text\" class=\"form-control\" id=\"rate\" name=\"rate[]\" value=\""+rate+"\" readonly/></td><td style=\"width:10%\"><input type=\"text\" class=\"form-control\" id=\"disc\" name=\"disc[]\" value=\""+disc+"\" readonly/></td><td style=\"width:10%\"><input type=\"text\" class=\"form-control\" id=\"min\" name=\"min[]\" value=\""+min+"\" readonly/></td><td style=\"width:15%\"><input type=\"text\" class=\"form-control\" id=\"total\" name=\"total[]\" value=\""+total+"\" readonly/></td><td style=\"width:10%\"><button type=\"button\" class=\"btn btn-default\" aria-label=\"Left Align\" onclick=\"$(this).parent().parent().popover('destroy');$(this).parent().parent().remove();no_entries--;$('#no_entries').html(no_entries);$('#qty_in').focus();update_counters()\" id=\"remove_row_btn\" name=\"remove_row_btn[]\"><span class=\"glyphicon glyphicon-minus-sign\" aria-hidden=\"true\"></span></button></td></tr>");								
			
			$('#item_id_in').val("");
			$('#qty_in').val("");
			$('#qty_in').parent().removeClass("has-error");
			$('#description_in').val("");	
			$('#description_in').parent().removeClass("has-error");		
			$('#suppliers_code_in').val("");
			$('#suppliers_code_in').parent().removeClass("has-error");
			$('#rate_in').val("");			
			$('#rate_in').parent().removeClass("has-error");
			$('#disc_in').val("");
			$('#disc_in').parent().removeClass("has-error");
			$('#min_hire_days_in').val("");
			$('#min_hire_days_in').parent().removeClass("has-error");
			$('#total_in').html("0.00");
			
			update_counters();
		});		
	}
}

function submit_receptions_form()
{
	$.ajax({
		type: "POST",
		url: '../receive_items',
		data: $('#receptions_form').serialize()
	}).done(function( response ) {
		if(response == "ok")
			window.location = '../../purchases_orders/edit/' + $('#order_id').val() ;
		else
			alert(response);
	}).fail(function(){
		alert("Request error");
	});
}

function complete( order_id )
{
	if(saved_items < 1)
	{
		alert("There no items in the order, add items before set it as completed.");
		return;
	}
	
	if(confirm('Once you set this order as completed, you will not be able to add items; please, confirm the action.'))
	{
		$.ajax({
			type: "GET",
			url: '../complete/'+order_id,
		}).done(function( response ) {
			if(response == "ok")
				window.location = '../../cross_hire/edit_order/' + order_id ;
			else
				alert(response);
		}).fail(function(){
			alert("Request error");
		});
	}
}

function mark_to_delete( ele_button )
{	
	if( $(ele_button).parent().parent().hasClass('danger') )
	{
		$(ele_button).parent().parent().removeClass('danger');
		$('input[name^=delete]:first', $(ele_button).parent().parent()).val("no");
	}else {
		$(ele_button).parent().parent().addClass('danger');
		$('input[name^=delete]:first', $(ele_button).parent().parent()).val("yes");
	}
		
}

function submit_edit_cross_hire_order_form()
{
	$.ajax({
		type: "POST",
		url: '../save_order',
		data: $('#edit_cross_hire_order_form').serialize()
	}).done(function( response ) {
		if(response == "ok")
			window.location = '../../cross_hire/edit_order/' + $('#order_id').val() ;
		else
			alert(response);
	}).fail(function(){
		alert("Request error");
	});
}

function fill_family_modal( member_id )
{
	if(member_id != "")
	{
		$('#description_in').popover('destroy');
		$('#family_group_table tbody').html("");
		$('#family_group_modal').modal('show');
		
		$.get( "../../sales_stock/items_from_family_from_json/", { id : member_id })
		.done( function( data ) {	
			jQuery.each(data, function() {
				$('#family_group_table tbody').append("<tr><td><input type=\"hidden\" id=\"family_group_modal_item_id_in\" name=\"family_group_modal_item_id_in[]\" value=\""+Number(this.pk_id)+"\" /><input type=\"hidden\" id=\"family_group_modal_suppliers_code_in\" name=\"family_group_modal_suppliers_code_in[]\" value=\""+this.supplier_code+"\" />" + this.quantity_balance + "</td><td>"+this.quantity_on_order+"</td><td>"+Number(this.quantity_rec_level)+"</td><td><input type=\"text\" class=\"form-control\" id=\"family_group_modal_qty_in\" name=\"family_group_modal_qty_in[]\" value=\"\"/></td><td><input type=\"hidden\" id=\"family_group_modal_description_in\" name=\"family_group_modal_description_in[]\" value=\""+this.label+"\"/>"+this.label+"</td><td><input type=\"hidden\" id=\"family_group_modal_rate_in\" name=\"family_group_modal_rate_in[]\" value=\""+this.cost_price+"\" />"+this.cost_price+"</td><td></td></tr>");
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
	}else
	{
		alert("Please, select a member item and click again.");
	}
}

function purchase_order_pdf(order_id)
{
	$('#purchase_ord_pdf_modal_iframe').attr("src", '../print_order_pdf/' + order_id);
}

$('button[name^=remove_row_btn]').click(function(event){
	event.stopPropagation();
	mark_to_delete(this);
});


function set_popover_to_tr(parent, qty_used, qty_idle, qty_rtns, qty_offhire)
{	
		
	var options = { 
					animation: false,
					placement: 'right',
					html: true,
					template:  	'<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
					title: '',
					content: '<table class="table"><thead><tr><td>Used</td><td>Idle</td><td>Rtns</td><td>Off hire</td></tr></thead><tbody><tr><td>'+qty_used+'</td><td>'+qty_idle+'</td><td>'+qty_rtns+'</td><td>'+qty_offhire+'</td></tr></tbody></table>',
					trigger: 'click'
				};
	$(parent).popover(options);
	//$(parent).popover('show');
}

function total_row()
{
	var discount = parseFloat($('#disc_in').val()).toFixed(2);
	var rate = parseFloat($('#rate_in').val()).toFixed(2);
	if( !isNaN(discount) )
		{
			rate = (rate - (rate * discount)/100);
		}
	var total = parseFloat(parseInt($('#qty_in').val()) * rate).toFixed(2);
	
	if( !isNaN(total) ){
				
		$('#total_in').html(parseFloat(total).toFixed(2));
	}
}

function update_counters()
{
	var total_amount = 0;
	$('input[name^=total]').each(function(){
		total_amount += Number(parseFloat($(this).val()).toFixed(2));
	});
	total_amount = parseFloat(total_amount).toFixed(2);
	$('#total_amount_span').html(total_amount);
	$('#no_entries').html(parseFloat(no_entries).toFixed(0));
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
	
	if( isNaN($('#rate_in').val()) || $('#rate_in').val() == "" )
	{
		$('#rate_in').parent().addClass("has-error");
		$('#rate_in').focus();
		result = false;
	}else {
		$('#rate_in').parent().removeClass("has-error");
	}
	
	if( isNaN($('#disc_in').val()) || $('#disc_in').val() == "" )
	{
		$('#disc_in').val("0.00");
	}else {
		$('#disc_in').parent().removeClass("has-error");
	}
	
	if( isNaN($('#min_hire_days_in').val()) || $('#min_hire_days_in').val() == "" )
	{
		$('#min_hire_days_in').val("0");
	}else {
		$('#min_hire_days_in').parent().removeClass("has-error");
	}
	
	
	return result;
}

update_counters();