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
		<input type="hidden" name="customerID" id="customerID" value="<?php echo $customerID; ?>">
		<h3>Client name <?php echo $customer_name; ?></h3>
		<input type="hidden" id="contract_type" name="contract_type" value="<?php echo $contract_type; ?>">
		<h4><?php echo $contract_type; ?></h4>
		<address>
			<?php echo $contract_details->address; ?>
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
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Sale</h3>
            </div>
            <div class="panel-body">
                <table class="table table-responsive table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>Stock No.</th>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Discount</th>
                            <th>Total</th>                            
                            <th></th>                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($SoldItems as $i): ?>
                            <tr>
                                <td style="display:none"><input type="hidden" id="itemRowID" name="itemRowID[]" value="<?php echo $i->pk_id; ?>"></td>
                                <td><?php echo $i->stock_number; ?></td>
                                <td><?php echo $i->description; ?></td>
                                <td><?php echo $i->qty; ?></td>
                                <td><?php echo $i->rate; ?></td>
                                <td><?php echo $i->discount_perc; ?></td>
                                <td><?php echo $i->value; ?></td>
                                <td><?php if ($contract_status < 3): ?><button type="button" class="btn btn-default btn-sm" onclick="deleteSaleItem(this)">Delete</button><?php endif; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Hire</h3>
            </div>
            <div class="panel-body">
                <table class="table table-responsive table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>Stock No.</th>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Discount</th>
                            <th></th>                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php $parent = false; ?>
                        <?php foreach($HiredItems as $i): ?>
                            <?php   if ( $parent == false || $parent != $i->parent_item) {
                                        $parent = $i->item_no;
                                    }
                            ?>
                            <tr>
                                <td style="display:none"><input type="hidden" id="itemRowID" name="itemRowID[]" value="<?php echo $i->pk_id; ?>"></td>
                                <td><?php echo $i->productID; ?></td>
                                <td <?php echo $parent == $i->parent_item ? "style=\"padding-left:20px;\"": "";  ?>  ><?php echo $i->description; ?></td>
                                <td><?php echo $i->qty; ?></td>
                                <td><?php echo $i->rate; ?></td>
                                <td><?php echo $i->discount_perc; ?></td>
                                <td><?php if ($contract_status < 3): ?><button type="button" class="btn btn-default btn-sm" onclick="deleteHireItem(this)">Delete</button><?php endif; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Cross Hire</h3>
            </div>
            <div class="panel-body">
                <table class="table table-responsive table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>Stock No.</th>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Discount</th>
                            <th>Day 1</th>                            
                            <th>Day 2</th>                            
                            <th>Day 3</th>                            
                            <th>Week</th>                            
                            <th>W/End</th>                            
                            <th></th>                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php $parent = false; ?>
                        <?php foreach($CrossedHireItems as $i): ?>
                            <tr>
                                <td style="display:none"><input type="hidden" id="itemRowID" name="itemRowID[]" value="<?php echo $i->pk_id; ?>"></td>
                                <td><?php echo $i->stock_number; ?></td>
                                <td ><?php echo $i->description; ?></td>
                                <td><?php echo $i->qty; ?></td>
                                <td><?php echo $i->rate; ?></td>
                                <td><?php echo $i->discount; ?></td>
                                <td><?php echo $i->day1; ?></td>
                                <td><?php echo $i->day2; ?></td>
                                <td><?php echo $i->day3; ?></td>
                                <td><?php echo $i->week; ?></td>
                                <td><?php echo $i->wend; ?></td>
                                <td><?php if ($contract_status < 3): ?><button type="button" class="btn btn-default btn-sm" onclick="deleteCrossHiredItem(this)">Delete</button><?php endif; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--<div class="row">
	<div class="col-md-12">
		<table class="table table-hover table-responsive" id="items">
			<thead>
				<tr>
					<th>Item No</th>
					<th style="width:10%">Qty</th>
					<th style="width:30%" >Rtn Description</th>
					<th style="width:20%">Rate per</th>
					<th style="width:10%">Disc. %</th>
					<th style="width:20%">Value</th>
				</tr>
			</thead>
			<?php //foreach($contract_items as $row): ?>
				<tr>					
					<td><?php //echo $row->item_no; ?></td>
					<td><?php //echo $row->qty; ?></td>
					<td><?php //echo $row->description; ?></td>
					<td><?php /*echo $row->rate;
							echo " ";
							switch( $row->regularity )
							{
								case 1: echo "Year"; break;
								case 2: echo "Month"; break;
								case 3: echo "Week"; break;
								case 4: echo "Day"; break;
							}
						*/?></td>
					<td><?php //echo $row->discount_perc; ?></td>
					<td><?php //echo $row->value; ?></td>
				</tr>
			<?php //endforeach; ?>
				<tr>					
										
				</tr>
		</table>
		<table class="table table-condensed" style="margin-bottom:0">
			<tr>							
				<td style="text-align:right; ">
					<span style="font-size:120%;">Total </span>
					<span id="total_amount_span" style="font-size:120%;">
						<?php //echo $contract_details->total_amount;?>
					</span>
				</td>
			</tr>
		</table>
		<table class="table table-responsive table-condensed">
			<tr class="success">
				<td><input class="form-control" type="text" id="item_no_in"/></td>
				<td style="width:10%"><input class="form-control" type="text" id="qty_in"/></td>
				<td style="width:30%"><input class="form-control" type="text" id="description_in"/></td>
				<td style="width:20%"><div class="form-group"><input class="form-control" type="text" id="rate_in"/><select class="form-control" id="regularity_in"><option value="" selected></option><option value="1">year</option><option value="2">month</option><option value="3">week</option><option value="4">day</option></select></div></td>
				<td style="width:10%"><input class="form-control" type="text" id="desc_in"/></td>
				<td style="width:20%">
					<?php //if($contract_status < 5): ?>
						<div class="btn-group" data-toggle="buttons">		
								 <label class="btn btn-primary <?php //echo $contract_type_sale_hire == 2 ? "active":"" ?>">
									<input type="radio" name="options" id="sale" autocomplete="off" value="sale">Sale
								  </label>
								<?php //if($contract_type_sale_hire == 1): ?>					  
									<label class="btn btn-primary">
										<input type="radio" name="options" id="hire" autocomplete="off" value="hire" >Hire
									</label>
								<?php //endif; ?>
						</div>
					<?php //endif; ?>
				</td>
			</tr>
		</table>
	</div>
