<h1>List customers / addresses</h1>
<table class="table table-hover table-responsive">
	<thead>
		<tr>
			<th>Customer Name</th><th>Addresses</th>
		</tr>
	</thead>
	<?php foreach($customers as $row): ?>
	<tr>
		<td><?php echo $row->name;?></td><td><?php echo $row->address;?></td>
	</tr>
	<?php endforeach; ?>
</table>