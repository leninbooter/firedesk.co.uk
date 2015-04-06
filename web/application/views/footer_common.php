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
	
<div class="modal fade" id="messenger_new_message_modal" tabindex="-1" role="dialog">
<div class="modal-dialog modal-sm">
	<div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title">New message</h4>
		</div>
		<div class="modal-body">
			<form id="messenger_new_message_form" role="form">
				<div class="row">
					<div class="col-sm-3">
						To:
					</div>
					<div class="col-sm-9">
						<div class="form-group">
							<input type="hidden" class="form-control" id="to" name="to" value="">
							<input type="text" class="form-control input-sm" id="to_string" value="">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3">
						Message:
					</div>
					<div class="col-sm-9">
						<textarea rows="5" class="form-control" id="message" name="message" value=""></textarea>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-sm-12">
						<button type="submit" class="btn btn-primary btn-block">Send</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
</div>

<div class="modal fade" id="messenger_messages_modal" tabindex="-1" role="dialog">
<div class="modal-dialog modal-smd">
	<div class="modal-content">			
	</div>
</div>
</div>

<div class="modal fade" id="multiporpuses_modal" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg">
	<div class="modal-content">			
	</div>
</div>
</div>

	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>-->
	<script src="<?php echo base_url('assets/js/jquery-1.11.0.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/jquery-ui-1.11.4.custom/jquery-ui.min.js'); ?>"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/js/global.js'); ?>"></script>
	<script src="<?php echo base_url('assets/js/firedesk.js'); ?>"></script>
	<script src="<?php echo base_url('assets/js/bootbox.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js'); ?>"></script>