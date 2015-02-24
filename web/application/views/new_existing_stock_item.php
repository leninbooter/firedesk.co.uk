<div class="row">
	<div class="col-md-12">&nbsp;</div>
</div>
<div class="row">
	<div class="col-md-6"><h1>New/Existing Stock Item</h1></div>
	<div class="col-md-6">
	<?php if(isset($editing)): ?>
		<?php if($editing): ?>
			<div class="alert alert-warning" role="alert"><b>Editing <?php echo $item[0]->description; ?>.</b><p>Unless you change the number of the item, all changes made will overwrite existing data.</p></div>
		<?php endif; ?>
	<?php endif; ?>
			
	</div>
</div>
<form role="form" id="new_supplier_form" method="post" action="<?php echo base_url('index.php/sales_stock/save_item'); ?>">
<input type="hidden" id="pk_id" name="pk_id" value="<?php echo isset($item[0]->pk_id) ? $item[0]->pk_id : ""; ?>">
<div class="row">
	<div class="col-md-9">
		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label for="stock_number">Stock Number</label>
					<input type="text" class="form-control" id="stock_number" name="stock_number" value="<?php echo isset($item[0]->stock_number) ? $item[0]->stock_number:"" ?>"/>
				</div>
			</div>
			<div class="col-md-7">
				<div class="form-group">
					<label for="description">Description</label>
					<input type="text" class="form-control" id="description" name="description" value="<?php echo isset($item[0]->description) ? $item[0]->description:"" ?>"/>
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label for="location">Location</label>
					<input type="text" class="form-control" id="location" name="location" value="<?php echo isset($item[0]->location) ? $item[0]->location:"" ?>"/>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Stock Cost</h3>				
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="total">Total</label>
									<input type="text" class="form-control" id="total" name="total" value="<?php echo isset($item[0]->stock_cost_total) ? $item[0]->stock_cost_total:"" ?>" disabled/>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="stock_cost_average">Average</label>
									<input type="text" class="form-control" id="stock_cost_average" name="stock_cost_average" value="<?php echo isset($item[0]->stock_cost_average) ? $item[0]->stock_cost_average:"" ?>" disabled/>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-5">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Quantities</h3>				
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label for="quantity_balance">Balance</label>										
										<input type="text" class="form-control" id="quantity_balance" name="quantity_balance" value="<?php echo isset($item[0]->quantity_balance) ? $item[0]->quantity_balance:"" ?>" disabled/>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="quantity_on_order">On order</label>
										<input type="text" class="form-control" id="quantity_on_order" name="quantity_on_order" value="<?php echo isset($item[0]->quantity_on_order) ? $item[0]->quantity_on_order:"" ?>" disabled/>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="quantity_rec_level">Rec Level</label>
										<input type="text" class="form-control" id="quantity_rec_level" name="quantity_rec_level" value="<?php echo isset($item[0]->quantity_rec_level) ? $item[0]->quantity_rec_level:"" ?>"/>
									</div>
								</div>
							</div>
						</div>
					</div>
			</div>
			<div class="col-md-3">
				<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Last Movement</h3>				
						</div>
						<div class="panel-body">
							<div class="col-md-12">
									<div class="form-group">
										<label for="last_movement">&nbsp;</label>
										<input type="text" class="form-control" id="last_" name="last_" value="<?php echo isset($item[0]->last_movement) ? date("d M Y",strtotime($item[0]->last_movement)):"" ?>" disabled/>
									</div>
							</div>							
						</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Sales Prices</h3>				
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-5">
								<div class="form-group">
									<label for="standard_price">Standard</label>
									<input type="text" class="form-control" id="standard_price" name="standard_price" value="<?php echo isset($item[0]->standard_price) ? $item[0]->standard_price:"" ?>"/>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="special_price">Special</label>
									<input type="text" class="form-control" id="special_price" name="special_price" value="<?php echo isset($item[0]->special_price) ? $item[0]->special_price:"" ?>"/>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="units_of_for_special">Units of</label>
									<input type="text" class="form-control" id="units_of_for_special" name="units_of_for_special" value="<?php echo isset($item[0]->units_of_for_special) ? $item[0]->units_of_for_special:"" ?>" />
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-5">
								<div class="form-group">
									<label for="fk_vat_code">VAT Code</label>
									<select class="form-control" id="fk_vat_code" name="fk_vat_code">
											<option value="0"></option>
											<?php foreach($vats as $option): ?>
											<option value="<?php echo $option->pk_id; ?>" <?php if(isset($item[0]->fk_vat_code)) { if($item[0]->fk_vat_code == $option->pk_id) { echo "selected";} } ?>><?php echo $option->description." (".$option->percentage."%)"; ?></option>
											<?php endforeach; ?>
										</select>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Cost Prices</h3>				
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label for="cost_price_a">A</label>										
										<input type="text" class="form-control" id="cost_price_a" name="cost_price_a" value="<?php echo isset($item[0]->cost_price_a) ? $item[0]->cost_price_a:"" ?>"/>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="cost_price_b">B</label>
										<input type="text" class="form-control" id="cost_price_b" name="cost_price_b" value="<?php echo isset($item[0]->cost_price_b) ? $item[0]->cost_price_b:"" ?>"/>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="cost_price_c">C</label>
										<input type="text" class="form-control" id="cost_price_c" name="cost_price_c" value="<?php echo isset($item[0]->cost_price_c) ? $item[0]->cost_price_c:"" ?>"/>
									</div>
								</div>
							</div>
						</div>
					</div>
			</div>			
		</div>
		<div class="row">
			<div class="col-md-4">
				<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Supplier A</h3>				
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">							
										<select class="form-control" id="fk_supplier_a" name="fk_supplier_a">
											<option value="0"></option>
											<?php foreach($suppliers as $option): ?>
											<option value="<?php echo $option->pk_id; ?>" <?php if(isset($item[0]->fk_supplier_a)) { if($item[0]->fk_supplier_a == $option->pk_id) { echo "selected";} } ?>><?php echo $option->name; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>															
							</div>
						</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Supplier B</h3>				
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<select class="form-control" id="fk_supplier_b" name="fk_supplier_b">
											<option value="0"></option>
											<?php foreach($suppliers as $option): ?>
											<option value="<?php echo $option->pk_id; ?>" <?php if(isset($item[0]->fk_supplier_b)) { if($item[0]->fk_supplier_b == $option->pk_id) { echo "selected";} } ?> ><?php echo $option->name; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>															
							</div>
						</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Supplier C</h3>				
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">										
										<select class="form-control" id="fk_supplier_c" name="fk_supplier_c">
											<option value="0"></option>
											<?php foreach($suppliers as $option): ?>
											<option value="<?php echo $option->pk_id; ?>" <?php if(isset($item[0]->fk_supplier_c)) { if($item[0]->fk_supplier_c == $option->pk_id) { echo "selected";} } ?> ><?php echo $option->name; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>															
							</div>
						</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Family Group</h3>				
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">										
										<select class="form-control" id="fk_family_group" name="fk_family_group">
											<option value="0"></option>
											<?php foreach($family_groups as $option): ?>
											<option value="<?php echo $option->pk_id; ?>" <?php if(isset($item[0]->fk_family_group)) { if($item[0]->fk_family_group == $option->pk_id) { echo "selected";} } ?> ><?php echo $option->name; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>															
							</div>
						</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Discount Group</h3>				
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">									
										<select class="form-control" id="fk_discount_group" name="fk_discount_group">
											<option value="0"></option>
											<?php foreach($family_discounts as $option): ?>
											<option value="<?php echo $option->pk_id; ?>" <?php if(isset($item[0]->fk_discount_group)) { if($item[0]->fk_discount_group == $option->pk_id) { echo "selected";} } ?>><?php echo $option->pk_id.") ".$option->description; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>															
							</div>
						</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				
			</div>
		</div>			
		
	</div>
	
	<div class="col-md-3">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">Help</h3>				
			</div>
			<div class="panel-body">
				<dl>
				  <dt>Stock Number</dt><dd>This field can be any combinationof letters andnumbersupto 15 characters.</dd>
				  <br/>
				  <dt>On order</dt><dd>The On order quantity is maintained by the Purchase Order section of the system and shows how many of this item arecurrently due on outstanding purchase order.</dd>				  
				  <br/>
				  <dt>Rec Level</dt><dd>The Rec. Level is the stock level that should ideally be maintained.</dd>
				</dl>
			</div>
		</div>
	</div>	
