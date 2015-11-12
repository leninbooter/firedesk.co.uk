var searchStart = 1;
var searchIdx;
var query;

$( document ).ready(function() {
	$('#tabs a').click(function (e) {
	  e.preventDefault()
	  $(this).tab('show')
	});
	
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
      e.target // newly activated tab
      e.relatedTarget // previous active tab
    })
    
    
	$('#tabs a[href="#access_rights"]').tab('show');
	
	//Holidays
    $('#jan_div').datepicker({
		startDate: "01/01/2015",
		endDate: "01/31/2015",
		multidate: true
	});
	$('#jan_div').datepicker()
		.on('changeDate', function(e){
			var days = "";
			$.each(e.dates, function(index, value) {
				days = days + e.format([index], "d,");
			});
			$('#jan').val(days);
    });
	
	$('#feb_div').datepicker({
		startDate: "02/01/2015",
		endDate: "02/28/2015",
		multidate: true
	});
	$('#feb_div').datepicker()
		.on('changeDate', function(e){
			var days = "";
			$.each(e.dates, function(index, value) {
				days = days + e.format([index], "d,");
			});
			$('#feb').val(days);
    });
	
	$('#mar_div').datepicker({
		startDate: "03/01/2015",
		endDate: "03/31/2015",
		multidate: true
	});
	$('#mar_div').datepicker()
		.on('changeDate', function(e){
			var days = "";
			$.each(e.dates, function(index, value) {
				days = days + e.format([index], "d,");
			});
			$('#mar').val(days);
    });
	
	$('#apr_div').datepicker({
		startDate: "04/01/2015",
		endDate: "04/30/2015",
		multidate: true
	});
	$('#apr_div').datepicker()
		.on('changeDate', function(e){
			var days = "";
			$.each(e.dates, function(index, value) {
				days = days + e.format([index], "d,");
			});
			$('#apr').val(days);
    });
	
	$('#may_div').datepicker({
		startDate: "05/01/2015",
		endDate: "05/31/2015",
		multidate: true
	});
	$('#may_div').datepicker()
		.on('changeDate', function(e){
			var days = "";
			$.each(e.dates, function(index, value) {
				days = days + e.format([index], "d,");
			});
			$('#may').val(days);
    });
	
	$('#jun_div').datepicker({
		startDate: "06/01/2015",
		endDate: "06/30/2015",
		multidate: true
	});
	$('#jun_div').datepicker()
		.on('changeDate', function(e){
			var days = "";
			$.each(e.dates, function(index, value) {
				days = days + e.format([index], "d,");
			});
			$('#jun').val(days);
    });
	
	$('#jul_div').datepicker({
		startDate: "07/01/2015",
		endDate: "07/31/2015",
		multidate: true
	});
	$('#jul_div').datepicker()
		.on('changeDate', function(e){
			var days = "";
			$.each(e.dates, function(index, value) {
				days = days + e.format([index], "d,");
			});
			$('#jul').val(days);
    });
	
	$('#aug_div').datepicker({
		startDate: "08/01/2015",
		endDate: "08/31/2015",
		multidate: true
	});
	$('#aug_div').datepicker()
		.on('changeDate', function(e){
			var days = "";
			$.each(e.dates, function(index, value) {
				days = days + e.format([index], "d,");
			});
			$('#aug').val(days);
    });
	
	$('#sep_div').datepicker({
		startDate: "09/01/2015",
		endDate: "09/30/2015",
		multidate: true
	});
	$('#sep_div').datepicker()
		.on('changeDate', function(e){
			var days = "";
			$.each(e.dates, function(index, value) {
				days = days + e.format([index], "d,");
			});
			$('#sep').val(days);
    });
	
	$('#oct_div').datepicker({
		startDate: "10/01/2015",
		endDate: "10/31/2015",
		multidate: true
	});
	$('#oct_div').datepicker()
		.on('changeDate', function(e){
			var days = "";
			$.each(e.dates, function(index, value) {
				days = days + e.format([index], "d,");
			});
			$('#oct').val(days);
    });
	
	$('#nov_div').datepicker({
		startDate: "11/01/2015",
		endDate: "11/30/2015",
		multidate: true
	});
	$('#nov_div').datepicker()
		.on('changeDate', function(e){
			var days = "";
			$.each(e.dates, function(index, value) {
				days = days + e.format([index], "d,");
			});
			$('#nov').val(days);
    });
	
	$('#dec_div').datepicker({
		startDate: "12/01/2015",
		endDate: "12/31/2015",
		multidate: true
	});
	$('#dec_div').datepicker()
		.on('changeDate', function(e){
			var days = "";
			$.each(e.dates, function(index, value) {
				days = days + e.format([index], "d,");
			});
			$('#dec').val(days);
    });
});

