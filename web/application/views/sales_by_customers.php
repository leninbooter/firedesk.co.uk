<input type="hidden" id="branch_id" value="<?= isset($branch_id) ? $branch_id:''?>">
<h2>Sales to <?= isset($customer_sales[0]->name) ? $customer_sales[0]->name:''?> <?= isset($branch) ? '('.$branch.')':''?> <small>From <input type="text" class="form-date-camaleon" id="salesFrom" name="salesFrom" value="<?=$startDate?>" > to <input type="text" class="form-date-camaleon" id="salesTo" name="salesTo" value="<?=$endDate?>"></small></h2>
<div class="row">
    <div class="col-md-10">
        <table class="table table-responsive table-condensed table-hover" style="width:100%">
            <thead>
                <tr>                    
                    <th style="width:14%">Customer</th>
                    <th style="width:14%">Date</th>
                    <th style="text-align: right">Total Sales</th>
                    <th style="text-align: right">Total Cost</th>
                    <th style="text-align: right">Total VAT</th>
                    <th style="text-align: right">Total Gross Profit</th>
                    <th style="text-align: right">Gross Margin %</th>
                </tr>
            </thead>
            <?php $count = count( $customer_sales ); ?>
            <?php for($i = 0; $i < $count; $i++ ) : ?>
                <tr>
                    <td style="width:14%"><?=$customer_sales[$i]->name?></td>
                    <td style="width:28%"><?= date('d M Y', strtotime($customer_sales[$i]->datetime))?></td>
                    <td style=" text-align:right"><?=$customer_sales[$i]->total_sales?></td>
                    <td style=" text-align:right"><?=$customer_sales[$i]->total_cost?></td>
                    <td style=" text-align:right"><?=$customer_sales[$i]->total_vat?></td>
                    <td style=" text-align:right"><?=$customer_sales[$i]->total_gross_profit?></td>
                    <td style=" text-align:right"><?=number_format($customer_sales[$i]->gross_margin,2)?>%</td>
                </tr>
            <?php endfor; ?>
        </table>
    </div>
</div>