<table class="table table-responsive table-condensed table-hover" style="width:100%">
    <thead>
        <tr>
            <th>Stock No.</th>
            <th style="width:25%">Description</th>
            <th>Qty</th>
            <th>Rate</th>
            <th>Discount</th>
            <th style="width:20%">Charging Band</th>
            <?php if ( $mode == 'write'): ?><th></th><?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php $parent = false; ?>
        <?php foreach($items as $i): ?>
            <?php   if ( $parent == false || $parent != $i->parent_item) {
                        $parent = $i->item_no;
                    }
            ?>
            <tr>
                <?php if ( $mode == 'write'): ?><td style="display:none"><input type="hidden" id="itemRowID" name="itemRowID[]" value="<?php echo $i->pk_id; ?>"></td><?php endif; ?>
                <td><?php
                    echo $i->non_hire_fleet_trans == 1 ? "?":"";
                    echo $i->productID; ?></td>
                <td <?php echo $parent == $i->parent_item ? "style=\"padding-left:20px;\"": "";  ?>  ><?php echo $i->description; ?></td>
                <td><?php echo $i->qty; ?></td>
                <td><?php echo $i->rate; ?></td>
                <td><?php echo !empty($i->discount_perc) ? $i->discount_perc.'%':''; ?></td>
                <td><?php echo $i->charging_band; ?></td>
                <?php if ( $mode == 'write'): ?><td><button type="button" class="btn btn-default btn-sm" onclick="deleteHireItem(this)">Delete</button></td><?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>