<style>
    .hppFrame {
        width: 100%;
        border: none;
        height: 389px; }

    .modal-dialog {
        min-width: 710px;
    }
</style>

<?php
$attributes = array('class' => '', 'id' => 'payment-form');
?>

<input type="hidden" name="id" value="<?= $id;?>">
<input type="hidden" name="type" value="card">
<?php
if (isset($errors) && !empty($errors) && is_array($errors)) {
    echo '<div class="alert alert-danger"><h4>Error!</h4>The following error(s) occurred:<ul>';
    foreach ($errors as $e) {
        echo "<li>$e</li>";
    }
    echo '</ul></div>';
}?>

<div id="payment-errors" class="payment-errors"></div>
<!--//TODO: this should be using the template variable to get to the location rather than hardcoding blueline-->
<div class="modal-content">
    <div class="modal-body">
        <iframe class="hppFrame" id="hpp_parent" scrolling="no"></iframe>
    </div>
    <div class="modal-footer">
        <input type="button" id="btnSubmit" name="btnSubmit" value="Submit" class="btn btn-info pull-left" onclick="$('#hpp_parent').contents().find('#btnSubmit').trigger( 'click' );" />
        <button type="button" data-dismiss="modal" class="btn btn-warning pull-right">Close</button>
    </div>
</div>
<script>
    var HID = null;
    var baseURI = '<?php echo PROTECT_PAY_HOSTED_TRANSACTION_BASE_URL; ?>';
    var transactionPaymentType = '<?php echo $paymentType; ?>';
    function recordTransaction() {
        var invoiceId = <?php echo $id; ?>;
        var url = '/propay/recordpayment?id=<?php echo $id; ?>&paymentType=' + transactionPaymentType;
        $.ajax({
            url: url,
            type: "GET",
            dataType: "JSON"
        }).done(function(json) {
            if (typeof json.success !== "undefined" && json.success == true) {
                //$('body').append(json);
                window.location.reload();
            } else {
                //$('body').append(json);
            }
        }).fail(function(json) {
            console.log('failed to get HID via ajax');
        });
    }


    // use back end api to get hosted transaction identifier and start HPP
    function getHostedTransactionIdentifier(paymentType) {
        transactionPaymentType = paymentType;
        var url = "/propay/hid?id=<?php echo $id; ?>&paymentType=" + paymentType + "&StoreCard=true";
        $.ajax({
            url: url,
            type: "GET",
            dataType: "JSON"
        }).done(function(json) {
            if (typeof json.success !== "undefined" && json.success == true) {
                HID = json.HostedTransactionIdentifier;
                //alert(HID);
                $( document ).ready(function() {
                    $('.hppFrame').attr('src', '/hpp.php');
                });
            } else {
                $('body').append(json);
            }
        }).fail(function(json) {
            console.log('failed to get HID via ajax');
        });
    }

    $( document ).ready(function() {
        var type = '<?php echo $paymentType; ?>';
        getHostedTransactionIdentifier(type);
    });
</script>
