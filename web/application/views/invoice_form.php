<input type="hidden" name="contract_id" id="contract_id" value="<?=$invoiceDetails->contractID?>">	
<input type="hidden" name="invoiceID" id="invoiceID" value="<?=$invoiceDetails->pk_id?>">	
<input type="hidden" name="accepted" id="accepted" value="<?=$invoiceDetails->accepted?>">	
    <div class="row">
		<div class="col-md-6">
			<h1>Contract No. <?=$invoiceDetails->contractID?></h1>
            <h2>Invoice No. <?=$invoiceDetails->pk_id?> <small><?=date('d F Y H:i', strtotime($invoiceDetails->creation_date))?></small></h2>
		</div>
        <div class="col-md-6">
        <h1></h1>
			<?php if( $invoiceDetails->accepted != 1): ?>
                <div class="alert alert-warning" role="alert"><b>Pending</b><p>At this moment, 
                you can save or discard the invoice if there is a problem with it.
                Unless you save it or print it, you will be alert when trying to leave the page.</p></div>
           <?php endif; ?>
		</div>				
	</div>

	<div class="row">
        <div class="col-md-4">
            <h3>Client</h3>		
            <?= $invoiceDetails->customerName?>
            <address>
                <?=$invoiceDetails->customerAddress?>
            </address>
        </div>	
        <div class="col-md-4">
            <h4>Site Address</h4>		
            <?=$invoiceDetails->deliveryAddress?>
        </div>
        <div class="col-md-4">
            <h4></h4>				
        </div>
    </div>

	<div class="row">
		<div class="col-md-12">
			<?=$invoiceItems?>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<hr/>
		</div>
	</div>

	<div class="row">
		<div class="col-md-9">
		</div>
		<div class="col-md-2">
			<h4>Sub total</h4>
		</div>
		<div class="col-md-1">
			<p class="text-right"><?=($invoiceDetails->subtotal);?></p>
		</div>
	</div>

	<div class="row">
		<div class="col-md-9">
		</div>
		<div class="col-md-2">
			<h4>V.A.T</h4>
		</div>
		<div class="col-md-1">
			<p class="text-right"><?=($invoiceDetails->vat);?></p>
		</div>
	</div>

	<div class="row">
		<div class="col-md-9">
		</div>
		<div class="col-md-1">
			<h4>Total</h4>
		</div>
		<div class="col-md-2">
			<p class="text-right"><?=($invoiceDetails->total);?></p>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<hr/>
		</div>
	</div>	

	<div class="row">
		<div class="col-md-7"></div>
		<?php if( $invoiceDetails->accepted != 1): ?>
        <div class="col-md-1"><button type="button" class="btn btn-default  btn-block" onclick="discard()"  >Discard</button></div>
        <div class="col-md-1"><button type="button" class="btn btn-primary  btn-block" onclick="save()"     >Save</button></div>
        <div class="col-md-2"><button type="button" class="btn btn-primary  btn-block" onclick="save('email')" >Save & Send via e-mail</button></div>
        <?php endif; ?>
		<div class="col-md-1"><button type="button" class="btn btn-primary  btn-block" onclick="pdf()"      >Print</button></div>
		<?php if( $invoiceDetails->accepted == 1): ?>
        <div class="col-md-2"><button type="button" class="btn btn-primary  btn-block" onclick="email()">Send via e-mail</button></div>
        <?php endif; ?>		
	</div>
	<input type="hidden" id="email_invoice" name="email_invoice" value="0">
	
	<div class="modal fade" id="payment_invoice" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Payment</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-4"><label class="control-label">Date:</label></div>
						<div class="col-md-6"><input class="form-control" type="text" value="<?php echo date('d/m/Y'); ?>" id="payment_date" name="payment_date" readonly="true"></div>
					</div>
					<div class="row">
						<div class="col-md-4"><label class="control-label">Reference:</label></div>
						<div class="col-md-6"><input class="form-control" type="text" id="payment_reference" name="payment_reference"></div>
					</div>
					<div class="row">
						<div class="col-md-4"><label type="text" class="control-label">Ammount:</label></div>
						<div class="col-md-4"><?=($invoiceDetails->total);?></div>	
					</div>
					<div class="row">
						<div class="col-md-4"><label class="control-label">Cash:</label></div>
						<div class="col-md-4"><input type="text" class="form-control" id="cash" name="cash"></div>
					</div>
					<div class="row">
						<div class="col-md-4"><label class="control-label">Cheque:</label></div>
						<div class="col-md-4"><input type="text" class="form-control" id="cheque" name="cheque"></div>
					</div>
					<div class="row">
						<div class="col-md-4"><label class="control-label">Card:</label></div>
						<div class="col-md-4"><input type="text" class="form-control" id="card" name="card"></div>
					</div>					
				</div>
				<div class="modal-footer">
						<button type="button" class="btn btn-default  btn-block" id="cancel_button" data-dismiss="modal">Cancel</button>
						<button type="button" class="btn btn-primary  btn-block" id="ok_button">Ok</button>
				</div>
			</div>
		</div>
	</div>	