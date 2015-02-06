$( document ).ready(function() {
	$('#tabs a').click(function (e) {
	  e.preventDefault()
	  $(this).tab('show')
	});
	
	$('#tabs a[href="#holidays_schema"]').tab('show');
	
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