</div>

<div class="row">	
	<?php if(isset($item[0]->pk_id)): ?>
		<div class="col-md-2">
			<button type="button" class="btn btn-default" data-toggle="modal" data-target="#change_quantity">Change Quantities</button>			
		</div>
		<div class="col-md-4">
			<button type="button" class="btn btn-default" data-toggle="modal" data-target="#special_prices">Prices</button>			
		</div>		
	<?php else: ?>
		<div class="col-md-6"></div>
	<?php endif; ?>
	
	<div class="col-md-1">
		<div class="form-group">
			<a href="javascript:history.back()" class="btn btn-default" role="button">Go back</a>
		</div>
	</div>
	
	<div class="col-md-2">
		<div class="form-group">
			<button type="submit" class="btn btn-primary  btn-block">Save</button>
		</div>
	</div>
	<div class="col-md-3">&nbsp;</div>
</div>		
</form>

<!-- Change quantity modal -->
<div class="modal fade" id="change_quantity" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Change Quantities</h4>
			</div>
			<div class="modal-body">
				<form role="form" id="change_quantity_form"  method="post" action="<?php echo base_url('index.php/sales_stock/update_qty_manually');?>">
					<input type="hidden" id="stock_item_id" name="stock_item_id" class="form-control" value="<?php echo isset($item[0]->pk_id) ? $item[0]->pk_id : ""; ?>">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="type">Action</label>
								<select id="type" name="type" class="form-control">
									<option value="add">Add</option>
									<option value="remove">Remove</option>
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="type">Qty</label>
								<input type="text" class="form-control" id="qty" name="qty">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="cost_price">Cost</label>										
								<input type="text" class="form-control" id="cost_price" name="cost_price"/>
							</div>
						</div>
					</div>					
					<button type="submit" class="btn btn-primary" onclick="$('#cost_price').val( parseFloat( $('#cost_price').val() ).toFixed(2) );">Save</button>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Special Prices Modal -->
