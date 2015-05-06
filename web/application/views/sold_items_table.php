<table class="table table-responsive table-condensed table-hover">
    <thead>
        <tr>
            <th>Stock No.</th>
            <th>Description</th>
            <th>Qty</th>
            <th>Rate</th>
            <th>Discount</th>
            <th>Total</th>                            
            <?php if ($mode == "write"): ?><th></th><?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach($items as $i): ?>
            <tr>
                <?php if ($mode == "write"): ?><td style="display:none"><input type="hidden" id="itemRowID" name="itemRowID[]" value="<?php echo $i->pk_id; ?>"></td><?php endif; ?>
                <td><?php echo $i->stock_number; ?></td>
                <td><?php echo $i->description; ?></td>
                <td><?php echo $i->qty; ?></td>
                <td><?php echo $i->rate; ?></td>
                <td><?php echo $i->discount_perc != "" ? $i->discount_perc."%" : ""; ?></td>
                <td><?php echo $i->value; ?></td>
                <?php if ($mode == "write"): ?><td><button type="button" class="btn btn-default btn-sm" onclick="deleteSaleItem(this)">Delete</button></td><?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>