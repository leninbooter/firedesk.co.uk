<div class="row">
	<div class="col-md-6"><h1>New Hire Item</h1></div>
	<div class="col-md-6">
	<?php if(isset($editing)): ?>
		<?php if($editing): ?>
			<div class="alert alert-warning" role="alert"><b>Editing <?php echo $item[0]->description; ?>.</b><p>Unless you change the number of the item, all changes made will overwrite existing data.</p></div>
		<?php endif; ?>
	<?php endif; ?>
			
	</div>
</div>
<form role="form" id="new_hire_item_form">
<div class="row">
	<div class="col-md-9">
		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label for="stock_number">Fleet Number</label>
					<input type="text" class="form-control" id="fleet_number" name="fleet_number" value="<?php echo isset($item[0]->stock_number) ? $item[0]->stock_number:"" ?>"/>
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
					<label for="location">Type</label>					
					<select id="hire_item_type" name="hire_item_type"  class="form-control">					
						<option value="1">Single</option>
						<option value="2">Multiple</option>
						<option value="3">Kit Heading</option>
						<option value="4">Bundle</option>
					</select>				
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<label for="group_id">Family group</label>
					<select class="form-control" id="group_id" name="group_id">
						<?php foreach($families as $f): ?>
							<option value="<?php echo $f->pk_id; ?>"><?php echo $f->name; ?> - Basic rate: <?php echo $f->basic_rate; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label for="basic_rate">Basic hire rate</label>
					<input type="text" class="form-control" id="basic_rate" name="basic_rate" />
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label for="date_string">Purchase date</label>
					<input type="text" class="form-control" id="date_string" name="date_string" />
					<input type="hidden" class="form-control" id="purchase_date" name="purchase_date"/>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label for="cost_price">Cost price</label>
					<input type="text" class="form-control" id="cost_price" name="cost_price" />
				</div>
			</div>			
		</div>				
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Other details</h3>				
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label for="serial_number">Serial number</label>
									<input type="text" class="form-control" id="serial_number" name="serial_number"  />
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="power">Power</label>
									<input type="text" class="form-control" id="power" name="power" />
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="earth">Earth</label>
									<input type="text" class="form-control" id="earth" name="earth"  />
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="fuse">Fuse</label>
									<input type="text" class="form-control" id="fuse" name="fuse" />
								</div>
							</div>
						</div>
						<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label for="flash_test">Flash test</label>
								<input type="text" class="form-control" id="flash_test" name="flash_test"/>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="engine_speed">Engine speed</label>
								<input type="text" class="form-control" id="engine_speed" name="engine_speed"  />
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="output_spindle">Output spindle</label>
								<input type="text" class="form-control" id="output_spindle" name="output_spindle" />
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="cable_type">Cable type</label>
								<input type="text" class="form-control" id="cable_type" name="cable_type"/>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label for="cable_length">Cable length</label>
								<input type="text" class="form-control" id="cable_length" name="cable_length" />
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="ppe_kit">PPE kit</label>
								<input type="text" class="form-control" id="ppe_kit" name="ppe_kit"  />
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="safety_leaflet">Safety leaflet</label>
								<input type="text" class="form-control" id="safety_leaflet" name="safety_leaflet" />
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="test_frequency">Test frequency</label>
								<input type="text" class="form-control" id="test_frequency" name="test_frequency"  />
							</div>
						</div>
					</div>
					<!--<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label for="total">Last test date</label>
								<input type="text" class="form-control" id="total" name="total" />
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="stock_cost_average">0 repairs</label>
								<input type="text" class="form-control" id="stock_cost_average" name="stock_cost_average" />
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="stock_cost_average">1/2 days hire</label>
								<input type="text" class="form-control" id="stock_cost_average" name="stock_cost_average" />
							</div>
						</div>						
					</div>-->
					</div>
					
				</div>
			</div>					
		</div>
		<div class="row">
			<div class="col-md-12">
				<button type="submit" class="btn btn-primary btn-block" >Save new item</button>
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
				</dl>
			</div>
		</div>
	</div>	
</div>		
</form>

<div class="modal fade" id="components_modal" tabindex="-1" role="dialog" aria-labelledby="components_modal" aria-hidden="true">
	<div class="modal-dialog">
		<form  role="form" id="components_form">
		<input type="hidden" id="parent_item" name="parent_item" value=""/>
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Components & quantities required</h4>
			</div>
			<div class="modal-body">
			<table id="items_table" class="table table-condensed">
				<thead>
					<tr>
						<th style="width:10%">Fleet number</th>
						<th style="width:50%">Description</th>
						<th style="width:20%">Total stock</th>
						<th style="width:20%">Required</th>
					</tr>
				</thead>
				<tbody>
				
				</tbody>
			</table>
				<div class="form-group">
				<label>Search an item to add it <span style="font-size:150%">&darr;</span></label>
				<input type="text" id="search_hire_item" class="form-control input-sm" />
				</div>
				<button type="submit" class="btn btn-primary btn-block">Save components</button>
			</div>
		</div>
		</form>
	</div>
</div>	