var barChart2;

load_activity_chart($('#from').val(), $('#to').val(), $('#hire_item_id').val());

$(window).resize(function(){
	barChart2.resize();
});
	
$('#select_month_from, #select_year_from, #select_month_to, #select_year_to').change(function() {
	var from = $('#select_month_from').val() + "/" + $('#select_year_from').val();
	var to = $('#select_month_to').val() + "/" + $('#select_year_to').val();
	var item = $('#hire_item_id').val();
	
	load_activity_chart(from, to, item);
	//location.href=base_url+"index.php/hire_stock/activity?from="+from+"&to="+to+"&item="+item;
});

function load_activity_chart(from, to, item)
{
$('#chartDiv').html("<div class=\"jumbotron\"><img src=\""+base_url+"assets/images/ajax-loader.gif"+"\"/><p class=\"text-center\"></p></div>");
$.get(base_url+"index.php/hire_stock/get_item_activity_json", {activity_from: from, activity_to: to, activity_item: item}, function(json){
	if(json[0].id > 0)
	{
		$('#chartDiv').html("");
		barChart2 =  new dhtmlXChart({
		view:"bar",
		container:"chartDiv",
		value:"#hired#",
		label:"#hired#",
		color:"#66ccff",
		gradient:"rising",
		barWidth:25,		
		xAxis:{
			title:"",
			template:"#month#",
			lines: false
		},
		padding:{
			top:20,
			bottom:20,
			right:10,
			left:10
		}
		});
		barChart2.parse(json,"json");
	}
	else
	{
		$('#chartDiv').html("<div class=\"jumbotron\"><p class=\"text-center\">No data</p></div>");
	}
}, "json").fail(function(response) {
		$('#chartDiv').html("Bad request");
	});
	
}