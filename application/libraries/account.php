<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 10/4/17
 * Time: 4:37 PM
 */

class account {

    /** @var  string */
    private $_databaseName;

    /** @var  CI_DB_mysql_driver */
    private $_primaryDatabase;

    /** @var  CI_DB_mysql_driver */
    private $_accountDatabase;

    /** @var  string */
    private $_accountSql;

    /** @var  string */
    private $_hashed_password;

    /** @var  string */
    private $_username;

    /** @var  string */
    private $_firstname;

    /** @var  string */
    private $_lastname;

    /** @var  string */
    private $_password;

    /** @var  string */
    private $_email;

    /** @var string */
    private $_encryptedString;

    /** @var string */
    private $_decryptedString;

    /** account based fields */

    /** @var string */
    private $_accountUrlPrefix;

    /** @var string */
    private $_accountName;

    /** @var string */
    private $_accountContactFirstName;

    /** @var string */
    private $_accountContactLastName;

    /** @var string */
    private $_db_hostname;

    /** @var string */
    private $_db_username;

    /** @var string */
    private $_db_password;

    /** @var string */
    private $_db_database;

    /** @var string */
    private $_db_version;

    /** @var  string */
    private $_promo_code;

    /** @var stdClass|bool */
    private $_account;

    /** @var stdClass|bool */
	private $_subscription;

	/** @var billing */
	private $_billing;

	/** @var stdClass|bool */
	private $_plan_type;

	/** @var stdClass|bool */
	private $_parent_plan_type;

	/** @var stdClass|bool */
	private $_child_plan_type;

	/** @var bool */
	private $_is_linked_plan_type;

	/** @var array */
    private $_plan_list;

	/** @var stdClass|bool */
	private $_last_paid_date;

	/** @var int */
	private $_last_paid_days;

	/** @var int */
	private $_account_age_days;

	/** @var bool */
	private $_due;

	/** @var int - in whole cents */
	private $_amount_due;

	/** @var int */
	private $_user_count;

	/**
	 * @var int
	 *
	 * 0 = inactive, 1 = active spera pro account, 2 = active spera app account
	 */
    private $_active = 1;


	/**
     * account constructor.
     * @param null|array $init_array
     */
    public function __construct($init_array = NULL)
    {
        if (!is_null($init_array)) {
            if (isset($init_array['databaseName'])) $this->_databaseName = $init_array['databaseName'];
            if (isset($init_array['primaryDatabase'])) $this->_primaryDatabase = $init_array['primaryDatabase'];
            if (isset($init_array['accountDatabase'])) $this->_accountDatabase = $init_array['accountDatabase'];
            if (isset($init_array['accountSql'])) $this->_accountSql = $init_array['accountSql'];
        }
    }

    /**
     * @param string $databaseName
     * @return $this
     */
    public function setDatabaseName($databaseName) {
        $this->_databaseName = $databaseName;
        return $this;
    }

    /**
     * @param CI_DB_mysql_driver $primaryDatabase
     * @return $this
     */
    public function setPrimaryDatabase(CI_DB_mysql_driver $primaryDatabase) {
        $this->_primaryDatabase = $primaryDatabase;
        return $this;
    }

    /**
     * @param CI_DB_mysql_driver $accountDatabase
     * @return $this
     */
    public function setAccountDatabase(CI_DB_mysql_driver $accountDatabase) {
        $this->_accountDatabase = $accountDatabase;
        return $this;
    }

    /**
     * @param string $accountSql
     * @return $this
     */
    public function setAccountSql($accountSql) {
        $this->_accountSql = $accountSql;
        return $this;
    }

    /**
     * @param string $promoCode
     * @return $this
     */
    public function setPromoCode($promoCode) {
        $this->_promo_code = $promoCode;
        return $this;
    }

	/**
	 * @return bool|stdClass
	 */
    public function getAccount() {
    	return $this->_account;
    }

    /**
     * @return bool
     */
    public function create() {

        $tableList = [
        	'user_invitations',
        	'projects_users_time_tracking',
            'article_has_attachments',
            'clients',
            'companies',
            'company_has_admins',
            'core',
            'custom_quotation_requests',
            'custom_quotations',
            'events',
            'expenses',
            'invoice_has_items',
            'invoice_has_payments',
            'invoice_has_users',
            'invoices',
            'items',
            'mentions',
            'messages',
            'migrations',
            'modules',
	        'phinxlog',
            'privatemessages',
            'project_has_activities',
            'project_has_files',
            'project_has_invoices',
            'project_has_milestones',
	        'project_has_tasks',
	        'project_has_task_lists',
	        'project_has_list_tasks',
            'project_has_timesheets',
            'project_has_workers',
            'project_chats',
            'projects',
            'pw_reset',
            'queues',
            'quotations',
            'subscription_has_items',
            'subscriptions',
            'slack_linked_channels',
            'slack_links',
            'task_has_comments',
            'task_has_subtasks',
            'task_has_workers',
            'templates',
            'ticket_has_articles',
            'ticket_has_attachments',
            'tickets',
            'types',
            'users'
        ];

        foreach ($tableList as $table) {
            $sql = "CREATE TABLE `" . $_SESSION['accountDatabasePrefix']  . "_" . ENVIRONMENT . '`.`' . $table . "` LIKE `seed_" . ENVIRONMENT . "`.`" . $table . "`;";
            /** @var  CI_DB_mysql_result $result */
            $result = $this->_primaryDatabase->query($sql, []);
            $sql = "INSERT INTO `" . $_SESSION['accountDatabasePrefix']  . "_" . ENVIRONMENT . '`.`' . $table . "` SELECT * FROM `seed_" . ENVIRONMENT . "`.`" . $table . "`;";
            /** @var  CI_DB_mysql_result $result */
            $result = $this->_primaryDatabase->query($sql, []);
        }

        //foreach (explode(';', $this->_accountSql) as $sql) {
        //    if (trim($sql) != '') {
        //        $this->_accountDatabase->query($sql, []);
        //    }
        //}
        return true;
    }

