<form role="form" id="generate_invoice_form_preview" method="post" action="<?php echo base_url(''); ?>">
	<br/>
	<div class="row">
		<div class="col-md-4">
			<h1>Invoice</h1>
		</div>
		<div class="col-md-4">		 
		</div>
		<div class="col-md-4">
			<input type="hidden" name="contract_id" id="contract_id" value="<?php echo $contract_id; ?>">
			<h2>Contract No. <?php echo $contract_id; ?></h2>
		</div>
	</div>

	<div class="row">
		<div class="col-md-4">
			<input type="hidden" name="customer_name" id="customer_name" value="<?php echo $customer_name; ?>">
			<h3>Client name <?php echo $customer_name; ?></h3>
			<input type="hidden" id="contract_type" name="contract_type" value="<?php echo $contract_type; ?>">
			<h4><?php echo $contract_type; ?></h4>
			<input type="hidden" id="contract_type" name="contract_type" value="<?php echo $contract_type; ?>">
			<address>
				<strong>xxxxxxx xxxxxx</strong><br>
				xxx xxxxxxxx xxxxxxx xx
			</address>
		</div>
		<div class="col-md-4">
			<div class="row">
				<div class="col-md-4">
					<input type="hidden" id="delivery_charge" name="delivery_charge" value="<?php echo $delivery_charge; ?>">
					<h4>Delivery</h4>
					<?php echo $delivery_charge; ?>
				</div>
				<div class="col-md-4">
					<h4>Collect</h4>				
				</div>
			</div>
			<div class="row">
				<div class="col-md-8">
					<h4 class="text-center">Order Number</h4>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8">
					<h4 class="text-center">Job Ref./Order By</h4>
				</div>
			</div>
		</div>
		<div class="col-md-2">
			<div class="alert alert-warning" role="alert"><p class="text-center">Invoice preview</p></div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<table class="table table-hover table-responsive" id="items">
				<thead>
					<tr>
						<th>Item No</th><th>Qty</th><th>Rtn Description</th><th>No entries</th><th>Rate per</th><th>Disc. %</th><th>Value</th>
					</tr>
				</thead>
				<?php $subtotal = 0; ?>
				<?php foreach($invoice_items as $row): ?>
					<?php if($row->qty_supplied > 0): ?>					
					<tr>
						<input type="hidden" id="item_id" name="item_id[]" value="<?php echo $row->pk_id; ?>">
						<td><?php echo $row->item_no; ?></td>
						<input type="hidden" id="item_no" name="item_no[]" value="<?php echo $row->item_no; ?>">
						<td><?php echo $row->qty_supplied; ?></td>
						<input type="hidden" id="qty" name="qty[]" value="<?php echo $row->qty_supplied; ?>">
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
						<input type="hidden" id="rate_per" name="rate_per[]" value="<?php echo $row->rate; ?>">	
						<input type="hidden" id="regularity" name="regularity[]" value="<?php echo $row->regularity; ?>">
						<td><?php echo $row->discount_perc; ?></td>
						<input type="hidden" id="discount_perc" name="discount_perc[]" value="<?php echo $row->discount_perc; ?>">
						<td><?php echo $row->value; ?></td>
						<input type="hidden" id="value" name="value[]" value="<?php echo $row->value; ?>">
						<input type="hidden" id="item_type" name="item_type[]" value="<?php echo $row->item_type; ?>">
						<input type="hidden" id="disc" name="disc[]" value="<?php echo $row->discount_perc; ?>">
						<?php $subtotal += $row->value; ?>
						<?php endif;?>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<hr/>
		</div>
	</div>

	<div class="row">
		<div class="col-md-9">
		</div>
		<div class="col-md-2">
			<h4>Sub total</h4>
		</div>
		<div class="col-md-1">
			<p class="text-right"><?php echo number_format($subtotal, 2);?></p>
			<input type="hidden" id="subtotal" name="subtotal" value="<?php echo $subtotal; ?>">
		</div>
	</div>

	<div class="row">
		<div class="col-md-9">
		</div>
		<div class="col-md-2">
			<h4>V.A.T</h4>
		</div>
		<div class="col-md-1">
			<p class="text-right"><?php echo number_format($subtotal * 0.15,2);?></p>
			<input type="hidden" id="vat" name="vat" value="<?php echo $subtotal*0.15; ?>">
		</div>
	</div>

	<div class="row">
		<div class="col-md-9">
		</div>
		<div class="col-md-1">
			<h4>Total</h4>
		</div>
		<div class="col-md-2">
			<p class="text-right"><?php echo number_format($subtotal + ($subtotal*0.15),2);?></p>
			<input type="hidden" id="total" name="total" value="<?php echo $subtotal + ($subtotal*0.15);?>">
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<hr/>
		</div>
	</div>	

	<div class="row">
		<div class="col-md-11"></div>
		<div class="col-md-1"><button type="button" class="btn btn-default  btn-block" id="cancel_button">Cancel</button></div>		
	</div>
	<input type="hidden" id="email_invoice" name="email_invoice" value="0">	
</form>