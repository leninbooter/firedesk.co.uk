<?php $started = false; ?>
[
	<?php foreach($addresses as $key=>$value): ?>
		<?php 	if($started)
				{
					echo ",";
				}else {
					$started = true;
				};
		?>
		<?php echo "\"".$value['address']."\""; ?>
	<?php endforeach; ?>
]