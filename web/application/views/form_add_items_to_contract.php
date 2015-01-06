<form role="form">
<div class="row">
	<div class="col-md-5">
		<h1>Adding items to contract</h1>
	</div>
	<div class="col-md-3"></div>
	<div class="col-md-4">
		<input type="hiiden" value="<?php echo $contract_id; ?>">
		<h2>Contract No. <?php echo $contract_id; ?></h2>
	</div>
</div>

<div class="row">
	<div class="col-md-4">
		<h3>Client name <?php echo $customer_name; ?></h3>
		<h4><?php echo $contract_type; ?></h4>
		<address>
			<strong>xxxxxxx xxxxxx</strong><br>
			xxx xxxxxxxx xxxxxxx xx
		</address>
	</div>
	<div class="col-md-4">
		<div class="row">
			<div class="col-md-4">
				<h4>Delivery</h4>
				<?php echo $delivery_charge; ?>
			</div>
			<div class="col-md-4">
				<h4>Collect</h4>				
			</div>
		</div>
		<div class="row">
			<div class="col-md-8">
				<h4 class="text-center">Order Number</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-md-8">
				<h4 class="text-center">Job Ref./Order By</h4>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<h4>Site Address</h4>
		<?php echo $address; ?>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover table-responsive">
			<thead>
				<tr>
					<th>Item No</th><th>Qty</th><th>Rtn Description</th><th>No entries</th><th>Rate per</th><th>Disc. %</th><th>Value</th>
				</tr>
			</thead>
			<div id="items">
				<tr>
					<td></td><td></td><td></td><td></td><td></td><td></td><td></td>
				</tr>
			</div>
			<div>
				<tr>
					<td><input class="form-control" type="text" id="item_no"/></td><td><input class="form-control" type="text" id="qty"/></td><td><input class="form-control" type="text" id="description"/></td><td><input class="form-control" type="text" id="entry"/></td><td><div class="form-group"><input class="form-control" type="text" id="rate"/><select  class="form-control" id="regularity"><option value="" selected></option><option value="1">year</option><option value="2">month</option><option value="3">week</option><option value="4">day</option></select></div></td><td><input class="form-control" type="text" id="desc"/></td><td><input class="form-control" type="text" id="value"/></td>
				</tr>
			</div>
		</table>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<hr/>
	</div>
</div>

<div class="row">
		<div class="col-md-2">
			<?php if($contract_status < 5): ?>
			<div class="btn-group" data-toggle="buttons">		
					 <label class="btn btn-primary">
						<input type="radio" name="options" id="sale" autocomplete="off">Sale
					  </label>
					<!-- <button type="button" class="btn btn-info  btn-block">Sale</button>-->
					<label class="btn btn-primary">
						<input type="radio" name="options" id="hire" autocomplete="off">Hire
					  </label>
					<!-- <button type="button" class="btn btn-info  btn-block">Hire</button>-->
			</div>
			<?php endif; ?>
		</div>
		<!--<div class="col-md-1">				
			<?php if($contract_status < 5): ?>
				 <button type="button" class="btn btn-info  btn-block">Sale</button>
			<?php endif; ?>
		</div>
		<div class="col-md-1">
			<?php if($contract_status < 5): ?>
				<button type="button" class="btn btn-info  btn-block">Hire</button>
			<?php endif; ?>
		</div>-->
	<div class="col-md-1">
		<?php if($contract_status < 5): ?>
			<button type="button" class="btn btn-info  btn-block">Changes</button>
		<?php endif; ?>
	</div>
	<div class="col-md-1">
		<?php if($contract_status == 3): ?>
			<button type="button" class="btn btn-info  btn-block">Exchange</button>
		<?php endif; ?>
	</div>
	<div class="col-md-1">
		<?php if($contract_status == 3 || $contract_status == 4): ?>
			<button type="button" class="btn btn-info  btn-block">Invoices</button>
		<?php endif; ?>
	</div>
	<div class="col-md-1">
		<?php if($contract_status > 1): ?>
			<button type="button" class="btn btn-info  btn-block">Print</button>
		<?php endif; ?>
	</div>
	<div class="col-md-1">
		<?php if($contract_status == 2): ?>
			<button type="button" class="btn btn-info  btn-block">Activate</button>
		<?php endif; ?>
	</div>
	<div class="col-md-1">
		<?php if($contract_status == 2): ?>
			<button type="button" class="btn btn-info  btn-block">Abandom</button>
		<?php endif; ?>
	</div>
	<div class="col-md-1">
		<?php if($contract_status > 2): ?>
			<button type="button" class="btn btn-info  btn-block">Collect</button>
		<?php endif; ?>
	</div>
	<div class="col-md-1">
		<?php if($contract_status == 3): ?>
			<button type="button" class="btn btn-info  btn-block">Returns</button>
		<?php endif; ?>
	</div>
	<div class="col-md-1">
		<div class="form-group">
			<a href="<?php echo base_url('index.php/contracts/form_add_items'); ?>"><button type="button" class="btn btn-primary  btn-block">Save</button></a>
		</div>
	</div>
	<div class="col-md-1">
		<button type="button" class="btn btn-default  btn-block">Exit</button>
	</div>
</div>
</form>