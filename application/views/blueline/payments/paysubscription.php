<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 5/2/18
 * Time: 3:02 PM
 */
?>
<div id="payment_div">

</div>
<script type="text/javascript">
    $( document ).ready(function() {
        $("#payment_div").load("<?=base_url()?>payments/propay?paymentType=<?=$paymentType?>&amountDue=<?=$amountDue?>&titlePrefix=<?=$titlePrefix?>");
    });
</script>
