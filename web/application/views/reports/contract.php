<h2><p class="text-center">Hire / Sale Contract</p></h2>
<h3><p class="text-center">Contract No. <?php echo $contractID; ?> <small><?php echo $contract_type; ?></small></p></h3>

<table style="width:100%" class="table">
    <tr>
        <td style="width:30%"><h4>Client</h4></td>
        <td style="width:30%"><h4>Delivery</h4></td>
        <td style="width:30%"></td>
    </tr>
    <tr>
        <td>
            <strong><?php echo $customer_name; ?></strong>
            <address>
                <?php echo $contract_details->address; ?>
            </address>
        </td>
        <td>
            <?php echo $address; ?>
            <p><strong>Delivery charge:</strong> <?php echo $delivery_charge; ?></p>
        </td>
        <td>
            <p><b>Create date:</b> <?php echo date('d/m/Y - H:i', strtotime( $contract_details->creation_date )); ?></p>
            <p><b>Start date:</b> <?php echo $contract_details->date_time == "" ? "":date('d/m/Y - H:i', strtotime( $contract_details->date_time )); ?></p>
        </td>
    </tr>
</table>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Sale</h3>
    </div>
    <div class="panel-body">
        <?php echo $soldItems; ?>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Hire</h3>
    </div>
    <div class="panel-body">
        <?php echo $hiredItems; ?>
    </div>
</div>

<h4>Charging Bands</h4>
<?php echo $chargingBands; ?>

<h4>Notes</h4>
<?php echo $contract_details->notes; ?>