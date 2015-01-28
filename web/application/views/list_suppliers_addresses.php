<h1>List Suppliers & Addresses</h1>
<table class="table table-hover table-responsive">
	<thead>
		<tr>
			<th>Supplier Name</th><th>Address</th>
		</tr>
	</thead>
	<?php foreach($suppliers as $row): ?>
	<tr>
		<td><a href="<?php echo base_url('index.php/suppliers/new_existing/'.$row->pk_id);?>"><?php echo $row->name;?></a></td><td><?php echo $row->address1;?>, <?php echo $row->address2;?>, <?php echo $row->address3;?>, <?php echo $row->address4;?>, <?php echo $row->address5;?>, <?php echo $row->address6;?></td>
	</tr>
	<?php endforeach; ?>
</table>