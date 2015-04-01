<?php foreach($components as $c): ?>
<tr>
	<td style="display:none">
	<input type="hidden" id="new_item" name="new_item[]" value="no">
	<input type="hidden" id="delete" name="delete[]" value="no">
	<input type="hidden" id="new_item_id_in" name="new_item_id_in[]" value="<?php echo $c->pk_id; ?>"></td>
	<td><?php echo $c->fleet_number; ?></td>
	<td><?php echo $c->label; ?></td>	
	<td><?php echo $c->qty_stock; ?></td>	
	<td><?php if(!empty($c->fleet_number)): ?>
	<input type="text" id="new_item_qty_in" name="new_item_qty_in[]" class="form-control input-sm" value="<?php echo $c->qty_required; ?>">
	<?php endif; ?></td>
	<td><button type="button" class="btn btn-default btn-sm" aria-label="Left Align" onclick="mark_to_delete(this)"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button></td>
</tr>
<?php endforeach; ?>