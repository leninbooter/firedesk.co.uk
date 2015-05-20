<form role="form" id="add_items_form" method="post" action="<?php echo base_url('index.php/contracts/save_contract_item'); ?>">
<div class="row">
	<div class="col-md-6">
		<input type="hidden" name="contract_id" id="contract_id" value="<?php echo $contract_id; ?>">
        <h1>Contract No. <?=$contract_id?> <small><?=date('d F Y H:i', strtotime($contract_details->creation_date))?> </small></h1>
        <kbd><?php echo $contract_details->status_name; ?></kbd>
	</div>
	<div class="col-md-6">				
        <!--<h1 class="text-right"><small><?=date('d F Y H:i', strtotime($contract_details->creation_date))?> </small></h1>-->
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
                <?php echo $soldItems; ?>
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
                <?php echo $hiredItems; ?>
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

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Notes</h3>
            </div>
            <div class="panel-body">
                <?php echo $contract_details->notes;  ?>
            </div>
        </div>
    </div>
</div>

<div class="row">               
        
    <?php if($contract_status > 2): ?>
        <div class="col-md-1">		
                <div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Collect <span class="caret"></span></button>		
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="javascript:void(0)" onclick="newCollect()">New</a></li>
                        <li><a href="javascript:void(0)" onclick="pastCollect()">Past</a></li>				
                    </ul>
              </div>                                           
        </div>
    <?php endif; ?>

    <?php if ($contract_status <= 3): ?>        		        
            <div class="col-md-1"><button type="button" class="btn btn-info " data-toggle="modal" data-target="#sales_stock_modal">Sale</button></div>
            <div class="col-md-1"><button type="button" class="btn btn-info " data-toggle="modal" data-target="#hire_fleet_modal" data-backdrop="static" data-keyboard="false" >Hire</button></div>
            <div class="col-md-1"><button type="button" class="btn btn-info " data-toggle="modal" data-target="#hired_items_modal">Cross Hire</button></div>
            <div class="col-md-1"><button type="button" class="btn btn-info">Changes</button></div>
            <!--<div class="col-md-1"><button type="button" class="btn btn-primary  btn-block" id="save_button">Save</button></div>			-->
    <?php endif; ?>
    
    <?php if($contract_status == 2): ?>
        <div class="col-md-1"><button type="button"  class="btn btn-info  btn-block" onclick="activate()">Activate</button></div>
        <div class="col-md-1"><button type="button" data-toggle="modal" data-target="#alerts" class="btn btn-info" onclick="abandon()">Abandon</button></div>
    <?php endif; ?>
    
    <?php if ($contract_status == 3): ?>
        <div class="col-md-1">
        <div class="btn-group">		
                <button type="button" class="btn btn-info  dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Returns <span class="caret"></span></button>		
                <ul class="dropdown-menu" role="menu">
                        <li><a href="javascript:void(0)" onclick="newHiredReturns()">Hires</a></li>
                        <li><a href="javascript:void(0)" onclick="returnSoldItems()">Sales</a></li>				
                    </ul>
            </div>
        </div>
        <div class="col-md-1"><button type="button" class="btn btn-info">Exchange</button></div>        
    <?php endif; ?>
    
    <?php if($contract_status == 3 || $contract_status == 4): ?>
        <div class="col-md-1">
        <div class="btn-group">
          <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
            Invoices <span class="caret"></span>
          </button>
          <ul class="dropdown-menu" role="menu">
            <li><a href="javascript:void(0)" onclick="generateInvoice('All')" data-toggle="modal" data-target="#processing">Invoice all items</a></li>
            <li><a href="javascript:void(0)" onclick="generateInvoice('Off')">Invoice off hired & sales</a></li>
            <!--<li><a href="#" data-toggle="modal" data-target="#invoice_preview_date">Preview</a></li>-->
            <li><a href="javascript:void(0)" onclick="pastInvoices()">Past</a></li>				
          </ul>
        </div>
        </div>
    <?php endif; ?>
    
    <div class="col-md-1"><button id="print_button" type="button" data-toggle="modal" data-target="#contract_details" class="btn btn-info  btn-block" onclick="contract_details_pdf()">Print</button></div>    
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
<div class="modal fade" id="processing" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body">
				<p class="text-center"><img src="<?=base_url('assets/images/ajax-loader.gif')?>"></p>
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
<?php if( $contract_status <= 3): ?>
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
                                <th style="width:25%">Description</th>                
                                <th style="width:10%">In Stock</th>
                                <th style="width:10%">On Order</th>
                                <th style="width:10%">Request Qty</th>
                                <th style="width:10%">Discount</th>
                                <th style="width:10%">Price</th>
                                <th style="width:15%">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="hidden" id="item_id" name="item_id" value=""><input type="hidden" id="sale_item_cost" name="sale_item_cost" value=""><p class="form-control-static" id="stock_no"></p></td>
                                <td><input type="text" id="sale_item_description" name="sale_item_description" class="form-control"></td>
                                <td><p class="form-control-static" id="sale_item_in_stock"></p></td>
                                <td><p class="form-control-static" id="sale_item_on_order"></p></td>
                                <td><input type="text" id="sale_item_qty" name="sale_item_qty" class="form-control" onchange="getPrice(this)"></td>
                                <td><input type="text" id="disc" name="disc" class="form-control percentage"></td>
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

