<div class="row">
	<div class="col-md-12">&nbsp;</div>
</div>
<div class="row">
	<div class="col-md-6"><h1>New/Existing Stock Item</h1></div>
	<div class="col-md-6">
	<?php if(isset($editing)): ?>
		<?php if($editing): ?>
			<div class="alert alert-warning" role="alert"><b>Editing <?php echo $item[0]->name; ?>.</b><p>Unless you change the number of the item, all changes made will overwrite existing data.</p></div>
		<?php endif; ?>
	<?php endif; ?>
			
	</div>
</div>
<form role="form" id="new_supplier_form" method="post" action="<?php echo base_url('index.php/sales_stock/save_item'); ?>">
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
										<input type="text" class="form-control" id="quantity_rec_level" name="quantity_rec_level" value="<?php echo isset($item[0]->quantity_rec_level) ? $item[0]->quantity_rec_level:"" ?>" disabled/>
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
										<input type="text" class="form-control" id="last_" name="last_" value="<?php echo isset($item[0]->last_) ? $item[0]->last_movement:"" ?>" disabled/>
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
										<label for="fk_supplier_a">&nbsp;</label>										
										<select class="form-control" id="fk_supplier_a" name="fk_supplier_a">
											<option value="NULL"></option>
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
										<label for="fk_supplier_a">&nbsp;</label>										
										<select class="form-control" id="fk_supplier_b" name="fk_supplier_b">
											<option value="NULL"></option>
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
										<label for="fk_supplier_a">&nbsp;</label>										
										<select class="form-control" id="fk_supplier_c" name="fk_supplier_c">
											<option value="NULL"></option>
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
										<label for="fk_family_group">&nbsp;</label>										
										<select class="form-control" id="fk_family_group" name="fk_family_group">
											<option value="NULL"></option>
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
										<label for="fk_discount_group">&nbsp;</label>										
										<select class="form-control" id="fk_discount_group" name="fk_discount_group">
											<option value="NULL"></option>
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
	<div class="col-md-6">
	</div>
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
</div>
		
</form>