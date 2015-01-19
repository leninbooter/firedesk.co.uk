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
<div class="row">
	<div class="col-md-12">
		<h2>Contract No. <?php echo $contract_id; ?></h2>
	</div>
</div>

<table style="width:100%">
	<tr><td><b>Client name</b></td><td> <?php echo $contract_details->name; ?></td><td><b>Delivery</b></td><td><b>Collect</b></td><td><b>Site Address</b></td></tr>
	<tr><td><?php 
				switch($contract_details->type)
				{
					case 1: echo "Cash"; break;
					case 2: echo "Credit"; break;
				}
			?></td><td></td><td><?php echo $contract_details->delivery_charge; ?></td><td></td><td><?php echo $contract_details->address; ?></td></tr>
	<tr><td></td><td></td><td colspan="2" style="text-align:center"><b>Order Number</b></td><td></td></tr>
	<tr><td></td><td></td><td colspan="2" style="text-align:center"><b>Job Ref./Order By</b></td><td></td></tr>
	<tr><td></td><td></td><td></td><td></td><td></td></tr>
</table>

		<table class="table table-hover table-responsive" id="items" style="width:100%">
			<thead>
				<tr>
					<th>Item No</th><th>Qty</th><th>Rtn Description</th><th>No entries</th><th>Rate per</th><th>Disc. %</th><th>Value</th>
				</tr>
			</thead>
			<?php foreach($contract_items as $row): ?>
				<tr>
					<td><?php echo $row->item_no; ?></td>
					<td><?php echo $row->qty; ?></td>
					<td><?php echo $row->description; ?></td>
					<td><?php echo $row->entries_no; ?></td>
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
					<td><?php echo $row->discount_perc; ?></td>
					<td><?php echo $row->value; ?></td>
				</tr>
			<?php endforeach; ?>
		</table>

<div class="row">
	<div class="col-md-12">
		<hr/>
	</div>
</div>