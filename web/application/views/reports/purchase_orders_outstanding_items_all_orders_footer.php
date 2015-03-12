<h1>Standing orders - General</h1>
<table cellspacing="3" cellpadding="2" style="width:100%">
	<tr>
		<th style="width:10%;border:solid 1px #000000;"><b>Qty</b></th>
		<th style="width:35%;border:solid 1px #000000; "><b>Description</b></th>
		<th style="width:15%;border:solid 1px #000000;text-align:center; "><b>Suppliers Code</b></th>
		<th style="width:15%;text-align:center;border:solid 1px #000000; "><b>Cost</b></th>
		<th style="width:20%;text-align:right;border:solid 1px #000000;"><b>Total</b></th>
	</tr>
	<?php foreach($items as $i): ?>
		<tr>
			<td style="text-align:left"><?php echo $i->qty; ?></td>
			<td style="text-align:left"><?php echo $i->description."<br>For ".$i->for; ?></td>
			<td style="text-align:center"><?php echo $i->suppliers_code; ?></td>
			<td style="text-align:center"><?php echo $i->cost; ?></td>
			<td style="text-align:right"><?php echo $i->total; ?></td>
		</tr>
	<?php endforeach; ?>													
</table>

<table cellspacing="3" cellpadding="2" style="width:100%">
<tr>
	<td style="width:10%;"><b></b></td>
	<td style="width:35%; "><b></b></td>
	<td style="width:15%;text-align:center; "><b></b></td>
	<td style="text-align:right;width:15%;">
	<b >Total</b>
	</td>
	<td style="text-align:right;width:20%;">
		<?php echo $order_details->total_amount; ?>
	</td>
</tr>
</table>
