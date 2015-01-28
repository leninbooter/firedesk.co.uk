<h1>Family Groups</h1>
<table class="table table-hover table-responsive">
	<thead>
		<tr>
			<th style="width:40%">Family Name</th><th style="width:10%">Members</th><th style="width:30%">&nbsp;</th>
		</tr>
	</thead>
	<?php foreach($groups as $row): ?>
	<tr>
		<td><a href="#"><?php echo $row->name;?></a></td><td><?php echo $row->members;?></td><td><a href="<?php echo base_url('index.php/family_groups/delete/'.$row->pk_id); ?>" class="btn btn-default" role="button">Delete</a></td>
	</tr>
	<?php endforeach; ?>
</table>
<div class="panel panel-primary">
	<div class="panel-heading">Add family group</div>
	<div class="panel-body">
		<form class="form-inline" role="form" id="new_family_group" method="post" action="<?php echo base_url('index.php/family_groups/save_group'); ?>">
			<div class="form-group">
				<label for="name">Family Name:</label>
				<input type="text" class="form-control" id="name" name="name" value="<?php echo isset($name) ? $name:"" ?>"/>
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-primary  btn-block">Save</button>
			</div>
		</form>
	</div>
</div>