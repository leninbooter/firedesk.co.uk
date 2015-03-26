<form role="form" id="modify_accesory_form">
<input type="hidden" id="group_id" name="group_id" value="<?php echo $group_id; ?>">
<div class="row">
	<div class="col-md-12">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th style="width:20%">Stock No.</th>
					<th style="width:50%">Description</th>
					<th style="width:20%">Qty</th>
					<th style="width:10%"></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($accesories as $acc): ?>
				<?php if(is_numeric($acc->qty)): ?>
					<tr>
						<td><input type="hidden" id="item_id" name="item_id[]" value="<?php echo $acc->pk_id; ?>"/><?php echo $acc->number; ?></td>
						<td><?php echo $acc->description; ?></td>
						<td><input type="text" id="qty_in" name="qty_in[]" class="form-control input-sm" value="<?php echo $acc->qty; ?>"/></td>
						<td><button type="button" class="btn btn-default" aria-label="Left Align" onclick="remove_accesory(this, <?php echo $group_id; ?>,<?php echo $acc->pk_id; ?>)" ><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button></td>
					</tr>
				<?php elseif(!is_numeric($acc->qty)): ?>
					<tr>
						<td><input type="hidden" id="item_id" name="item_id[]" value="<?php echo $acc->pk_id; ?>"/><?php echo $acc->number; ?></td>
						<td><?php echo $acc->description; ?></td>
						<td><input type="hidden" id="qty_in" name="qty_in[]" class="form-control" value="NULL"></td>
						<td><button type="button" class="btn btn-default" aria-label="Left Align" onclick="remove_accesory(this, <?php echo $group_id; ?>,<?php echo $acc->pk_id; ?>)"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button></td>
					</tr>
					<?php $children = $this->hire_stock_m->get_childrens_items_from_accesory($acc->pk_id); ?>
					<?php foreach($children as $ch): ?>
						<tr>
							<td style="padding-left:30px;"><input type="hidden" id="item_id" name="item_id[]" value="<?php echo $ch->pk_id; ?>"/><?php echo $ch->number; ?></td>
							<td style="padding-left:30px;"><?php echo $ch->description; ?></td>
							<td><input type="text" id="qty_in" name="qty_in[]" class="form-control input-sm" value="<?php echo $ch->qty; ?>"/></td>
							<td></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>		
			<?php endforeach; ?>
			</tbody>
		</table>
		
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="form-group">
			Search an item to add it as accesory <span style="font-size:150%">&darr;</span>
		<input type="text" id="search_items_for_group" class="form-control input-sm" />
		<input type="hidden" id="accesory_groups" name="accesory_groups" />
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<button type="button" class="btn btn-primary btn-block" onclick="send_modify_accesory_form();">Save changes</button>
	</div>
</div>
</form>