<!-- Cross Hired items modal -->
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
				<button type="button" class="btn btn-info" onclick="$('#cross_hired_form').submit()" >Add items</button>
			</div>
			</form>
		</div>
	</div>
</div>

<!-- Hired items modal -->
<div class="modal fade" id="hire_fleet_modal" tabindex="-1" role="dialog" aria-labelledby="hire_fleet_modal" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">			
			<div class="modal-header">
				<h4 class="modal-title">Hire Fleet</h4>
			</div>
			<div class="modal-body">				
				<form id="hireItemForm" class="form-inline">                
                    <input type="hidden" id="contractID" name="contractID" value="<?php echo $contract_id; ?>"/>
                    <input type="hidden" id="hireItemType" name="hireItemType" value=""/>                    
                    <div class="form-group" style="width:35%">
                        <label ><p class="text-right">Search:</p></label>
                            <input type="text" id="search_hire_item_field" name="search_hire_item_field" class="form-control input-sm" STYLE="width:70%">
                            <input type="hidden" id="hire_item_id" name="hire_item_id">
                            <input type="hidden" id="allocated" name="allocated">
                    </div>                       
                    
                    <div class="form-group"  style="width:15%">                    
                        <label   class="" style=""><p class="text-right">Avbl:</p></label>
                            <input type="text" id="search_hire_item_avbl_qty"  class="form-control input-sm" STYLE="width:70%" disabled>
                    </div>
                    
                    <div class="form-group"  style="width:15%">                    
                        <label  class="" style=""><p class="text-right">Qty:</p></label>
                            <input type="text" id="hire_item_qty" name="hire_item_qty" class="form-control input-sm" STYLE="width:60%">
                    </div>
                    <div class="form-group" style="width:20%">
                        <label class=""><p class="text-right">Price:</p></label>
                            <input type="text" id="hire_item_price" name="hire_item_price" class="form-control input-sm" STYLE="width:70%">
                    </div>
                    
                    <div class="form-group" style="width:10%">
                        <p class="text-right"><button type="button" class="btn btn-info" id="addHire_btn" name="addHire_btn" onclick="$('#hireItemForm').submit();" >Add items</button></p>
                    </div>
				</form>
				<div id="componentsRow"  class="row">					                   
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
				<div id="recommendedRow" class="row">
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
                <!--<div class="row">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <p class="text-right"><button type="button" class="btn btn-info" id="addHire_btn" name="addHire_btn" onclick="$('#hireItemForm').submit();" >Add items</button></p>
                    </div>
                </div>-->
                
                <div id="allocatedOnRowForm" class="row" style="display:none">
                    <div class="col-md-12">
                        <div  class="panel panel-danger">
                            <div class="panel-heading">
                                <h4 class="panel-title">Already allocated on hire</h4>
                            </div>
                            <div class="panel-body">
                                <form id="allocatedOnForm" role="form">                                
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <p id="itemLabelP" class="form-control-static"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                               <label>Contract</label>
                                               <p id="contractIDP" class="form-control-static"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                               <label>Date out</label>
                                               <p id="itemDateOutP" class="form-control-static"></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                               <label>Customer</label>
                                               <p id="custNameAddressP" class="form-control-static"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                               <label>Site Address</label>
                                               <p id="siteAddressP" class="form-control-static"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>                                    
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <blockquote>
                                              <p>Confirmation will make this hire fleet item swap to this contract. Original entry will be prefixed with a "?".</p>
                                            </blockquote>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-default btn-block" data-dismiss="modal">Abandon</button>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="submit" class="btn btn-default btn-block">Confirm</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
				</div>
                
                <!-- <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Non Stock</h3>
                            </div>
                            <div class="panel-body">
                                <form id="nsHIForm" role="form">
                                <table class="table table-condensed table-responsive">
                                    <thead>
                                        <tr>
                                            <th>Ref. No.</td>
                                            <th>Description</td>
                                            <th style="width: 10%">Qty</td>
                                            <th style="width: 15%">Hire Rate</td>
                                            <th>Charging Band</td>
                                            <th></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" class="form-control" id="nsHIRef" name="nsHIRef" vaue=""></td>
                                            <td><input type="text" class="form-control" id="nsHIDesc" name="nsHIDesc" vaue=""></td>
                                            <td><input type="text" class="form-control" id="nsHIQty" name="nsHIQty" vaue=""></td>
                                            <td><input type="text" class="form-control" id="nsHIRate" name="nsHIRate" vaue=""></td>
                                            <td><select class="form-control" id="nsHICB" name="nsHICB">
                                                </select>
                                            </td>                                            
                                            <td><button type="button" class="btn btn-info btn-block" onclick="$('#nsHIForm').submit();">Add item</button></td>
                                        </tr>
                                        
                                    </tbody>
                                </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>-->
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>				
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<div class="modal fade" id="multipurposeModal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
                <input type="hidden" id="contractID" name="contractID" value="<?php echo $contract_id; ?>"/>
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>                    
                </div>
        </div>
    </div>
</div>