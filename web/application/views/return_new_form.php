<form role="form" id="returnForm">
<input type="hidden" id="contractID" name="contractID" value="<?=$contractID?>">
<input type="hidden" id="type" name="type" value="hired">
<div class="container-fluid">
    <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-inline">
                                <div class="form-group">
                                    <label class=" control-label">Date:</label>
                                     <input type="text" class="form-control" id="returnDate" name="returnDate" value="<?php echo $date; ?>" onclick="$(this).datepicker({format: 'dd/mm/yyyy'}).on('changeDate', function(ev){
                                     $(this).datepicker('hide');}).datepicker('show');">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Time:</label>
                                    <input type="text" class="form-control" id="returnTime" name="returnTime" style="width:30%" value="<?php echo $time; ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">                    
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Items on hire</h3>
                                    </div>
                                    <div class="panel-body">
                                        
                                            <table class="table table-responsive table-hover table-condensed">
                                                <thead>
                                                    <th>Number</th>
                                                    <th>Description</th>
                                                    <th style="width:10%">On Hire</th>
                                                    <th style="width:10%">Off Hire?</th>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($items as $i): ?>
                                                        <tr>
                                                            <td><input type="hidden" id="collectItemId" name="collectItemId[]" value="<?=$i->collection_item_id?>"><?=$i->number?></td>
                                                            <td><?=$i->description?></td>
                                                            <td><?=$i->on_hire?></td>
                                                            <td><input type="text" class="form-control input-sm" id="returnedQty" name="returnedQty[]" value="<?=$i->on_hire?>"></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        
                                    </div>
                                </div>                    
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">Notes:</label>
                            <textarea id="returnNotes" name="returnNotes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-11"></div>
                    <div class="col-md-1"><button type="button" class="btn btn-default" onclick="submitReturn()">Save</button></div>
                </div>
            </div>             
    </div>
</div>
</form>