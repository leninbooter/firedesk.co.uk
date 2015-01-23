<h1>New Contract</h1>
<form role="form" id="new_contract_form" action="<?php echo base_url('index.php/contracts/save_contract'); ?>" method="post">
<div class="row">
	<div class="col-md-2">
		<div class="form-group">
			<label for="account_reference">Ref.</label>
			<input type="text" class="form-control" id="account_reference" name="account_reference" autocomplete="off"/>
			<input type="hidden" class="form-control" id="account_reference_id" name="account_reference_id"/>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label for="identification_type">Identification</label>
					<select class="form-control" id="identification_type" name="identification_type">
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
					<select class="form-control" id="payment_method" name="payment_method">
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
			<select class="form-control" id="contract_type" name="contract_type">
				<option value="1">Hires/Sales Contract</option>
				<option value ="2">Sales Contract</option>
			</select>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">		
					<label for="identification">&nbsp;</label>
					<input type="text" class="form-control" id="identification" name="identification"/>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label for="payment_ammount">&nbsp;</label>
					<input type="text" class="form-control" id="payment_ammount" name="payment_ammount"/>
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
					<select class="form-control" id="time" name="time">
					  <option value="00:00">00:00</option>
					  <option value="01:00">01:00</option>
					  <option value="02:00">02:00</option>
					  <option value="03:00">03:00</option>
					  <option value="04:00">04:00</option>
					  <option value="05:00">05:00</option>
					  <option value="06:00">06:00</option>
					  <option value="07:00">07:00</option>
					  <option value="08:00">08:00</option>
					  <option value="09:00">09:00</option>
					  <option value="10:00">10:00</option>
					  <option value="11:00">11:00</option>
					  <option value="12:00">12:00</option>
					  <option value="13:00">13:00</option>
					  <option value="14:00">14:00</option>
					  <option value="15:00">15:00</option>
					  <option value="16:00">16:00</option>
					  <option value="17:00">17:00</option>
					  <option value="18:00">18:00</option>
					  <option value="19:00">19:00</option>
					  <option value="20:00">20:00</option>
					  <option value="21:00">21:00</option>
					  <option value="22:00">22:00</option>
					  <option value="23:00">23:00</option>
					</select>
				</div>
				<div class="form-group">
					<label for="date">Date</label>
					<input type="text" class="form-control input-sm datepicker" id="date" name="date" readonly/>
				</div>
				<div class="form-group">
					<label for="due_back">Due Back</label>
					<input type="text" class="form-control input-sm datepicker" id="due_back" name="due_back" readonly/>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-4">		
		<div class="form-group">
			<label for="saved_address">Site / Delivery Address</label>
			<select class="form-control" id="saved_addresses" name="saved_addresses">
			</select>
			<span id="helpBlock" class="help-block">New Address</span>
			<input type="text" class="form-control" id="new_address name="new_address" />
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
	<div class="col-md-4">
		<div class="form-group">
			<label for="notes">Notes</label>
			<textarea class="form-control" rows="3" id="notes"></textarea>
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
			<a href="<?php echo base_url('index.php/contracts/form_add_items'); ?>"><button type="submit" class="btn btn-primary  btn-block">Save & Continue</button></a>
		</div>
	</div>
</div>
</form>
<div id="dropdown_parents_list" style="display:none">
</div>