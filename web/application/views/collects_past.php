<div class="container-fluid">
    <div class="row">
            <div class="col-md-12">                
                    <div class="row">
                        <div class="col-md-12">                    
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"><?=$notesCount?> collection notes</h3>
                                    </div>
                                    <div class="panel-body">                                        
                                            <table class="table table-responsive table-hover table-condensed">
                                                <thead>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                    <th>Collect No.</th>                                                    
                                                </thead>
                                                <tbody>
                                                    <?php foreach($notes as $c): ?>
                                                        <tr onclick="document.location='<?=base_url('index.php/collects/view?id=').$c->pk_id?>'" style="cursor: pointer; cursor: hand">
                                                            <td><?=date('d F Y', strtotime( $c->datetime ))?></td>
                                                            <td><?=date('H:i', strtotime( $c->datetime ))?></td>
                                                            <td><?=$c->pk_id?></td>                                                            
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
