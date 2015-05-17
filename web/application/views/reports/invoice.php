<table>
    <tr>
        <td style=" border:0px solid #00; vertical-align: top;"><img src="<?php echo $logoURL; ?>"  width="80" ></td>
        <td style="padding-left:5px"><?php echo $companyName; ?>
        </td>
    </tr>
</table>

<h2><p class="text-center">Invoice No. <?=$invoiceDetails->pk_id?></p></h2>
<table style="width:100%" class="table">
    <tr>
        <td style="width:50%"><h4>Client</h4></td>        
        <td style="width:50%"></td>
    </tr>
    <tr>
        <td>
            <strong><?=$invoiceDetails->customerName?></strong>
            <address><?=$invoiceDetails->customerAddress?></address>
        </td>        
        <td style="text-align:right">
            <table>
                <tr><td>Contract No.  </td><td><?=$invoiceDetails->contractID?></td></tr>
                <tr><td>Invoice date: </td><td><?=date('d F Y H:i', strtotime($invoiceDetails->creation_date))?></td></tr>
            </table>
        </td>
    </tr>
</table>
<h4>Items</h4>
<?php echo $invoiceItems; ?>
<table align="right">
    <tr><td style="text-align:right">Sub total:</td><td style="text-align:right; padding-left: 10px"><?=($invoiceDetails->subtotal);?></td></tr>
    <tr><td style="text-align:right">V.A.T.:</td><td style="text-align:right;  padding-left: 10px"><?=($invoiceDetails->vat);?></td></tr>
    <tr><td style="text-align:right">Total:</td><td style="text-align:right;  padding-left: 10px"><?=($invoiceDetails->total);?></td></tr>
</table>
<hr/>