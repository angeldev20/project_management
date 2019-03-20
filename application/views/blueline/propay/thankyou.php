<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 7/10/18
 * Time: 1:54 PM
 */
?>

Thank you for your <?php echo $_SESSION['paymentType']; ?> payment of $<?php echo $_SESSION['paymentAmount']; ?> on account <?php echo $_SESSION['accountUrlPrefix']; ?>.
<br>
<br>Your transaction ID is <?php echo $_SESSION['TransactionId']; ?>.
