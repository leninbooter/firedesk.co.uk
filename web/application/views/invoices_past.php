<div class="container-fluid">
    <div class="row">
            <div class="col-md-12">                
                    <div class="row">
                        <div class="col-md-12">                    
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"><?=$invoicesCount?> invoices</h3>
                                    </div>
                                    <div class="panel-body">                                        
                                            <table class="table table-responsive table-hover table-condensed">
                                                <thead>
                                                    <th>Date</th>
                                                    <th>Doc No.</th>
                                                    <th>Value</th>                                                    
                                                    <th>Due</th>                                                    
                                                    <th><p class="text-center">Paid now</p></th>                                                    
                                                </thead>
                                                <tbody>
                                                    <?php foreach($invoices as $i): ?>
                                                        <tr onclick="document.location='<?=base_url('index.php/invoices/view?iid=').$i->pk_id?>'" style="cursor: pointer; cursor: hand">
                                                            <td><?=date('d F Y', strtotime( $i->creation_date ))?></td>
                                                            <td><?=$i->pk_id?></td>
                                                            <td><?=$i->total?></td>
                                                            <td><?=$i->unpaid_ammount?></td>                                                            
                                                            <td><p class="text-center"><button class="btn btn-info btn-sm">Pay</button></p></td>		
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>                                        
                                    </div>
                                </div>                    
                        </div>
                    </div>                                
            </div>             
    </div>
</div>
