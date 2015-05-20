<div class="container-fluid">
<form role="form" id="payment_form">
<input type="hidden" id="invoiceID" name="invoiceID" value="<?=$invoice->pk_id?>">
<input type="hidden" id="contractID" name="contractID" value="<?=$invoice->contractID?>">
<div class="row">
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-12">
                <h4>Invoice No. <?=$invoice->pk_id?></h4>
            </div>            
        </div>
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-3 control-label">Date</label>
                <div class="col-sm-4">
                    <p class="form-control-static text-right"><?=date('d M Y', strtotime($invoice->creation_date))?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Subtotal</label>
                <div class="col-sm-4">
                    <p class="form-control-static text-right"><?=$invoice->subtotal?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">V.A.T</label>
                <div class="col-sm-4">
                    <p class="form-control-static text-right"><?=$invoice->vat?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Total</label>
                <div class="col-sm-4">
                    <p class="form-control-static text-right"><?=$invoice->total?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Due</label>
                <div class="col-sm-4">
                    <p class="form-control-static text-right"><?=$invoice->unpdaid_ammount?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-12">
                <h3>Payment details</h3>
            </div>
        </div>
        <div class="form-horizontal">
        <div class="form-group">
            <label class="control-label col-sm-3">Date:</label>
            <div class="col-sm-4"><p class="form-control-static text-right"><?=date('d/m/Y')?></p></div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Ammount:</label>
            <div class="col-sm-4"><p class="form-control-static text-right" id="totalPaid"></p></div>	
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Reference:</label>
            <div class="col-sm-4"><p class="form-control-static text-right"><textarea class="form-control" rows="2" id="payment_reference" name="payment_reference"></textarea></p></div>
        </div>        
        <div class="form-group">
            <label class="control-label col-sm-3">Cash:</label>
            <div class="col-sm-4"><p class="form-control-static text-right"><input type="text" class="form-control" id="cash" name="cash"></p></div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Cheque:</label>
            <div class="col-sm-4"><p class="form-control-static text-right"><input type="text" class="form-control" id="cheque" name="cheque"></p></div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Card:</label>
            <div class="col-sm-4"><p class="form-control-static text-right"><input type="text" class="form-control" id="card" name="card"></p></div>
        </div>        
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-11"></div>
    <div class="col-md-1"><button type="button" class="btn btn-default" onclick="submitPay()">Save</button></div>
</div>
</form>
</div>