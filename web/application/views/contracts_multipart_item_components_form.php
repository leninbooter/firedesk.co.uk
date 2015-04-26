<input type="hidden" id="contractID" name="contractID" value="<?php echo $contractID; ?>"/>
<input type="hidden" id="hireItemID" name="hireItemID" value="<?php echo $hireItemID; ?>"/>
<?php if(!empty($components)): ?>
<table class="table table-condensed table-hover">
    <thead>
        <tr>
            <th style="width:10%">Fleet Number</th>
            <th style="width:30%">Description</th>
            <th style="width:10%">Stock</th>
            <th style="width:10%">Required</th>
            <th style="width:10%">Request Qty</th>
            <th style="width:20%">Price</th>
            <th style="width:10%"></th>
        </tr>
    </thead>
    <tbody>            
    <?php foreach($components as $c): ?>
        <tr>
            <td style="display:none">
            <input type="hidden" id="new_item" name="new_item[]" value="no">
            <input type="hidden" id="delete" name="delete[]" value="no">
            <input type="hidden" id="item_no" name="item_no[]" value="<?php echo $c->pk_id; ?>"></td>
            <td><?php echo $c->fleet_number; ?></td>
            <td><?php echo $c->label; ?><input type="hidden" id="description" name="description[]" value="<?php echo $c->label; ?>"></td>	
            <td><?php echo $c->qty_stock; ?></td>	
            <td><p class="form-control-static"><?php echo $c->qty_required; ?></p></td>
            <td>
                <input type="text" id="requestQty" name="requestQty[]" class="form-control input-sm" onchange="getPrice(this)">                
            </td>
            <td><input type="text" id="rate" name="rate[]" class="form-control input-sm" value="<?php echo $c->rate; ?>"></td>
            <td><p class="text-center"><button type="button" class="btn btn-default btn-sm" aria-label="Left Align" onclick="$(this).parent().parent().parent().remove()"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button></p></td>
        </tr>
    <?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>		