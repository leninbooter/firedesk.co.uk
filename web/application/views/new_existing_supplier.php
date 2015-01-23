<h1>New / Existing Supplier</h1> <!--<button type="button" class="btn btn-info btn-sm">Load client</button>-->
<form role="form" id="new_supplier_form" method="post" action="<?php echo base_url('index.php/suppliers/save_supplier'); ?>">
<div class="row">
	<div class="col-md-6">
		<div class="row">
			<div class="col-md-2">
				<div class="form-group">
					<label for="name">Name</label>
					<input type="text" class="form-control" id="name" name="name"/>
				</div>
			</div>
			<div class="col-md-5">
				
			</div>
			<div class="col-md-5">
				<div class="form-group">
					<label for="address">Address</label>
					<input type="text" class="form-control" id="address" name="address"/>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label for="telephone">Telephone</label>
					<input type="text" class="form-control" id="telephone" name="telephone"/>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label for="fax">Fax</label>
					<input type="tel" class="form-control" id="fax" name="fax"/>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">		
					<label for="email">E-mail</label>
					<input type="email" class="form-control" id="email" name="email"/>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="contact_name">Contact name</label>
					<input type="text" class="form-control" id="contact_name" name="contact_name"/>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">	
					<label for="representative">Representative</label>
					<input type="text" class="form-control" id="representative" name="representative"/>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-2">
				<div class="form-group">
					<label for="type">Type</label>
					<select class="form-control" id="type" name="type">
						<option value="1">Credit</option>
						<option value="0">Cash</option>
					</select>
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label for="credit_limit">Credit limit</label>
					<input type="text" id="credit_limit" class="form-control" name="credit_limit">
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label for="order_number">Order No.</label>
					<select class="form-control" id="order_number" name="order_number">
						<option value="1">Required</option>
						<option value="0">Optional</option>
					</select>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label for="vat">VAT</label>
					<select class="form-control" id="vat" name="vat">
							<option value="1">Not exempt</option>
							<option value="0">Exempt</option>
					</select>
				</div>
			</div>	
			<div class="col-md-3">
				<div class="form-group">
					<label for="days_week">Days/week</label>
					<select class="form-control" id="days_week" name="days_week">
						<option value="1">7 day</option>
						<option value="2">6 day</option>
						<option value="3">5 day</option>
						<option value="4">Pro rata</option>
						<option value="5">7 Pro rata</option>
						<option value="6">6 Pro rata</option>
						<option value="7">5 Pro rata</option>
						<option value="8" selected>Standard</option>
					</select>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<label for="invoicing">Invoicing</label>
					<select class="form-control" id="invoicing" name="invoicing">
						<option value="1">One per contract</option>
						<option value="2">Per site: shows date contract and order</option>
						<option value="3">Per site: shows contract and order no.</option>
						<option value="4">Per site: shows date and order no.</option>
						<option value="5">Per site: shows date and contract</option>
						<option value="6">Per site: full site</option>
						<option value="7">Per site and order number</option>
						<option value="8">Per site and order no: full details</option>
						<option value="9">Exclude from invoice runs</option>
					</select>
				</div>
			</div> 
			<div class="col-md-4">
				<div class="form-group">
					<label for="holiday_credit">Holiday Credit</label>
					<select class="form-control" id="holiday_credit" name="holiday_credit">
						<option value="1" selected>Standard</option>
						<option value="2">No Holiday</option>
						<option value="3">BUILDING</option>
					</select>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label for="prices_type">Prices</label>
					<select class="form-control" id="prices_type" name="prices_type">
						<option value="1" selected>Standard</option>
						<option value="2">Special</option>
					</select>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="statement_address">Statement Address</label>
					<input type="text" class="form-control" id="statement_address" name="statement_address"/>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="parent_account">Parent Account </label>
					<input id="parent_account_id" type="hidden" value="" name="parent_account_id">
					<input type="text" class="form-control" id="parent_account" autocomplete="off">						
				</div>		
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<hr/>
			</div>
		</div>

		<div class="row">
			<div class="col-md-10">
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<button type="submit" class="btn btn-primary  btn-block">Save</button>
				</div>
			</div>
		</div>
	</div>
	<div id="message_board" class="col-md-6 message_board">
	</div>
</div>
</form>
<div id="dropdown_parents_list" style="display:none">
</div>