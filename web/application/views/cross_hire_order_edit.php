<div class="row">
	<div class="col-xs-6"><h1>Cross Hire Order No. <?php echo $order_id; ?></h1></div>
	<div class="col-xs-6"></div>
</div>
<form id="edit_purchase_order_form" role="form" method="post" action="<?php echo base_url('index.php/cross_hire/save_order'); ?>"><input type="hidden" id="order_id" name="order_id" value="<?php echo $order_id; ?>"/>
	<div class="row">
		<div class="col-xs-9">
			<div class="row">
				<div class="col-xs-7">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Supplier</h3>
							<input type="hidden" id="supplier_id" name="supplier_id" value="<?php echo $supplier_id; ?>"/>
						</div>
						<div class="panel-body">
								<strong><?php echo $order_details->name; ?></strong><br>
								<?php echo $order_details->address1; ?><br>
								<?php echo $order_details->telephone1; ?><br>
								<?php echo $order_details->telephone2; ?>
						</div>
					</div>					
				</div>				
				<div class="col-xs-5">
					<div class="row">
						<div class="col-xs-12">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title">Delivery To:</h3>
								</div>
								<div class="panel-body">
									<?php echo $order_details->delivery_address; ?><br>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title">Contact Name:</h3>
								</div>
								<div class="panel-body">
									<?php echo "$order_details->contact_name $order_details->contact_telephone"; ?><br>									
								<strong>Placed by: </strong><?php echo $order_details->operator; ?>
								</div>
							</div>
						</div>
					</div>
										
				</div>
			</div>			
			<div class="row">
				<div class="col-xs-12">
					<table id="items" class="table table-hover table-condensed">
						<thead>
							<tr>
								<th style="width:10%">Qty</th>
								<th style="width:30%">Description</th>
								<th style="width:15%">Suppliers Code</th>
								<th style="width:10%">Rate</th>
								<th style="width:10%">Disc %</th>
								<th style="width:10%">Min Hire Days</th>
								<th style="width:15%">Total</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($items as $i): ?>
								<tr><input type="hidden" id="delete" name="delete[]" value="no"/><td><input type="hidden" id="item_id" name="item_id[]" value="<?php echo $i->fk_item_id; ?>"/><input type="text" class="form-control" id="qty" name="qty[]" value="<?php echo $i->qty; ?>" readonly/></td><td><input type="text" class="form-control" id="description" name="description[]" value="<?php echo $i->description; ?>" readonly/><br/><div class="form-inline"><div class="form-group"><label for="for">For </label> <input type="text" class="form-control input-sm" id="for" name="for[]" value="<?php echo $i->for; ?>" readonly/></div></div></td><td><input type="text" class="form-control" id="suppliers_code" name="suppliers_code[]" value="<?php echo $i->suppliers_code; ?>" readonly/></td><td><input type="text" class="form-control" id="cost" name="cost[]" value="<?php echo $i->cost; ?>" readonly/></td><td><input type="text" class="form-control" id="total" name="total[]" value="<?php echo $i->total; ?>" readonly/></td><td><?php if( $order_details->fk_status == 1): ?><button type="button" class="btn btn-default" aria-label="Left Align" onclick="mark_to_delete(this)" id="remove_row_btn" name="remove_row_btn[]"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button><?php endif; ?></td></tr>
							<?php endforeach; ?>
						</tbody>
						</table>
						<table class="table table-condensed" style="margin-bottom:0">
						<tr>							
							<td style="text-align:right; "><span style="font-size:120%;">Total </span><span id="total_amount_span" style="font-size:120%;"><?php echo $order_details->total_amount;?></span></td>
						</tr>
						</table>
						<?php if( $order_details->fk_status == 1): ?>
							<table class="table table-condensed">
							<tr class="success">
								<td style="width:10%"><input type="hidden" id="item_id_in" name="item_id_in"/><input type="hidden" id="hidden_name" name="hidden_name"/><input type="text" class="form-control" id="qty_in" name="qty_in" autocomplete="off"/></td>
								<td style="width:30%"><input type="text" class="form-control" id="description_in" name="description_in" autocomplete="off"/></td>
								<td style="width:15%"><input type="text" class="form-control" id="suppliers_code_in" name="suppliers_code_in" autocomplete="off"/></td>
								<td style="width:10%"><input type="text" class="form-control" id="rate_in" name="rate_in" autocomplete="off"/></td>
								<td style="width:10%"><input type="text" class="form-control" id="disc_in" name="disc_in" autocomplete="off"/></td>
								<td style="width:10%"><input type="text" class="form-control" id="min_hire_days_in" name="min_hire_days_in" autocomplete="off"/></td>
								<td style="width:15%; text-align:right; font-size: 120%; vertical-align:middle"><span id="total_in" autocomplete="off"></span></td>
								<td>&nbsp;</td>
							</tr>
							</table>								
						<?php endif; ?>
				</div>
			</div>
			<div class="row">			
				<div class="col-xs-1">
					<?php if( $order_details->fk_status == 1): ?>
						<button type="button" class="btn btn-default  btn-block" data-toggle="modal" data-target="#levels_modal">Copy</button>
					<?php endif; ?>
				</div>							
				<div class="col-xs-1">
					<button type="button" class="btn btn-default  btn-block" data-toggle="modal" data-target="#purchase_ord_pdf_modal" onclick="purchase_order_pdf(<?php echo $order_id; ?>)">Print</button>
				</div>				
				<div class="col-xs-2">
					<?php if( $order_details->fk_status == 1): ?>
						<button type="button" class="btn btn-default  btn-block" onclick="complete(<?php echo $order_id; ?>);">Complete</button>
					<?php endif; ?>
				</div>
				<div class="col-xs-2">
					<?php if( $order_details->fk_status  < 3): ?>
						<button type="button" class="btn btn-default  btn-block" onclick="abandon(<?php echo $order_id; ?>);">Abandon</button>
					<?php endif; ?>
				</div>
				<div class="col-xs-2">
					<?php if( $order_details->fk_status == 2): ?>
						<button type="button" class="btn btn-default  btn-block" data-toggle="modal" data-target="#receipts_modal">Receipts</button>
					<?php endif; ?>
				</div>
				<div class="col-xs-1">
				</div>
				<div class="col-xs-1">
						<a class="btn btn-default btn-block" role="button" href="<?php echo base_url('index.php/desk'); ?>">Exit</a>
				</div>
				<div class="col-xs-2">
						<button type="button" class="btn btn-primary  btn-block" onclick="submit_edit_purchase_order_form();">Save</button>
				</div>				
			</div>			
		</div>		
		
		<!-- Help -->
		<div class="col-xs-3">
			<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">Help</h3>				
			</div>
			<div class="panel-body">
				<dl>
				  <dt></dt><dd></dd>
				  <br/>				  
				</dl>
			</div>
		</div>
		</div>
		<!-- Help end -->
	</div>
