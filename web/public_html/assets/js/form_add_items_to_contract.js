if ( $('#sales_stock_modal').length > 0 ) {
    $.get( base_url + "index.php/sales_stock/getItemsNameAndIDJSON", function( data ) {
        
        items = data;
        
        $( "#sale_item_description" ).autocomplete({
            minLength: 0,
            source: items,
            focus: function( event, ui ) {
                $( "#sale_item_description" ).val( ui.item.label );
                    return false;
                },
                select: function( event, ui ) {				
                    $('#item_id').val(ui.item.pk_id);
                    $('#stock_no').text(ui.item.stock_number);
                    $('#sale_item_in_stock').text(ui.item.quantity_balance);
                    $('#sale_item_on_order').text(ui.item.quantity_on_order);
                }
                })
                .autocomplete( "instance" )._renderItem = function( ul, item ) {
                    return $( "<li>" )
                    .append( "<a>" + item.label + "</a>" )
                    .appendTo( ul );
        };
    });
    
    $("#sale_item_qty").change(function(e) {
        
        total();
    });
}

$.get( base_url + "index.php/hire_stock/get_items_json", function( data ) {
	
	suppliers = data;
	
	$( "#search_hire_item_field" ).autocomplete({
		minLength: 0,
		source: suppliers,
		focus: function( event, ui ) {
			$( "#search_hire_item_field" ).val( ui.item.label );
				return false;
			},
			select: function( event, ui ) {				
				$( "#hire_item_id" ).val( ui.item.id );
				$( "#hire_item_price" ).val( ui.item.rate );
				
                $('#components_panel').parent().parent().addClass('magictime loading');
                componentsDivAnimInterval = setInterval(function(){ 
                                                                $('#components').parent().parent().toggleClass('magictime loading');
                                                            }, 1000 );
                $.get(base_url+"index.php/hire_stock/getMultipartItemComponentsContractForm/", { itemID:ui.item.id, contractID: $('#contract_id').val() }, function(html){
                    $('#components_panel').html(html);
                      clearInterval(componentsDivAnimInterval);
                    $('#components_panel').parent().parent().removeClass('magictime loading');
                });
				
               
               $('#recommended_items_panel').parent().parent().addClass('magictime loading');
                accesoriesDivAnimInterval = setInterval(function(){ 
                                                                    $('#recommended_items_panel').parent().parent().toggleClass('magictime loading');
                                                                }, 
                                                       1000 );
				$.get(base_url+"index.php/hire_stock/getGroupAccesoriesContractForm/", { groupID: ui.item.family_id, contractID: $('#contract_id').val(), hireItemID:ui.item.id }, function(html) {
                    $('#recommended_items_panel').html(html);                                       
                    clearInterval(accesoriesDivAnimInterval);
                    $('#recommended_items_panel').parent().parent().removeClass('magictime loading');
                });                
                return false;
			}
			})
			.autocomplete( "instance" )._renderItem = function( ul, item ) {
				return $( "<li>" )
				.append( "<a>" + item.label + "</a>" )
				.appendTo( ul );
	};
});

$('#addItem_btn').click(function() {
    $('#hireItemForm').submit();
});

$('#hireItemForm').submit( function(e) {
    
    e.preventDefault();
    
    $.ajax( base_url+"index.php/contracts/saveHireItem", {
            type: "post",
            data: $(this).serialize(), 
            success: function(response) {
                        alert("ok");
                        $('#hireFleetItemComponentsForm').submit();
                    },
            statusCode: {
                400: function(response) {
                    alert(response);
                }
            },
            dataType: "text"}
            
    );
});

$('#hireFleetItemComponentsForm').submit(function(e) {
	e.preventDefault();
    
    $.ajax( base_url+"index.php/contracts/saveMultipartItemsComponents", {
            type: "post",
            data: $(this).serialize(), 
            success: function(response) {
                        alert("ok");
                        $('#hireFleetItemAccesoriesForm').submit();
                    },
            statusCode: {
                400: function(response) {
                    alert(response);
                }
            },
            dataType: "text"}
            
    );
});

$('#hireFleetItemAccesoriesForm').submit(function(e) {
	e.preventDefault();

     $.ajax( base_url+"index.php/contracts/saveHireItemAccesories", {
            type: "post",
            data: $(this).serializeArray(), 
            success: function(response) {
                        alert("ok");
                    },
            statusCode: {
                400: function(response) {
                    alert(response);
                }
            },
            dataType: "text"}
            
    );
    
});

$('#hire_fleet_modal').on('show.bs.modal', function(e) {
    $('#components_panel, #recommended_items_panel').html("");
    $('#search_hire_item_field, #hire_item_id, #hire_item_price').val("");
});

$('#sales_stock_modal').on('show.bs.modal', function(e) {
    $("#item_id").val("");
    $("#stock_no").text("");
    $("#sale_item_description").val("");
    $("#sale_item_in_stock").text("");
    $("#sale_item_on_order").text("");
    $("#sale_item_qty").val("");
    $("#price").val("");
    $("#sale_item_total").text("");
    
});

function getPrice(caller) {
    
    var itemID = $( '#item_id', $(caller).parent().parent()).val();
    var customerID = $( '#customerID').val();
    var qty = $(caller).val();
    
    $('#price', $(caller).parent().parent()).addClass('magictime loading');
    priceFieldInterval = setInterval(function(){ 
                                                    $('#price', $(caller).parent().parent()).toggleClass('magictime loading');
                                                }, 1000 );
                                                            
    $.get(base_url+"index.php/sales_stock/getSalesPriceOf", {itemID: itemID, customerID: customerID, qty: qty}, function(response) {
        $( '#price', $(caller).parent().parent()).val(response);
        clearInterval(priceFieldInterval);
        total();
    }, "text");    
}

function total() {
    
    if ( !isNaN($('#price').val()) && !isNaN($('#sale_item_qty').val()) ) {
        
        $('#sale_item_total').text( parseFloat($('#price').val()*$('#sale_item_qty').val()).toFixed(2) );
    }
}