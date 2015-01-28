<div class="row">
	<div class="col-md-12">&nbsp;</div>
</div>
<div class="row">
	<div class="col-md-6"><h1>New/Existing Supplier</h1></div>
	<div class="col-md-6">
	<?php if(isset($editing)): ?>
		<?php if($editing): ?>
			<div class="alert alert-warning" role="alert"><b>Editing <?php echo $supplier[0]->name; ?>.</b><p>Unless you change the name of the supplier, all changes made will overwrite existing data.</p></div>
		<?php endif; ?>
	<?php endif; ?>
			
	</div>
</div>
<form role="form" id="new_supplier_form" method="post" action="<?php echo base_url('index.php/suppliers/save_supplier'); ?>">
<div class="row">
	<div class="col-md-5">
		<div class="row">
			<div class="col-md-8">
				<div class="form-group">
					<label for="name">Name</label>
					<input type="text" class="form-control" id="name" name="name" value="<?php echo isset($supplier[0]->name) ? $supplier[0]->name:"" ?>"/>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label for="email">E-mail</label>
					<input type="text" class="form-control" id="email" name="email" value="<?php echo isset($supplier[0]->email) ? $supplier[0]->email:"" ?>"/>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<label for="name">Telephone 1</label>
					<input type="text" class="form-control" id="telephone1" name="telephone1" value="<?php echo isset($supplier[0]->telephone1) ? $supplier[0]->telephone1:"" ?>"/>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label for="email">Telephone 2</label>
					<input type="text" class="form-control" id="telephone2" name="telephone2" value="<?php echo isset($supplier[0]->telephone2) ? $supplier[0]->telephone2:"" ?>"/>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label for="fax">Fax</label>
					<input type="text" class="form-control" id="fax" name="fax" value="<?php echo isset($supplier[0]->fax) ? $supplier[0]->fax:"" ?>"/>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<label for="name">Address</label>
					<input type="text" class="form-control" id="address1" name="address1" value="<?php echo isset($supplier[0]->address1) ? $supplier[0]->address1:"" ?>"/>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label for="name">&nbsp;</label>
					<input type="text" class="form-control" id="address2" name="address2" value="<?php echo isset($supplier[0]->address2) ? $supplier[0]->address2:"" ?>"/>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label for="name">&nbsp;</label> 
					<input type="text" class="form-control" id="address3" name="address3" value="<?php echo isset($supplier[0]->address3) ? $supplier[0]->address3:"" ?>"/>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<label for="name">&nbsp;</label>
					<input type="text" class="form-control" id="address4" name="address4" value="<?php echo isset($supplier[0]->address4) ? $supplier[0]->address4:"" ?>"/>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label for="name">&nbsp;</label>
					<input type="text" class="form-control" id="address5" name="address5" value="<?php echo isset($supplier[0]->address5) ? $supplier[0]->address5:"" ?>"/>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label for="name">&nbsp;</label>
					<input type="text" class="form-control" id="address6" name="address6" value="<?php echo isset($supplier[0]->address6) ? $supplier[0]->address6:"" ?>"/>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label for="name">ZIP Code</label>
					<input type="text" class="form-control" id="zipcode" name="zipcode" value="<?php echo isset($supplier[0]->zipcode) ? $supplier[0]->zipcode:"" ?>"/>
				</div>
			</div>
			<div class="col-md-1">
			</div>
			<div class="col-md-8">
				<div class="form-group">
					<label for="name">Contact</label>
					<input type="text" class="form-control" id="contact" name="contact" value="<?php echo isset($supplier[0]->contact) ? $supplier[0]->contact:"" ?>"/>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">Bank Account</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="name">Bank Name</label>
									<input type="text" class="form-control" id="bank_name" name="bank_name" value="<?php echo isset($supplier[0]->bank_name) ? $supplier[0]->bank_name:"" ?>"/>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="name">Account Number</label>
									<input type="text" class="form-control" id="account_number" name="account_number" value="<?php echo isset($supplier[0]->account_number) ? $supplier[0]->account_number:"" ?>"/>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="name">Swift Code</label>
									<input type="text" class="form-control" id="swift_code" name="swift_code" value="<?php echo isset($supplier[0]->swift_code) ? $supplier[0]->swift_code:"" ?>"/>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="name">Account Type</label>
									<select id="account_type" name="account_type" class="form-control">
										<option value="1" <?php echo isset($supplier[0]->account_type) && $supplier[0]->account_type == 1 ? "selected":"" ?>>Checking</option>
										<option value="2" <?php echo isset($supplier[0]->account_type) && $supplier[0]->account_type == 2 ? "selected":"" ?>>Savings</option>
									</select>								
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="name">Account Address</label>
									<input type="text" class="form-control" id="account_address1" name="account_address1" value="<?php echo isset($supplier[0]->account_address1) ? $supplier[0]->account_address1:"" ?>"/>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="name">&nbsp;</label>
									<input type="text" class="form-control" id="account_address2" name="account_address2" value="<?php echo isset($supplier[0]->account_address2) ? $supplier[0]->account_address2:"" ?>"/>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="name">&nbsp;</label>
									<input type="text" class="form-control" id="account_address3" name="account_address3" value="<?php echo isset($supplier[0]->account_address3) ? $supplier[0]->account_address3:"" ?>"/>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="name">Telephone</label>
									<input type="text" class="form-control" id="bank_telephone" name="bank_telephone" value="<?php echo isset($supplier[0]->bank_telephone) ? $supplier[0]->bank_telephone:"" ?>"/>
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
	
	<div class="col-md-4">
		<div class="panel panel-default">
					<div class="panel-heading">Terms</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-6">
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="name">Account Credit</label>
									<select id="account_credit" name="account_credit" class="form-control">
										<option value="1" <?php echo isset($supplier[0]->account_credit) && $supplier[0]->account_credit == 1 ? "selected":"" ?>>No</option>
										<option value="2" <?php echo isset($supplier[0]->account_credit) && $supplier[0]->account_credit == 2 ? "selected":"" ?>>Yes</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="name">Payment</label>
									<select id="account_payment_credit" name="account_payment_credit" class="form-control">
										<option value="1" <?php echo isset($supplier[0]->account_payment_credit) && $supplier[0]->account_payment_credit == 1 ? "selected":"" ?>>Days</option>
										<option value="2" <?php echo isset($supplier[0]->account_payment_credit) && $supplier[0]->account_payment_credit == 2 ? "selected":"" ?>>Months</option>
										<option value="3" <?php echo isset($supplier[0]->account_payment_credit) && $supplier[0]->account_payment_credit == 3 ? "selected":"" ?>>Weeks</option>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="name">From</label>
									<input type="text" class="form-control" id="from" name="from" value="<?php echo isset($supplier[0]->account_payment_credit_from) ? $supplier[0]->account_payment_credit_from:"" ?>"/>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="name">Settlement</label>
									<input type="text" class="form-control" id="settlement" name="settlement" value="<?php echo isset($supplier[0]->account_credit_setlement) ? $supplier[0]->account_credit_setlement:"" ?>"/>
								</div>
							</div>
						</div>
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
				  <dt>Name</dt><dd>This field can contain only letters, numbers, dots, commas, dashes abd underscores.</dd>
				  <br/>
				  <dt>E-mail</dt><dd>The data format for this field is <i>name@domain.com</i>.</dd>				  
				  <br/>
				  <dt>...</dt><dd></dd>
				</dl>
			</div>
		</div>
	</div>	
</div>

<div class="row">
	<div class="col-md-9">
		<hr/>
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