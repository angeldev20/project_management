<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 5/3/18
 * Time: 3:39 PM
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
$attributes = array('class' => '', 'id' => 'billing-form');
?>

<input type="hidden" name="paymentType" value="card">
<?php
if (isset($errors) && !empty($errors) && is_array($errors)) {
	echo '<div class="alert alert-danger"><h4>Error!</h4>The following error(s) occurred:<ul>';
	foreach ($errors as $e) {
		echo "<li>$e</li>";
	}
	echo '</ul></div>';
}?>

<div id="payment-errors" class="payment-errors"></div>
<div class="modal-content">
	<div class="modal-body">
		<div class="col-md-10">
		    <?php /** @var billing $billing */ ?>
            <?php foreach ($billing->getPlanList() as $plan) : ?>
			<div class="panel">
                <?php if ($plan->billingFrequencyDays == 365) : ?>
				    <div class="panel-heading"><?=$plan->name ?> (<?php echo ($billing->getUserCount() == 1) ? $billing->getUserCount() . ' user' : $billing->getUserCount() . ' users' ?>) $<?php echo number_format($plan->amount/100,2);?> - Annual Rate save about 17%</div>
                    <div class="panel-body">
                        <a href="<?=base_url()?>payments/paysubscription?planType=<?php echo $plan->type; ?>" id="btnSubmit" name="btnSubmit" class="btn btn-info pull-left"> Select Annual</a>
                    </div>
                <?php elseif($plan->billingFrequencyDays == 30) : ?>
                    <div class="panel-heading"><?=$plan->name ?> (<?php echo ($billing->getUserCount() == 1) ? $billing->getUserCount() . ' user' : $billing->getUserCount() . ' users' ?>) $<?php echo number_format($plan->amount/100,2);?> - Monthly Rate</div>
                    <div class="panel-body">
                        <a href="<?=base_url()?>payments/paysubscription?planType=<?php echo $plan->type; ?>" id="btnSubmit" name="btnSubmit" class="btn btn-info pull-left"> Select Monthly</a>
                    </div>
                <?php else: ?>
                    <div class="panel-heading"><?=$plan->name ?> (<?php echo ($billing->getUserCount() == 1) ? $billing->getUserCount() . ' user' : $billing->getUserCount() . ' users' ?>) $<?php echo number_format($plan->amount/100,2);?> - Plan Rate</div>
                    <div class="panel-body">
                        <a href="<?=base_url()?>payments/paysubscription?planType=<?php echo $plan->type; ?>" id="btnSubmit" name="btnSubmit" class="btn btn-info pull-left"> Select Plan</a>
                    </div>
                <?php endif; ?>
			</div>
            <?php endforeach; ?>
		</div>
	</div>
	<div class="modal-footer">
	</div>
</div>

