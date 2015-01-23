	<div class="modal fade" id="balance_orders_submenu" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Balance orders</h4>
				</div>
				<div class="modal-body">
					<form id="outstanding_items_form_contract_no" class="form-inline" method="get" action="<?php echo base_url('index.php/contracts/edit_outstanding_items');?>">
					<div id="div_group_contract_no" class="form-group"><button id="contract_no_button" type="submit" class="btn btn-primary">Contract No.</button> <input type="text" id="contract_no_field" name="contract_no_field" class="form-control"></div>
					<a href="<?php echo base_url('index.php/contracts/list_balance_orders/3'); ?>"><button type="button" class="btn btn-primary">List in date order</button></a>
					<a href="<?php echo base_url('index.php/contracts/list_balance_orders/1'); ?>"><button type="button" class="btn btn-primary">List in customer order</button></a>
					<button type="button" class="btn btn-primary">Print balance items</button>
					</form>
				</div>
			</div>
		</div>
	</div>
	
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>-->
	<script src="<?php echo base_url('assets/js/jquery-1.11.0.min.js'); ?>"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/js/firedesk.js'); ?>"></script>
	<script src="<?php echo base_url('assets/js/bootbox.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js'); ?>"></script>