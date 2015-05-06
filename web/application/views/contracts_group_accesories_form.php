<input type="hidden" id="contractID" name="contractID" value="<?php echo $contractID; ?>"/>
<input type="hidden" id="hireItemID" name="hireItemID" value="<?php echo $hireItemID; ?>"/>
<?php if( !empty($accesories) ): ?>
    <table class="table table-condensed table-hover">
        <thead>
            <tr>
                <th style="width:10%">Stock No.</th>
                <th style="width:25%">Description</th>                
                <th style="width:10%">In Stock</th>
                <th style="width:10%">Required</th>
                <th style="width:10%">Request Qty</th>
                <th style="width:10%">Disc</th>
                <th style="width:15%">Price</th>
                <th style="width:10%"></th>
            </tr>
        </thead>
        <tbody>
            <tr style="display:none">
                <td colspan="4"><input type="hidden" id="group_id" name="group_id" value="<?php echo $group_id; ?>"></td>
            </tr>
            <?php foreach($accesories as $acc): ?>
                <?php //if(is_numeric($acc->qty)): ?>
                    <tr>
                        <td><input type="hidden" id="item_type" name="item_type[]" value="<?php echo $acc->item_type; ?>"/><input type="hidden" id="item_id" name="item_id[]" value="<?php echo $acc->pk_id; ?>"/><?php echo $acc->number; ?></td>
                        <td><input type="hidden" id="description" name="description[]" value="<?php echo $acc->description; ?>"><?php echo $acc->description; ?></td>                        
                        <td><p class="form-control-static"><?php echo $acc->balance_qty; ?></p></td>
                        <td><p class="form-control-static"><?php echo $acc->required_qty; ?></p></td>
                        <td>
                            <input type="text" id="requestQty" name="requestQty[]" class="form-control input-sm" onkeyup="getPrice(this)" value="">                            
                        </td>
                        <td><input type="text" id="disc" name="disc[]" class="form-control input-sm percentage" value=""/></td>
                        <td><input type="text" id="price" name="price[]" class="form-control input-sm" value="<?php echo isset($acc->rate) ? $acc->rate:""; ?>"/></td>
                        <td><p class="text-center"><button type="button" class="btn btn-default" aria-label="Left Align" onclick="$(this).parent().parent().parent().remove();" ><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button></p></td>
                    </tr>
                <!--<?php // elseif(!is_numeric($acc->qty)): ?>
                    <tr>
                        <td><input type="hidden" id="item_id" name="item_id[]" value="<?php // echo $acc->pk_id; ?>"/><?php //echo $acc->number; ?></td>
                        <td><?php //echo $acc->description; ?></td>
                        <td><input type="hidden" id="qty_in" name="qty_in[]" class="form-control" value="NULL"></td>
                        <td><p class="text-center"><button type="button" class="btn btn-default" aria-label="Left Align" onclick="$(this).parent().parent().parent().remove();"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button></p></td>
                    </tr>
                    <?php //$children = $this->hire_stock_m->get_childrens_items_from_accesory($acc->pk_id); ?>
                    <?php //foreach($children as $ch): ?>
                        <tr>
                            <td style="padding-left:30px;"><input type="hidden" id="item_id" name="item_id[]" value="<?php //echo $ch->pk_id; ?>"/><?php //echo $ch->number; ?></td>
                            <td style="padding-left:30px;"><?php //echo $ch->description; ?></td>
                            <td><input type="text" id="qty_in" name="qty_in[]" class="form-control input-sm" value="<?php //echo $ch->qty; ?>"/></td>
                            <td></td>
                        </tr>
                    <?php // endforeach; ?>-->
                <?php // endif; ?>		
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>