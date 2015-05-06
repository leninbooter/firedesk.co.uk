<?php foreach($prices as $p): ?>
<div class="row">
    <input type="hidden" id="price_id" name="price_id[]" value="<?php echo $p['pk_id']; ?>">
    <div class="col-md-3">
        <div class="form-group">
            <select class="form-control" id="customers_pk_id" name="customers_pk_id[]">
                <option value="0"></option>
                <?php foreach($customers as $customer): ?>
                    <option value="<?php echo $customer->pk_id;?>" <?php echo $customer->pk_id == $p['fk_customer_id'] ? "selected" : ""; ?> ><?php echo $customer->name;?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="col-md-2">
        <select id="price_type" name="price_type[]" class="form-control">
            <option value="0" <?php echo "0" == $p['price_type'] ? "selected" : ""; ?> <?php echo is_numeric($p['price_type']) &&  $p['price_type'] != "0" ? "disabled=\"true\"" : ""; ?>>Standard</option>
            <option value="1" <?php echo "1" == $p['price_type'] ? "selected" : ""; ?> <?php echo is_numeric($p['price_type']) &&  $p['price_type'] != "1" ? "disabled=\"true\"" : ""; ?>>Special</option>
            <option value="2" <?php echo "2" == $p['price_type'] ? "selected" : ""; ?> <?php echo is_numeric($p['price_type']) &&  $p['price_type'] != "2" ? "disabled=\"true\"" : ""; ?>  <?php echo "" == $p['price_type'] ? "disabled=\"true\"" : ""; ?> >Nett</option>           
        </select>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <input type="number" min="1" max="1000000000" id="min_qty" name="min_qty[]" class="form-control" value="<?php echo $p['min_qty']; ?>">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <input type="number" min="1" max="1000000000" id="max_qty" name="max_qty[]" class="form-control" value="<?php echo $p['max_qty']; ?>">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <input type="text" id="price" name="price[]" class="form-control" value="<?php echo $p['price']; ?>">
        </div>
    </div>
    <div class="col-md-1">
        <p class="text-center">
            <button type="button" class="btn btn-default" aria-label="Left Align" onclick="removePrice(this);">
                <span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span>
            </button>
        </p>
    </div>
</div>
<?php endforeach; ?>