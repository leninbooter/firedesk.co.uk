<form role="form" id="add_items_form" method="post" action="<?php echo base_url('index.php/contracts/save_contract_item'); ?>">
<div class="row">
	<div class="col-md-5">
		<h1>Adding items to contract</h1>
	</div>
	<div class="col-md-3"></div>
	<div class="col-md-4">
		<input type="hidden" name="contract_id" id="contract_id" value="<?php echo $contract_id; ?>">
		<h2>Contract No. <?php echo $contract_id; ?></h2>
	</div>
</div>

<div class="row">
	<div class="col-md-4">
		<input type="hidden" name="customer_name" id="customer_name" value="<?php echo $customer_name; ?>">
		<h3>Client name <?php echo $customer_name; ?></h3>
		<input type="hidden" id="contract_type" name="contract_type" value="<?php echo $contract_type; ?>">
		<h4><?php echo $contract_type; ?></h4>
		<address>
			<strong>xxxxxxx xxxxxx</strong><br>
			xxx xxxxxxxx xxxxxxx xx
		</address>
	</div>
	<div class="col-md-4">
		<div class="row">
			<div class="col-md-4">
				<input type="hidden" id="delivery_charge" name="delivery_charge" value="<?php echo $delivery_charge; ?>">
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
		<input type="hidden" name="saved_address" id="saved_address" value="<?php echo $address; ?>">
		<?php echo $address; ?>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover table-responsive" id="items">
			<thead>
				<tr>
					<th>Item No</th><th>Qty</th><th>Rtn Description</th><th>No entries</th><th>Rate per</th><th>Disc. %</th><th>Value</th>
				</tr>
			</thead>
			<?php foreach($contract_items as $row): ?>
				<tr>
					<td><?php echo $row->item_no; ?></td>
					<td><?php echo $row->qty; ?></td>
					<td><?php echo $row->description; ?></td>
					<td><?php echo $row->entries_no; ?></td>
					<td><?php echo $row->rate;
							echo " ";
							switch( $row->regularity )
							{
								case 1: echo "Year"; break;
								case 2: echo "Month"; break;
								case 3: echo "Week"; break;
								case 4: echo "Day"; break;
							}
						?></td>
					<td><?php echo $row->discount_perc; ?></td>
					<td><?php echo $row->value; ?></td>
				</tr>
			<?php endforeach; ?>
				<tr>
					<td><input class="form-control" type="text" id="item_no_in"/></td><td><input class="form-control" type="text" id="qty_in"/></td><td><input class="form-control" type="text" id="description_in"/></td><td><input class="form-control" type="text" id="entry_in"/></td><td><div class="form-group"><input class="form-control" type="text" id="rate_in"/><select  class="form-control" id="regularity_in"><option value="" selected></option><option value="1">year</option><option value="2">month</option><option value="3">week</option><option value="4">day</option></select></div></td><td><input class="form-control" type="text" id="desc_in"/></td><td></td>
				</tr>
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
					 <label class="btn btn-primary <?php echo $contract_type == 2 ? "active":"" ?>">
						<input type="radio" name="options" id="sale" autocomplete="off" value="sale">Sale
					  </label>
					<?php if($contract_type == 1): ?>					  
						<label class="btn btn-primary">
							<input type="radio" name="options" id="hire" autocomplete="off" value="hire" >Hire
						</label>
					<?php endif; ?>
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
			<div class="btn-group">
			  <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				Invoices <span class="caret"></span>
			  </button>
			  <ul class="dropdown-menu" role="menu">
				<li><a href="" data-toggle="modal" data-target="#invoice_options">New</a></li>
				<li><a href="#" data-toggle="modal" data-target="#invoice_preview_date">Preview</a></li>
				<li><a href="#" data-toggle="modal" data-target="#past_invoices">Past</a></li>				
			  </ul>
			</div>
		<?php endif; ?>
	</div>
	<div class="col-md-1">
		<?php if($contract_status > 1): ?>
			<button id="print_button" type="button" data-toggle="modal" data-target="#contract_details" class="btn btn-info  btn-block" onclick="contract_details_pdf(<?php echo $contract_id; ?>)">Print</button>
		<?php endif; ?>
	</div>
	<div class="col-md-1">
		<?php if($contract_status == 2): ?>
			<button type="button" data-toggle="modal" data-target="#alerts" class="btn btn-info  btn-block" onclick="activate('<?php echo $contract_id; ?>')">Activate</button>
		<?php endif; ?>
	</div>
	<div class="col-md-1">
		<?php if($contract_status == 2): ?>
			<button type="button" data-toggle="modal" data-target="#alerts" class="btn btn-info  btn-block" onclick="abandon('<?php echo $contract_id; ?>')">Abandon</button>
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
			<?php if($contract_status < 3): ?>
				<button type="button" class="btn btn-primary  btn-block" id="save_button">Save</button>
			<?php endif; ?>
		</div>
	</div>
	<div class="col-md-1">
		<button type="button" class="btn btn-default  btn-block">Exit</button>
	</div>
