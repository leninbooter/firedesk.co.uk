<table class="table table-hover table-responsive" style="<?= $mode == 'pdf'? 'font-size:60%;':''; ?>">
    <thead>
        <tr style="<?= $mode == 'pdf'? 'border-top: 0.1mm solid #00':''; ?>">
            <th style="width:5%; <?= $mode == 'pdf'? 'height: 20px;':''; ?>">Stock No.</th>
            <th style="width:5%">Qty</th>
            <th style="width:20%">Description</th>
            <th style="width:12%"></th>
            <th style="width:10%; text-align:center">Interval</th>
            <th style="text-align:right">Rate</th>
            <th style="width:7%; text-align:center">Per</th>                        
            <th style="text-align:right">Disc. %</th>
            <th style="text-align:right">Value</th>
            <th style="text-align:right">VAT</th>
            <?php if ( $mode == "write" ): ?> 
            <th></th>
            <?php endif; ?>
        </tr>
    </thead>
    <?php foreach($invoiceItems as $i): ?>
        <tr style="<?=$mode == 'pdf'? 'border: 0.1mm solid #dfdfdf; border-right:none; border-left:none; ':''; ?>">						
            <td style="<?=$mode == 'pdf'? 'height: 20px; ':''; ?>"><?php if ( $mode == "write" ): ?> <input type="hidden" id="itemID" name="itemID[]" value="<?=$i->pk_id?>"><?php endif; ?><?=$i->stock_number; ?></td>
            <td><?=$i->qty?></td>
            <td><?=$i->description?></td>
            <td><?=$i->item_type == 2 ? date('d-m-Y H:i', strtotime($i->hire_date_from)).' - '.date('d-m-Y H:i', strtotime($i->hire_date_to)):''?></td>
            <td style="width:10%; text-align:center"><?=$i->item_type == 2 ? $i->weeks.'w '.$i->days.'d '.$i->hours.'h':''?></td>
            <td style="text-align:right"><?=$i->rate; ?></td>
            <td style="text-align:center"><?=$i->item_type == 2 ? 'week':''?></td>						
            <td style="text-align:right"><?=$i->discount_perc?>%</td>
            <td style="text-align:right"><?=$i->value?></td>
            <td style="text-align:right"><?=$i->vat?>%</td>
            <?php if ( $mode == "write" ): ?> 
            <td><button type="button" class="btn btn-default btn-sm" onclick="removeRow(this)">Delete</button></td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
</table>