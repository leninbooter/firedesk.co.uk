<h1>Massive changes to stock</h1>
<div class="row">
	<div class="col-md-8">
		<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
			<div class="panel panel-default">
			<div class="panel-heading" role="tab" id="headingOne">
			  <h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
				  Balances
				</a>
			  </h4>
			</div>
			<div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
			  <div class="panel-body">
				<form id="update_balances_massive_form" class="form-inline" role="form" method="post">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="apply_to">Apply to</label>
								<select id="apply_to" name="apply_to" class="form-control">
									<option >Family group</option>
									<option >Entire stock</option>
								</select>
							</div>
							<div class="form-group">
								<label for="family_groups"></label>
								<select id="family_groups" name="family_groups" class="form-control">
									<?php foreach($family_groups as $option): ?>
									<option value="<?php echo $option->pk_id; ?>" <?php if(isset($item[0]->fk_family_group)) { if($item[0]->fk_family_group == $option->pk_id) { echo "selected";} } ?> ><?php echo $option->name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="form-group">
								<label for="set_balance">set balance</label>
								<select id="set_balance" name="set_balance" class="form-control">
									<option >Zero all</option>
									<option >Zero negative balances</option>
								</select>
							</div>
							<div class="form-group">
									<button type="submit" class="btn btn-danger" data-toggle="modal" data-target="#working_modal">Make changes</button>
							</div>
						</div>
					</div>					
				</form>
			  </div>
			</div>
		  </div>
			<div class="panel panel-default">
			<div class="panel-heading" role="tab" id="headingTwo">
			  <h4 class="panel-title">
				<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
				  Prices
				</a>
			  </h4>
			</div>
			<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
			  <div class="panel-body">
				<form id="update_prices_massive_form" role="form" class="form-inline">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="apply_to">Apply to</label>
								<select id="apply_to" name="apply_to" class="form-control">
									<option>Family group</option>
									<option>Entire stock</option>
								</select>
							</div>
							<div class="form-group">
								<label for="family_groups"></label>
								<select id="family_groups" name="family_groups" class="form-control">
									<?php foreach($family_groups as $option): ?>
									<option value="<?php echo $option->pk_id; ?>" <?php if(isset($item[0]->fk_family_group)) { if($item[0]->fk_family_group == $option->pk_id) { echo "selected";} } ?> ><?php echo $option->name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="form-group">
								<label for="set_raised_by">set raise by</label>
								<select id="set_raised_by" name="set_raised_by" class="form-control">
									<option>Fixed amounts</option>
									<option>Percentages</option>
								</select>
							</div>
						</div>						
					</div>
					<div class="row">
						<div class="col-md-12">&nbsp;</div>
					</div>
					<div class="row">			
						<div class="col-md-6">
							<div class="row">
								<div class="col-md-4"><label>Standard Price</label></div>
								<div class="col-md-6"><input type="text" id="standard_price" name="standard_price" class="form-control"></div>															
							</div>
							<div class="row">
								<div class="col-md-4"><label>Special Price</label></div>
								<div class="col-md-6"><input type="text" id="special_price" name="special_price" class="form-control"></div>								
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="row">
								<div class="col-md-4"><label>Cost price A</label></div>
								<div class="col-md-6"><input type="text" id="cost_price_a" name="cost_price_a" class="form-control"></div>								
							</div>
							<div class="row">
								<div class="col-md-4"><label>Cost price B</label></div>
								<div class="col-md-6"><input type="text" id="cost_price_b" name="cost_price_b" class="form-control"></div>								
							</div>
							<div class="row">
								<div class="col-md-4"><label>Cost price C</label></div>
								<div class="col-md-6"><input type="text" id="cost_price_c" name="cost_price_c" class="form-control"></div>								
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
									<button type="submit" class="btn btn-danger" data-toggle="modal" data-target="#working_modal">Make changes</button>
							</div>
						</div>
					</div>					
				</form>
			  </div>
			</div>
		  </div>
			<div class="panel panel-default">
			<div class="panel-heading" role="tab" id="headingThree">
			  <h4 class="panel-title">
				<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
				  Locations
				</a>
			  </h4>
			</div>
			<div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
			  <div class="panel-body">
				<form id="update_location_massive_form" role="form" class="form-inline">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="apply_to">Apply to</label>
								<select id="apply_to" name="apply_to" class="form-control">
									<option>Family group</option>
									<option>Entire stock</option>
								</select>
							</div>
							<div class="form-group">
								<label for="family_groups"></label>
								<select id="family_groups" name="family_groups" class="form-control">
									<?php foreach($family_groups as $option): ?>
									<option value="<?php echo $option->pk_id; ?>" <?php if(isset($item[0]->fk_family_group)) { if($item[0]->fk_family_group == $option->pk_id) { echo "selected";} } ?> ><?php echo $option->name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="form-group">
								<label for="location">set location to</label>
								<input type="text" id="location" name="location" class="form-control">
							</div>
							<div class="form-group">
									<button type="submit" data-toggle="modal" data-target= "#working_modal" class="btn btn-danger">Make changes</button>
							</div>
						</div>
					</div>					
				</form>
			  </div>
			</div>
		  </div>		  
			<div class="panel panel-default">
			<div class="panel-heading" role="tab" id="headingFour">
			  <h4 class="panel-title">
				<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
				  Vat codes
				</a>
			  </h4>
			</div>
			<div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
			  <div class="panel-body">
				<form id="update_vats_massive_form" role="form" class="form-inline">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="apply_to">Apply to</label>
								<select id="apply_to" name="apply_to" class="form-control">
									<option>Family group</option>
									<option>Entire stock</option>
								</select>
							</div>
							<div class="form-group">
								<label for="family_groups"></label>
								<select id="family_groups" name="family_groups" class="form-control">
									<?php foreach($family_groups as $option): ?>
									<option value="<?php echo $option->pk_id; ?>" <?php if(isset($item[0]->fk_family_group)) { if($item[0]->fk_family_group == $option->pk_id) { echo "selected";} } ?> ><?php echo $option->name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="form-group">
								<label for="vat_codes">vat code</label>
								<select class="form-control" id="fk_vat_code" name="fk_vat_code">
											<option value="0"></option>
											<?php foreach($vats as $option): ?>
											<option value="<?php echo $option->pk_id; ?>" <?php if(isset($item[0]->fk_vat_code)) { if($item[0]->fk_vat_code == $option->pk_id) { echo "selected";} } ?>><?php echo $option->description." (".$option->percentage."%)"; ?></option>
											<?php endforeach; ?>
								</select>
							</div>							
							<div class="form-group">
									<button type="submit" data-toggle="modal" data-target="#working_modal" class="btn btn-danger">Make changes</button>
							</div>
						</div>
					</div>					
				</form>
			  </div>
			</div>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
	</div>
</div>

<div class="modal fade" id="working_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-body">
				<img src="<?php echo base_url('assets/images/ajax-loader.gif'); ?>"/>				
			</div>
		</div>
	</div>
</div>