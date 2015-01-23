<?php if(!empty($invoices)): ?>
	<table class="table table-hover table-responsive" id="items">
		<thead>
			<tr>
				<th>Date</th><th>Invoice No.</th><th>Value</th><th>Due</th><th><p class="text-center">Paid now</p></th>
			</tr>
		</thead>		
	<?php foreach($invoices as $row):?>
	<tr>
		<td><?php echo $row->creation_date; ?></td>		
		<td><?php echo $row->pk_id; ?></td>		
		<td><?php echo $row->total; ?></td>		
		<td><?php echo $row->unpaid_ammount; ?></td>		
		<td><p class="text-center"><button class="btn btn-info">Pay</button></p></td>		
	</tr>
	<?php endforeach; ?>
	</table>
<?php else: ?>
	<p class="text-center"><div class="alert alert-warning" role="alert">There are no invoices generated for this contract.</div></p>	
<?php endif; ?>