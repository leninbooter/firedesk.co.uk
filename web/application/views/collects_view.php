<input type="hidden" name="contract_id" id="contract_id" value="<?=$contractDetails->pk_id?>">
<input type="hidden" name="collect_id" id="collect_id" value="<?=$collectDetails->pk_id?>">
<div class="row">
	<div class="col-md-6">		
        <h1>Contract No. <?=$contractDetails->pk_id?></h1>
        <h2>Collect note (O/H) No. <?=$collectDetails->pk_id?> <small><?=date('d F Y H:i', strtotime($collectDetails->datetime))?></small></h1>        
	</div>	
</div>

<div class="row">
	<div class="col-md-6">
		<h3>Client name <?= $contractDetails->name?></h3>		
		<address>
			<?=$contractDetails->address?>
		</address>
	</div>	
	<div class="col-md-6">
		<h4>Site Address</h4>		
		<?=$contractDetails->delivery_address?>
	</div>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table table-responsive table-hover table-condensed">
            <thead>
                <th style="width:10%">Qty</th>
                <th>Description</th>            
            </thead>
            <tbody>
                <?php foreach($items as $i): ?>
                    <tr>
                        <td><?=$i->qty?></td>
                        <td><?=$i->description?></td>                    
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-md-1"><button type="button" class="btn btn-default" onclick="history.go(-1)" >Back</button></div>
</div>