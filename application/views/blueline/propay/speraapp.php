<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 7/9/18
 * Time: 4:12 PM
 */
?>

<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 7/10/18
 * Time: 10:15 AM
 */
?>
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
        <input type="button" id="btnSubmit" name="btnSubmit" value="Submit" class="btn btn-info pull-right" onclick="$('#hpp_parent').contents().find('#btnSubmit').trigger( 'click' );" />
        <!--button type="button" data-dismiss="modal" class="btn btn-warning pull-right">Close</button-->
    </div>
</div>
<script>
    var HID = null;
    var baseURI = '<?php echo PROTECT_PAY_HOSTED_TRANSACTION_BASE_URL; ?>';
    function recordTransaction() {
        var url = '/propay/recordapppayment';
        $.ajax({
            url: url,
            type: "GET",
            dataType: "JSON"
        }).done(function(json) {
            if (typeof json.success !== "undefined" && json.success == true) {
                var jqxhr = $.get( 'https://spera-api-test.herokuapp.com/api/HID/<?php echo urlencode($hid); ?>', function() {
                    //alert( "success: recorded HID <?php echo urlencode($hid);?> in Spera App facing API" );
                })
                    .done(function() {
                    })
                    .fail(function() {
                        //TODO: this is allways firing we should find out why, for now we are just always
                        // accepting that payment was recorded on the API since it is and redirecting to thank you
                        //alert( "error, could not record HID  <?php echo urlencode($hid);?> in spera app API at url 'https://spera-api-test.herokuapp.com/api/HID/<?php echo $hid; ?>'" );
                    })
                    .always(function() {
                        window.location.href = "<?=base_url()?>" + json.redirectUrl;
                    });
                jqxhr.always(function() {
                });
            }
        }).fail(function(json) {
            console.log("failed to record payment for HID <?php echo $hid;?>  in spera app API at url 'https://spera-api-test.herokuapp.com/api/HID/<?php echo $hid; ?>'");
        });
    }


    // use back end api to get hosted transaction identifier and start HPP
    function getHostedTransactionIdentifier() {
        HID = '<?php echo $hid; ?>';
        $( document ).ready(function() {
            $('.hppFrame').attr('src', '<?php echo base_url(); ?>hpp.php');
        });
    }

    $( document ).ready(function() {
        getHostedTransactionIdentifier();
        $("#test-payment").click(function(){ recordTransaction(); });
    });
</script>


<button id="test-payment" class="btn btn-lg btn-success">Test Payment</button>
<!--form method="GET" action="https://spera-api-test.herokuapp.com/api/HID/<?php echo $hid; ?>">
    <input type="submit" value="Click to Pay" />
</form-->