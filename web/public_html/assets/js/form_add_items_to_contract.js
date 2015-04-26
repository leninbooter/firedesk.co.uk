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
                    $('#sale_item_qty').focus();
                }
                })
                .autocomplete( "instance" )._renderItem = function( ul, item ) {
                    return $( "<li>" )
                    .append( "<a>" + item.label + "</a>" )
                    .appendTo( ul );
        };
    });
    
    $("#sale_item_qty, #price").change(function(e) {
        
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


$('#cross_hired_form').submit( function(e) {
    
    e.preventDefault();
    
     $.ajax( base_url+"index.php/contracts/saveCrossHireItem", {
            type: "post",
            data: $(this).serialize(), 
            success: function(response) {
                       location.reload(true);
                    },
            statusCode: {
                400: function(r) {
                    alert(r.responseText);
                }
            },
            dataType: "text"}
            
    );
    
});

$('#hireItemForm').submit( function(e) {
    
    e.preventDefault();
    
    $.ajax( base_url+"index.php/contracts/saveHireItem", {
            type: "post",
            data: $(this).serialize(), 
            success: function(response) {
                        $('#hireFleetItemComponentsForm').submit();
                    },
            statusCode: {
                400: function(r) {
                    alert(r.responseText);
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
                        $('#hireFleetItemAccesoriesForm').submit();
                    },
            statusCode: {
                400: function(r) {
                    alert(r.responseText);
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
                       location.reload();
                    },
            statusCode: {
                400: function(response) {
                    alert(response);
                }
            },
            dataType: "text"}
            
    );    
});

$('#sales_items_form').submit( function(e) {
    e.preventDefault();
    
    $.ajax( base_url+"index.php/contracts/saveSaleItem", {
        
        type: "post",
        data: $(this).serialize(),
        success: function(r) {
            location.reload();
        },
        statusCode: {
            400: function(r) {
                alert(r.responseText);
            }
        },
        dataType: "text"
        }
    );
});

$('#hire_fleet_modal').on('shown.bs.modal', function(e) {
    $('#components_panel, #recommended_items_panel').html("");
    $('#search_hire_item_field, #hire_item_id, #hire_item_price').val("");
    $('#search_hire_item_field').focus();
});

$('#sales_stock_modal').on('shown.bs.modal', function(e) {
    
    $("#item_id").val("");
    $("#stock_no").text("");
    $("#sale_item_description").val("");
    $("#sale_item_in_stock").text("");
    $("#sale_item_on_order").text("");
    $("#sale_item_qty").val("");
    $("#price").val("");
    $("#sale_item_total").text("");
    
    $("#sale_item_description").focus();
    
});

$('[name^=rate]').change( function() {
    var rate = parseFloat($(this).val());
    
    $('[name^=day1], [name^=day2],[name^=day3],[name^=week],[name^=wend]', 
        $(this).parent().parent().parent()).each( function() {
            totalRateChargingBand(this, rate);
        });    
});

$(  '[name^=day1],\
    [name^=day2],\
    [name^=day3],\
    [name^=week],\
    [name^=wend]').change( function() {
        
        var rate = $('[name^=rate]', $(this).parent().parent().parent()) .val();
        totalRateChargingBand(this, rate);
    });

function chiQtyChanged(caller) {
    if ($(caller).val() > 0 ) {
        
        $('table', $(caller).parent().parent()).show();
    }else {
        $('table', $(caller).parent().parent()).hide();
    }
}
function deleteCrossHiredItem( caller ) {
    
    if (confirm('Are you sure you want to remove this item from the contract?')) {
        
        $.ajax(base_url + "index.php/contracts/removeCrossHiredItem", {
        type: "post",
        data: { itemRowID:  $('#itemRowID',      $(caller).parent().parent()).val(),
                contractID: $('#contract_id').val()
              },
        dataType: "text",
        success: function(r) {
            if ( r == "ok" ){
               $(caller).parent().parent().remove();
            }            
        },
        statusCode: {
            400: function(r) {
                alert('Please, reload the page and try again.');
            }
        }
        });
    }    
    
}

function deleteHireItem( caller ) {
    
    if (confirm('Are you sure you want to remove this item from the contract?')) {
        
        $.ajax(base_url + "index.php/contracts/removeHireItem", {
        type: "post",
        data: { itemRowID:  $('#itemRowID',      $(caller).parent().parent()).val(),
                contractID: $('#contract_id').val()
              },
        dataType: "text",
        success: function(r) {
            if ( r == "ok" ){
                var recursion = function removeMe(item){
				if( $('td:nth-child(3)', item).css('padding-left') == "20px" )
				{
					removeMe($(item).next());
					$(item).remove();
				}
			}
			
			recursion($(caller).parent().parent().next());
            $(caller).parent().parent().remove();
            }            
        },
        statusCode: {
            400: function(r) {
                alert('Please, reload the page and try again.');
            }
        }
        });
    }    
}

function deleteSaleItem( caller ) {
    
    if (confirm('Are you sure you want to remove this item from the contract?')) {
        
        $.ajax(base_url + "index.php/contracts/removeSaleItem", {
        type: "post",
        data: { itemRowID:  $('#itemRowID',      $(caller).parent().parent()).val(),
                contractID: $('#contract_id').val()
              },
        dataType: "text",
        success: function(r) {
            if ( r == "ok" ){
               $(caller).parent().parent().remove();
            }            
        },
        statusCode: {
            400: function(r) {
                alert('Please, reload the page and try again.');
            }
        }
        });
    }    
}    
    
function totalRateChargingBand( ele, rate ) {
    var p    = parseFloat($(ele).val());
    if ( !isNaN(rate) && !isNaN(p) ) {
        var total= ((rate*p)/100).toFixed(2);
        $('p', $(ele).parent().parent()).text( total );
    }
}

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
        
        $('#sale_item_total').text( parseFloat($('#price').val()*$('#sale_item_qty').val()).toFixed(2).toLocaleString() ) ;
    }
}