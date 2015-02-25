<div class="row">
	<div class="col-md-12">&nbsp;</div>
</div>
<div class="row">
	<div class="col-md-6"><h1>Purchase Order No. <?php echo $order_id; ?></h1></div>
	<div class="col-md-6"></div>
</div>
<form id="edit_purchase_order_form" role="form" method="post" action="<?php echo base_url('index.php/purchases_orders/save_order'); ?>"><input type="hidden" id="order_id" name="order_id" value="<?php echo $order_id; ?>"/>
	<div class="row">
		<div class="col-md-9">
			<div class="row">
				<div class="col-md-7">
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
					<p class="text-left"><small>No entries: <span id="no_entries"><?php echo !empty($items) ? count($items) : "0"; ?></span></small></p>
				</div>				
				<div class="col-md-5">
					<div class="row">
						<div class="col-md-12">
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
						<div class="col-md-12">
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
				<div class="col-md-12">
					<table id="items" class="table table-hover">
						<thead>
							<tr>
								<th style="width:10%">Qty
								</th>
								<th style="width:35%">Description								
								</th>
								<th style="width:15%">Suppliers Code</th>
								<th style="width:15%">Cost</th>
								<th style="width:20%">Total</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($items as $i): ?>
								<tr><input type="hidden" id="delete" name="delete[]" value="no"/><td><input type="hidden" id="item_id" name="item_id[]" value="<?php echo $i->fk_item_id; ?>"/><input class="form-control" id="qty" name="qty[]" value="<?php echo $i->qty; ?>" readonly/></td><td><input class="form-control" id="description" name="description[]" value="<?php echo $i->description; ?>" readonly/><br/><div class="form-inline"><div class="form-group"><label for="for">For </label> <input type="text" class="form-control input-sm" id="for" name="for[]" value="<?php echo $i->for; ?>" readonly/></div></div></td><td><input class="form-control" id="suppliers_code" name="suppliers_code[]" value="<?php echo $i->suppliers_code; ?>" readonly/></td><td><input class="form-control" id="cost" name="cost[]" value="<?php echo $i->cost; ?>" readonly/></td><td><input class="form-control" id="total" name="total[]" value="<?php echo $i->total; ?>" readonly/></td><td><button type="button" class="btn btn-default" aria-label="Left Align" onclick="mark_to_delete(this)" id="remove_row_btn" name="remove_row_btn[]"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button></td></tr>
							<?php endforeach; ?>
							<tr class="success"><td><input type="hidden" id="item_id_in" name="item_id_in"/><input type="hidden" id="hidden_name" name="hidden_name"/><div class="form-group"><input class="form-control" id="qty_in" name="qty_in" autocomplete="off"/></div></td><td><input class="form-control" id="description_in" name="description_in" autocomplete="off"/>							
							</td><td><input class="form-control" id="suppliers_code_in" name="suppliers_code_in" autocomplete="off"/></td><td><input class="form-control" id="cost_in" name="cost_in" autocomplete="off"/></td><td colspan="2">
								<div class="form-inline">
										<div class="form-group" style="width:100%">
											<label for="for">For</label>										
											<input type="text" class="form-control input-sm" id="for_in" style="width:80%">
										</div>
									</div>
								</td>
								</tr>							
						</tbody>
					</table>
				</div>
			</div>
			<div class="row">
				<div class="col-md-1">
					
				</div>
				<div class="col-md-2">
					<!-- <button type="default" class="btn btn-default  btn-block" tabindex="">Changes</button> -->
				</div>
				<div class="col-md-1">
					<button type="button" class="btn btn-default  btn-block" data-toggle="modal" data-target="#levels_modal">Level</button>
				</div>
				<div class="col-md-1">
					<button type="button" class="btn btn-default  btn-block" onclick="fill_family_modal($('#item_id_in').val());">Family</button>
				</div>
				<div class="col-md-1">
					<button type="button" class="btn btn-default  btn-block">Print</button>
				</div>
				<div class="col-md-2">
					<button type="button" class="btn btn-default  btn-block">Complete</button>
				</div>
				<div class="col-md-2">
					<button type="button" class="btn btn-default  btn-block">Abandon</button>
				</div>
				<div class="col-md-2">
					<button type="button" class="btn btn-default  btn-block">Receipts</button>
				</div>				
			</div>
			<div class="row">
				<div class="col-md-8"></div>
				<div class="col-md-2">
					<div class="form-group">
						<label>&nbsp;</label>
						<button type="button" class="btn btn-default  btn-block">Exit</button>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<label>&nbsp;</label>
						<button type="button" class="btn btn-primary  btn-block" onclick="submit_edit_purchase_order_form();">Save</button>
					</div>
				</div>
			</div>
		</div>
		
		
		<!-- Help -->
		<div class="col-md-3">
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

<!-- Family Group Items Modal -->
<div class="modal fade" id="family_group_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Family Order</h4>
			</div>
			<div class="modal-body">
				<form id="family_group_items_form">
					<table id="family_group_table" class="table table-hover">
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
					<button type="submit" class="btn btn-primary">Add to order</button>
				</form>
			</div>
		</div>
	</div>
</div>