</div>
</form>

<div class="modal fade" id="contract_details" tabindex="-1" role="dialog" aria-labelledby="contract_details_label" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
			</div>
			<iframe style="width:100%; height:600px" id="contract_details_content_iframe" src=""></iframe>
		</div>
	</div>
</div>
<div class="modal fade" id="alerts" tabindex="-1" role="dialog" aria-labelledby="alerts_label" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">			
		</div>
	</div>
</div>
<div class="modal fade" id="outstanding_items" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form id="items_supplied_form">
			<input type="hidden" name="contract_id" id="contract_id" value="<?php echo $contract_id; ?>">
			<div class="modal-header">
				<h4 class="modal-title">Outstanding items</h4>
			</div>			
			<div class="modal-body">			
			<?php 
				if(isset($outstanding_items))
				{					
					echo "<div class=\"row\">";
					echo "<div class=\"col-md-2\"><h4>Item No.</h4></div>";
					echo "<div class=\"col-md-3\"><h4>Description</h4></div>";
					echo "<div class=\"col-md-1\"><h4>Price</h4></div>";
					echo "<div class=\"col-md-1\"><h4>Ordered</h4></div>";
					echo "<div class=\"col-md-1\"><h4>Sent</h4></div>";
					echo "<div class=\"col-md-1\"><h4>Now?</h4></div>";
					echo "</div>";
					foreach($outstanding_items as $i)
					{
						if( $i->qty_supplied < $i->qty )
						{
							echo "<div class=\"row\">";
							echo "<input type=\"hidden\" value=\"".$i->pk_id."\" id=\"item_id\" name=\"item_id[]\">";
							echo "<div class=\"col-md-2\">".$i->item_no."</div>";
							echo "<div class=\"col-md-3\">".$i->description."</div>";
							echo "<div class=\"col-md-1\">".$i->rate."</div>";
							echo "<div class=\"col-md-1\">".$i->qty."</div>";
							echo "<div class=\"col-md-1\">".$i->qty_supplied."</div>";
							echo "<div class=\"col-md-1\"><input type=\"text\" id=\"now\" name=\"now[]\" class=\"form-control\" autocomplete=\"off\"></div>";
							echo "</div>";
						}
					}
					if(count($outstanding_items) == 0)
					{
						echo "<div class=\"row\">";
						echo "<div class=\"col-md-9\"><div class=\"alert alert-warning\" role=\"alert\">This contract does not have outstanding items</div></div>";
						echo "</div>";
					}
				}
			?>			
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-info" data-dismiss="modal" onclick="location.reload()">Refresh</button>
				<?php if( count($outstanding_items) > 0 ): ?>
				<button type="submit" class="btn btn-primary">Save changes</button>
				<button type="button" class="btn btn-primary">Save changes & print delivery note</button>
				<?php endif; ?>
			</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="invoice_options" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body">
				<a href="<?php echo base_url('index.php/invoices/generate?type=2&contract_id='.$contract_id);?>"><button type="button" class="btn btn-info">All items</button></a>
				<a href="<?php echo base_url('index.php/invoices/generate?type=1&contract_id='.$contract_id);?>"><button type="button" class="btn btn-info">Off hired & sales</button></a>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="invoice_preview_date" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<form class="form-inline">
					<div class="form-group">
						<input type="text" class="form-control datepicker" id="invoice_date_preview" readonly>
						<button type="button" class="btn btn-info">Ok</button>
						<button type="button" class="btn btn-defaul" data-dismiss="modal">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="past_invoices" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Past invoices</h4>
			</div>
			<div class="modal-body">				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>