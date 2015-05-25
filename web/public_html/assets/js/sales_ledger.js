$('#salesFrom, #salesTo').datepicker({format: 'dd / mm / yyyy'})
		.on('changeDate', function(ev){
			window.location = location.href.split("?")[0] + '?branchID='+$('#branch_id').val()+'&startDate='+$('#salesFrom').val()+'&endDate='+$('#salesTo').val();
		});