<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 5/2/18
 * Time: 3:01 PM
 */
?>
<div id="payment_choices_div">

</div>
<script type="text/javascript">
    $( document ).ready(function() {
        $("#payment_choices_div").load("<?=base_url()?>payments/billing?paymentType=<?=$paymentType?>&titlePrefix=<?=$titlePrefix?>");
    });
</script>