</form>

<!-- Level Items Modal -->
<div class="modal fade" id="levels_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Items with balance below recommended levels</h4>
			</div>
			<div class="modal-body">
				<table id="level_items_table" class="table table-hover">
						<thead>
							<tr>
								<th style="width:5%">Balance</th>
								<th style="width:5%">Ordered</th>
								<th style="width:10%">Rec Stk</th>
								<th style="width:10%">Order</th>
								<th style="width:40%">Description</th>
								<th style="width:10%">Cost</th>
								<th style="width:10%"></th>
							</tr>
						</thead>
						<tbody>
						
						</tbody>
				</table>
			</div>
		</div>
	</div>
</div>


<!-- pdf purchase order modal -->
<div class="modal fade" id="purchase_ord_pdf_modal" tabindex="-1" role="dialog" aria-labelledby="purchase_ord_pdf_modal" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
			</div>
			<iframe style="width:100%; height:600px" id="purchase_ord_pdf_modal_iframe" src=""></iframe>
		</div>
	</div>
</div>


<?php if( $order_details->fk_status == 2): ?>
<!-- Receipts modal -->
<div class="modal fade" id="receipts_modal" tabindex="-1" role="dialog" aria-labelledby="receipts_modal" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form id="receptions_form" action="" role="form" >
			<input type="hidden" id="order_id" name="order_id" value="<?php echo $order_id; ?>"/>
			<div class="modal-header">
				<h4 class="modal-title">Outstanding items from this order</h4>
			</div>
			<div class="modal-body">				
					<table id="receipts_form_table" class="table table-hover table-condensed">
							<thead>
								<tr>
									<th style="width:10%">Total</th>
									<th style="width:10%">Received</th>
									<th style="width:40%">Description</th>
									<th style="width:10%">Due</th>
									<th style="width:10%">In now?</th>									
									<th style="width:20%">Cost</th>									
								</tr>
							</thead>
							<tbody>
								<?php foreach($items as $outstanding_item): ?>
									<?php if( $outstanding_item->qty_received < $outstanding_item->qty ):?>
										<tr>
											<td><?php echo $outstanding_item->qty; ?></td>
											<td><?php echo $outstanding_item->qty_received; ?></td>
											<td><?php echo $outstanding_item->description; ?></td>
											<td><?php echo $outstanding_item->qty-$outstanding_item->qty_received; ?></td>
											<td><input type="hidden" id="purchase_order_item_id" name="purchase_order_item_id[]" class="form-control" value="<?php echo $outstanding_item->pk_id ?>"/><input type="text" id="qty_in_now" name="qty_in_now[]" class="form-control" value="<?php echo $outstanding_item->qty-$outstanding_item->qty_received; ?>"/></td>
											<td><input type="text" id="cost" name="cost[]" class="form-control" value="<?php echo $outstanding_item->cost; ?>"/></td>
										</tr>
									<?php endif; ?>
								<?php endforeach; ?>
							</tbody>
					</table>				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onclick="submit_receptions_form()">Save</button>
			</div>
			</form>
		</div>
	</div>
</div>
<?php endif; ?>
<script>
	var total_amount = Number(<?php echo $order_details->total_amount; ?>);
	var no_entries = saved_items = Number(<?php echo count($items); ?>);
</script>