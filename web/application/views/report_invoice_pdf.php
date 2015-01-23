<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

		<link href="<?php echo base_url('assets/css/firedesk.css'); ?>" rel="stylesheet">		
		<style>
			body
			{
				font-family: "Arial";
			}
		</style>
		<title>FireDesk</title>
	</head>
	<body>
	<div class="container">
		<div class="masthead">
			<h3 class="text-muted">Firedesk / <img src="<?php echo base_url('assets/images/shell_logo.jpg');?>" style="width:3%"/> Shell</h3>			
		  </div>		  
	<table style="width:100%">
		<tr>
			<td style="vertical-align:top"><h1>Invoice</h1></td><td style="text-align:right; vertical-align:top"><h2>Invoice No. <?php echo $invoice_id; ?></h2><h2>Contract No. <?php echo $contract_id; ?></h2></td>
		</tr>
		<tr>
			<td>
			<h3>Client name <?php echo $customer_name; ?></h3>
			<h4><?php echo $contract_type; ?></h4>
			<h4>Delivery</h4>
			<?php echo $delivery_charge; ?>
			</td><td></td>
		</tr>
	</table>
	</div>

	<div class="row">
		<div class="col-md-12">
			<table class="table table-hover table-responsive" id="items" style="width:100%">
				<thead>
					<tr>
						<th>Item No</th><th>Qty</th><th>Rtn Description</th><th>No entries</th><th>Rate per</th><th>Disc. %</th><th>Value</th>
					</tr>
				</thead>
				<?php foreach($invoice_items as $row): ?>
				<?php $subtotal = 0; ?>
					<tr>
						<td><?php echo $row->item_no; ?></td>
						<input type="hidden" id="item_no" name="item_no[]" value="<?php echo $row->item_no; ?>">
						<td><?php echo $row->qty; ?></td>
						<input type="hidden" id="qty_supplied" name="qty_supplied[]" value="<?php echo $row->qty; ?>">
						<td><?php echo $row->description; ?></td>
						<input type="hidden" id="description" name="description[]" value="<?php echo $row->description; ?>">
						<td><?php echo $row->entries_no; ?></td>
						<input type="hidden" id="entries_no" name="entries_no[]" value="<?php echo $row->entries_no; ?>">
						<td><?php echo $row->rate;
								echo " ";
								switch( $row->regularity )
								{
									case 1: echo "Year"; break;
									case 2: echo "Month"; break;
									case 3: echo "Week"; break;
									case 4: echo "Day"; break;
								}
							?></td>
						<input type="hidden" id="regularity" name="regularity[]" value="<?php echo $row->regularity; ?>">
						<td><?php echo $row->discount_perc; ?></td>
						<input type="hidden" id="discount_perc" name="discount_perc[]" value="<?php echo $row->discount_perc; ?>">
						<td><?php echo $row->value; ?></td>
						<input type="hidden" id="value" name="value"[] value="<?php echo $row->value; ?>">
						<?php $subtotal += $row->value; ?>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>
	</div>
	<hr/>	
	
	<table style="">
		<tr>
			<td><b>Sub total: </b></td><td style="text-align:right"><?php echo number_format($subtotal, 2);?></td>
		</tr>
		<tr>
			<td><b>V.A.T: </b></td><td style="text-align:right"><?php echo number_format($subtotal * 0.15,2);?></td>
		</tr>
		<tr>
			<td><b>Total: </b></td><td style="text-align:right"><?php echo number_format($subtotal + ($subtotal*0.15),2);?></td>
		</tr>
	</table>
	<hr/>
	<h3>Payment</h3>
	<table style="">	
		<tr>
			<td><b>Cash: </b></td><td><?php echo isset($cash) ? $cash : 0.00;?></td>
		</tr>
		<tr>
			<td><b>Credit: </b></td><td><?php echo isset($credit) ? $credit : 0.00;?></td>
		</tr>
		<tr>
			<td><b>Cheque: </b></td><td><?php echo isset($cheque) ? $cheque : 0.00;?></td>
		</tr>
	</table>
	<div class="row">
		<div class="col-md-9">
			<h6>Reference:</h6><?php echo isset($reference) ? $reference : ""; ?>
		</div>
		<div class="col-md-1">
			
		</div>
		<div class="col-md-2">
			
			<input type="hidden" id="total" name="total" value="<?php echo $subtotal + ($subtotal*0.15);?>">
		</div>
	</div>	
</body>
</html>