<h1>Firedesk / Demo Company</h1>
<table style="width:100%">
<tr>
<td style="width:70%;"><h1>Purchase Order No. <?php echo $order_id; ?></h1></td>
<td style="text-align:right;width:30%;">
	<table align="right">
		<tr>
			<td style="text-align:center">
				<p><b>Order Date</b><br><?php echo date('d/m/Y - H:i', strtotime( $order_details->creation_date )); ?></p>
			</td>
		</tr>
		<tr>
		<td>&nbsp;
		</td>
		</tr>
		<tr>
			<td style="text-align:center">
				<p><b>Status</b><br>
				<span style="font-size:18px">
				<?php echo $order_details->fk_status == 1 ? "INCOMPLETE":""; ?>
				<?php echo $order_details->fk_status == 2 ? "COMPLETE":""; ?>
				<?php echo $order_details->fk_status == 3 ? "ABANDONED":""; ?>
				</span></p>
			</td>
		</tr>
	</table>
</td>
</tr>
</table>
<br>
<br>
<br>
<br>
<table>
	<tr>
		<td style="width:33%;"><h2>Supplier</h2></td>
		<td style="width:33%;"><h2>Delivery To</h2></td>
		<td style="width:33%;"><h2>Contact Name</h2></td>
	</tr>
	<tr>
		<td>
			<strong><?php echo $order_details->name; ?></strong><br>
			<?php echo $order_details->address1; ?><br>
			<?php echo $order_details->telephone1; ?><br>
			<?php echo $order_details->telephone2; ?>
		</td>
		<td>
			<?php echo $order_details->delivery_address; ?><br>
		</td>
		<td>
			<?php echo "$order_details->contact_name $order_details->contact_telephone"; ?><br>			
			<strong>Placed by: </strong><?php echo $order_details->operator; ?>
		</td>
	</tr>
</table>
<br>
<br>
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