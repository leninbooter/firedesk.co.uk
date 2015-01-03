<h1>New Contract</h1>
<form role="form">
<div class="row">
	<div class="col-md-2">
		<div class="form-group">
			<label for="account_reference">Ref.</label>
			<input type="text" class="form-control" id="account_reference" autocomplete="off"/>
			<input type="hidden" class="form-control" id="account_reference_id"/>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label for="identification_type">Identification</label>
					<select class="form-control" id="identification_type">
						<option value="1">Primary ID</option>
						<option value ="2">Passport</option>
					</select>					
				</div>
			</div>			
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label for="payment_method">Payment</label>
					<select class="form-control" id="payment_method">
						<option value="1">Cash</option>
						<option value ="2">Check</option>
						<option value ="3">Card</option>
						<option value ="4">Mixed</option>
					</select>					
				</div>
			</div>			
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<label for="contract_type">Type</label>
			<select class="form-control" id="contract_type">
				<option value="1">Hire Contract</option>
				<option value ="2">Sales Contract</option>
				<option value ="3">Sales Delivery Notes</option>
			</select>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">		
					<label for="identification">&nbsp;</label>
					<input type="text" class="form-control" id="identification"/>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label for="payment_ammount">&nbsp;</label>
					<input type="text" class="form-control" id="payment_ammount"/>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<button type="button" class="btn btn-info btn-xs btn-block">Notes</button>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-2">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="form-group">
					<label for="time">Time</label>
					<input type="time" class="form-control input-sm" id="time"/>
				</div>
				<div class="form-group">
					<label for="date">Date</label>
					<input type="text" class="form-control input-sm datepicker" id="date"/>
				</div>
				<div class="form-group">
					<label for="due_back">Due Back</label>
					<input type="text" class="form-control input-sm datepicker" id="due_back"/>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-4">		
		<div class="form-group">
			<label for="saved_address">Site / Delivery Address</label>
			<select class="form-control" id="saved_addresses">
			</select>
			<span id="helpBlock" class="help-block">New Address</span>
			<input type="text" class="form-control" id="new_addresses" />
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<label for="">Delivery Charge</label>
			<input type="text" class="form-control" id="delivery_charge" />
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-4">
		<div class="form-group">
			<label for="notes">Notes</label>
			<textarea class="form-control" rows="3"></textarea>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-6">
		<hr/>
	</div>
</div>

<div class="row">
	<div class="col-md-4">
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<a href="<?php echo base_url('index.php/contracts/form_add_items'); ?>"><button type="button" class="btn btn-primary  btn-block">Save & Continue</button></a>
		</div>
	</div>
</div>
</form>
<div id="dropdown_parents_list" style="display:none">
</div>