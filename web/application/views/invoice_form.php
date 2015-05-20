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

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Payment History</h3>
      </div>
      <div class="panel-body">
        <table class="table table-responsive table-hover table-condensed">
            <thead>
                <th>Date</th>
                <th>Cash</th>
                <th>Cheque</th>                                                    
                <th>Card</th>                                                    
                <th>Total</th>                                                    
                <th style="width:40%">Reference</th>                                                    
            </thead>
            <tbody>
                <?php foreach($payments as $p): ?>                    
                    <tr>
                        <td><?=date('d M Y', strtotime($p->datetime))?></td>
                        <td><?=$p->cash?></td>
                        <td><?=$p->cheque?></td>
                        <td><?=$p->card?></td>
                        <td><?=$p->ammount?></td>
                        <td><?=$p->reference?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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
	
    
			