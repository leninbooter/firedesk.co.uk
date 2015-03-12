<h2><?php echo $order->name; ?></h2>
<b>Date: </b><?php echo $order->creation_date; ?>
<br>
<br>
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
	<tr>
		<td colspan="3"></td>
		<td style="text-align:right; font-size:130%"><b>Total</b></td>
		<td style="text-align:right; font-size:130%"><?php echo $order->total_amount; ?></td>
	</tr>
</table>
