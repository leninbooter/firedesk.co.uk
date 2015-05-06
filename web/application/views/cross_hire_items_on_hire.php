<h1>Contracts containing cross hired items</h1>
<table class="table table-hover table-responsive">
	<thead>
		<tr>
			<th>Contract</th><th>Customer Name</th><th>Qty</th><th>Description</th><th>Date</th><th></th>
		</tr>
	</thead>
	<?php foreach($items as $row): ?>
        <tr onclick="edit(<?php echo $row->pk_id;?>)">
            <td><?php echo $row->pk_id;?></td>
            <td><?php echo $row->name;?></td>
            <td><?php echo $row->qty;?></td>
            <td><?php echo $row->description;?></td>				
            <td><?php echo $row->date_time; ?></td>
            <td><p class="text-center"><a class="btn btn-default btn-sm " href="<?php echo base_url('index.php/contracts/edit?id='.$row->pk_id); ?>" role="button">Load contract</a></p></td>
        </tr>
	<?php endforeach; ?>
</table>