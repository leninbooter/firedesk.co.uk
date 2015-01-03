<form role="form">
<div class="row">
	<div class="col-md-5">
		<h1>Adding items to contract</h1>
	</div>
	<div class="col-md-3"></div>
	<div class="col-md-4">
		<h2>Contract No. </h2>
	</div>
</div>

<div class="row">
	<div class="col-md-4">
		<h3>Client name</h3>
		<h4>Credit</h4>
		<address>
			<strong>Thistle Security</strong><br>
			Conde de Peñalver, 80
		</address>
	</div>
	<div class="col-md-4">
		<div class="row">
			<div class="col-md-4">
				<h4>Deliver</h4>
				No charge
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
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover table-responsive">
			<thead>
				<tr>
					<th>Item No</th><th>Qty</th><th>Rtn Description</th><th>No entries</th><th>Rate per</th><th>Disc%</th><th>Value</th>
				</tr>
			</thead>
			<div id="items">
				<tr>
					<td></td><td></td><td></td><td></td><td></td><td></td><td></td>
				</tr>
			</div>
			<div>
				<tr>
					<td><input class="form-control" type="text" id="item_no"/></td><td><input class="form-control" type="text" id="qty"/></td><td><input class="form-control" type="text" id="description"/></td><td><input class="form-control" type="text" id="entry"/></td><td><input class="form-control" type="text" id="rate"/></td><td><input class="form-control" type="text" id="desc"/></td><td><input class="form-control" type="text" id="value"/></td>
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
	<div class="col-md-1">
		<button type="button" class="btn btn-info  btn-block">Sale</button>
	</div>
	<div class="col-md-1">
		<button type="button" class="btn btn-info  btn-block">Hire</button>
	</div>
	<div class="col-md-1">
		<button type="button" class="btn btn-info  btn-block">Change</button>
	</div>
	<div class="col-md-1">
		<button type="button" class="btn btn-info  btn-block">Exchange</button>
	</div>
	<div class="col-md-1">
		<button type="button" class="btn btn-info  btn-block">Invoices</button>
	</div>
	<div class="col-md-1">
		<button type="button" class="btn btn-info  btn-block">Print</button>
	</div>
	<div class="col-md-1">
		<button type="button" class="btn btn-info  btn-block">Activate</button>
	</div>
	<div class="col-md-1">
		<button type="button" class="btn btn-info  btn-block">Abandom</button>
	</div>
	<div class="col-md-1">
		<button type="button" class="btn btn-info  btn-block">Collect</button>
	</div>
	<div class="col-md-1">
		<button type="button" class="btn btn-info  btn-block">Return</button>
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