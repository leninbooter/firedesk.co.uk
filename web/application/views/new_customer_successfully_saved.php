<p class="text-center"><div class="alert alert-success" role="alert"><b>Well done!</b> <?php echo $message; ?></div></p>
<div class="row">
	<div class="col-md-12">
		<a href="<?php echo base_url('index.php/contracts/new_contract?customer_id='.$customer_id); ?>"><button type="button" class="btn btn-default"><?php echo $this->lang->line('new_contract_curr_cust'); ?></button></a>
		<a href="<?php echo base_url('index.php/customers/new_existing'); ?>"><button type="button" class="btn btn-default"><?php echo $this->lang->line('new_customer'); ?></button></a>
		<a href="<?php echo base_url('index.php/desk'); ?>"><button type="button" class="btn btn-default"><?php echo $this->lang->line('go_to_main'); ?></button></a>
	</div>
</div>