	/**
	 * @return bool|stdClass
	 */
    public function getSubscription() {
    	return $this->_subscription;
    }

	/**
	 * @return billing
	 */
    public function getBilling() {
    	return $this->_billing;
    }

	/**
	 * @return mixed
	 */
    public function getPlanType() {
    	return $this->_plan_type;
    }

	/**
	 * @return int
	 */
    public function getAmountDue() {
    	return $this->_amount_due;
    }

	/**
	 * @param string $accountUrlPrefix
	 * @param int $TransactionId
	 *
	 * @return bool
	 */
    public function getAccountPaymentByTransaction($accountUrlPrefix, $TransactionId) {
    	$sql = "SELECT * FROM accounts_payments WHERE payorAccountUrlPrefix='" . $accountUrlPrefix . "'
    	    AND TransactionId=" . $TransactionId . ";";
	    /** @var  CI_DB_mysql_result $result */
	    $result = $this->_primaryDatabase->query($sql, []);
	    if ($result->num_rows() > 0) {
		    $accountPayment =  $result->result()[0];
	    } else {
		    $accountPayment =  false;
	    }
	    return $accountPayment;
    }

	/**
	 * @param string $accountUrlPrefix
	 *
	 * @return array|mixed
	 */
    public function getAccountPaymentHistory($accountUrlPrefix) {
	    $sql = "SELECT * FROM accounts_payments WHERE payorAccountUrlPrefix='" . $accountUrlPrefix . "';";
	    /** @var  CI_DB_mysql_result $result */
	    $result = $this->_primaryDatabase->query($sql, []);
	    if ($result->num_rows() > 0) {
		    $accountPayments =  $result->result();
	    } else {
		    $accountPayments =  [];
	    }
	    return $accountPayments;
    }


	/**
	 * @param $accountUrlPrefix
	 * @param billing $billing
	 *
	 * @return $this
	 */
    public function isSubscriptionDue($accountUrlPrefix, $billing) {
	    $this->_due = false;
    	$this->_billing = $billing;
    	$sql = "SELECT * FROM accounts WHERE accountUrlPrefix='" . $accountUrlPrefix . "';";
	    /** @var  CI_DB_mysql_result $result */
	    $result = $this->_primaryDatabase->query($sql, []);
	    if ($result->num_rows() > 0) {
		    $this->_account =  $result->result()[0];
	    } else {
		    $this->_account =  false;
	    }
	    $this->_subscription = $billing->loadSubscription($accountUrlPrefix)->getSubscription();

	    if ($this->_subscription) {
		    $this->_plan_type = $billing->loadPlanType($this->_subscription)->getPlanType();
		    if($billing->doesPlanNeedUpgrade()) {
		    	$_SESSION['billing']['OldPlanType'] = $this->_plan_type->type;
                $billing->upgradePlan();
			    //reload subscription and selected plan
                $this->_subscription = $billing->loadSubscription($accountUrlPrefix)->getSubscription();
			    $this->_plan_type = $billing->loadPlanType($this->_subscription)->getPlanType();
			    $_SESSION['billing']['PlanType'] = $this->_plan_type->type;
		    }
		    $this->_is_linked_plan_type = $billing->isLinkedPlanType();
		    if($this->_is_linked_plan_type) {
			    $this->_parent_plan_type = $billing->getParentPlanType();
			    $this->_child_plan_type = $billing->getChildPlanType();
			    $greaterPlan = $billing->whichPlanIsGreater();
			    if ($greaterPlan == 'parent') {
				    $this->_plan_list = [
					    $this->_parent_plan_type,
					    $this->_child_plan_type
				    ];
			    } else {
				    $this->_plan_list = [
					    $this->_child_plan_type,
					    $this->_parent_plan_type
				    ];
			    }
			    $this->_user_count = $billing->getUserCount();
			    foreach ($this->_plan_list as $index => $plan) {
                    $this->_plan_list[$index]->amount =  $this->_plan_list[$index]->amount * $this->_user_count;
			    }
			    $billing->setPlanList($this->_plan_list);
			} else {
		    	$this->_plan_list = [$this->_plan_type];
		    }

			$this->loadLastPaidDate();
		    $this->_last_paid_days = $this->getLastPaidDays($this->getLastPaidDate());
		    $this->_account_age_days = $this->getLastPaidDays($this->_account->created);
		    $this->_first_time_billed = false;
		    if($this->_promo_code && $this->_promo_code->discount_duration >= $this->_account_age_days) {
			    $discountMultiplier = 1 - $this->_promo_code->discount_percentage;
		    } else {
			    $discountMultiplier = 1;
		    }
		    if ($this->_subscription->lastBilled == null) {
			    $this->_first_time_billed = true;
		    	if ( $this->_last_paid_days > 29 ) {
				    $this->_due               = true;
				    $this->_amount_due = (int) (($this->_plan_type->amount / 100) * $discountMultiplier * 100);
			    }
		    } else {
			    $this->_first_time_billed = false;
			    if ( $this->_last_paid_days > $this->_plan_type->billingFrequencyDays) {
				    $this->_due               = true;
				    $this->_amount_due = (int) (($this->_plan_type->amount / 100) * $discountMultiplier * 100);
			    }
		    }
		    $this->_billing->setDue( $this->_due );
		    $this->_billing->setAmountDue( $this->_amount_due );
	    } else {
		    $this->_billing->setDue(false);
	        $this->_due = $this->_billing->getDue();
	    }
	    return $this;
    }

	/**
	 * @return bool|stdClass
	 */
    public function getLastPaidDate() {
    	return $this->_last_paid_date;
    }

	/**
	 * @param string $lastPaidDate - mysql datetime
	 *
	 * @return int
	 */
    public function getLastPaidDays($lastPaidDate) {
	    $current = new DateTime();
	    $db_date = new DateTime($lastPaidDate);
	    $days = $current->diff($db_date)->days;
	    return $days;
    }

