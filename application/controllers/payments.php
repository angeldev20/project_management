<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 5/3/18
 * Time: 9:34 AM
 */
class Payments extends MY_Controller {

	public function billing() {
			$titlePrefix = (isset($_REQUEST['titlePrefix'])) ? $_REQUEST['titlePrefix'] : 'SPERA SUBSCRIPTION DUE:';
			$paymentType = (isset($_REQUEST['paymentType'])) ? $_REQUEST['paymentType'] : 'card';
			$amountDue = (isset($_REQUEST['amountDue'])) ? $_REQUEST['amountDue'] : $this->billing->getAmountDue();
			if ($paymentType == 'ach') {
				$this->view_data['title'] = $titlePrefix . ' ' . $this->lang->line('application_pay_with_ach');
			} else {
				$this->view_data['title'] = $titlePrefix . ' ' . $this->lang->line('application_pay_with_credit_card');
			}
			$this->view_data['billing'] = $this->billing;
			$this->view_data['paymentType'] = $paymentType;
			$this->theme_view               = 'modal';
			$this->view_data['form_action'] = 'payments/billing';
			$this->content_view = 'payments/_payment_choices';
	}

	public function expiredsubscription() {
		$titlePrefix = (isset($_REQUEST['titlePrefix'])) ? $_REQUEST['titlePrefix'] : 'SPERA SUBSCRIPTION DUE:';
		$paymentType = (isset($_REQUEST['paymentType'])) ? $_REQUEST['paymentType'] : 'card';
		if ($paymentType == 'ach') {
			$this->view_data['title'] = $titlePrefix . ' ' . $this->lang->line('application_pay_with_ach');
		} else {
			$this->view_data['title'] = $titlePrefix . ' ' . $this->lang->line('application_pay_with_credit_card');
		}
		$this->view_data['billing'] = $this->billing;
		$this->view_data['paymentType'] = $paymentType;
		$this->theme_view       = 'fullpage';
		$this->content_view     = 'payments/expiredsubscription';
	}

	public function paysubscription() {
		$planType = (isset($_REQUEST['planType'])) ? $_REQUEST['planType'] : 'SPERA SUBSCRIPTION DUE:';
		$titlePrefix = (isset($_REQUEST['titlePrefix'])) ? $_REQUEST['titlePrefix'] : 'SPERA SUBSCRIPTION DUE:';
		$paymentType = (isset($_REQUEST['paymentType'])) ? $_REQUEST['paymentType'] : 'card';
		$amountDue = (isset($_REQUEST['amountDue'])) ? $_REQUEST['amountDue'] : null;
        if (!$amountDue) {
        	foreach ($this->billing->getPlanList() as $plan) {
        		if ($plan->type == $planType) {
        			$amountDue = $plan->amount;
		        }
	        }
        }

		if ($paymentType == 'ach') {
			$this->view_data['title'] = $titlePrefix . ' ' . $this->lang->line('application_pay_with_ach');
		} else {
			$this->view_data['title'] = $titlePrefix . ' ' . $this->lang->line('application_pay_with_credit_card');
		}
		$this->view_data['amountDue'] = $amountDue;
		$this->view_data['billing'] = $this->billing;
		$this->view_data['paymentType'] = $paymentType;
		$this->theme_view       = 'fullpage';
		$this->content_view     = 'payments/paysubscription';
	}

	function propay()
	{
		$paymentType = $_REQUEST['paymentType'];
		$amount_due = $_REQUEST['amountDue'];
		$titlePrefix = (isset($_REQUEST['titlePrefix'])) ? $_REQUEST['titlePrefix'] : 'SPERA SUBSCRIPTION DUE:';

		//generate a spera invoice for this person, or lookup.
		//$this->view_data['invoices'] = Invoice::find_by_id( 1 ); //todo: stuffing invoice number for now

		$this->view_data['amount_due']  = $amount_due;
		//$this->view_data['sum'] = $sum;
		$this->view_data['paymentType'] = $paymentType;
		$this->theme_view       = 'modal';

		$this->view_data['form_action'] = 'payments/propay';

		if ($paymentType == 'ach') {
			$this->view_data['title'] = $titlePrefix . ' ' . $this->lang->line('application_pay_with_ach');
		} else {
			$this->view_data['title'] = $titlePrefix . ' ' . $this->lang->line('application_pay_with_credit_card');
		}
		$this->content_view             = 'payments/_propay_hpp';
	}

}