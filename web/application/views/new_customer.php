<h1>New / Existing Customer</h1> <!--<button type="button" class="btn btn-info btn-sm">Load client</button>-->
<form role="form" id="new_customer_form" method="post" action="<?php echo base_url('index.php/customers/save_customer'); ?>">
<div class="row">
	<div class="col-md-8">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<label for="account_reference">Ref.</label>
							<input tabindex="1" type="text" class="form-control" id="account_reference" name="account_reference" maxlength="10"/>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="name">Name</label>
							<input tabindex="2" type="text" class="form-control" id="name" name="name"/>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="contact_name">Contact name</label>
							<input tabindex="3" type="text" class="form-control" id="contact_name" name="contact_name"/>
						</div>
					</div>					
				</div>

				<div class="row">
					<div class="col-md-9">
						<div class="form-group">
							<label for="address">Address</label>
							<textarea rows="3" tabindex="4" class="form-control" id="address" name="address"></textarea>
						</div>						
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="post_code">Postal Code</label>
							<input tabindex="5" type="text" class="form-control" id="post_code" name="post_code"/>
						</div>
					</div>
					<!--<div class="col-md-3">
						<div class="form-group">
							<label for="address">&nbsp;</label>
							<input tabindex="5" type="text" class="form-control" id="address1" name="address1"/>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="address">&nbsp;</label>
							<input tabindex="6" type="text" class="form-control" id="address2" name="address2"/>
						</div>
					</div>-->					
				</div>
				
				<!--<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label for="address">&nbsp;</label>
							<input tabindex="7" type="text" class="form-control" id="address3" name="address3"/>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="address">&nbsp;</label>
							<input tabindex="8" type="text" class="form-control" id="address4" name="address4"/>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="address">&nbsp;</label>
							<input tabindex="9" type="text" class="form-control" id="address5" name="address5"/>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="post_code">Postal Code</label>
							<input tabindex="11" type="text" class="form-control" id="post_code" name="post_code"/>
						</div>
					</div>
				</div>-->
				
				<!-- Statement Address -->
				<div class="row">
					<div class="col-md-9">
						<div class="form-group">
							<label for="statement_address">Statement Address</label>
							<textarea tabindex="6" rows="3" class="form-control" id="statement_address" name="statement_address"></textarea>
						</div>
					</div>	
					<div class="col-md-3">
						<div class="form-group">
							<label for="post_code">Postal Code</label>
							<input tabindex="7" type="text" class="form-control" id="post_code" name="post_code"/>
						</div>
					</div>					
				</div>
				<!--<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label for="address">&nbsp;</label>
							<input tabindex="7" type="text" class="form-control" id="address3" name="address3"/>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="address">&nbsp;</label>
							<input tabindex="8" type="text" class="form-control" id="address4" name="address4"/>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="address">&nbsp;</label>
							<input tabindex="9" type="text" class="form-control" id="address5" name="address5"/>
						</div>
					</div>
				</div>-->
				<!-- Statement Address End -->
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="account_department">Account Contact Name</label>
							<input tabindex="8" type="text" class="form-control" id="account_department" name="account_department"/>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="account_dept_number">Account Contact Number</label>
							<input tabindex="9" type="text" class="form-control" id="account_dept_number" name="account_dept_number"/>
						</div>
					</div>
				</div>
				
				<div class="row">
					
					<div class="col-md-3">
						<div class="form-group">		
							<label for="email">E-mail</label>
							<input tabindex="12" type="email" class="form-control" id="email" name="email"/>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="telephone">Telephone</label>
							<input tabindex="13" type="text" class="form-control" id="telephone" name="telephone"/>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="mobile">Mobile</label>
							<input tabindex="14" type="tel" class="form-control" id="mobile" name="mobile"/>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="fax">Fax</label>
							<input tabindex="15" type="tel" class="form-control" id="fax" name="fax"/>
						</div>
					</div>			
				</div>
				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<label for="type">Type</label>
							<select tabindex="16" class="form-control" id="type" name="type">
								<option value="1">Credit</option>
								<option value="0">Cash</option>
							</select>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label for="credit_limit">Credit limit</label>
							<input tabindex="17" type="text" id="credit_limit" class="form-control" name="credit_limit">
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label for="order_number">Order No.</label>
							<select tabindex="18" class="form-control" id="order_number" name="order_number">
								<option value="1">Required</option>
								<option value="0">Optional</option>
							</select>
						</div>
					</div>
					<!-- <div class="col-md-3">
						<div class="form-group">
							<label for="invoicing">Invoicing</label>
							<select tabindex="19" class="form-control" id="invoicing" name="invoicing">
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
					</div> -->
					<div class="col-md-2">
						<div class="form-group">
							<label for="holiday_credit">Holiday Credit</label>
							<select tabindex="20" class="form-control" id="holiday_credit" name="holiday_credit">
								<option value="1" selected>Standard</option>
								<option value="2">No Holiday</option>
							</select>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label for="prices_type">Disc.%</label>
							<input tabindex="21" type="text" class="form-control" id="discount_perc" name="discount_perc">
						</div>
					</div>					
				</div>

				<div class="row">
					
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
	</div>
	<div class="col-md-4">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">Help</h3>				
			</div>
			<div class="panel-body">
				<dl>
					<dt>Ref.</dt><dd><p>This field can only contain letters, numbers, underscores, dashes and up to 10 characteres. </p><p>If this is a cash customer, you don't have to specify a customer reference.</p></dd>
					<br/>
					<dt>Name</dt><dd>This field can contain only letters, numbers, dots, commas, dashes abd underscores.</dd>
					<br/>
					<dt>E-mail</dt><dd>The data format for this field is <i>name@domain.com</i>.</dd>				  
					<br/>
					<dt>Credit Limit</dt><dd><p>This field can only contain an integer number in the format: 1000. You don't have to use a thousands or decimal separator.</p><p>If this is a cash customer, you don't have to fill this field.</p></dd>				  
				</dl>
			</div>
		</div>
	</div>
</div>
</form>
<div id="dropdown_parents_list" style="display:none">
</div>