	/**
	 * @return $this
	 */
    public function loadLastPaidDate() {
	    if($this->_subscription != false) {
		    $this->_last_paid_date = $this->_subscription->lastBilled;
    	    if ( $this->_last_paid_date == null ) {
		        $this->_last_paid_date = $this->_account->created;
        	}
	    } else {
		    $this->_last_paid_date = date( "Y-m-d H:i:s" );
	    }
	    return $this;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername($username) {
        $this->_username = $username;
        return $this;
    }

    /**
     * @param string $firstname
     * @return $this
     */
    public function setFirstname($firstname) {
        $this->_firstname = $firstname;
        return $this;
    }

    /**
     * @param string $lastname
     * @return $this
     */
    public function setLastname($lastname) {
        $this->_lastname = $lastname;
        return $this;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword($password) {
        $this->_password = $password;
        return $this;
    }

    /** used for first user and accounts */

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email) {
        $this->_email = $email;
        return $this;
    }

	/**
	 * @param int $active
	 *
	 * 0 = inactive, 1 = active Spera Pro acccount, 2 = active spera app account
	 * @return $this
	 */
    public function setActive($active) {
    	$this->_active = $active;
    	return $this;
    }

    /** account fields */

    /**
     * @param string $accountUrlPrefix
     * @return $this
     */
    public function setAccountUrlPrefix($accountUrlPrefix) {
        $this->_accountUrlPrefix = strtolower($accountUrlPrefix);
        return $this;
    }

    /**
     * @param string $accountName
     * @return $this
     */
    public function setAccountName($accountName) {
        $this->_accountName = $accountName;
        return $this;
    }

    /**
     * @param string $accountContactFirstName
     * @return $this
     */
    public function setAccountContactFirstName($accountContactFirstName) {
        $this->_accountContactFirstName = $accountContactFirstName;
        return $this;
    }

    /**
     * @param string $accountContactLastName
     * @return $this
     */
    public function setAccountContactLastName($accountContactLastName) {
        $this->_accountContactLastName = $accountContactLastName;
        return $this;
    }

    /**
     * @param string $dbHostname
     * @return $this
     */
    public function setDbHostname($dbHostname) {
        $this->_db_hostname = $dbHostname;
        return $this;
    }

    /**
     * @param string $dbUsername
     * @return $this
     */
    public function setDbUsername($dbUsername) {
        $this->_db_username = $dbUsername;
        return $this;
    }

    /**
     * @param string $dbPassword
     * @return $this
     */
    public function setDbPassword($dbPassword) {
        $this->_db_password = $dbPassword;
        return $this;
    }

    /**
     * @param string $dbDatabase
     * @return $this
     */
    public function setDbDatabase($dbDatabase) {
        $this->_db_database = $dbDatabase;
        return $this;
    }

    /**
     * @param string $dbVersion
     * @return $this
     */
    public function setDbVersion($dbVersion) {
        $this->_db_version = $dbVersion;
        return $this;
    }

    /**
     * Generate a strong random password with uppercase, lowercase and numbers
     * @param int $length
     * @return string
     */
    public function generateRandomPassword($length = 20) {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = [];
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }

    /**
     * returns all account data
     * @return bool|object
     */
    public function getAccountData() {
        $sql = "SELECT * FROM accounts WHERE accountUrlPrefix='" . $this->_accountUrlPrefix . "';";
        /** @var  CI_DB_mysql_result $result */
        $result = $this->_primaryDatabase->query($sql, []);
        if ($result->num_rows() == 1) {
            return (object) $result->_fetch_assoc();
        } else {
            return false;
        }
    }

    /**
     * @param string $urlPrefix
     * @return bool|object
     */
    public function checkForActiveAccount($urlPrefix) {
        $sql = "SELECT * FROM accounts WHERE accountUrlPrefix='" . $urlPrefix . "' AND active=1;";
        /** @var  CI_DB_mysql_result $result */
        $result = $this->_primaryDatabase->query($sql, []);
        if ($result->num_rows() == 1) {
            return (object) $result->_fetch_assoc();
        } else {
            return false;
        }
    }

    /**
     * Return a list of accounts by email
     * @param string $emailAddress
     * @return bool|mixed
     */
    public function getAccountListByEmail($emailAddress) {
        $emailAddress = strtolower($emailAddress);
        $sql = "SELECT accountUrlPrefix,accountName,accountContactFirstName,accountContactLastName,email FROM accounts WHERE email='" . $emailAddress . "' AND active=1;";
        /** @var  CI_DB_mysql_result $result */
        $result = $this->_primaryDatabase->query($sql, []);
        if ($result->num_rows() >= 1) {
            return $result->result();
        } else {
            return false;
        }
    }

	/**
	 * Returns whether or not an invoice is set for crypto payment
	 * @param int $invoiceId
	 * @param string $accountUrlPrefix
	 * @param string $username
	 * @var int $aup_transaction_id - id of accounts_users_propay_transactions row
	 * @var string $accountUrlPrefix -  accounts url prefix
	 * @var string $username - $username to be paid (optional, used if paying a subcontractor)
	 * @var int $invoice_id - invoice to be paid
	 * @var string[3] $destination_currency - currency to be purchased with cleared transaction net
	 * @var string $currency_amount - currency which was purchased as a large precision decimal
	 * @var int $status - 0 = unpaid, 1 = paid, 2 = fulfilled (crypto purchased)
	 * @var string $created - datetime of invoice being called to be paid in crypto
	 * @var string $updated - datetime of last currency purchase pipeline status
	 * @return mixed
	 */
    public function isInvoiceCryptoPayment($invoiceId, $accountUrlPrefix, $username = false) {
	    $response = false;
	    $sql = "SELECT * FROM accounts_crypto_payments WHERE accountUrlPrefix='" . $accountUrlPrefix . "'";
	    $sql .= " AND invoice_id=" . $invoiceId;
	    if($username) $sql .= " AND username='" . $username . "'";
	    $sql .= ";";
	    /** @var  CI_DB_mysql_result $result */
	    $result = $this->_primaryDatabase->query($sql, []);
	    if ($result->num_rows() >= 1) {
		    $response =  $result->result();
	    }
    	return $response;
    }

	/**
	 * Insert or update crypto payment type for an account's invoice whether contractor or subcontractor
	 * @param string $payment_currency
	 * @param int $invoiceId
	 * @param string $accountUrlPrefix
	 * @param string|bool $username
	 *
	 * @return bool|mixed
	 */
    public function updatePaymentCurrency($payment_currency, $invoiceId, $accountUrlPrefix, $username = false) {
	    $existing = $this->isInvoiceCryptoPayment($invoiceId, $accountUrlPrefix, $username);
	    if ($existing) {
	    	$sql = "UPDATE accounts_crypto_payments SET destination_currency='" . $payment_currency . "' WHERE invoice_id=" . $invoiceId
			   . " AND accountUrlPrefix='" . $accountUrlPrefix . "';";
	    } else {
	    	if ($username) {
	    		// handle case to pay subcontractors in crypto
			    $sql = "INSERT INTO accounts_crypto_payments (invoice_id,accountUrlPrefix,destination_currency,username) 
                        VALUES (" . $invoiceId . ",'" . $accountUrlPrefix . "','" . $payment_currency . "','" . $username . "');";
		    } else {
			    $sql = "INSERT INTO accounts_crypto_payments (invoice_id,accountUrlPrefix,destination_currency) 
                        VALUES (" . $invoiceId . ",'" . $accountUrlPrefix . "','" . $payment_currency . "');";
		    }
	    }
	    /** @var  CI_DB_mysql_result $result */
	    $result = $this->_primaryDatabase->query($sql, []);
	    if (is_object($result) && $result->num_rows() >= 1) {
		    $response =  $result->result();
	    } else {
		    $response = $result;
	    }
	    return $response;
    }

	/**
	 * get the account_users_propay_transaction record based on the HostedTransactionIdentifier that was stored from the transaction
	 * @param string $HostedTransactionIdentifier
	 *
	 * @return CI_DB_mysql_result|mixed
	 */
    public function getAccountUsersPropayTransaction( $HostedTransactionIdentifier ) {
    	$sql = "SELECT * from accounts_users_propay_transactions WHERE HostedTransactionIdentifier='" . $HostedTransactionIdentifier . "';";
	    /** @var  CI_DB_mysql_result $result */
	    $result = $this->_primaryDatabase->query($sql, []);
	    if (is_object($result) && $result->num_rows() >= 1) {
		    $response =  $result->result();
	    } else {
		    $response = $result;
	    }
	    return $response;
    }

	/**
	 * @param string $accountUrlPrefix
	 * @param string $username
	 * @param string $type
	 * @param string $request - json string up to 65535 characters
	 * @param string $response - json string up to 65535 characters
	 */
	public function logApiCall($accountUrlPrefix, $username, $type, $request, $response) {
		$sql = "INSERT INTO accounts_users_api_log (
                    `accountUrlPrefix`,
                    `username`,
                    `type`,
                    `request`,
                    `response`
                ) VALUES (
                    '" . $accountUrlPrefix . "',
                    '" . $username . "',
                    '" . $type . "',
                    '" . $request . "',
                    '" . $response . "'
                    );";
		$result = $this->_primaryDatabase->query($sql, []);
	}

