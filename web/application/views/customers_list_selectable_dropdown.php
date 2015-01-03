<table class="table table-hover table-responsive" style="margin-bottom:0">
	<tr onclick="setParentAccount('')">
		<td></td>
	</tr>
	<?php foreach($customers as $key=>$value): ?>
		<tr onclick="setParentAccount('<?php echo $value['pk_id']; ?>','<?php echo $value['name']; ?>')">		
			<td><?php echo $value['name']; ?></td>
		</tr>
	<?php endforeach; ?>
</table>