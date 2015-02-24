<h1>New Contract</h1>
<form role="form" id="new_contract_form" action="<?php echo base_url('index.php/contracts/save_contract'); ?>" method="post">
<div class="row">
	<!-- Form left panel-->
	<div class="col-md-9">
		<div class="row">
			<div class="col-md-2">
				<div class="form-group">
					<label for="account_reference">Account Number</label>
					<input type="text" class="form-control" id="account_reference" name="account_reference" autocomplete="off"/>
					<input type="hidden" id="account_reference_id" name="account_reference_id" value="<?php echo $this->input->get('customer_id');?>"/>
				</div>
			</div>
			<div class="col-md-2">				
				<div class="form-group">
					<label for="">&nbsp;</label>
					<a href="<?php echo base_url('index.php/customers/new_existing'); ?>"><button type="button" class="btn btn-primary  btn-block">New customer</button></a>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label for="contract_type">Type</label>
					<select class="form-control" id="contract_type" name="contract_type">
						<option value="1">Hires/Sales Contract</option>
						<option value ="2">Sales Contract</option>
					</select>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label for="date">Date</label>
					<input type="text" class="form-control" id="date_string" name="date_string" readonly disabled="disabled"/>
					<input type="hidden" class="form-control" id="date" name="date"/>
				</div>
			</div><div class="col-md-2">
				<div class="form-group">
					<label for="time">Time</label>
					<input type="text" class="form-control" id="time" name="time" readonly/>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-2">		
				<div class="checkbox">
					<label>
						<input type="checkbox" id="cash" name="cash">Cash
					</label>
				</div>
			</div>
			<!--<div class="col-md-4">		
				<div class="form-group">
					<label for="saved_address">Site / Delivery Address</label>
					<select class="form-control" id="saved_addresses" name="saved_addresses">
					</select>					
				</div>
			</div>-->
			<div class="col-md-4">
				<div class="form-group">
					<label for="new_address">Delivery Address</label>
					<!--<input type="text" class="form-control" id="new_address name="new_address" />-->
					<textarea class="form-control" rows="3" id="new_address" name="new_address"></textarea>
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label for="">Delivery Charge</label>
					<input type="text" class="form-control" id="delivery_charge" name="delivery_charge"  />
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="notes">Notes</label>
					<textarea class="form-control" rows="3" id="notes"></textarea>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-10"></div>
			<div class="col-md-2">
				<div class="form-group">
					<button type="submit" class="btn btn-primary  btn-block">Save & Continue</button>
				</div>
			</div>
		</div>
		
	</div>
	
	<!-- Help right panel -->
	<div class="col-md-3">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">Help</h3>				
			</div>
			<div class="panel-body">
				<dl>
					<dt>Customer Account</dt><dd>Start typing the name of the customer and you will see a list of all the recorded ones.</dd>				  
				  <br/>
				  <dt>Type</dt><dd>Hire and sales contract can have these two types of items. Sales contract can only contain items for selling.</dd>
				</dl>
			</div>
		</div>
	</div>
</div>
</form>
<div id="dropdown_parents_list" style="display:none">
</div>