	/**
	 * Updates account_crypto_payments to paid awaiting crypto purchase
	 * @param int $accountsCryptoPaymentId
	 * @param mixed $data
	 * @param int $status
	 *
	 * @return CI_DB_mysql_result|mixed
	 */
    public function updateCryptoPaymentStatus($accountsCryptoPaymentId, $data, $status) {
	    $response = false;
	    switch ( (int) $status ) {
		    case 1:
			    $source_type = ( $data->NetAmt == 0 ) ? 'ach' : 'card';
			    $sql         = "UPDATE accounts_crypto_payments SET aup_transaction_id='" . $data->id . "',
			            status=1,source_type='" . $source_type . "' 
			            WHERE id=" . $accountsCryptoPaymentId . ";";
			    /** @var  CI_DB_mysql_result $result */
			    $result = $this->_primaryDatabase->query( $sql, [] );
			    if ( is_object( $result ) && $result->num_rows() >= 1 ) {
				    $response = $result->result();
			    } else {
				    $response = $result;
			    }
			    break;
		    case 2:
		    	if (isset($data->type)) {
				    //"assetAmount":0.00021909
				    $sql = "UPDATE accounts_crypto_payments SET currency_amount='" . $data->assetAmount . "',
			            status=2 
			            WHERE id=" . $accountsCryptoPaymentId . ";";
				    /** @var  CI_DB_mysql_result $result */
				    $result = $this->_primaryDatabase->query( $sql, [] );
				    if ( is_object( $result ) && $result->num_rows() >= 1 ) {
					    $response = $result->result();
				    } else {
					    $response = $result;
				    }
			    } else {
		    		//todo:  need to log or notify that something went wrong with the purchase
			    }
			    break;
		    default;
	    }
	    return $response;
    }

    /**
     * signup a new account with it's own database, database user, and first user
     * @param string $accountUrlPrefix
     * @param string $databaseName
     * @param array $signupData
     * @return bool
     */
    public function signup($accountUrlPrefix, $databaseName, $signupData, $isSperaApp=false) {
        $active = ($isSperaApp) ? 2 : 1;
    	$this->setUsername($signupData['Username'])
            ->setFirstname($signupData['Firstname'])
            ->setLastname($signupData['Lastname'])
            ->setAccountName($signupData['AccountName'])
            ->setPassword($signupData['Password'])
            ->setEmail($signupData['Email'])
            ->updateFirstUser()
            ->updateCore();

        $dbUsername =  (ENVIRONMENT == 'production') ? explode('_',$databaseName)[0] : $databaseName;
        $dbHostname = (ENVIRONMENT == 'production') ? PRODUCTION_RDS_HOST : 'localhost';

        $this->setAccountUrlPrefix($accountUrlPrefix)
            ->setAccountName($signupData['AccountName'])
            ->setAccountContactFirstName($signupData['AccountContactFirstName'])
            ->setAccountContactLastName($signupData['AccountContactLastName'])
            ->setEmail($signupData['Email'])
            ->setDbHostname($dbHostname)
            ->setDbUsername($dbUsername)
            ->setDbPassword($this->encryptString($this->generateRandomPassword(20))->getEncryptedString())
            ->setDbDatabase($databaseName)
            ->setDbVersion('1.3.1.2')
	        ->setActive($active)
	        ->createAccountEntry();

        if(isset($signupData['promoCode'])) {
            $this->setPromoCode($signupData['promoCode']);
            $promoCodeRecord = $this->checkValidPromoCode($signupData['promoCode']);
            $this->createAccountDiscount($promoCodeRecord[0]->id);
        }

        return $this->validateUrlPrefix($accountUrlPrefix, $databaseName);
    }

