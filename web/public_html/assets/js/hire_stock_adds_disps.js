var options= { backdrop:'static', keyboard:true, show:true};
var grid = new dhtmlXGridObject('gridbox');

$( document ).ready(function() {	
	grid.setImagePath("../../../assets/dhtmlx-4.13/skins/web/imgs/dhxgrid_web/");
	grid.setHeader("_item_id,_type,Qty,Disposed,Date,Cost each, Notes");
	grid.setInitWidths("0,0,100,100,100,100,500");
	grid.setColAlign(",,right,right,center,right,left"); 
	grid.setColTypes("ron,ron,dyn,ron,ron,ro"); 
	grid.setColSorting("int,int,int,int,str,str,str");
	grid.init();
	grid.load("../acqs_rms_json?itemID="+$('#item_id').val(), attachEvents,"json");
	grid.setColumnHidden(0,true);
	grid.setColumnHidden(1,true);

	grid.attachEvent("onRowDblClicked", function(rId,cInd){
		var disposed = parseInt(grid.cells(grid.getSelectedRowId(),3).getValue());
		disposed = isNaN(disposed) ? 0:disposed;
		var requested = parseInt(grid.cells(grid.getSelectedRowId(),2).getValue());
		var stock = requested - disposed;
		
		if(grid.cells(grid.getSelectedRowId(),1).getValue() == "1" &&  stock > 0) 
		{
			$('#remove_stock_modal').modal(options);
			$('#remove_stock_modal	#acquisition_id').val(grid.cells(grid.getSelectedRowId(),0).getValue());
			$('#remove_stock_modal	#acquisition_qty').text(grid.cells(grid.getSelectedRowId(),2).getValue());
			$('#remove_stock_modal	#acquisition_date').text(grid.cells(grid.getSelectedRowId(),4).getValue());
			$('#remove_stock_modal	#acquisition_cost').text(grid.cells(grid.getSelectedRowId(),5).getValue());
		}		
	});
	
	/*
	* Date 
	* ====
	*/
	var d = new Date();
	var weekday = new Array(7);
	var month = new Array(12);
	
	weekday[0]=  "Sun";
	weekday[1] = "Mon";
	weekday[2] = "Tue";
	weekday[3] = "Wed";
	weekday[4] = "Thu";
	weekday[5] = "Fri";
	weekday[6] = "Sat";
	month[0] = "january";
	month[1] = "february";
	month[2] = "march";
	month[3] = "april";
	month[4] = "may";
	month[5] = "june";
	month[6] = "july";
	month[7] = "august";
	month[8] = "september";
	month[9] = "october";
	month[10] = "november";
	month[11] = "december";

	$("#add_stock_modal, #remove_stock_modal").each(function(){
		var curr_form = this;
		$('[id=date_string]', curr_form).datepicker({format: 'yyyy-mm-dd'})
		.on('changeDate', function(ev){
			$(this).datepicker('hide');
			$('[id=date]', curr_form).val($('[id=date_string]', curr_form).val());
			var new_date = new Date($('[id=date_string]', curr_form).val());
			var nw = weekday[new_date.getDay()];
			$('[id=date_string]', curr_form).val(nw + ", " + new_date.getDate() + " of " + month[new_date.getMonth()] + " of " +  new_date.getFullYear());
		});
	});
	
	
		
	var nw = weekday[d.getDay()];
	$('[id=date_string]').val(nw + ", " + d.getDate() + " of " + month[d.getMonth()] + " of " +  d.getFullYear());
	$('[id=date]').val(d.getFullYear() +"-"+ (d.getMonth()+1) + "-" + d.getDate());
	
});

$('#add_stock_btn').click(function(){	
	$('#add_stock_modal').modal(options);
});

$('#add_stock_form').submit(function(event){
	event.preventDefault();
	$.ajax({
			type: "POST",
			url: '../save_acquisition',
			data: $(this).serialize(),
			dataType: "json"
		}).done(function( json ) {
			if(json.result == "ok")
			{
				location.reload();
							
			}else if( json.result == "ko" ){
				
				alert( json.error );
			}
			
		}).fail(function(){
			alert("Technical error");
		});		
});

$('#remove_stock_form').submit(function(event){
	event.preventDefault();
	$.ajax({
			type: "POST",
			url: '../save_disposal',
			data: $(this).serialize(),
			dataType: "json"
		}).done(function( json ) {
			if(json.result == "ok")
			{
				location.reload();
							
			}else if( json.result == "ko" ){
				
				alert( json.error );
			}
			
		}).fail(function(){
			alert("Technical error");
		});		
})

function attachEvents()
{
	
}
