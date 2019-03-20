<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 4/4/18
 * Time: 4:04 PM
 */
class billing {

	/** @var bool */
	private $_due;

	/** @var int - whole cents */
	private $_amount_due;

	/** @var  string */
	private $_databaseName;

	/** @var  CI_DB_mysql_driver */
	private $_primaryDatabase;

	/** @var  CI_DB_mysql_driver */
	private $_accountDatabase;

	/** @var int */
	private $_discount;

	/** @var stdClass|bool */
	private $_promo_code;

	/** @var stdClass|bool */
	private $_plan_type;

	/** @var stdClass|bool */
	private $_parent_plan_type;

	/** @var stdClass|bool */
	private $_child_plan_type;

	/** @var array|null */
	private $_plan_list;

	/** @var int|null */
	private $_user_count;

	/** @var stdClass|bool */
	private $_account_discount;

	/** @var stdClass|bool */
	private $_subscription;

	/** @var CI_Session */
	private $_session;

	/**
	 * billing constructor.
	 *
	 * @param array|null $init_array
	 */
	public function __construct($init_array = NULL)
	{
		if (!is_null($init_array)) {
			if (isset($init_array['databaseName'])) $this->_databaseName = $init_array['databaseName'];
			if (isset($init_array['primaryDatabase'])) $this->_primaryDatabase = $init_array['primaryDatabase'];
			if (isset($init_array['accountDatabase'])) $this->_accountDatabase = $init_array['accountDatabase'];
			if (isset($init_array['session'])) $this->_session = $init_array['session'];
		}
	}

	/**
	 * @param $due
	 *
	 * @return $this
	 */
	public function setDue($due) {
		$this->_due = $due;
		return $this;
	}

	/**
	 * @param int $amountInWholeCents
	 */
	public function setAmountDue($amountInWholeCents) {
		$this->_amount_due = $amountInWholeCents;
	}

	/**
	 * @return int  - in whole cents
	 */
	public function getAmountDue() {
		return $this->_amount_due;
	}

	/**
	 * @return bool
	 */
	public function getDue() {
        return $this->_due;
	}

	/**
	 * @return bool|stdClass
	 */
	public function getPromoCode() {
		return $this->_promo_code;
	}

	/**
	 * @return int
	 */
	public function getDiscount() {
		return $this->_discount;
	}

	/**
	 * @return stdClass|bool
	 */
	public function getPlanType() {
		return $this->_plan_type;
	}

	/**
	 * @return bool|stdClass
	 */
	public function getParentPlanType() {
		return $this->_parent_plan_type;
	}

	/**
	 * @return bool|stdClass
	 */
	public function getChildPlanType() {
		return $this->_child_plan_type;
	}

	/**
	 * @return $this
	 */
	public function loadPlanType($subscription) {
		$sql = "SELECT * FROM plan_types WHERE `type`='" . $subscription->type . "';";
		/** @var  CI_DB_mysql_result $result */
		$result = $this->_primaryDatabase->query($sql, []);
		if ($result->num_rows() > 0) {
			$this->_plan_type =  $result->result()[0];
		} else {
			$this->_plan_type =  false;
		}
		return $this;
	}

	/**
	 * @return $this
	 */
	private function _isParentPlanType() {
        if ($this->_plan_type->parent_id > 0) {
	        $sql = "SELECT * FROM plan_types WHERE `id`=" . $this->_plan_type->parent_id . ";";
	        /** @var  CI_DB_mysql_result $result */
	        $result = $this->_primaryDatabase->query($sql, []);
	        if ($result->num_rows() > 0) {
		        $this->_parent_plan_type =  $result->result()[0];
		        $this->_child_plan_type = $this->_plan_type;
	        } else {
		        $this->_parent_plan_type =  false;
	        }
	        return $this;
        }
	}

	/**
	 * @return $this
	 */
	private function _isChildPlanType() {
		if ($this->_plan_type->parent_id > 0) {
			$sql = "SELECT * FROM plan_types WHERE `parent_id`=" . $this->_plan_type->id . ";";
			/** @var  CI_DB_mysql_result $result */
			$result = $this->_primaryDatabase->query($sql, []);
			if ($result->num_rows() > 0) {
				$this->_child_plan_type =  $result->result()[0];
				$this->_parent_plan_type = $this->_plan_type;
			} else {
				$this->_child_plan_type =  false;
			}
			return $this;
		}
	}

	/**
	 * @return int|null
	 */
	public function getUserCount() {
		$_SESSION['billing']['UserCount'] = $this->_user_count;
		return $this->_user_count;
	}

