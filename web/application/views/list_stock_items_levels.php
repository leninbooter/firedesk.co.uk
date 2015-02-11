<h1>Stock Levels</h1>
<table class="table table-hover table-responsive">
	<thead>
		<tr>
			<th>Stock Number</th>
			<th>Description</th>
			<th>Location</th>
			<th>Rec Level</th>
			<th>Balance</th>
			<th></th>
		</tr>
	</thead>
	<?php foreach($items as $row): ?>
	<tr  <?php echo $row->quantity_rec_level == $row->quantity_balance ? "class=\"warning\"":"" ?> <?php echo $row->quantity_rec_level > $row->quantity_balance ? "class=\"danger\"":""?>>
		<td><a href="<?php echo base_url('index.php/sales_stock/new_existing/'.$row->pk_id);?>"><?php echo $row->stock_number;?></a></td>
		<td><?php echo $row->description;?></td>
		<td><?php echo $row->location;?></td>
		<td><kbd><?php echo $row->quantity_rec_level;?></kbd></td>
		<td><kbd><?php echo $row->quantity_balance;?></kbd></td>
		<td>
			<?php if($row->quantity_rec_level == $row->quantity_balance): ?>
				<button type="button" class="btn btn-warning">
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				</div>
			<?php endif; ?>
			
			<?php if($row->quantity_rec_level > $row->quantity_balance): ?>
				<button type="button" class="btn btn-danger">
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				</div>
			<?php endif; ?>
		</td>
	</tr>
	<?php endforeach; ?>
</table>