$('#inputCode, #inputDescription').keypress(function( event ) {
    
    if ( event.which == 13 ) {
        
        query = $(this).val();
        
        if ( $(this).attr('id') == 'inputCode' ) {
            
            searchIdx = '0';            
            
        }else {
            
            searchIdx = '2';
            
        }                                
        if ( searchAccount( searchIdx, query, 0) == 0) {
            
            options = {
                trigger:    'manual',
                placement: 'bottom',
                title:      'No found!',
                template:   '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
            };
            $(this).tooltip(options);
            $(this).tooltip('show');
            objThis = $(this).attr('id');
            setTimeout('$(\'#'+objThis+'\').tooltip(\'destroy\')', 2000);
        }
    }
        
    
});

$('#newAccountForm').submit(function(event) {
    
    event.preventDefault();
    
    obj = $(this).attr('id');
    url = base_url + 'index.php/accounting/addAccount';
    data = {
        inputCode: $('#inputCode').val(),
        inputDescription: $('#inputDescription').val()
    };
    callBackFn = function (){
        $('#'+obj).addClass('magictime tinDownOut');
        loadCOA();
        setTimeout( function() {
            $('input:text', $('#'+obj)).val('');
            searchAccount(0,data.inputCode,0 );
        }, 1000);        
    };
    
    callBackFnAlways = function() {
        
        setTimeout("$('#'+obj).removeClass('magictime tinDownOut')", 2000);
    }
    ajaxForm(url, data, callBackFn, callBackFnAlways);
});

function loadCOA() {
    
    $('#coa_DIV').load(base_url+'index.php/accounting/getCOAli');
}

function deleteAccount( accountCode, confirmated ) {
    
    if ( typeof confirmated == 'undefined' ) {
        confirmated = false;
    }
    
    url = base_url + 'index.php/accounting/remAccount';
    data = {
         accountCode: accountCode,
         confirmated: confirmated
    };
    
    $.ajax( url, 
        {
            type:       'post',
            data:       data,
            dataType:   'json'
        }
    )
    .done( function(r) {
        
        if ( r.result == 'ok' ) {
                    
            loadCOA();
            
        }else if ( r.result == 'ko') {
            
            alert(r.message);
        }else if ( r.result == 'confirmation' ) {
            
            if( confirm(r.message) ) {
                    
                    deleteAccount( accountCode, 'yes');
                
            }
        }
        
    })
    .fail( function( r ) {
        
        alert( 'Request failed!' );
    })
    .always( function() {
        
        //
    });   
}

function searchAccount ( searchIdx, query,  start) {
    
    var findings = 0;
    
    $( "#coa_DIV li" ).each(function( index ) {      
                    
        if ( ( searchIdx == '0' && $( "span:eq("+searchIdx+")", this).text().toLowerCase() == query.toLowerCase() )  || ( searchIdx == '2' && $( "span:eq("+searchIdx+")", this).text().toLowerCase().indexOf(query.toLowerCase()) != -1 ) ) {
            
            findings++;
            if ( findings >= start ) {                                
                $( this ).append("<div><span class=\"glyphicon glyphicon-search\" aria-hidden=\"true\" onclick=\"location.hash='#searchBox';$(this).parent().remove();\" style=\"cursor:pointer\"></span><span class=\"glyphicon glyphicon-chevron-right\" aria-hidden=\"true\" onclick=\"searchAccount(searchIdx, query, searchStart);$(this).parent().remove();\" style=\"cursor:pointer\"></span></div>");
                searchStart = findings + 1;    
                location.hash = '#'+$( "span:eq(0)", this).text();

                $( this ).animate({
                    backgroundColor: "#ffff00"
                }, 250 )
                .animate({
                    backgroundColor: 'none'
                },550);
                
                return false;
            }
        }
    
    });
    return findings;    
}