	/**
	 * @return bool
	 */
	public function doesPlanNeedUpgrade() {
		$planNeedsUpgrade=false;
		$sql = "SELECT count(username) AS user_count FROM users WHERE 1;";
		/** @var  CI_DB_mysql_result $result */
		$result = $this->_accountDatabase->query($sql, []);
		if ($result->num_rows() > 0) {
            $row = $result->result()[0];
            $this->_user_count = $row->user_count;
            if ($this->_plan_type->max_users != null && $this->_plan_type->max_users < $row->user_count ) $planNeedsUpgrade = true;
		    $_SESSION['billing']['UpgradeReason'] = 'Number of users increased.';
		}
		return $planNeedsUpgrade;
	}

	/**
	 * @return $this
	 */
	public function upgradePlan() {
		$sql = "SELECT * FROM plan_types WHERE `id`=" . $this->_plan_type->upgrades_to . ";";
		/** @var  CI_DB_mysql_result $result */
		$result = $this->_primaryDatabase->query($sql, []);
		if ($result->num_rows() > 0) {
			$this->_plan_type =  $result->result()[0];
		} else {
			$this->_plan_type =  false;
		}
		if ($this->_plan_type) {
			$sql = "UPDATE account_plans SET `type`='" . $this->_plan_type->type . "' WHERE accountUrlPrefix='" . $_SESSION['accountUrlPrefix'] . "';";
			$result = $this->_primaryDatabase->query($sql, []);
		}
		$_SESSION['billing']['planUpgrade'] = $this->_plan_type->type;
		return $this;
	}

	/**
	 * @return bool
	 * requires that you called isParentPlanType and isChildPlanType first
	 */
	public function isLinkedPlanType() {
	    $isLinkedPlanType = false;
		if($this->_isParentPlanType()->getParentPlanType()) {
	    	$isLinkedPlanType = true;
	    } else {
			if ( $this->_isChildPlanType()->getChildPlanType() ) {
				$isLinkedPlanType = true;
			}
		}
        return $isLinkedPlanType;
	}

	/**
	 * @return string
	 */
	public function whichPlanIsGreater() {
		if ($this->_parent_plan_type->amount > $this->_child_plan_type->amount) {
			return 'parent';
		} else {
			return 'child';
		}
	}

	/**
	 * Set account qualified plan list
	 * @param array $planList
	 */
	public function setPlanList(array $planList) {
		$this->_plan_list = $planList;
	}

    /**
	 * @return array|null
	 */
	public function getPlanList() {
	    return $this->_plan_list;
	}

	/**
	 * @return bool|stdClass
	 */
	public function getSubscription() {
		return $this->_subscription;
	}

	/**
	 * @param string $accountUrlPrefix
	 * @return $this
	 */
	public function loadSubscription($accountUrlPrefix) {
		$sql = "SELECT * FROM account_plans WHERE accountUrlPrefix='" . $accountUrlPrefix . "';";
		/** @var  CI_DB_mysql_result $result */
		$result = $this->_primaryDatabase->query($sql, []);
		if ($result->num_rows() > 0) {
			$this->_subscription =  $result->result()[0];
			if ($this->_session) {
				$this->_session->set_userdata('lastBilled', $this->_subscription->lastBilled);
		    }
		} else {
			$this->_subscription =  false;
		}
		return $this;
	}

	/**
	 * @param int $promoCodeId
	 *
	 * @return $this
	 */
	private function _getAccountPromoCode($promoCodeId) {
		$sql = "SELECT * FROM accounts_promo_codes WHERE id=" . $promoCodeId . ";";
		/** @var  CI_DB_mysql_result $result */
		$result = $this->_primaryDatabase->query($sql, []);
		if ($result->num_rows() > 0) {
			$this->_promo_code =  $result->result()[0];
		} else {
			$this->_promo_code =  false;
		}
		return $this;
	}

	/**
	 * @return bool|stdClass
	 */
	public function getAccountDiscount() {
		return $this->_account_discount;
	}

	/**
	 * @param $accountUrlPrefix
	 *
	 * @return $this
	 */
	public function getAccountPromoCode($accountUrlPrefix) {
		$sql = "SELECT * FROM accounts_discounts WHERE accountUrlPrefix='" . $accountUrlPrefix . "';";
		/** @var  CI_DB_mysql_result $result */
		$result = $this->_primaryDatabase->query($sql, []);
		if ($result->num_rows() > 0) {
			$this->_account_discount =  $result->result()[0];
		} else {
			$this->_account_discount =  false;
		}
		if ($this->_account_discount) {
			$this->_getAccountPromoCode($this->_account_discount->accounts_promo_code_id);
			if ($this->_promo_code) {
				$this->_discount = $this->_promo_code->discount_percentage;
			} else {
				$this->_discount = 0;
			}
		} else {
			$this->_discount = 0;
			$$this->_promo_Code = false;
		}
		return $this;
	}
}