    /**
     * get the url prefix of the server host
     * @return bool|string
     */
    public function getAccountUrlPrefix() {
        $urlPrefix = false;
        $serverHost = $_SERVER['HTTP_HOST'];
        $hostArray = explode('.', $serverHost);
        if (count($hostArray) > 2) {
            $urlPrefix = strtolower($hostArray[0]);
        }
        return $urlPrefix;
    }

    /**
     * returns encrypted database password and accountUrlPrefix
     * @return bool|object
     */
    public function searchForAccountUrlPrefix() {
        $sql = "SELECT accountUrlPrefix,db_password FROM accounts WHERE accountUrlPrefix='" . $this->_accountUrlPrefix . "';";
        /** @var  CI_DB_mysql_result $result */
        $result = $this->_primaryDatabase->query($sql, []);
        if ($result->num_rows() == 1) {
            return (object) $result->_fetch_assoc();
        } else {
            return false;
        }
    }

    /**
     * Check to see if an account exists
     * @param string $accountUrlPrefix
     * @return bool
     */
    public function accountExists($accountUrlPrefix){
        $accountExists = false;
        $sql = "SELECT accountUrlPrefix,db_password FROM accounts WHERE accountUrlPrefix='" . $accountUrlPrefix . "';";
        /** @var  CI_DB_mysql_result $result */
        $result = $this->_primaryDatabase->query($sql, []);
        if ($result->num_rows() == 1) {
            $databaseRow = (object) $result->_fetch_assoc();
            if ($databaseRow->accountUrlPrefix == $accountUrlPrefix) $accountExists = true;
        }
        return $accountExists;
    }