</div>-->

<div class="row">
	<div class="col-md-12">
		<hr/>
	</div>
</div>

<div class="row">
    <div class="col-md-1">	
        <?php if($contract_status < 3): ?>
            <button type="button" class="btn btn-info  btn-block" data-toggle="modal" data-target="#sales_stock_modal">Sale</button>
        <?php endif; ?>
    </div>		
    <div class="col-md-1">
        <?php if($contract_status < 3): ?>
            <button type="button" class="btn btn-info  btn-block" data-toggle="modal" data-target="#hire_fleet_modal">Hire</button>
        <?php endif; ?>
    </div>
    <div class="col-md-2">				
        <?php if($contract_status < 3): ?>
             <button type="button" class="btn btn-info  btn-block" data-toggle="modal" data-target="#hired_items_modal">Cross Hire</button>
        <?php endif; ?>
    </div>
	<div class="col-md-2">
		<?php if($contract_status < 3): ?>
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
	<div class="col-md-2">
		<?php if($contract_status == 2): ?>
			<button type="button" data-toggle="modal" data-target="#alerts" class="btn btn-info  btn-block" onclick="abandon('<?php echo $contract_id; ?>')">Abandon</button>
		<?php endif; ?>
	</div>	
</div>
<br>
<div class="row">
    <?php if($contract_status > 2): ?>
        <div class="col-md-1">		
                <button type="button" class="btn btn-info  btn-block">Collect</button>		
        </div>
    <?php endif; ?>

    <?php if($contract_status == 3): ?>
        <div class="col-md-1">		
                <button type="button" class="btn btn-info  btn-block">Returns</button>		
        </div>
    <?php endif; ?>

	<div class="col-md-1">
		<button type="button" class="btn btn-default  btn-block">Exit</button>
	</div>
    
    <?php if($contract_status < 3): ?>
        <div class="col-md-1">
            <button type="button" class="btn btn-primary  btn-block" id="save_button">Save</button>			
        </div>
    <?php endif; ?>
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
				<?php // if( count($outstanding_items) > 0 ): ?>
				<!--<button type="submit" class="btn btn-primary">Save changes</button>
				<button type="button" class="btn btn-primary">Save changes & print delivery note</button>-->
				<?php // endif; ?>
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

