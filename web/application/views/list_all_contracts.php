<h1>Existing Contracts</h1>
<table class="table table-hover table-responsive">
	<thead>
		<tr>
			<th>Account Number</th><th>Customer Name</th><th>Contract No.</th><!--<th>Creation Date</th>--><th>Status</th>
		</tr>
	</thead>
	<?php foreach($contracts as $row): ?>
	<tr onclick="edit(<?php echo $row->pk_id;?>)">
		<td><?php echo $row->account_reference;?></td>
		<td><?php echo $row->name;?></td>
		<td><?php echo $row->pk_id;?></td>		
		<!--<td><?php
			/*if(isset($row->creation_date))
			{
				$date = new DateTime( $row->creation_date );
				echo $date->format("F, d \of Y - H:i");
			}*/?></td>-->
		<td><?php echo $row->status?></td>
	</tr>
	<?php endforeach; ?>
</table>