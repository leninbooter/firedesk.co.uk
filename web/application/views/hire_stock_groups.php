<div class="row">
	<div class="col-md-6"><h1>Hire Fleet Family Groups</h1></div>
	<div class="col-md-6">
	<?php if(isset($editing)): ?>
		<?php if($editing): ?>
			<div class="alert alert-warning" role="alert"><b>Editing <?php echo $item[0]->description; ?>.</b><p>Unless you change the number of the item, all changes made will overwrite existing data.</p></div>
		<?php endif; ?>
	<?php endif; ?>
			
	</div>
</div>
<div class="row">
	<div class="col-md-9">
		<div class="row">
			<div class="col-md-12">
				<div id="gridbox" style="width:100%;height:350px; box-sizing:content-box !important"></div>
				<br>
			</div>
		</div>
		<div class="row">
		<form role="form" id="new_hire_fleet_family_group" method="post" action="<?php echo base_url('index.php/hire_stock/ins_group'); ?>">
			<input type="hidden" id="group_id" name="group_id" value=""/>
			<div class="col-md-12">
				<div class="panel panel-primary">
				<div class="panel-heading">Add family group</div>
				<div class="panel-body">					
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="name">Family Name</label>
									<input autocomplete="off" type="text" class="form-control" id="name" name="name" value="<?php echo isset($name) ? $name:"" ?>"/>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="basic_rate">Basic rate</label>
									<input autocomplete="off" type="text" class="form-control" id="basic_rate" name="basic_rate" value="<?php echo isset($basic_rate) ? $basic_rate:"" ?>"/>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
										<label for="calc_code">Calc. code</label>
										<select id="charging_band" name="charging_band"  class="form-control" onchange="">
											<option value="" selected></option>
											<option value="new">Add charging band</option>
											<option disabled>-</option>
										<?php foreach($calc_codes as $code): ?>
											<option value="<?php echo $code->pk_id; ?>"><?php echo $code->name; ?></option>
										<?php endforeach; ?>											
										</select>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
										<label for="vat_code">Vat code</label>
										<select id="vat_code" name="vat_code" class="form-control">
											<?php foreach($vats as $option): ?>
											<option value="<?php echo $option->pk_id; ?>" <?php if(isset($item[0]->fk_vat_code)) { if($item[0]->fk_vat_code == $option->pk_id) { echo "selected";} } ?>><?php echo $option->description." (".$option->percentage."%)"; ?></option>
											<?php endforeach; ?>
										</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="">Accesory group</label>
									<ul id="accesory_group_ul" class="list-unstyled" >										
									</ul>									
									Search an item to add it as accesory <span style="font-size:150%">&darr;</span>
								<input type="text" id="search_items_for_group" class="form-control input-sm" />
								<input type="hidden" id="accesory_groups" name="accesory_groups" />
								</div>
							</div>							
							<div class="col-md-4"></div>
						</div>
						<div class="row">
							<div class="col-md-6">							
								<button type="button" class="btn btn-primary  btn-block"onclick="submit_new_hire_fleet_family_group();">Save</button>
							</div>
						</div>							
				</div>
			</div>
			</div>
		</form>
		</div>		
	</div>
	<div class="col-md-3">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">Help</h3>				
			</div>
			<div class="panel-body">
				<dl>
				  <dt>Family name</dt><dd>This field can be any combination of letters an dnumbers up to 15 characters.</dd>
				  <br/>				  
				</dl>
			</div>
		</div>
	</div>	
</div>