<div class="modal fade" id="special_prices" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Prices</h4>
			</div>
			<div class="modal-body">
				<form role="form" id="prices_form" >
					<input type="hidden" id="stock_item_id" name="stock_item_id" class="form-control" value="<?php echo isset($item[0]->pk_id) ? $item[0]->pk_id : ""; ?>">
					<!-- headers -->
					<div class="row">						
						<div class="col-md-3">
							<div class="row">
								<div class="row">
								<div class="col-md-12">&nbsp;</div>
							</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									Customer
								</div>
							</div>							
						</div>
						<div class="col-md-2">
							<div class="row">
								<div class="row">
									<div class="col-md-12">&nbsp;</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<p class="text-center">Price Type</p>
								</div>
							</div>														
						</div>
						<div class="col-md-4">
							<div class="row">
								<div class="col-md-12">
									<p class="text-center">Prices Break</p>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<p class="text-center">Min</p>
								</div>
								<div class="col-md-6">
									<p class="text-center">Max</p>
								</div>
							</div>							
						</div>
						<div class="col-md-2">
							<div class="row">
								<div class="row">
									<div class="col-md-12">&nbsp;</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<p class="text-center">Price</p>
								</div>
							</div>
						</div>
						<div class="col-md-1"></p>
						</div>
					</div>
					<!-- data -->
					<?php foreach($prices as $p): ?>
						<div class="row" id="saved_prices_rows">
							<div class="col-md-3">
								<div class="form-group">
									<select class="form-control" id="customers_pk_id" name="customers_pk_id[]">
										<option value="0"></option>
										<?php foreach($customers as $customer): ?>
											<option value="<?php echo $customer->pk_id;?>" <?php echo $customer->pk_id == $p->fk_customer_id ? "selected" : ""; ?> ><?php echo $customer->name;?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
							<div class="col-md-2">
								<select id="price_type" name="price_type[]" class="form-control">
									<option value="0" <?php echo "0" == $p->price_type ? "selected" : ""; ?>>Standard</option>
									<option value="1" <?php echo "1" == $p->price_type ? "selected" : ""; ?>>Special</option>
									<option value="2" <?php echo "2" == $p->price_type ? "selected" : ""; ?>>Nett</option>
								</select>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<input type="number" min="1" max="1000000000" id="min_qty" name="min_qty[]" class="form-control" value="<?php echo $p->min_qty; ?>">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<input type="number" min="1" max="1000000000" id="max_qty" name="max_qty[]" class="form-control" value="<?php echo $p->max_qty; ?>">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<input type="text" id="price" name="price[]" class="form-control" value="<?php echo $p->price; ?>">
								</div>
							</div>
							<div class="col-md-1">								
							</div>
						</div>
					<?php endforeach; ?>					
					<div id="first_row" <?php echo empty($prices) ? "":"style=\"display:none\"" ?>>
					<div class="row" id="first_row_child">
						<div class="col-md-3">
							<div class="form-group">
								<select class="form-control" id="customers_pk_id" name="customers_pk_id[]">
									<option value="0"></option>
									<?php foreach($customers as $customer): ?>
										<option value="<?php echo $customer->pk_id;?>"><?php echo $customer->name;?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<select id="price_type" name="price_type[]" class="form-control">
								<option value="0">Standard</option>
								<option value="1">Special</option>
								<option value="2" disabled="true">Nett</option>
							</select>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<input type="number" min="1" max="1000000000" id="min_qty" name="min_qty[]" class="form-control">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<input type="number" min="1" max="1000000000" id="max_qty" name="max_qty[]" class="form-control">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<input type="text" id="price" name="price[]" class="form-control">
							</div>
						</div>
						<div class="col-md-1">
							<p class="text-center">
								<button type="button" class="btn btn-default" aria-label="Left Align" id="remove_row_btn" name="remove_row_btn[]">
									<span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span>
								</button>
							</p>
						</div>
					</div>
					</div>
					<div id="rows">
					</div>					
					<div class="row">
						<div class="col-md-11"></div>
						<div class="col-md-1">
							<p class="text-center">
								<button id="add_row" type="button" class="btn btn-default" aria-label="Left Align">
									<span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>
								</button>
							</p>
						</div>
					</div>
					<button type="submit" class="btn btn-primary" onclick="">Save</button>
				</form>
			</div>
		</div>
	</div>
</div>