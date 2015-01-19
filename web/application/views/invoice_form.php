<form role="form" id="generate_invoice_form" method="post" action="<?php echo base_url('index.php/invoices/process'); ?>">
	<div class="row">
		<div class="col-md-5">
			<h1>Invoice</h1>
		</div>
		<div class="col-md-3"></div>
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
		<div class="col-md-4">
			<h2>Invoice No. <?php echo $invoice_id; ?></h2>
			<input type="hidden" name="invoice_id" id="invoice_id" value="<?php echo $invoice_id; ?>">
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
				<?php foreach($invoice_items as $row): ?>
				<?php $subtotal = 0; ?>
					<tr>
						<td><?php echo $row->item_no; ?></td>
						<input type="hidden" id="item_no" name="item_no[]" value="<?php echo $row->qty_supplied; ?>">
						<td><?php echo $row->qty_supplied; ?></td>
						<input type="hidden" id="qty_supplied" name="qty_supplied[]" value="<?php echo $row->qty_supplied; ?>">
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
	</form>

	<div class="row">
		<div class="col-md-8"></div>
		<div class="col-md-1"><button type="button" class="btn btn-default  btn-block" id="cancel_button">Cancel</button></div>
		<div class="col-md-1"><button type="button" class="btn btn-primary  btn-block" id="save_button">Save</button></div>
		<div class="col-md-2"><button type="button" class="btn btn-primary  btn-block" id="save_send_button">Save & Send via e-mail</button></div>
	</div>
	<input type="hidden" id="email_invoice" name="email_invoice" value="0">
	
	<div class="modal fade" id="payment_invoice" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4>Payment</h4>
			</div>
			<div class="modal-body">
			<div class="row">
				<div class="col-md-1"><label class="control-label">Date:</label></div>
				<div class="col-md-1"></div>
			</div>
			<div class="row">
				<div class="col-md-1"><label class="control-label">Reference:</div></div>
				<div class="col-md-1"><input class="form-control" type="text" id="payment_reference" name="payment_reference"></div>
			</div>
			<div class="row">
				<div class="col-md-1"><label type="text" class="control-label">Ammount:</div></div>
				<div class="col-md-1">"<?php echo $subtotal + ($subtotal*0.15);?></div>
			</div>
			<div class="row">
				<div class="col-md-1"><label class="control-label">Cash:</div></div>
				<div class="col-md-1"><input type="text" class="form-control" id="cash" name="cash"></div>
			</div>
			<div class="row">
				<div class="col-md-1"><label class="control-label">Cheque:</div></div>
				<div class="col-md-1"><input type="text" class="form-control" id="cheque" name="cheque"></div>
			</div>
			<div class="row">
				<div class="col-md-1"><label class="control-label">Card:</div></div>
				<div class="col-md-1"><input type="text" class="form-control" id="card" name="card"></div>
			</div>
			<div class="modal-footer">
				
			</div>
		</div>
	</div>
</div>
	
</form>

<div class="modal fade" id="contract_details" tabindex="-1" role="dialog" aria-labelledby="contract_details_label" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
			</div>
			<iframe style="width:100%; height:600px" id="contract_details_content_iframe" src=""></iframe>
		</div>
	</div>
</div>
<div class="modal fade" id="alerts" tabindex="-1" role="dialog" aria-labelledby="alerts_label" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">			
		</div>
	</div>
</div>
<div class="modal fade" id="outstanding_items" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">			
		</div>
	</div>
</div>