<div class="modal fade" id="new_charging_band_modal" tabindex="-1" role="dialog" aria-labelledby="new_charging_band_modal" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<form id="new_charging_band_form" role="form" >			
			<div class="modal-header">
				<h4 class="modal-title">New charging band</h4>
			</div>
			<div class="modal-body">				
				<div class="row">
					<div class="col-md-4"><label>Name</label></div>
					<div class="col-md-8"><input type="text" class="form-control" id="name" name="name"/></div>
				</div>
				<div class="row">
					<div class="col-md-4"><label>Rate</label></div>
					<div id="rate_div" class="col-md-4"></div>
					<div class="col-md-4"></div>
				</div>
				<div class="row">
					<div class="col-md-4"><label>4 Hr</label></div>
					<div class="col-md-4"><input type="text" class="form-control" id="_4hr_perc" name="_4hr_perc"/></div>
					<div class="col-md-4"><input type="text" class="form-control" id="_4hr_amount" name="_4hr_amount"/></div>
				</div>
				<div class="row">
					<div class="col-md-4"><label>8 Hr</label></div>
					<div class="col-md-4"><input type="text" class="form-control" id="_8hr_perc" name="_8hr_perc"/></div>
					<div class="col-md-4"><input type="text" class="form-control" id="_8hr_amount" name="_8hr_amount"/></div>
				</div>
				<div class="row">
					<div class="col-md-4"><label>1 Day</label></div>
					<div class="col-md-4"><input type="text" class="form-control" id="_1day_perc" name="_1day_perc"/></div>
					<div class="col-md-4"><input type="text" class="form-control" id="_1day_amount" name="_1day_amount"/></div>
				</div>
				<div class="row">
					<div class="col-md-4"><label>2 Day</label></div>
					<div class="col-md-4"><input type="text" class="form-control" id="_2day_perc" name="_2day_perc"/></div>
					<div class="col-md-4"><input type="text" class="form-control" id="_2day_amount" name="_2day_amount"/></div>
				</div>
				<div class="row">
					<div class="col-md-4"><label>3 Day</label></div>
					<div class="col-md-4"><input type="text" class="form-control" id="_3day_perc" name="_3day_perc"/></div>
					<div class="col-md-4"><input type="text" class="form-control" id="_3day_amount" name="_3day_amount"/></div>
				</div>
				<div class="row">
					<div class="col-md-4"><label>4 Day</label></div>
					<div class="col-md-4"><input type="text" class="form-control" id="_4day_perc" name="_4day_perc"/></div>
					<div class="col-md-4"><input type="text" class="form-control" id="_4day_amount" name="_4day_amount"/></div>
				</div>
				<div class="row">
					<div class="col-md-4"><label>5 Day</label></div>
					<div class="col-md-4"><input type="text" class="form-control" id="_5day_perc" name="_5day_perc"/></div>
					<div class="col-md-4"><input type="text" class="form-control" id="_5day_amount" name="_5day_amount"/></div>
				</div>
				<div class="row">
					<div class="col-md-4"><label>6 Day</label></div>
					<div class="col-md-4"><input type="text" class="form-control" id="_6day_perc" name="_6day_perc"/></div>
					<div class="col-md-4"><input type="text" class="form-control" id="_6day_amount" name="_6day_amount"/></div>
				</div>
				<div class="row">
					<div class="col-md-4"><label>Week</label></div>
					<div class="col-md-4"><input type="text" class="form-control" id="_week_perc" name="_week_perc"/></div>
					<div class="col-md-4"><input type="text" class="form-control" id="_week_ammount" name="_week_ammount"/></div>
				</div>
				<div class="row">
					<div class="col-md-4"><label>Weekend</label></div>
					<div class="col-md-4"><input type="text" class="form-control" id="_weekend_perc" name="_weekend_perc"/></div>
					<div class="col-md-4"><input type="text" class="form-control" id="_weekend_ammount" name="_weekend_ammount"/></div>
				</div>
				<div class="row">
					<div class="col-md-4"><label>Subsequent day</label></div>
					<div class="col-md-4"><input type="text" class="form-control" id="_subsequent_perc" name="_subsequent_perc"/></div>
					<div class="col-md-4"><input type="text" class="form-control" id="_subsequent_ammount" name="_subsequent_ammount"/></div>
				</div>
				<hr/>
				<div class="row">
					<div class="col-md-4"><label>Days / week</label></div>
					<div class="col-md-4"><input type="text" class="form-control" id="days_week" name="days_week"/></div>
				</div>
				<div class="row">
					<div class="col-md-4"><label>Thereafter</label></div>
					<div class="col-md-4"><input type="text" class="form-control" id="thereafter" name="thereafter"/></div>
				</div>
				<div class="row">
					<div class="col-md-4"><label>Min. days</label></div>
					<div class="col-md-4"><input type="text" class="form-control" id="min_days" name="min_days"/></div>
				</div>
			</div>			
			<div class="modal-footer">				
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Save and use</button>
			</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="edit_qtys_form_modal" tabindex="-1" role="dialog" aria-labelledby="edit_qtys_form_modal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Accesory Group</h4>
			</div>
			<div class="modal-body">
                <form role="form" id="modify_accesory_form">                    
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-condensed">
                                <thead>
                                    <tr>
                                        <th style="width:20%">Stock No.</th>
                                        <th style="width:50%">Description</th>
                                        <th style="width:20%">Qty</th>
                                        <th style="width:10%"></th>
                                    </tr>
                                </thead>
                                <tbody id="accesories_group">
                                </tbody>
                        </table>                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            Search an item to add it as accesory <span style="font-size:150%">&darr;</span>
                        <input type="text" id="search_items_for_group" class="form-control input-sm" />
                        <input type="hidden" id="accesory_groups" name="accesory_groups" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary btn-block" onclick="send_modify_accesory_form();">Save changes</button>
                    </div>
                </div>
                </form>
			</div>
		</div>
	</div>
</div>	