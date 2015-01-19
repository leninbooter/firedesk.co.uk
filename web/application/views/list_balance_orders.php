<h1>Balance Orders</h1>
<table class="table table-hover table-responsive">
	<thead>
		<tr>
			<th>Customer Name</th><th>Contract No.<br/></th><th>Creation Date</th><th>Date</th><th>Due Back</th>
		</tr>
	</thead>
	<?php foreach($contracts as $row): ?>
	<tr onclick="edit(<?php echo $row->pk_id;?>)">
		<td><?php echo $row->name;?></td><td><?php echo $row->pk_id;?></td><td><?php
			if(isset($row->creation_date))
			{
				$date = new DateTime( $row->creation_date );
				echo $date->format("F, d \of Y - H:i");
			}?></td><td><?php echo $row->date?></td><td><?php echo $row->dueback?></td>
	</tr>
	<?php endforeach; ?>
</table>