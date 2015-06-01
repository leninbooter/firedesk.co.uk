<h2>Default Nominal Codes</h2>
<div class="row">
    <div class="col-md-9">
        <div class="row">
        <form id="defNominalAccountsForm" role="form" class="form-horizontal">
            <div class="col-md-4">
                <?php for( $i=0; $i<=10; $i++ ): ?>                    
                      <div class="form-group">
                        <label for="<?=$accounts[$i]->pk_id?>" class="col-md-7 control-label"><?=$accounts[$i]->account_name?></label>
                        <div class="col-md-5">
                          <input type="text" class="form-control" id="<?=$accounts[$i]->pk_id?>" name="<?=$accounts[$i]->fk_account_id?>" >
                        </div>                       
                      </div>                
                <?php endfor; ?>
            </div>            
            <div class="col-md-4">
                <?php for( $i=11; $i<=20; $i++ ): ?>                    
                      <div class="form-group">
                        <label for="<?=$accounts[$i]->pk_id?>" class="col-md-7 control-label"><?=$accounts[$i]->account_name?></label>
                        <div class="col-md-5">
                          <input type="text" class="form-control" id="<?=$accounts[$i]->pk_id?>" name="<?=$accounts[$i]->pk_id?>" value="<?=$accounts[$i]->fk_account_id?>" >
                        </div>                       
                      </div>                
                <?php endfor; ?>
            </div>
            <div class="col-md-4">
                <?php for( $i=21; $i<=26; $i++ ): ?>                    
                      <div class="form-group">
                        <label for="<?=$accounts[$i]->pk_id?>" class="col-md-7 control-label"><?=$accounts[$i]->account_name?></label>
                        <div class="col-md-5">
                          <input type="text" class="form-control" id="<?=$accounts[$i]->pk_id?>" name="<?=$accounts[$i]->fk_account_id?>" value="<?=$accounts[$i]->fk_account_id?>" >
                        </div>                       
                      </div>                
                <?php endfor; ?>
            </div>
            </form>
        </div>
    </div>
    <div class="col-md-3">
        <div class="row">
            <div class="col-md-12">
                <button type="button" class="btn btn-primary btn-block">Save</button>
            </div>
        </div>
    </div>
</div>