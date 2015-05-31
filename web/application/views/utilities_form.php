<h1>Utilities</h1>
<div class="row">
	<div class="col-md-9">
		<ul id="tabs" class="nav nav-tabs" role="tablist">
			<li role="presentation" ><a href="#access_rights"   data-toggle="tab">Access Rights</a></li>
			<li role="presentation" ><a href="#coa"             data-toggle="tab" onclick="loadCOA()">Chart of Accounts</a></li>
			<li role="presentation" ><a href="#holidays_schema" data-toggle="tab">Holidays Schema</a></li>
			<li role="presentation" ><a href="#terminal"        data-toggle="tab">Terminal</a></li>  
		</ul>

		<!-- Tab panes -->
		<!-- Access Rights -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane" id="access_rights">            
				<form class="form">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label for="">Regional Manager</label>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label for="">Shop Manager</label>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label for="">Shop Floor</label>
							</div>
						</div>
					</div>
				</form>
			</div>			
			
            <!-- Charts of Account -->
			<div role="tabpanel" class="tab-pane" id="coa">				
                <form id="newAccountForm" role="form" class="form-horizontal">
                  <div class="form-group">
                   <a name="searchBox"></a> <label for="inputCode" class="col-sm-1 control-label">Code</label>
                    <div class="col-sm-2">
                      <input type="text" class="form-control" id="inputCode" name="inputCode" >
                    </div>
                    <label for="inputDescription" class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-5">
                      <input type="text" class="form-control form-block" id="inputDescription" name="inputDescription" >
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-default btn-block" onclick="$('#newAccountForm').submit();">Add</button>
                    </div>
                  </div>
                </form>                    					
                <div class="row">
                    <div id="coa_DIV"  class="col-md-12">	
                    </div>
                </div>
			</div>
            
            <!-- HOlidays Schema -->
			<div role="tabpanel" class="tab-pane" id="holidays_schema">
				<form class="form-inline" method="post" action="<?php echo base_url('index.php/utilities/save_holiday_schema');?>">
					<div class="row">
						<div class="col-md-4">
							<div id="jan_div" data-date="<?php echo $jan_dates;?>"></div>
							<input type="hidden" id="jan" name="jan" value="<?php echo $jan_days;?>">
						</div>
						<div class="col-md-4">
							<div id="feb_div" data-date="<?php echo $feb_dates;?>"></div>
							<input type="hidden" id="feb" name="feb" value="<?php echo $feb_days;?>">
						</div>
						<div class="col-md-4">
							<div id="mar_div" data-date="<?php echo $mar_dates;?>"></div>
							<input type="hidden" id="mar" name="mar" value="<?php echo $mar_days;?>">
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div id="apr_div" data-date="<?php echo $apr_dates;?>"></div>
							<input type="hidden" id="apr" name="apr" value="<?php echo $apr_days;?>">
						</div>
						<div class="col-md-4">
							<div id="may_div" data-date="<?php echo $may_dates;?>"></div>
							<input type="hidden" id="may" name="may" value="<?php echo $may_days;?>">
						</div>
						<div class="col-md-4">
							<div id="jun_div" data-date="<?php echo $jun_dates;?>"></div>
							<input type="hidden" id="jun" name="jun" value="<?php echo $jun_days;?>">
						</div>
					</div><div class="row">
						<div class="col-md-4">
							<div id="jul_div" data-date="<?php echo $jul_dates;?>"></div>
							<input type="hidden" id="jul" name="jul" value="<?php echo $jul_days;?>">
						</div>
						<div class="col-md-4">
							<div id="aug_div" data-date="<?php echo $aug_dates;?>"></div>
							<input type="hidden" id="aug" name="aug" value="<?php echo $aug_days;?>">
						</div>
						<div class="col-md-4">
							<div id="sep_div" data-date="<?php echo $sep_dates;?>"></div>
							<input type="hidden" id="sep" name="sep" value="<?php echo $sep_days;?>">
						</div>
					</div><div class="row">
						<div class="col-md-4">
							<div id="oct_div" data-date="<?php echo $oct_dates;?>"></div>
							<input type="hidden" id="oct" name="oct" value="<?php echo $oct_days;?>">
						</div>
						<div class="col-md-4">
							<div id="nov_div" data-date="<?php echo $nov_dates;?>"></div>
							<input type="hidden" id="nov" name="nov" value="<?php echo $nov_days;?>">
						</div>
						<div class="col-md-4">
							<div id="dec_div" data-date="<?php echo $dec_dates;?>"></div>
							<input type="hidden" id="dec" name="dec" value="<?php echo $dec_days;?>">
						</div>
					</div>
					<div id="row">
						<div class="col-md-5"></div>
						<div class="col-md-4"></div>
						<div class="col-md-2"></div>
					</div>
					<div id="row">
						<div class="col-md-5"></div>
						<div class="col-md-5"></div>
						<div class="col-md-2"><button type="submit" class="btn btn-primary  btn-block">Save</button></div>
					</div>
				</form>
			</div>
			<div role="tabpanel" class="tab-pane" id="terminal">terminal</div>
		</div>
	</div>
	
	<div class="col-md-3"></div>
</div>
