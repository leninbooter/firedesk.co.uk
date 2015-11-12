if ( $('#hire_fleet_modal').length > 0 ) {
    
    $('#nsHICB').load(base_url+'index.php/hire_stock/getChargingBandsOptions');
    
    $('#hire_fleet_modal').on('shown.bs.modal', function(e) {
    
        resetHireFleetModal();
        
        // Set hire fleet searcher
        $( "#search_hire_item_field" )
            .select2({
                    ajax: {
                            url: base_url + 'index.php/hire_stock/get_items_like_json',
                            processResults: function (data) {
                              return {
                                results: data
                              };
                            }
                          },
                    minimumInputLength: 2
                    
                })
                .on("select2:select", function (e) { 
                    
                    //resetHireFleetModal();                                       
                    
                     $.get( base_url + 'index.php/hire_stock/getitem/', { id: e.params.data.id },
                    function(result) {
                        
                        $( "#hire_item_id" ).val( result.pk_id );
                        $( "#hireItemType" ).val( result.type );
                        $( "#hire_item_price" ).val( result.rate );
                        
                        if (result.type == "Multiple") {
                        
                            $('#hireItemForm #search_hire_item_avbl_qty').val(result.qty);
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
                        $.get(base_url+"index.php/hire_stock/getMultipartItemComponentsContractForm/", { itemID:result.pk_id, contractID: $('#contract_id').val(), hireItemType: result.type }, function(html){
                            $('#components_panel').html(html);
                              clearInterval(componentsDivAnimInterval);
                            $('#components_panel').parent().parent().removeClass('magictime loading');
                        });


                       $('#recommended_items_panel').parent().parent().addClass('magictime loading');
                        accesoriesDivAnimInterval = setInterval(function(){
                                                                            $('#recommended_items_panel').parent().parent().toggleClass('magictime loading');
                                                                        },
                                                               1000 );
                        $.get(base_url+"index.php/hire_stock/getGroupAccesoriesContractForm/", { groupID: result.family_group_id, contractID: $('#contract_id').val(), hireItemID:result.pk_id, hireItemType: result.type }, function(html) {
                            $('#recommended_items_panel').html(html);
                            clearInterval(accesoriesDivAnimInterval);
                            $('#recommended_items_panel').parent().parent().removeClass('magictime loading');
                        });    
                        
                    },
                    'json');

                }); 
        
        /*$.get( base_url + "index.php/hire_stock/get_items_json", function( data ) {

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
        });*/
        
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
                         if ( response == "ok" ) {
                               
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
    
    $.post(base_url + 'index.php/contracts/savesaleitem',
        $(this).serialize(),
        function(r) {
            if (r == "ok") {
                location.reload(true);
            }else {

                alert(r);
            }
        },
        'text'
    )
    .fail(function() {
        
        alert('Technical error!');
    });
    
});


$('#sales_stock_modal').on('shown.bs.modal', function(e) {
    
    $('#sale_item_description')
        .select2({
            ajax: {
                    url: base_url + 'index.php/sales_stock/getitemsnameandidjson',
                    processResults: function (data) {
                      return {
                        results: data
                      };
                    }
                  },
            minimumInputLength: 2
            
        })
        .on("select2:select", function (e) { 
            
            $.get( base_url + 'index.php/sales_stock/getitem/'+ e.params.data.id, {},
            function(result) {
                
                $('#item_id').val(result.pk_id);
                $('#stock_no').text( result.stock_number);
                $('#sale_item_in_stock').text(result.quantity_balance);
                $('#sale_item_on_order').text(result.quantity_on_order);
                $('#sale_item_cost').val(result.cost);
                $('#sale_item_desc_text').val(result.label);
                $('#sale_item_qty').focus();
                
            },
            'json');
        
        }); 
    
    $("#sale_item_qty, #price, #disc").change(function(e) {

        total();
    });
    
    /* $("#item_id").val("");
    $("#stock_no").text("");
    $("#sale_item_in_stock").text("");
    $("#sale_item_on_order").text("");
    $("#sale_item_qty").val("");
    $("#disc").val("");
    $("#price").val("");
    $("#sale_item_total").text(""); */
       

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
        data: { 
                itemRowID:  $('#itemRowID',      $(caller).parent().parent()).val(),
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

function generateInvoice( type ) {
    
    if ( type == "All") {
 
        type = "All";
        
    }else if ( type == "Off") {
        
        type = "Off";
    }else {
        
        return;
    }
    
    $.ajax(base_url + "index.php/invoices/generate", {
            type: "post",
            data: { type:  type,
                    contract_id: $('#contract_id').val()
                  },
            dataType: "json",
            success: function(json) {
                if ( json.result == "ok" ){
                   location.href = base_url+'index.php/invoices/view?iid='+json.invoiceID;
                   
                }else if ( json.result== "ko") {
                    alert(json.message);
                }
            },
            fail: function(r) {
                
                alert(r);
            },
            statusCode: {
                400: function(r) {
                     alert('Please, try again the operation.');
                }
            }
    });
}

function newCollect() {

     $('#multipurposeModal').modal({
        backdrop: 'static',
        keyboard: false,
        show:     true        
    });
    
    $('#multipurposeModal .modal-dialog').addClass('magictime loading');
    collectModalInterval = setInterval(function(){
                                                    $('#multipurposeModal .modal-dialog').toggleClass('magictime loading');
                                                }, 1000 );

    $.post(base_url+'index.php/collects/collect', { contractID: $('#contract_id').val() }, function(html) {
        $('#multipurposeModal .modal-title').html('Collect');
        $('#multipurposeModal .modal-body').html(html);
    }) 
    .always(function() {
        clearInterval(collectModalInterval);
    }); 
}


function newHiredReturns() {

     $('#multipurposeModal').modal({
        backdrop: 'static',
        keyboard: false,
        show:     true        
    });
    
    $('#multipurposeModal .modal-dialog').addClass('magictime loading');
    collectModalInterval = setInterval(function(){
                                                    $('#multipurposeModal .modal-dialog').toggleClass('magictime loading');
                                                }, 1000 );

    $.post(base_url+'index.php/contracts/returnHiredItems', { contractID: $('#contract_id').val() }, function(html) {
        $('#multipurposeModal .modal-title').html('Return');
        $('#multipurposeModal .modal-body').html(html);
        
    }) 
    .always(function() {
        clearInterval(collectModalInterval);
    });              
}

function pay(event, invoiceID ) {
    event.stopPropagation();
    $('#multipurposeModal').modal({
        backdrop: 'static',
        keyboard: false,
        show:     true        
    });
    
    $('#multipurposeModal .modal-dialog').addClass('magictime loading');
    
    collectModalInterval = setInterval(function(){
                                                    $('#multipurposeModal .modal-dialog').toggleClass('magictime loading');
                                                }, 1000 );;

   $.get(base_url+'index.php/invoices/pay', { invoiceID: invoiceID }, function(html) {
         $('#multipurposeModal .modal-title').html('Payment');
         $('#multipurposeModal .modal-body').html(html);
         clearInterval(collectModalInterval);
         $.getScript( base_url + 'assets/js/payments.js');
    });    
 
}

function returnSoldItems() {

     $('#multipurposeModal').modal({
        backdrop: 'static',
        keyboard: false,
        show:     true        
    });
    
    $('#multipurposeModal .modal-dialog').addClass('magictime loading');
    collectModalInterval = setInterval(function(){
                                                    $('#multipurposeModal .modal-dialog').toggleClass('magictime loading');
                                                }, 1000 );

    $.post(base_url+'index.php/contracts/returnSoldItems', { contractID: $('#contract_id').val() }, function(html) {
        $('#multipurposeModal .modal-title').html('Return');
        $('#multipurposeModal .modal-body').html(html);        
    })
    .always(function() {
        clearInterval(collectModalInterval);
    });              
}

function pastCollect() {
    
     $('#multipurposeModal').modal({
        backdrop: 'static',
        keyboard: false,
        show:     true        
    });
    
    $('#multipurposeModal .modal-dialog').addClass('magictime loading');
    
    collectModalInterval = setInterval(function(){
                                                    $('#multipurposeModal .modal-dialog').toggleClass('magictime loading');
                                                }, 1000 );;

   $.get(base_url+'index.php/collects/past', {contractID: $('#contract_id').val() }, function(html) {
         $('#multipurposeModal .modal-title').html('Collect');
         $('#multipurposeModal .modal-body').html(html);
         clearInterval(collectModalInterval);
    });
    
   
}

function pastInvoices() {
    
     $('#multipurposeModal').modal({
        backdrop: 'static',
        keyboard: false,
        show:     true        
    });
    
    $('#multipurposeModal .modal-dialog').addClass('magictime loading');
    
    collectModalInterval = setInterval(function(){
                                                    $('#multipurposeModal .modal-dialog').toggleClass('magictime loading');
                                                }, 1000 );;

   $.get(base_url+'index.php/invoices/past', {contractID: $('#contract_id').val() }, function(html) {
         $('#multipurposeModal .modal-title').html('Invoices');
         $('#multipurposeModal .modal-body').html(html);
         clearInterval(collectModalInterval);
    });
    
   
}

function totalRateChargingBand( ele, rate ) {
    var p    = parseFloat($(ele).val());
    if ( !isNaN(rate) && !isNaN(p) ) {
        var total= ((rate*p)/100).toFixed(2);
        $('p', $(ele).parent().parent()).text( total );
    }
}

function getPrice(caller) {

    var itemID =    $( '#sale_item_description', $(caller).parent().parent()).val();
    var itemType =  1;
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
    
}

function submitCollect() {
    
    $.ajax( base_url+'index.php/collects/collect',
            {
                type: 'post',
                data: $('#collectForm').serialize(),
                success: function(r) {
                            if ( r=="ok") {
                                
                                location.reload(true);
                                
                            }else {
                                
                                alert(r);
                            }
                            
                        },
                fail: function(r) {
                        alert(r.responseText);
                    },
                statusCode: {
                    400: function(r) {
                        alert(r.responseText);
                    }
                },
                datatYpe: 'json'
            });
};

function submitReturn() {
    
    $.ajax( base_url+'index.php/returns/save',
            {
                type: 'post',
                data: $('#returnForm').serialize(),
                success: function(r) {
                            if ( r=="ok") {
                                
                                location.reload(true);
                                
                            }else {
                                
                                alert(r);
                            }
                            
                        },
                fail: function(r) {
                        alert(r.responseText);
                    },
                statusCode: {
                    400: function(r) {
                        alert(r.responseText);
                    }
                },
                datatYpe: 'json'
            });
};

function total() {

    var discount = $('#disc').val().replace(/[^0-9\.]/g, '').replace('', '0');
    var price   = $('#price').val().replace('', '0');
    var qty     = $('#sale_item_qty').val().replace('', '0');    
    
    if ( !isNaN(price) && !isNaN(qty) && !isNaN(discount) ) {
        
        discount = parseFloat(discount);
        price   = parseFloat(price);
        qty     = parseInt(qty);
                   
        price = price - ((price*discount)/100);
        $('#sale_item_total').html( parseFloat(price*qty).toFixed(2).toLocaleString() ) ;
        
    }else {
        
        $('#sale_item_total').html( parseFloat(0).toFixed(2).toLocaleString() ) ;
    }
}