<!-- Sale items modal -->
<?php if( $contract_status < 3): ?>
<div class="modal fade" id="sales_stock_modal" tabindex="-1" role="dialog" aria-labelledby="sales_stock_modal" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">			
			<div class="modal-header">
				<h4 class="modal-title">Sales Stock</h4>
			</div>
            <div class="modal-body">
                <form id="sales_items_form" action="">
                <input type="hidden" id="contractID" name="contractID" value="<?php echo $contract_id; ?>"/>
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th style="width:10%">Stock No.</th>
                                <th style="width:30%">Description</th>                
                                <th style="width:10%">In Stock</th>
                                <th style="width:10%">On Order</th>
                                <th style="width:10%">Request Qty</th>
                                <th style="width:10%">Price</th>
                                <th style="width:20%">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="hidden" id="item_id" name="item_id" value=""><p class="form-control-static" id="stock_no"></p></td>
                                <td><input type="text" id="sale_item_description" name="sale_item_description" class="form-control"></td>
                                <td><p class="form-control-static" id="sale_item_in_stock"></p></td>
                                <td><p class="form-control-static" id="sale_item_on_order"></p></td>
                                <td><input type="text" id="sale_item_qty" name="sale_item_qty" class="form-control" onchange="getPrice(this)"></td>
                                <td><input type="text" id="price" name="price" class="form-control"></td>
                                <td><p class="form-control-static text-right" id="sale_item_total"></p></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-info" id="addSale_btn" name="addSale_btn" onclick="$('#sales_items_form').submit();">Add items</button>
			</div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Cross Hired items modal -->
