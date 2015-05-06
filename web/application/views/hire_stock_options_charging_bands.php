<option value="" selected></option>
<!--<option value="new">Add charging band</option>
<option disabled>-</option>-->
<?php foreach($bands as $b): ?>
<option value="<?php echo $b->pk_id; ?>"><?php echo $b->name; ?></option>
<?php endforeach; ?>