<h1 style="text-align:center"><?php echo $company_name; ?></h1>
<h3 style="text-align:center">Hire Stock Price List</h3>
<h5 style="text-align:center">Date: <?php echo $printing_date; ?></h5>
<br>
<br/>
<br/>
<table>
	<tr>
		<th style="width:20%"><strong>Item type</strong></th>
		<th style="width:20%"><strong>Fleet number</strong></th>
		<th style="width:40%"><strong>Description</strong></th>		
		<th style="width:10%"><strong>Basic rate</strong></th>
		<th style="width:10%"><strong>Family</strong></th>
	</tr>
	<?php foreach($items as $key=>$i): ?>
		<tr style="<?php echo $key%2 != 0 ? "background-color:#f2f2f2":''; ?>">
			<td><?php echo $i->name_type; ?></td>
			<td><?php echo $i->fleet_number; ?></td>
			<td><?php echo $i->item_description; ?></td>
			<td><?php echo $i->basic_rate; ?></td>
			<td><?php echo $i->family; ?></td>
		</tr>
		<?php foreach($childs as $c): ?>
			<?php if($c->parent == $i->pk_id): ?>
				<tr style="<?php echo $key%2 != 0 ? "background-color:#f2f2f2":""; ?>">
					<td><?php echo $c->name_type; ?></td>
					<td><?php echo $c->fleet_number; ?></td>
					<td >&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $c->item_description; ?></td>
					<td><?php echo $c->basic_rate; ?></td>
					<td><?php echo $c->family; ?></td>
				</tr>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endforeach; ?>
</table>