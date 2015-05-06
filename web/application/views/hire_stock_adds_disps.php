<input type="hidden" id="item_id" name="item_id" value="<?php echo $item_id; ?>"/>
<div class="row">
	<div class="col-md-6"><h1>Fleet Item - Addition / Disposal</h1></div>
</div>
<form class="form-horizontal">
<div class="row">
	<div class="col-md-9">
		<div class="row">
			<div class="col-md-2">
				<div class="form-group">
					<label class="col-sm-3 control-label">Type</label>
					<div class="col-sm-5">
					  <p class="form-control-static"><?php echo $item_details->item_type; ?></p>
					</div>
				  </div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label class="col-sm-5 control-label">Quantity</label>
					<div class="col-sm-7">
					  <p class="form-control-static"><?php echo $item_details->qty; ?></p>
					</div>
				  </div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label class="col-sm-3 control-label">Description</label>
					<div class="col-sm-9">
					  <p class="form-control-static"><?php echo $item_details->description; ?></p>
					</div>
				  </div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label class="col-sm-5 control-label">Fleet number</label>
					<div class="col-sm-7">
					  <p class="form-control-static"><?php echo $item_details->fleet_number; ?></p>
					</div>
				  </div>
			</div>		
		</div>
		<div class="row">
			<div class="col-md-12">
				<div id="gridbox" style="width:100%;height:350px; box-sizing:content-box !important"></div>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="row">
			<div class="col-md-12">
				<button type="button" class="btn btn-default btn-block" id="add_stock_btn">Add stock</button>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<br>
				<div class="panel panel-info">
					<div class="panel-heading">
						<h3 class="panel-title">Help</h3>				
					</div>
					<div class="panel-body">
						<dl>
						  <dt>Help</dt><dd>...</dd>
						  <br/>				  
						</dl>
					</div>
				</div>
			</div>
		</div>
		
	</div>
</div>
</form>
<div class="modal fade" id="add_stock_modal" tabindex="-1" role="dialog" aria-labelledby="add_stock_modal" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<form  role="form" id="add_stock_form">
		<input type="hidden" id="item_id" name="item_id" value="<?php echo $item_id; ?>"/>
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Add Stock</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-4">
							<div class="form-group">
								<label>Quantity</label>
								<input type="text" id="qty" name="qty" class="form-control input-sm" />
							</div>							
							
					</div>
					<div class="col-md-8">
							
							<div class="form-group">
								<label>Date</label>
								<input type="text" id="date_string" class="form-control input-sm" />
								<input type="hidden" id="date" name="date" class="form-control input-sm" />
							</div>
														
					</div>
				</div>
				<div class="row">					
					<div class="col-md-4">
							<div class="form-group">
								<label>Cost each</label>
								<input type="text" id="cost" name="cost" class="form-control input-sm" />
							</div>							
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label>Notes</label>
							<textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<button type="submit" class="btn btn-primary btn-block">Add</button>
						<button type="button" data-dismiss="modal" class="btn btn-default btn-block">Cancel</button>
					</div>
				</div>					
			</div>
		</div>
		</form>
	</div>
</div>

<div class="modal fade" id="remove_stock_modal" tabindex="-1" role="dialog" aria-labelledby="remove_stock_modal" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<form  role="form" id="remove_stock_form">
		<input type="hidden" id="item_id" name="item_id" value="<?php echo $item_id; ?>"/>
		<input type="hidden" id="acquisition_id" name="acquisition_id" value=""/>
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Disposal Stock</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-4">
							<div class="form-group">
								<label>Quantity</label>
								<p class="form-control-static" id="acquisition_qty"></p>
							</div>							
							
					</div>
					<div class="col-md-8">							
							<div class="form-group">
								<p class="form-control-static">(Added <span id="acquisition_date"></span>)</p>
							</div>
														
					</div>
				</div>
				<div class="row">					
					<div class="col-md-4">
						<div class="form-group">
							<label>Cost each value</label>
							<p class="form-control-static" id="acquisition_cost"></p>
						</div>							
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
							<div class="form-group">
								<label>Quantity</label>
								<input type="text" id="qty" name="qty" class="form-control input-sm" />
							</div>							
							
					</div>
					<div class="col-md-8">
							
							<div class="form-group">
								<label>Date</label>
								<input type="text" id="date_string" class="form-control input-sm" />
								<input type="hidden" id="date" name="date" class="form-control input-sm" />
							</div>
														
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
							<div class="form-group">
								<label>Cost each</label>
								<input type="text" id="cost" name="cost" class="form-control input-sm" />
							</div>							
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label>Notes</label>
							<textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<button type="submit" class="btn btn-primary btn-block">Add</button>
						<button type="button" data-dismiss="modal" class="btn btn-default btn-block">Cancel</button>
					</div>
				</div>					
			</div>
		</div>
		</form>
	</div>
</div>