    public function storeSignupInfo($post) {
        $email = trim(htmlspecialchars($post['email']));
        $firstname = trim(htmlspecialchars($post['firstname']));
        $lastname = trim(htmlspecialchars($post['lastname']));
        $promoCode = (isset($post['promoCode'])) ? strtoupper(trim( htmlspecialchars( $post['promoCode'] ) )) : '';
        $planType = (isset($post['planType'])) ? strtoupper(trim( htmlspecialchars( $post['planType'] ) )) : '';
        $source_ip = $_SERVER['REMOTE_ADDR'];

        $sql = "INSERT INTO accounts_signup_emails (
                    email,
                    firstname,
                    lastname,
                    promoCode,
                    planType,
                    source_ip
                ) VALUES (
                    '" . $email . "',
                    '" . $firstname . "',
                    '" . $lastname . "',
                    '" . $promoCode . "',
                    '" . $planType . "',
                    '" . $source_ip . "'
                    );";
        $result = $this->_primaryDatabase->query($sql, []);
    }

    /**
     * make sure an account database exists
     * @param string $databaseName
     * @return bool
     */
    public function databaseExists($databaseName) {
        $databaseExists = false;
        $sql = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . $databaseName . "';";
        /** @var  CI_DB_mysql_result $result */
        $result = $this->_primaryDatabase->query($sql, []);
        if ($result->num_rows() == 1) {
            $databaseRow = (object) $result->_fetch_assoc();
            if ($databaseRow->SCHEMA_NAME == $databaseName) {
                $databaseExists = true;
            }
        }
        return $databaseExists;
    }

    /**
     * Check to see if a mysql user exists.
     * @param string $username
     * @return mixed
     */
    public function mysqlUserExists($username) {
        $usernameExists = false;
        $sql = "SELECT User from mysql.user WHERE User = '" . $username . "';";
        /** @var  CI_DB_mysql_result $result */
        $result = $this->_primaryDatabase->query($sql, []);
        if ($result->num_rows() == 1) {
            $databaseRow = (object) $result->_fetch_assoc();
            if ($databaseRow->User == $username) {
                $usernameExists = true;
            }
        }
        return $usernameExists;
    }

    /**
     * checks to see if we have an account record for this urlPrefix already
     * @param string $urlPrefix
     * @return bool
     */
    public function validateUrlPrefix($urlPrefix, $databaseName) {
        $isAccount = false;
        switch ($urlPrefix) {
            case null:
            case 'www':
            case 'app':
            case 'platform':
            case 'dev':
            case 'release':
                break;
            default:
                $account = $this->checkForActiveAccount($urlPrefix);
                if ($account) {
                    if($this->databaseExists($databaseName )) {
                        $isAccount = true;
                    }
                }
        }
        return $isAccount;
    }


    public function createAccountEntry() {

        $accountData = $this->searchForAccountUrlPrefix();

        if (!$accountData) {
            $sql = "INSERT INTO accounts (
                    accountUrlPrefix,
                    accountName,
                    accountContactFirstName,
                    accountContactLastName,
                    email,
                    db_hostname,
                    db_username,
                    db_password,
                    db_database,
                    db_version,
                    active
                ) VALUES (
                    '" . $this->_accountUrlPrefix . "',
                    '" . $this->_accountName . "',
                    '" . $this->_accountContactFirstName . "',
                    '" . $this->_accountContactLastName . "',
                    '" . $this->_email . "',
                    '" . $this->_db_hostname . "',
                    '" . $this->_db_username . "',
                    '" . $this->_db_password . "',
                    '" . $this->_db_database . "',
                    '" . $this->_db_version . "',
                    " . $this->_active . "
                    );";
            $result = $this->_primaryDatabase->query($sql, []);
        } else {
            //TODO: need to report back that account url prefex already exists.
        }

        $accountData = $this->getAccountData();

        $decryptedPassword = $this->decryptString($accountData->db_password)->getDecryptedString();

        $userHost = (ENVIRONMENT == 'production') ? '%' : 'localhost';
        $sql = "GRANT ALL PRIVILEGES ON " . $this->_db_database . ".* To '" . $accountData->db_username . "'@'" . $userHost . "' IDENTIFIED BY '" . $decryptedPassword . "';";
        /** @var  CI_DB_mysql_result $result */
        $result = $this->_accountDatabase->query($sql, []);

        $sql = "FLUSH PRIVILEGES;";
        $result = $this->_accountDatabase->query($sql, []);

        //TODO: return error if accountUrlPrefix already exists
    }

    /**
     *
     * @param string $planType - foreign keyed to type in plan_types (need an entry there first)
     * @param null|string $firstBilled - mysql timestamp
     * @param null|string $lastBilled - mysql timestamp
     * @return bool
     */
    public function storeAccountPlan($planType, $firstBilled = null, $lastBilled = null) {
        $sql = "SELECT * FROM account_plans WHERE accountUrlPrefix='" . $this->_accountUrlPrefix  . "';";
        $firstBilled = ($firstBilled == NULL) ? "NULL" : "'" . $firstBilled . "'";
        $lastBilled = ($lastBilled == NULL) ? "NULL" : "'" . $lastBilled . "'";

        /** @var  CI_DB_mysql_result $result */
        $result = $this->_primaryDatabase->query($sql, []);
        if ($result->num_rows() == 1) {
            //update
            $sql = "UPDATE account_plans SET
                        type='" . $planType . "',
                        firstBilled=" . $firstBilled . ",
                        lastBilled=" . $lastBilled . " 
                        WHERE accountUrlPrefix='" . $this->_accountUrlPrefix . "';";
            $result = $this->_primaryDatabase->query($sql, []);
        } else {
            $sql = "INSERT INTO account_plans (
                        accountUrlPrefix,
                        type,
                        firstBilled,
                        lastBilled
                    ) VALUES (
                        '" . $this->_accountUrlPrefix . "',
                        '" . $planType . "',
                        " . $firstBilled . ",
                        " . $lastBilled . "
                    );";
            $result = $this->_primaryDatabase->query($sql, []);
        }
        return true;
    }

    /**
     * @param int $promoCodeId
     * @return bool
     */
    public function createAccountDiscount($promoCodeId) {
        $sql = "INSERT INTO accounts_discounts (
                        accountUrlPrefix,
                        accounts_promo_code_id
                    ) VALUES (
                        '" . $this->_accountUrlPrefix . "',
                        " . $promoCodeId . "
                    );";
        $result = $this->_primaryDatabase->query($sql, []);
        return true;
    }

    /**
     * @param string $planType - one word no spaces (this is the plan type namespace) - up to 45 characters
     * @param string $name - a description of the plan type with spaces etc (for display) up to 45 characters
     * @param int $amount - $amount per billing period in cents - retrieve and divide by 100 to get the dollars float
     * @param int $billingFrequencyDays - number of days in between billing
     * @return bool
     */
    public function storePlanType($planType, $name, $amount, $billingFrequencyDays) {
        $sql = "SELECT * FROM plan_types WHERE type='" . $planType  . "';";
        /** @var  CI_DB_mysql_result $result */
        $result = $this->_primaryDatabase->query($sql, []);
        if ($result->num_rows() == 1) {
            $sql = "UPDATE plan_types SET 
                    name='" . $name . "',
                    amount=" . $amount . ",
                    billingFrequencyDays=" . $billingFrequencyDays . "
                    WHERE type='" . $planType  . "';";
            $result = $this->_primaryDatabase->query($sql, []);
        } else {
            $sql = "INSERT INTO plan_types (
                        type,
                        name,
                        amount,
                        billingFrequencyDays
                    ) VALUES (
                        '" . $planType . "',
                        '" . $name . "',
                        " . $amount . ",
                        " . $billingFrequencyDays . "
                    );";
            $result = $this->_primaryDatabase->query($sql, []);
        }
        return true;
    }

    /**
     * Returns a list of plan types
     * @return mixed
     */
    public function getPlanTypes() {
        $sql = "SELECT * FROM plan_types ORDER BY id;";
        /** @var  CI_DB_mysql_result $result */
        $result = $this->_primaryDatabase->query($sql, []);
        if ($result->num_rows() > 0) {
            return $result->result();
        } else {
            return [];
        }
    }

    /**
     * Returns a list of currencies
     * @return mixed
     */
    public function getCurrencies() {
        $sql = "SELECT * FROM currencies ORDER BY code;";
        /** @var  CI_DB_mysql_result $result */
        $result = $this->_primaryDatabase->query($sql, []);
        if ($result->num_rows() > 0) {
            return $result->result();
        } else {
            return [];
        }
    }

    public function checkValidPromoCode($promoCode) {
        $sql = "SELECT * FROM accounts_promo_codes WHERE promo_code='" . strtoupper($promoCode) . "';";
        /** @var  CI_DB_mysql_result $result */
        $result = $this->_primaryDatabase->query($sql, []);
        if ($result->num_rows() > 0) {
            return $result->result();
        } else {
            return [];
        }
    }

	/**
	 * @param string $manualLastPaymentDate - allows optional lastPaymentDate
	 * which allows for setting an appropriate backdate for those people paying late
	 * this way we don't loose prorated periods of time in our payments
	 * This must be formatted in YYYY-MM-DD HH:MM:SS in UTC time  HH:MM:SS could be 00:00:00 if
	 * no specific time is desired (or it could be a back date but with the current time) so that
	 * we keep the payment time on the record
	 *
	 * i.e.
	 * 	    $datetime = new DateTime(time());
	 *      $datetime->setTimezone(new DateTimeZone('UTC'));
	 *      $formatted_date_long=date_format($datetime, 'Y-m-d H:i:s');
	 *
	 * @return $this
	 */
    public function updateLastPaymentDate($manualLastPaymentDate="now()") {
    	if ($manualLastPaymentDate != "now()") $manualLastPaymentDate = "'" . $manualLastPaymentDate . "'";
	    $sql = "SELECT * FROM account_plans WHERE accountUrlPrefix='" . $this->_accountUrlPrefix  . "';";
	    /** @var  CI_DB_mysql_result $result */
	    $result = $this->_primaryDatabase->query($sql, []);
	    if ($result->num_rows() == 1) {
		    //update
		    $account_plan = $result->result()[0];
		    if ($account_plan->firstBilled == null) {
		    	$prependUpdateFields = "firstBilled=now(),";
		    } else {
			    $prependUpdateFields = "";
		    }
		    $sql = "UPDATE account_plans SET
                        " . $prependUpdateFields . "
                        lastBilled=" . $manualLastPaymentDate . " 
                        WHERE accountUrlPrefix='" . $this->_accountUrlPrefix . "';";
		    $result = $this->_primaryDatabase->query($sql, []);
	    }
	    return $this;
    }

    /**
     * @param \stdClass $transactionObject
     * @param string $username
     * @return $this
     */
    public function storeAccountUserPaymentMethod($transactionObject, $username) {
        if ($transactionObject->HostedTransaction->PaymentMethodInfo) {
            $sql = "SELECT * from accounts_users_payment_methods WHERE PaymentMethodID='" .
                $transactionObject->HostedTransaction->PaymentMethodInfo->PaymentMethodID
                . "';";
            /** @var  CI_DB_mysql_result $result */
            $result = $this->_primaryDatabase->query($sql, []);
            if ($result->num_rows() == 1) {
                //update
                $sql = "UPDATE accounts_users_payment_methods SET
                        PaymentMethodType='" . $transactionObject->HostedTransaction->PaymentMethodInfo->PaymentMethodType . "'
                        ExpirationDate='" . $transactionObject->HostedTransaction->PaymentMethodInfo->ExpirationDate . "'
            ;";
                $result = $this->_primaryDatabase->query($sql, []);
            } else {
                $sql = "INSERT INTO accounts_users_payment_methods (
                        accountUrlPrefix,
                        username,
                        PaymentMethodID,
                        PaymentMethodType,
                        ExpirationDate  
                    ) VALUES (
                        '" . $this->_accountUrlPrefix . "',
                        '" . $username . "',
                        '" . $transactionObject->HostedTransaction->PaymentMethodInfo->PaymentMethodID . "',
                        '" . $transactionObject->HostedTransaction->PaymentMethodInfo->PaymentMethodType . "',
                        '" . $transactionObject->HostedTransaction->PaymentMethodInfo->ExpirationDate . "'
                    );";
                $result = $this->_primaryDatabase->query($sql, []);
            }
        }
        return $this;
    }

    /**
     * updates the default user in the users table with the account holders user information and stores a hash of their password
     * like unto what the system uses so they can log in normally using their password.
     * @return $this
     */
    public function updateFirstUser() {
        $this->_email = strtolower($this->_email);
        $this->hashPassword($this->_password);

        $sql = "UPDATE users SET username='" . $this->_username . "', 
                        firstname='" . $this->_firstname . "', 
                        lastname='" . $this->_lastname . "', 
                        hashed_password='" . $this->_hashed_password . "', 
                        email='" . $this->_email . "' 
                WHERE username='Admin';";
        $this->_accountDatabase->query($sql, []);
        return $this;
    }

    public function updateCore() {
        $domainParts = explode('.', $_SERVER['HTTP_HOST']);
        $domainSuffix = $domainParts[count($domainParts)-2] . '.' . $domainParts[count($domainParts)-1];
        $domain = 'https://' . $this->_username .  '.' . $domainSuffix;

        //TODO: we need a tool to update invoice_address, city, tel if one does not exist
        $sql = "UPDATE core SET domain='" . $domain . "', 
                        email='" . $this->_email . "', 
                        invoice_contact='" . $this->_firstname . ' ' . $this->_lastname . "', 
                        company='" . $this->_accountName . "', 
                        invoice_address='', 
                        invoice_city='', 
                        invoice_tel='' 
                WHERE company='Spera';";
        $this->_accountDatabase->query($sql, []);
        return $this;
    }


    /**
     * @return string
     */
    function getHashedPassword() {
        return $this->_hashed_password;
    }

    /**
     * @return string
     */
    function getEncryptedString() {
        return $this->_encryptedString;
    }

    /**
     * @return string
     */
    function getDecryptedString() {
        return $this->_decryptedString;
    }

    /**
     * @param string $plaintextPassword
     * @return $this
     */
    function hashPassword($plaintextPassword)
    {
        $this->_hashed_password = $this->createPasswordHash($plaintextPassword);
        return $this;
    }

    /**
     * create a hashed password from a password string
     * @param string $password
     * @return string
     */
    private function createPasswordHash($password)
    {
        $salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
        $hash = hash('sha256', $salt . $password);

        return $salt . $hash;
    }


    /**
     * Validates that the provide password matches the password that was hashed originally
     * @param string $password
     * @return bool
     */
    function validatePassword($password)
    {
        $salt = substr($this->_hashed_password, 0, 64);
        $hash = substr($this->_hashed_password, 64, 64);

        $password_hash = hash('sha256', $salt . $password);

        return $password_hash == $hash;
    }

    /**
     * Encrypts the string using the server defined SALT string
     * @param string $stringToEncrypt
     * @return $this
     */
    public function encryptString($stringToEncrypt) {
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($stringToEncrypt, $cipher, SALT, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, SALT, $as_binary=true);
        $this->_encryptedString = base64_encode( $iv.$hmac.$ciphertext_raw );
        return $this;
    }

    /**
     * Decrypts the string using the server defined SALT string
     * @param string $stringToDecrypt
     * @return $this
     */
    public function decryptString($stringToDecrypt) {
        $c = base64_decode($stringToDecrypt);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, SALT, $options=OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, SALT, $as_binary=true);
        if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
        {
            $this->_decryptedString = $original_plaintext;
        }
        return $this;
    }

    /**
     * @param array $dbResult
     * @return string
     */
    public function formatCsv(array $dbResult) {
        $csv='';
        if (is_array($dbResult)) {
            foreach ($dbResult as $resultItem) {
                $csv .= implode(',', (array) $resultItem) . "\r\n";
            }
        }
        return $csv;
    }

    /**
     * @param array $dbResult
     * @return string
     */
    public function formatTsv(array $dbResult) {
        $tsv='';
        if (is_array($dbResult)) {
            foreach ($dbResult as $resultItem) {
                $tsv .= implode("\t", (array) $resultItem) . "\r\n";
            }
        }
        return $tsv;
    }

    /**
     * @return mixed
     */
    public function getRawAccountList() {
        $fieldNames = [];
        $sql = "SELECT accountUrlPrefix,accountName,accountContactFirstname,accountContactLastname,email,active,created 
                    FROM accounts WHERE active=1; 
        ";
        /** @var  CI_DB_mysql_result $result */
        $result = $this->_primaryDatabase->query($sql, []);
        $resultArray = $result->result();
        if ($result->num_rows() > 0) {
            foreach ((array) $resultArray[0] as $fieldName => $fieldValue) $fieldNames[] = $fieldName;
            $resultArray = array_merge([(object) $fieldNames], $resultArray);
        }
        return $resultArray;
    }

	public function getReportingExclusionList() {
		return [
			'123qwe',
			'1687founda',
			'4skylerwilliams',
			'86therobot',
			'agency',
			'anziano',
			'avengers',
			'cpesci',
			'damon',
			'demo',
			'dhall',
			'dsadfsd',
			'freetrial',
			'freetrial1111',
			'freetrial1111222',
			'garry',
			'random',
			'random1',
			'1damontest',
			'damontest',
			'damontest1',
			'dptest',
			'malan',
			'86tr',
			'brayden2544',
			'brayden2554test',
			'hapostolos',
			'hapostolos95',
			'hci',
			'hlopresti18',
			'hlopresti189',
			'independent',
			'jonas',
			'josh3test',
			'khishor',
			'libero',
			'lpesci',
			'marcopesci',
			'mzamin',
			'newgraph',
			'pescisports',
			'pescisports',
			'rlusk',
			'spera',
			'sperauu',
			'team',
			'teamspera',
			'test1',
			'test180124',
			'test7',
			'tester327',
			'testing123',
			'usertesting17',
			'ux',
			'braydentest',
			'ddsfsdfsdf',
			'dfsdf',
			'matrix',
			'sherrie',
			'brayden',
			'danieltest',
			'smarso',
			'kayrena',
			'froglady1',
			'ericapesci'
		];
	}

	/**
     * @return mixed
     */
    public function getAccountListWithCounts() {
        $fieldNames = [];
        $accountList = [];
        $sql = "SELECT accountUrlPrefix FROM accounts WHERE active=1";
        $accountsResult = $this->_primaryDatabase->query($sql, []);
        $accountsResultArray = $accountsResult->result();
        $count = 0;
        if ($accountsResult->num_rows() > 0) {
            foreach ($accountsResultArray as $account) {
                $count ++;
                $databasePrepend = (is_numeric(substr($account->accountUrlPrefix,0,1))) ? 'z' : '';
                $sql = "SELECT 
                    a.accountUrlPrefix,
                    a.accountName,
                    a.accountContactFirstname,
                    a.accountContactLastname,
                    a.email,
                    apc.promo_code,
                    a.active,
                    a.created,
                    (SELECT count(us.id) FROM `" . $databasePrepend . $account->accountUrlPrefix . "_" . ENVIRONMENT . "`.users AS us WHERE 1) AS TeamMembers,  
                    (SELECT count(p.id) FROM `" . $databasePrepend . $account->accountUrlPrefix . "_" . ENVIRONMENT . "`.projects AS p WHERE 1) AS projects,  
                    (SELECT count(pht.id) FROM `" . $databasePrepend . $account->accountUrlPrefix . "_" . ENVIRONMENT . "`.project_has_tasks AS pht WHERE 1) AS tasks,  
                    (SELECT count(i.id) FROM `" . $databasePrepend . $account->accountUrlPrefix . "_" . ENVIRONMENT . "`.invoices AS i WHERE 1) AS invoices,  
                    u.last_login,
                    ap.type AS AccountPlan,
                    (pt.amount/100) AS PlanPrice,
                    (SELECT IF(SUM(NetAmt) IS NULL,0,SUM(NetAmt)) FROM accounts_payments AS aps WHERE aps.payorAccountUrlPrefix = a.accountUrlPrefix) AS NetPayments 
                    FROM accounts AS a
                    LEFT JOIN `" . $databasePrepend . $account->accountUrlPrefix . "_" . ENVIRONMENT . "`.users AS u on u.username = a.accountUrlPrefix
                    LEFT JOIN accounts_discounts AS ad ON ad.accountUrlPrefix = a.accountUrlPrefix
                    LEFT JOIN accounts_promo_codes AS apc ON apc.id = ad.accounts_promo_code_id
                    LEFT JOIN account_plans AS ap ON ap.accountUrlPrefix = a.accountUrlPrefix
                    LEFT JOIN plan_types AS pt ON pt.type = ap.type
                    WHERE a.accountUrlPrefix='" . $account->accountUrlPrefix . "'                     
                    AND a.active=1;
                    ";
                /** @var  CI_DB_mysql_result $result */
                $result = $this->_primaryDatabase->query($sql, []);
                $resultArray = $result->result();
                if ($count == 1) {
                    if ($result->num_rows() > 0) {
                        foreach ((array)$resultArray[0] as $fieldName => $fieldValue) $fieldNames[] = $fieldName;
                        $resultArray = array_merge([(object)$fieldNames], $resultArray);
                    }
                }
                $accountList = array_merge($accountList,$resultArray);
            }
        }
        return $accountList;
    }

}