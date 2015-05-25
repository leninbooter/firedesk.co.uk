<input type="hidden" id="branch_id" value="<?= isset($branch_id) ? $branch_id:''?>">
<div class="row">
    <div class="col-md-6">
        <h2>Sales <?= isset($branch) ? '('.$branch.')':''?>  <?php if (isset($branch) ): ?><small>From <input type="text" class="form-date-camaleon" id="salesFrom" name="salesFrom" value="<?=$startDate?>" > to <input type="text" class="form-date-camaleon" id="salesTo" name="salesTo" value="<?=$endDate?>"></small><?php endif; ?></h2>
    </div>
    <div class="col-md-5">        
    </div>
</div>

<div class="row">
            <?php if ( isset($branches) ): ?>
            <div class="col-md-4">
            </div>
                <div class="col-md-4">
                    <p class="text-center">
                        <h2>Pick a branch</h2>
                        <div class="list-group">
                            <?php foreach( $branches as $b ): ?>
                                <a href="<?=base_url('index.php/sales_ledger/salesProfitByInvoice?branchID='.$b->pk_id)?>" class="list-group-item <?=$b->branch_name == $currentBranch ? 'active' : '' ?>"><?=$b->branch_name?></a>
                          <?php endforeach; ?>
                        </div>
                    </p>
                </div>
            <div class="col-md-4">
            </div>
            <?php else: ?>
            <div class="col-md-10">            
                <table class="table table-responsive table-hover">
                    <thead>
                        <tr>
                            <th style="width:7%">Invoice No.</th>
                            <th style="width:14%">Customer</th>
                            <th style="width:28%">Description</th>
                            <th>Qty</th>
                            <th style="text-align: right">Total Sales</th>
                            <th style="text-align: right">Total Cost</th>
                            <th style="text-align: right">Total VAT</th>
                            <th style="text-align: right">Total Gross Profit</th>
                            <th style="text-align: right">Gross Margin %</th>
                        </tr>
                    </thead>
                </table>
                <?php $currentInvoice            = 0; ?>
                <?php $grand_total_sales         = 0; ?>
                <?php $grand_total_cost          = 0; ?>
                <?php $grand_total_gross_profit  = 0; ?>
                <?php $grand_total_vat           = 0; ?>
                <?php $grand_total_gross_margin  = array(); ?>
                <?php $count = count($invoices); ?>
                <?php for($i = 0; $i < $count; $i++ ) : ?>
                    
                    <?php if ( $invoices[$i]->invoice_ID != $currentInvoice || $i == 0 ) : ?>
                        <?php $currentInvoice = $invoices[$i]->invoice_ID; ?>
                        <table class=" table-responsive table-condensed" style="width:100%">
                            <tr>
                                <td style="width:7%"><?=$invoices[$i]->invoice_ID?></td>
                                <td style="width:14%"><a href="<?=base_url('index.php/sales_ledger/salesProfitByCustomer?branchID='.$branch_id.'&customerID='.$invoices[$i]->customer_id.'&startDate='.$startDate.'&endDate='.$endDate)?>" title="See invoice profit of this customer"><?=$invoices[$i]->customer_name?></a></td>
                                <td style="width:28%"><?= date('d M Y', strtotime($invoices[$i]->datetime))?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </table>
                        <table class="table table-responsive table-hover table-condensed">
                    <?php endif; ?>
                        <tr>      
                            <td style="width:7%"></td>
                            <td style="width:14%"></td>
                            <td style="width:28%"><?=$invoices[$i]->description?></td>
                            <td><?=$invoices[$i]->qty?></td>
                            <td style="text-align: right"><?=$invoices[$i]->sales_price?></td>
                            <td style="text-align: right"><?=$invoices[$i]->cost?></td>
                            <td style="text-align: right"><?=$invoices[$i]->vat?></td>
                            <td style="text-align: right"><?=$invoices[$i]->gross_profit?></td>
                            <td style="text-align: right"><?=$invoices[$i]->gross_margin?>%</td>
                        </tr>
                    <?php if ( (isset($invoices[$i+1]) && $invoices[$i+1]->invoice_ID != $currentInvoice) || $i == $count-1 ) : $idx = $i?>
                        </table>
                        <table class="table-responsive table-condensed" style="width:100%">
                            <tr>
                                <td style="width:7%"></td>
                                <td style="width:14%">Total</td>
                                <td style="width:28%"></td>
                                <td></td>
                                <td style="text-align: right"><?=$invoices[$idx]->total_sales?></td>
                                <td style="text-align: right"><?=$invoices[$idx]->total_cost?></td>
                                <td style="text-align: right"><?=$invoices[$idx]->total_vat?></td>
                                <td style="text-align: right"><?=$invoices[$idx]->total_gross_profit?></td>
                                <td style="text-align: right"><?=$invoices[$idx]->total_gross_margin?>%</td>
                            </tr>
                        </table>
                    <?php 
                        $grand_total_sales         += $invoices[$idx]->total_sales;
                        $grand_total_cost          += $invoices[$idx]->total_cost;
                        $grand_total_gross_profit  += $invoices[$idx]->total_gross_profit;
                        $grand_total_vat           += $invoices[$idx]->total_vat;
                        endif; ?>
                <?php endfor; ?>
                <table class="table-responsive table-condensed" style="width:100%">
                    <tr>
                        <td style="width:7%">Grand</td>
                        <td style="width:14%">Total</td>
                        <td style="width:28%"></td>
                        <td></td>
                        <td style="text-align: right"><?=number_format($grand_total_sales,2)?></td>
                        <td style="text-align: right"><?=number_format($grand_total_cost,2)?></td>
                        <td style="text-align: right"><?=number_format($grand_total_vat,2)?></td>
                        <td style="text-align: right"><?=number_format($grand_total_gross_profit,2)?></td>
                        <td style="text-align: right"><?= $grand_total_sales > 0 ? number_format( ($grand_total_gross_profit/$grand_total_sales)*100 ,2):floatval(0)?>%</td>
                    </tr>
                </table>
            </div>
            <?php endif; ?>       
</div>