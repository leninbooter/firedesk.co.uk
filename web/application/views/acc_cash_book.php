<input type="hidden" id="accCode" value="<?= isset($accCode) ? $accCode:''?>">
<div class="row">
    <div class="col-md-8">
        <h2>Cash Book <small><?= isset($bankAccDtls) ? $bankAccDtls->bankAccountName:''?></small> </h2>
    </div>
    <div class="col-md-4">        
    </div>
</div>

<div class="row">
    <?php if ( isset($banksAcc) ): ?>
    <div class="col-md-4">
    </div>
        <div class="col-md-4">
            <p class="text-center">
                <h3>Choose an account:</h3>
                <div class="list-group">
                    <?php foreach( $banksAcc as $c ): ?>
                        <a href="<?=base_url('index.php/accounting/cashBook?accCode='.$c->code)?>" class="list-group-item"><?=$c->name?></a>
                  <?php endforeach; ?>
                </div>
            </p>
        </div>
    <div class="col-md-4">
    </div>
    <?php else: ?>
    
    <div class="col-md-9">                
        <div class="row">
            <div class="col-md-6">
                
            </div>
            <div class="col-md-6 ">
                    <table class="" style="float:right">
                        <tr>
                            <th style="width:80px;">Cleared</th><td style="width:100px; text-align:right"><?=$bankAccDtls->cleared?></td>
                        </tr>
                        <tr>
                            <th>Held</th><td style="width:60px; text-align:right"><?=$bankAccDtls->held?></td>
                        </tr>
                        <tr>
                            <th>Pending</th><td style="width:60px; text-align:right"><?=$bankAccDtls->pending?></td>
                        </tr>
                        <tr>
                            <th></th><td style="width:60px; text-align:right; border-bottom: 5px double #000"></td>
                        </tr>
                        <tr>
                            <th>Balance</th><td style="width:60px; text-align:right"><?=$bankAccDtls->balance?></td>
                        </tr>
                        <tr>
                            <th>Today</th><td style="width:60px; text-align:right"><?=$bankAccDtls->today?></td>
                        </tr>
                    </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width:5%"></th>
                            <th style="width:15%">Type</th>
                            <th style="width:12%">Date</th>
                            <th style="width:10%">Ref.</th>
                            <th style="width:13%">Account</th>
                            <th style="width:13%">Net Amnt</th>
                            <th style="width:5%">TC</th>
                            <th style="width:13%">Debit</th>
                            <th style="width:13%">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-3">                
        <div class="row">
            <div class="col-md-12">
                <a role="button" class="btn btn-default btn-block btn-sm" data-target="#newTransactionModal" data-toggle="modal" data-backdrop="static" data-keyboard="true">Add/edit transactions</a>
                <a role="button" class="btn btn-default btn-block btn-sm"  href="">Change cash book</a>
            </div>
        </div>
        <!-- help -->
    </div>    
    
    <?php endif; ?>
</div>

<div class="modal fade" role="dialog" id="newTransactionModal" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="gridSystemModalLabel">New transaction</h4>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-4">
              </div>              
            </div>            
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->