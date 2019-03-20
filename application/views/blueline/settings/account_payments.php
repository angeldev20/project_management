<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 5/11/18
 * Time: 2:22 PM
 */
?>
<div id="row" class="grid">

	<div class="grid__col-sm-12 grid__col-md-3 grid__col-lg-3">
		<div class="list-group">
			<?php foreach ($submenu as $name=>$value):
				$badge = "";
				$active = "";
                if($value == "settings/updates"){ $badge = '<span class="badge badge-success">'.$update_count.'</span>';}
				if($value == 'settings/account_payments') { $active = 'active';}?>
				    <a style="<?php if ($name=="SMTP Settings") echo "display: none;"; ?>" class="list-group-item <?=$active;?>" id="<?php $val_id = explode("/", $value); if(!is_numeric(end($val_id))){echo end($val_id);}else{$num = count($val_id)-2; echo $val_id[$num];} ?>" href="<?=site_url($value);?>"><?=$badge?> <?=$name?></a>
			<?php endforeach;?>
		</div>
	</div>

	<div class="grid__col-sm-12 grid__col-md-9 grid__col-lg-9">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-md-10">
                    <div class="panel">
                        <?php if (isset($_SESSION['billing']['PlanType'])) : ?>
                        <div class="panel-heading"><?=$_SESSION['billing']['PlanType'] ?> Successfully Paid</div>
                        <div class="panel-body">
							<?php
							if ($just_paid) {
								if ( isset( $_SESSION['billing']['OldPlanType'] ) ) {
									echo "Your plan was upgraded from " . $_SESSION['billing']['OldPlanType'] . ' to ' . $_SESSION['billing']['PlanType'] . ' ' . $_SESSION['billing']['UserCount'] . ' user(s)<br><br>';
									echo "Upgrade Reason: " . $_SESSION['billing']['UpgradeReason'] . '<br><br>';
								}
								echo "Your payment of $" . $_SESSION['billing']['GrossPaid'] . " is recorded. <br><br>";
								echo "Your confirmation number is " . $_SESSION['billing']['TransactionId'] . '.';
							}
					    	?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">Payment History</div>
                        <div class="panel-body">
                            <?php if ($paymentHistory) : ?>
                                <div class="col-lg-6">
                                    <!-- START panel-->
                                    <div class="panel panel-default">
                                        <div class="panel-heading">Payments</div>
                                        <div class="panel-body">
                                            <!-- START table-responsive-->
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th>conf #</th>
                                                        <th>Payment Date</th>
                                                        <th>Starting Plan</th>
                                                        <th>Ending Plan</th>
                                                        <th>User Count</th>
                                                        <th>Change Reason if any</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php foreach ($paymentHistory as $payment) : ?>
                                                    <tr>
                                                        <td><?php echo $payment->TransactionId; ?></td>
                                                        <td><?php echo $payment->created; ?></td>
                                                        <td><?php echo $payment->starting_plan; ?></td>
                                                        <td><?php echo $payment->ending_plan; ?></td>
                                                        <td><?php echo $payment->user_count; ?></td>
                                                        <td><?php echo $payment->change_reason; ?></td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!-- END table-responsive-->
                                        </div>
                                    </div>
                                    <!-- END panel-->
                                </div>
                            <?php else: ?>
                                <div class="row">You have no payment history.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!--a href="<?=base_url()?>" class="btn btn-info pull-right">CONTINUE</a-->
            </div>
        </div>
	</div>
</div>
