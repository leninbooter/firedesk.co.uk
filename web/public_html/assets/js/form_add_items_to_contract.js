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

    $("#sale_item_qty, #price, #disc").change(function(e) {

        total();
    });
}

if ( $('#hire_fleet_modal').length > 0 ) {
    
    $('#nsHICB').load(base_url+'index.php/hire_stock/getChargingBandsOptions');
    
    $('#hire_fleet_modal').on('shown.bs.modal', function(e) {
    
        resetHireFleetModal();
    });
    
    $('#hireItemForm').submit( function(e) {

    e.preventDefault();
    
    $(this).parent().parent().addClass('magictime loading');
    hireItemFormSendingAnimInterfavl = setInterval(function(){
                                                    $(this).parent().parent().toggleClass('magictime loading');
                                                }, 1000 );
    
    $.ajax( base_url+"index.php/contracts/saveHireItem", {
            type: "post",
            data: $(this).serialize(),
            always: function() {
                
                clearInterval(hireItemFormSendingAnimInterfavl);
                $(this).parent().parent().removeClass('magictime loading');
            },
            success: function(response) {
                if (response == "ok") {
                   if ( $('#hireItemType').val() != "Single" && $('#hireItemType').val() != "Multiple" ) {
                        
                        $('#hireFleetItemComponentsForm').submit();
                   }else {
                       
                        $('#hireFleetItemAccesoriesForm').submit();
                   }
                                  
               }else if (response == "allocated") {                    
                    
                    $.get( base_url+"index.php/contracts/getHireContractOf", { itemID: $('#hire_item_id').val() }, function(r) {
                        
                        $('#itemLabelP').html(r.fleet_number + "-" + r.description);
                        $('#contractIDP').html(r.pk_id);
                        $('#itemDateOutP').html(r.date_time);
                        $('#custNameAddressP').html(r.name+"<br>"+r.address);
                        $('#siteAddressP').html(r.delivery_address);
                    
                    }, "json");
                    
                    $('#addHire_btn').prop('disabled', true);
                    
                    $('#componentsRow').slideUp();
                    $('#recommendedRow').slideUp();
                    $('#allocatedOnRowForm').show();
                    
                    phInterval = setInterval(function(){ $('#allocatedOnRowForm .panel-heading').toggleClass('magictime tinRightOut'); }, 1000);                    
                    
                }else {

                    alert(response);
                }
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
                            if (response == "ok") {
                                    $('#hireFleetItemAccesoriesForm').submit();
                            }else {

                                alert(response);
                            }
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
                             if (response == "ok") {
                                   
                                   location.reload();
                            }else {

                                alert(response);
                            }
                        },
                statusCode: {
                    400: function(response) {
                        
                        alert(response.responseText);
                    }
                },
                dataType: "text"}

        );
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
                
                resetHireFleetModal();               
                
                $( "#hire_item_id" ).val( ui.item.id );
				$( "#search_hire_item_field" ).val( ui.item.label );
				$( "#hireItemType" ).val( ui.item.type );
                $( "#hire_item_price" ).val( ui.item.rate );
                
                if (ui.item.type == "Multiple") {
                    
                    $('#hireItemForm #search_hire_item_avbl_qty').val(ui.item.qty);
                    $('#hireItemForm #hire_item_qty').prop('disabled', false).focus();
                }else {
                    
                    $('#hireItemForm #hire_item_qty').prop('disabled', true);
                    $('#hireItemForm #hire_item_qty').val("1");
                    $( "#hire_item_price" ).focus();
                }
                
				
				

                $('#components_panel').parent().parent().addClass('magictime loading');
                componentsDivAnimInterval = setInterval(function(){
                                                                $('#components').parent().parent().toggleClass('magictime loading');
                                                            }, 1000 );
                $.get(base_url+"index.php/hire_stock/getMultipartItemComponentsContractForm/", { itemID:ui.item.id, contractID: $('#contract_id').val(), hireItemType: ui.item.type }, function(html){
                    $('#components_panel').html(html);
                      clearInterval(componentsDivAnimInterval);
                    $('#components_panel').parent().parent().removeClass('magictime loading');
                });


               $('#recommended_items_panel').parent().parent().addClass('magictime loading');
                accesoriesDivAnimInterval = setInterval(function(){
                                                                    $('#recommended_items_panel').parent().parent().toggleClass('magictime loading');
                                                                },
                                                       1000 );
				$.get(base_url+"index.php/hire_stock/getGroupAccesoriesContractForm/", { groupID: ui.item.family_id, contractID: $('#contract_id').val(), hireItemID:ui.item.id, hireItemType: ui.item.type }, function(html) {
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

$('#allocatedOnForm').submit( function(e) {
    
    e.preventDefault();
    
    $('#hireItemForm #allocated').val("yes");
    $('#hireItemForm').submit();
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


$('#sales_items_form').submit( function(e) {
    e.preventDefault();

    $.ajax( base_url+"index.php/contracts/saveSaleItem", {

        type: "post",
        data: $(this).serialize(),
        success: function(r) {
                    if (r == "ok") {
                        location.reload(true);
                    }else {

                        alert(r);
                    }
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


$('#sales_stock_modal').on('shown.bs.modal', function(e) {

    $("#item_id").val("");
    $("#stock_no").text("");
    $("#sale_item_description").val("");
    $("#sale_item_in_stock").text("");
    $("#sale_item_on_order").text("");
    $("#sale_item_qty").val("");
    $("#disc").val("");
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

function activate(  )
{
	if( confirm('Are you sure you want to activate this contract? No further hire items can be added after activation') )
	{
		$.ajax({
			type: "POST",
			url: "activate_contract",
			data: { contract_id: $('#contract_id').val() },
			dataType: "text"
			})
			.done(function( r ) {
				if( r != 'ko' )
				{	
					$('#alerts .modal-content').html( "<div class=\"modal-body\">The contract was activated successfully!</div><div class=\"modal-footer\"><button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button></div>" );
					$('#alerts .modal-content').css('background-color','#D6E9C6');
					location.reload(true);
				}else
				{
					$('#alerts .modal-content').html( "<div class=\"modal-body\">There was a problem activating the contract; please, try again.!</div><div class=\"modal-footer\"><button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button></div>" );	
					$('#alerts .modal-content').css('background-color', '#F2DEDE');
				}
			})
			.fail(function() {
				$('#alerts .modal-content').html( "<div class=\"modal-body\">There was a problem activating the contract; please, try again.!</div><div class=\"modal-footer\"><button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button></div>" );	
				$('#alerts .modal-content').css('background-color', '#F2DEDE');
			});
	}
}    
    
function chiQtyChanged(caller) {
    if ($(caller).val() > 0 ) {

        $('table', $(caller).parent().parent()).show();
    }else {
        $('table', $(caller).parent().parent()).hide();
    }
}

function contract_details_pdf() {
	
    $('#contract_details_content_iframe').attr("src", 'contractPDF?id=' + $('#contract_id').val());
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
    var itemType = $( '#item_type', $(caller).parent().parent()).val();
    var customerID = $( '#customerID').val();
    var qty = $(caller).val();

    $('#price, #disc', $(caller).parent().parent()).addClass('magictime loading');
    priceFieldInterval = setInterval(function(){
                                                    $('#price, #disc', $(caller).parent().parent()).toggleClass('magictime loading');
                                                }, 1000 );

    $.get( base_url+"index.php/sales_stock/getSalesPriceOf", 
            {itemType: itemType, itemID: itemID, customerID: customerID, qty: qty}, 
            function(response) {                
                $( '#price', $(caller).parent().parent()).val(response.price);
                $( '#disc',  $(caller).parent().parent()).val(response.discount+"%");                
                total();
            }, 
            "json")
            .done(function() {
                console.log('done');
            })
            .fail(function() {
                console.log('fail');
            })
            .always(function() {
                clearInterval(priceFieldInterval);
            });
    
    formatInputs();
}

function resetHireFleetModal() {
    
    $('#components_panel, #recommended_items_panel').html("");
    $(' #search_hire_item_field, #hire_item_id, \
        #hire_item_price, \
        #hireItemForm #search_hire_item_avbl_qty, \
        #hireItemForm #hire_item_qty').val("");
    
    
    $('#addHire_btn').removeAttr('disabled');
                    
    $('#componentsRow').show();
    $('#recommendedRow').show();
    $('#allocatedOnRowForm').hide();
    
    if (typeof phInterval !== 'undefined') {
        
        clearInterval(phInterval);
    }
    
    $('#search_hire_item_field').focus();
}

function total() {

    var discount = $('#disc').val().replace(/[^0-9\.]/g, '').replace('', '0');
    var price   = $('#price').val().replace('', '0');
    var qty     = $('#sale_item_qty').val().replace('', '0');    
    
    if ( !isNaN(price) && !isNaN(qty) && !isNaN(discount) ) {

        discount = parseFloat(discount);
        price   = parseFloat(price);
        qty     = parseInt(qty);
                   
        price = price - ((price*discount)/100);
        $('#sale_item_total').text( parseFloat(price*qty).toFixed(2).toLocaleString() ) ;
    }else {
        
        $('#sale_item_total').text( parseFloat(0).toFixed(2).toLocaleString() ) ;
    }
}