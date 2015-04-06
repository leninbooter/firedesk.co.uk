$(document).ready(function(){    
	$('#celndar_placeholder').datepicker({
    todayBtn: "linked"
    })
	.on('changeDate', function(event){
		var selected_date = new Date($(this).datepicker('getDate'));
		load_events_of(("0"+selected_date.getDate()).toString().slice(-2)+"-"+("0"+(selected_date.getMonth()+1)).toString().slice(-2)+"-"+selected_date.getFullYear());
	});
	$('#celndar_placeholder').datepicker('update', "0"+(now.getMonth()+1).toString().slice(-2)+"-"+"0"+now.getDate().toString().slice(-2)+"-"+now.getFullYear());
	
	$('#start_date, #end_date').datepicker({format: 'dd-mm-yyyy'})
		.on('changeDate', function(ev){
			$(this).datepicker('hide');
			var new_date = new Date();
			$('input', $(this).parent().next().next()).focus();
		});

	//Contract time
	//$('#time').val(d.getHours() + ":" + d.getMinutes());
	
	$.get( base_url +"index.php/users/get_all_users_json", function( data ) {
		var items = data;
		
		$( '#searcher' ).autocomplete({
			minLength: 0,
			source: items,
			focus: function( event, ui ) {
				//$('#messenger_new_message_form #to_string').val( ui.item.label );
					return false;
				},
				select: function( event, ui ) {
					$('#searcher').val( ui.item.label );
					$('#guests_list_li').append(
												"<li><input type=\"hidden\" id=\"guest\" name=\"guest[]\" value=\""+ui.item.pk_id+"\"><div class=\"form-inline\" style=\" float:right; width:100%\"><button type=\"button\" class=\"close\" aria-label=\"Close\" onclick=\"$(this).parent().parent().remove();\"><span aria-hidden=\"true\">&times;</span></button>"+ui.item.label+"</div></li>"
												);
					$('#searcher').val( "" );
					return false;
				}
			})
			.keyup(function() {
				var $parentContext = $(this);
			})
			.autocomplete( "instance" )._renderItem = function( ul, item ) {
				return $( "<li>" )
				.append( "<a>" + item.label + "</a>" )
				.appendTo( ul );
			};
	});
	
	load_events_of(("0"+now.getDate()).toString().slice(-2)+"-"+("0"+(now.getMonth()+1)).toString().slice(-2)+"-"+now.getFullYear());
});

$('#newevent_form').submit(function(event){
	event.preventDefault();
	$('#neweventform_placerholder').addClass('magictime rotateLeft');
	$.ajax({
		type: "POST",
		url: 'diary/save_event',
		data: $(this).serialize(),
		dataType: "json"
	}).done(function( json ) {
		if(json.result == "ok")
		{			
			$("#newevent_form")[0].reset();
			$("#newevent_form #guests_list_li").html("");
			setTimeout(function(){ 
				$('#neweventform_placerholder').removeClass('magictime rotateLeft');
			}, 1500 );
		}
		else if(json.result = "ko"){
			$('#neweventform_placerholder').removeClass('magictime rotateLeft');
			alert(json.error);
		}
		
	}).fail(function(){
		$('#neweventform_placerholder').removeClass('magictime rotateLeft');
		alert("Technical error");
	});	
});

function delete_event(obj,id)
{
	if(confirm('Are you sure you want to delete this event? None guests will be able to see it again.'))
	{
		$(obj).parent().parent().parent().hide();
		$.ajax({
			type: "GET",
			url: 'diary/delete_event',
			data: {event_id:id},
			dataType: "json"
		}).done(function( json ) {
			if(json.result == "ok")
			{
				$(obj).parent().parent().parent().remove();
			}
			else if(json.result = "ko"){
				alert(json.error);
			}
			
		}).fail(function(){
			alert("Technical error");
		});
	}
		
}

function load_events_of(date)
{	
	$('#events_placeholder')
		.html("<div class=\"jumbotron\"><h1>Events</h1><img src=\""+base_url+"assets/images/ajax-loader.gif"+"\"/><p class=\"text-center\"></p></div>")
		.load(base_url+"index.php/diary/events_of/"+date, function(data){
			if(data == "")
				$(this).html("<div class=\"jumbotron\"><h1>Events</h1><p>No events for the selected date</p></div>");
		});
}