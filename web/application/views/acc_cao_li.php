<ul class="list-unstyled">
<?php for( $i=0; $i<count($accounts); $i++): ?>
    
    <?php if ( $i == 0 ): ?>
        <?php $llength = strlen($accounts[$i]->code); ?>            
    <?php endif; ?>
    <?php $currentLen = strlen($accounts[$i]->code); ?>    
    
    <?php if ( $currentLen > $llength  ): ?>
        <ul style=" list-style-type: none;">
    <?php endif; ?>
    
    <li><span style=""><?=$accounts[$i]->code?></span><span style="display:inline-block; width: 10px"></span><?=$accounts[$i]->name?> <button type="button" class="close" style="float:none !important" data-dismiss="modal" aria-label="Close" onclick="deleteAccount(<?=$accounts[$i]->code?>)"><span aria-hidden="true">×</span></button>
    
    <?php if ( isset($accounts[$i+1]) && (strlen($accounts[$i+1]->code) <= $currentLen ) ): ?>
        </li>        
        <?php if ( $currentLen > 1 && strlen($accounts[$i+1]->code) < $currentLen ): ?>
             
            <?php for($j=1; $j<= ($currentLen - strlen($accounts[$i+1]->code))*2 ; $j++): ?>
                <?php if ( $j % 2 != 0): ?>
                    </ul>
                <?php else: ?>
                    </li>
                <?php endif; ?>
                
                 <?php if (  isset($accounts[$i-$j]) && strlen($accounts[$i-$j]->code) == strlen($accounts[$i+1]->code) ): ?>
                    <?php break; ?>
                <?php endif; ?>
                
            <?php endfor; ?>
        <?php endif; ?>
    <?php endif; ?>
    
    <?php $llength = $currentLen; ?>
<?php endfor; ?>
</ul>