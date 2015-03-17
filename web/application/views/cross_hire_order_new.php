<div class="row">
	<div class="col-md-12">&nbsp;</div>
</div>
<div class="row">
	<div class="col-md-6"><h1>New Cross Hire Order</h1></div>
	<div class="col-md-6"></div>
</div>
<form id="generate_crosss_hire_order_form" role="form" method="post" action="<?php echo base_url('index.php/cross_hire/generate_order'); ?>">
	<div class="row">
		<div class="col-md-9">
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label for="supplier_name">Supplier</label>
						<input type="hidden" id="supplier_pk_id" name="supplier_pk_id"/>
						<input type="text" class="form-control" id="supplier_name" name="suplier_name" tabindex="1"/>
					</div>
				</div>
				<div class="col-md-2">
				<label for="">&nbsp;</label>
					<a href="<?php echo base_url('index.php/suppliers/new_existing'); ?>"><button type="button" class="btn btn-primary  btn-block">Add Supplier</button></a>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label for="delivery_address">Delivery Address</label>
						<textarea  class="form-control" id="delivery_address" name="delivery_address"  tabindex="2"></textarea>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="contact_name">Contact Name</label>
						<input type="text" class="form-control" id="contact_name" name="contact_name"  tabindex="3"/>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<label for="contact_telephone">Contact Telephone</label>
						<input type="text" class="form-control" id="contact_telephone" name="contact_telephone"  tabindex="4"/>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="contact_email">Contact E-mail</label>
						<input type="text" class="form-control" id="contact_email" name="contact_email"  tabindex="5"/>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-9"></div>
				<div class="col-md-3">
					<button type="submit" class="btn btn-primary  btn-block" tabindex="6">Continue</button>
				</div>
			</div>
		</div>
		
		
		<!-- Help -->
		<div class="col-md-3">
			<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">Help</h3>				
			</div>
			<div class="panel-body">
				<dl>
				  <dt></dt><dd></dd>
				  <br/>				  
				</dl>
			</div>
		</div>
		</div>
		<!-- Help end -->
	</div>
</form>