<?php if( $contract_status < 3): ?>
<div class="modal fade" id="hired_items_modal" tabindex="-1" role="dialog" aria-labelledby="receipts_modal" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form id="cross_hired_form"  role="form" >
            <input type="hidden" id="contractID" name="contractID" value="<?php echo $contract_id; ?>"/>
			<div class="modal-header">
				<h4 class="modal-title">Available cross hired equipment</h4>
			</div>
			<div class="modal-body">				
					<table id="hire_items_table" class="table table-hover table-condensed">
							<thead>
								<tr>
									<th style="width:10%">Hired from</th>
									<th style="width:10%">Order number</th>
									<th style="width:10%">Cost</th>
									<th style="width:10%">Qty</th>
									<th style="width:30%">Description</th>																	
									<th style="width:30%">Charging Band</th>																	
								</tr>
							</thead>
							<tbody>
								<?php foreach($hired_items as $item): ?>
									<tr>
										<td>
										<input id="chi_stock_id_in" name="chi_stock_id_in[]" type="hidden" value="<?php echo $item->stock_id; ?>"/>
										<input id="chi_order_item_id" name="chi_order_item_id[]" type="hidden" value="<?php echo $item->fk_cross_hire_order_item_id; ?>"/>
										<?php echo $item->hired_from; ?></td>
										<td><?php echo $item->cross_hire_order_id; ?></td>
										<td>
										<input id="chi_cost_in" name="chi_cost_in[]" type="hidden" value="<?php echo $item->cost; ?>"/>
										<?php echo $item->cost; ?></td>
										<td>
										<select id="chi_qty_in" name="chi_qty_in[]" class="form-control" onchange="chiQtyChanged(this);">
											<?php for($i=0; $i <= $item->max; $i++): ?>
												<option><?php echo $i; ?></option>
											<?php endfor; ?>
										</select>
										</td>
										<td>
										<input id="chi_description_in" name="chi_description_in[]" type="hidden" value="<?php echo $item->description; ?>"/>
										<?php echo $item->description; ?></td>
                                        <td>
                                            <table style="display:none">
                                                <tr><td style="width:40%">Rate</td><td style="width:30%"><input type="text" id="rate" name="rate[]" class="form-control"></td><td style="width:30%"></td></tr>
                                                <tr><td>1 Day</td><td><input type="text" id="day1"  name="day1[]" class="form-control percentage"></td><td><p class="form-control-static text-right"></p></td></tr>
                                                <tr><td>2 Day</td><td><input type="text" id="day2"  name="day2[]" class="form-control percentage"></td><td><p class="form-control-static text-right"></p></td></tr>
                                                <tr><td>3 Day</td><td><input type="text" id="day3"  name="day3[]" class="form-control percentage"></td><td><p class="form-control-static text-right"></p></td></tr>
                                                <tr><td>Week</td><td><input type="text" id="week"   name="week[]" class="form-control percentage"></td><td><p class="form-control-static text-right"></p></td></tr>
                                                <tr><td>W/End</td><td><input type="text"id="wend" name="wend[]"  class="form-control percentage"></td><td><p class="form-control-static text-right"></p></td></tr>
                                            </table>
                                        </td>
									</tr>
								<?php endforeach; ?>
							</tbody>
					</table>				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-info" onclick="$(this).prop('disabled', true); $('#cross_hired_form').submit()" >Add items</button>
			</div>
			</form>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- Hired items modal -->
<?php if( $contract_status < 3): ?>
<div class="modal fade" id="hire_fleet_modal" tabindex="-1" role="dialog" aria-labelledby="hire_fleet_modal" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">			
			<div class="modal-header">
				<h4 class="modal-title">Hire Fleet</h4>
			</div>
			<div class="modal-body">				
				<form id="hireItemForm" class="form-horizontal">
                    <input type="hidden" id="contractID" name="contractID" value="<?php echo $contract_id; ?>"/>
                    <div class="form-group">
                        <label class="col-sm-1">Search:</label>
                        <div class="col-sm-8">
                            <input type="text" id="search_hire_item_field" name="search_hire_item_field" class="form-control input-sm">
                            <input type="hidden" id="hire_item_id" name="hire_item_id">
                        </div>						
                        <label class="col-sm-1"><p class="text-right">Price:</p></label>
                        <div class="col-sm-2">
                            <input type="text" id="hire_item_price" name="hire_item_price" class="form-control input-sm">
                        </div>						
                    </div>
				</form>
				<div  class="row">					                   
                   <div class="col-md-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h4 class="panel-title">Multipart item components</h4>
                            </div>
                            <form id="hireFleetItemComponentsForm" role="form" >
                                <div id="components_panel">
                                </div>
                            </form>
                        </div>
                   </div>
                </div>
				<div class="row">
                    <div class="col-md-12">
                        <div  class="panel panel-primary">
                            <div class="panel-heading">
                                <h4 class="panel-title">Recommended items</h4>
                            </div>
                            <form id="hireFleetItemAccesoriesForm" role="form" >
                                <div id="recommended_items_panel">
                                </div>
                            </form>
                        </div>
                    </div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-info" id="addHire_btn" name="addHire_btn" onclick="$('#hireItemForm').submit();" >Add items</button>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>