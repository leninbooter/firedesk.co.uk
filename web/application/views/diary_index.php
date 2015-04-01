<div class="row">
	<div class="col-xs-6"><h1>Diary</h1>	
	</div>
</div>
<div class="row">
<div id="celndar_placeholder" class="col-md-3"></div>
<div id="events_placeholder" class="col-md-5">
	<div class="jumbotron">
	  <h1>Events</h1>	 
	</div>
</div>
<div id="neweventform_placerholder" class="col-md-4">
	<div class="panel panel-primary">
		<div class="panel-heading">New event</div>
		<div class="panel-body">
			<form class="form-horizontal">
				<div class="form-group">
					<label class="col-md-3 control-label">Title</label>
					<div class="col-md-9">
						<input type="text" id="title" name="title" class="form-control input-sm">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label">Description</label>
					<div class="col-md-9">
						<textarea id="description" name="description" class="form-control input-sm" rows="3"></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label">From</label>
					<div class="col-md-6">
						<input id="start_date" name="start_date" class="form-control input-sm" >
					</div>
					<div class="col-md-3">
						<input id="start_time" name="start_time" class="form-control input-sm" >
					</div>					
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label">To</label>
					<div class="col-md-6">
						<input id="end_date" name="end_date" class="form-control input-sm">
					</div>					
					<div class="col-md-3">
						<input id="end_time" name="end_time" class="form-control input-sm">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label">Guests</label>
					<div class="col-md-9">
						<input id="searcher" name="searcher" class="form-control input-sm" >
					</div>				
				</div>
				<button type="primary" class="btn btn-primary btn-block">Create event</button>
		</form>
	  </div>
	</div>
</div>
</div>