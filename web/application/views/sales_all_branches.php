<h2>Sales all branches <small>From <input type="text" class="form-date-camaleon" id="salesFrom" name="salesFrom" value="<?=$startDate?>" > to <input type="text" class="form-date-camaleon" id="salesTo" name="salesTo" value="<?=$endDate?>"></small></h2>
<div class="row">
    <div class="col-md-10">
        <table class="table table-responsive table-condensed table-hover" style="width:100%">
            <thead>
                <tr>                    
                    <th style="width:14%">Branch</th>
                    <th style="width:14%">Date</th>
                    <th style="text-align: right">Total Sales</th>
                    <th style="text-align: right">Total Cost</th>
                    <th style="text-align: right">Total VAT</th>
                    <th style="text-align: right">Total Gross Profit</th>
                    <th style="text-align: right">Gross Margin %</th>
                </tr>
            </thead>
            <?php $count = count( $sales ); ?>
            <?php for($i = 0; $i < $count; $i++ ) : ?>
                <tr>
                    <td style="width:14%"><a href="<?=base_url('index.php/sales_ledger/salesProfitByInvoice?branchID='.$sales[$i]->branch_id)?>" title="See invoice profit of this branch"><?=$sales[$i]->branch_name?></a></td>
                    <td style="width:28%"><?= date('d M Y', strtotime($sales[$i]->datetime))?></td>
                    <td style=" text-align:right"><?=$sales[$i]->total_sales?></td>
                    <td style=" text-align:right"><?=$sales[$i]->total_cost?></td>
                    <td style=" text-align:right"><?=$sales[$i]->total_vat?></td>
                    <td style=" text-align:right"><?=$sales[$i]->total_gross_profit?></td>
                    <td style=" text-align:right"><?=number_format($sales[$i]->gross_margin,2)?>%</td>
                </tr>
            <?php endfor; ?>
        </table>
    </div>
</div>