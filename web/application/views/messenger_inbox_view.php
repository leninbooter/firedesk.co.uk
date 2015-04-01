<div class="modal-header">
	<h4 class="modal-title">Messages</h4>
</div>
<div class="modal-body">
	<form id="messenger_messages_form" role="form">
		<div class="list-group">
		<?php foreach($messages as $m): ?>
		  <a href="#" onclick="<?php echo $m->isread == "0" ? "messenger_read_message(this, $m->pk_id)":""; ?>" class="list-group-item <?php echo $m->isread == "0" ? "active":""; ?>">			
			<div class="row">
				<div class="col-md-6"><h4 class="list-group-item-heading"><?php echo $m->name?></h4></div>
				<div class="col-md-5" style="text-align:right"><?php echo date('d/m/Y h:i a', strtotime( $m->creation_date)) ?></div>
				<div class="col-md-1" style="text-align:right"><span class="glyphicon glyphicon-floppy-remove" aria-hidden="true" onclick="messenger_delete_msg(event, this, <?php echo $m->pk_id; ?>);"></span></div>
			</div>
			<div class="row">
				<div class="col-md-12"><p class="list-group-item-text"><?php echo $m->message; ?></p></div>
			</div>		
		  </a>
		<?php endforeach; ?>
		</div>
	</form>
	<p id="no-messages-text" class="text-center" style="<?php echo empty($messages) ? "display:block":"display:none"; ?>">No messages to display!</p>
</div>
