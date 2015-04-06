<table style="border: 1px solid #337AB7;
	padding: 3px;font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;
    font-size: 14px;
    line-height: 1.42857;
    color: #333; width: 50%">
	<tr><td colspan="2" style="text-align:center; vertical-align:middle; padding: 5px;background-color: #337AB7; color:#ffffff">
		<h2>Firedesk Event Invitation</h2>
	</td></tr>
	<tr><td style="padding: 5px;" colspan="2">
		<em><?php echo $e->inviter; ?> invited you to the following event:</em>
	</td></tr>
	<tr><td style="padding: 5px;"><strong>Title:</strong></td><td style="padding: 5px;"><?php echo $e->title; ?></td></tr>
	<tr><td style="padding: 5px;"><strong>Description:</strong></td><td style="padding: 5px;"><?php echo $e->description; ?></td></tr>
	<tr><td style="padding: 5px;"><strong>From:</strong></td><td style="padding: 5px;"><?php echo date('D, d/M/Y \a\t H:i', strtotime( $e->start_datetime)); ?></td></tr>
	<tr><td style="padding: 5px;"><strong>To:</strong></td><td style="padding: 5px;"><?php echo date('D, d/M/Y \a\t H:i', strtotime( $e->start_datetime)); ?></td></tr>
	<tr><td style="padding: 5px;"><strong>Guests:</strong></td><td style="padding: 5px;"><ul id="guests_list_li" class="list-unstyled" style="padding-left:15px" >
							<?php foreach($e->guests as $g): ?>
								<li><?php echo $g->name; ?></li>
							<?php endforeach; ?>
						</ul></td></tr>
	
</table>

