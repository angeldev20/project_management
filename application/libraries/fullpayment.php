<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 3/20/18
 * Time: 2:21 PM
 */
class fullpayment {

	/** @var  string */
	private $_databaseName;

	/** @var  CI_DB_mysql_driver */
	private $_primaryDatabase;

	/** @var  CI_DB_mysql_driver */
	private $_accountDatabase;

	/** @var string */
	private $_accountUrlPrefix;

	/** @var string  */
	private $_username;

    /** @var \stdClass|null */
	private $_merchant_data;

	/** @var account */
	private $_account;

	/** @var crypto */
	private $_crypto;

	/** @var propay_api */
	private $_propay_api;

	/** @var \stdClass */
    private $_merchant_info;

    /** @var \stdClass */
	private $_merchant_add_funds_info;

    /** @var \stdClass */
    private $_merchant_balances;

	/** @var \stdClass */
    private $_crypto_balances;

    /** @var \stdClass */
    private $_merchant_to_mercant_payment_info;

    /** @var \stdClass */
    private $_primary_bank_account_info;

	/** @var \stdClass */
	private $_secondary_bank_account_info;

	/** @var \stdClass */
	private $_reset_password_info;

	/** @var \stdClass */
	private $_withdraw_to_bank_account_info;

	/** @var \stdClass */
	private $_refund_ach_info;

	/** @var \stdClass */
	private $_void_or_refund_card_info;

	/** @var \stdClass */
	private $_remove_affiliation_info;

	/**
	 * fullpayment constructor.
	 * @param null|array $init_array
	 */
	public function __construct($init_array = NULL)
	{
		if (!is_null($init_array)) {
			if (isset($init_array['databaseName'])) $this->_databaseName = $init_array['databaseName'];
			if (isset($init_array['primaryDatabase'])) $this->_primaryDatabase = $init_array['primaryDatabase'];
			if (isset($init_array['accountDatabase'])) $this->_accountDatabase = $init_array['accountDatabase'];
			if (isset($init_array['accountUrlPrefix'])) $this->_accountUrlPrefix = $init_array['accountUrlPrefix'];
			if (isset($init_array['username'])) $this->_username = $init_array['username'];
			if (isset($init_array['account'])) $this->_account = $init_array['account'];
			if (isset($init_array['crypto'])) $this->_crypto = $init_array['crypto'];
			if (isset($init_array['propay_api'])) $this->_propay_api = $init_array['propay_api'];
		}
		$this->_crypto_balances = new \stdClass();
	}

	/**
	 * returns all merchant account info
	 * @return $this
	 */
	public function isMerchantData() {
		$sql = "SELECT * FROM accounts_users_propay WHERE accountUrlPrefix='" . $this->_accountUrlPrefix . "' 
		AND username='" . $this->_username . "';";
		/** @var  CI_DB_mysql_result $result */
		$result = $this->_primaryDatabase->query($sql, []);
		if ($result->num_rows() == 1) {
			$this->_merchant_data =  (object) $result->_fetch_assoc();
		} else {
			$this->_merchant_data = null;
		}
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getMerchantData() {
		return $this->_merchant_data;
	}

	/**
	 * @return stdClass
	 */
	public function getMerchantInfo() {
		return $this->_merchant_info;
	}

	/**
	 * @return stdClass
	 */
	public function getRemoveAffiliationInfo() {
		return $this->_remove_affiliation_info;
	}

	/**
	 * @return stdClass
	 */
	public function getRefundACHInfo() {
        return $this->_refund_ach_info;
	}

	/**
	 * @return stdClass
	 */
	public function getVoidOrRefundCardInfo() {
		return $this->_void_or_refund_card_info;
	}

	/**
	 * @return stdClass
	 */
	public function getWithdrawToBankAccountInfo() {
		return $this->_withdraw_to_bank_account_info;
	}

	/**
	 * @return stdClass
	 */
	public function getPrimaryBankAccountInfo() {
		return $this->_primary_bank_account_info;
	}

	/**
	 * @return stdClass
	 */
	public function getSecondaryBankAccountInfo() {
		return $this->_secondary_bank_account_info;
	}

	/**
	 * @return stdClass
	 */
	public function getMerchantAddFundsInfo() {
		return $this->_merchant_add_funds_info;
	}

	/**
	 * @return stdClass
	 */
	public function getMerchantBalances() {
		return $this->_merchant_balances;
	}

	/**
	 * @return stdClass
	 */
	public function getResetPasswordInfo() {
		return $this->_reset_password_info;
	}

	/**
	 * Returns all filled user balances for all users
	 * @return \stdClass
	 */
	public function getCryptoBalances() {
		return $this->_crypto_balances;
	}

	/**
	 * gets the status and transaction number of a merchant to merchant payment
	 * (called spendback transaction)
	 * @return stdClass
	 */
	public function getMerchantToMerchantPaymentInfo() {
		return $this->_merchant_to_mercant_payment_info;
	}

	/**
	 * @param string $userId
	 *
	 * @return \stdClass
	 */
	public function getUserCryptoBalances($userId) {
		return (isset($this->_crypto_balances->$userId)) ? $this->_crypto_balances->$userId :

			json_decode(json_encode( [
				'USD' =>
					[
						'asset' => 'USD',
						'qty' => '0.00000000',
						'value' => '0.00',
					],
				'BTC' =>
					[
						'asset' => 'BTC',
						'qty' => '0.00000000',
						'value' => '0.00',
					],
				'ETH' =>
					[
						'asset' => 'ETH',
						'qty' => '0.00000000',
						'value' => '0.00',
					],
				'LTC' =>
					[
						'asset' => 'LTC',
						'qty' => '0.00000000',
						'value' => '0.00',
					],
			]));
	}

	/**
	 * Transfer funds from one bank account to another
	 * @param int $accountNum - account to take the money from
	 * @param int $recAccntNum - account to sent the money too
	 * @param int $amountInCents - supply dollars * 100
	 * @param string $allowPending - allows transfer of funds which are in a pending state, which can result in
	 *     a negative available balance
	 * @param string $comment - a small comment about the transaction
	 *
	 * @return $this
	 */
	public function merchantToMerchantPayment($accountNum, $recAccntNum, $amountInCents, $allowPending='Y', $comment='') {
		if ($this->_merchant_data) {
		$data = "<?xml version='1.0'?>
            <!DOCTYPE Request.dtd>
            <XMLRequest>
            </XMLRequest>";
			$simpleXML = new \SimpleXMLElement( $data );
			$simpleXML->addChild( 'certStr', PROPAY_CERT_STRING );
			$simpleXML->addChild( 'class', 'partner' );
			$simpleXML->addChild( 'XMLTrans' );
			$simpleXML->XMLTrans->addChild( 'transType', 11 );
			$simpleXML->XMLTrans->addChild( 'amount', $amountInCents);
			$simpleXML->XMLTrans->addChild( 'accountNum', $accountNum);
			$simpleXML->XMLTrans->addChild( 'recAccntNum', $recAccntNum);
			$simpleXML->XMLTrans->addChild( 'allowPending', $allowPending);
			$simpleXML->XMLTrans->addChild( 'comment1', $comment);

			$result =
				$this->_propay_api->setXMLUrl( PROPAY_API_XML_URL )
				                  ->setXMLRequestData( $simpleXML->asXML() )
				                  ->postXML()

				                  ->getXMLRequestObject()->asXML();
			$result = json_encode($this->_propay_api->getXMLResponseObject());
			$this->_merchant_to_mercant_payment_info = json_decode($result);
			if($this->_merchant_to_mercant_payment_info->XMLTrans->status == '59') {
				$this->_merchant_to_mercant_payment_info = $this->getBlankMerchantInfo();
			}
		} else {
			$this->_merchant_to_mercant_payment_info = $this->getBlankMerchantToMerchantPaymentInfo();
		}
		return $this;

	}
	/**
	 * @param int $amountInCents
	 * @return $this
	 */
	public function merchantAddFundsFromBank($amountInCents) {
		if ($this->_merchant_data) {
			$data      = "<?xml version='1.0'?>
            <!DOCTYPE Request.dtd>
            <XMLRequest>
            </XMLRequest>";
			$simpleXML = new \SimpleXMLElement( $data );
			$simpleXML->addChild( 'certStr', PROPAY_CERT_STRING );
			$simpleXML->addChild( 'class', 'partner' );
			$simpleXML->addChild( 'XMLTrans' );
			$simpleXML->XMLTrans->addChild( 'transType', 37 );
			$simpleXML->XMLTrans->addChild( 'amount', $amountInCents );
			$simpleXML->XMLTrans->addChild( 'accountNum', $this->_merchant_data->AccountNumber);
			$result =
				$this->_propay_api->setXMLUrl( PROPAY_API_XML_URL )
				                  ->setXMLRequestData( $simpleXML->asXML() )
				                  ->postXML(true)
				                  ->getXMLRequestObject()->asXML();
			$result = json_encode($this->_propay_api->getXMLResponseObject());
			$this->_merchant_add_funds_info = json_decode($result);
		} else {
			$this->_merchant_add_funds_info = $this->getBlankMerchantAddFundsInfo();
		}
		return $this;
	}

	/**
	 *
	 * @return $this
	 */
	public function merchantInfo() {
		if ($this->_merchant_data) {
			$data      = "<?xml version='1.0'?>
            <!DOCTYPE Request.dtd>
            <XMLRequest>
            </XMLRequest>";
			$simpleXML = new \SimpleXMLElement( $data );
			$simpleXML->addChild( 'certStr', PROPAY_CERT_STRING );
			$simpleXML->addChild( 'class', 'partner' );
			$simpleXML->addChild( 'XMLTrans' );
			$simpleXML->XMLTrans->addChild( 'transType', 14 );
			$simpleXML->XMLTrans->addChild( 'accountNum', $this->_merchant_data->AccountNumber);

			$result =
				$this->_propay_api->setXMLUrl( PROPAY_API_XML_URL )
				                  ->setXMLRequestData( $simpleXML->asXML() )
				                  ->postXML()

				        	                  ->getXMLRequestObject()->asXML();
			$result = json_encode($this->_propay_api->getXMLResponseObject());
			$this->_merchant_info = json_decode($result);
		    if(isset($this->_merchant_info->XMLTrans->status) && $this->_merchant_info->XMLTrans->status == '59') {
		    	$this->_merchant_info = $this->getBlankMerchantInfo();
		    }
		} else {
			$this->_merchant_info = $this->getBlankMerchantInfo();
		}
		return $this;
	}

	/**
	 * create or replace primary bank account information
	 * @param string $accountCountryCode - 'USA'
	 * @param string $accountNickName - 'MyBankAccount'
	 * @param int $accountNumber - 123456789
	 * @param string $accountType - 'C' for checking
	 * @param string $officialBankName - 'Wells Fargo'
	 * @param int $routingNumber - 102000076
	 *
	 * @return $this
	 */
	public function editPrimaryBankAccount(
		$accountCountryCode,
		$accountNickName,
		$accountNumber,
		$accountType,
		$officialBankName,
		$routingNumber
	) {
		if ($this->_merchant_data) {
			$data      = "<?xml version='1.0'?>
            <!DOCTYPE Request.dtd>
            <XMLRequest>
            </XMLRequest>";
			$simpleXML = new \SimpleXMLElement( $data );
			$simpleXML->addChild( 'certStr', PROPAY_CERT_STRING );
			$simpleXML->addChild( 'class', 'partner' );
			$simpleXML->addChild( 'XMLTrans' );
			$simpleXML->XMLTrans->addChild( 'transType', 42 );
			$simpleXML->XMLTrans->addChild( 'accountNum', $this->_merchant_data->AccountNumber);
			$simpleXML->XMLTrans->addChild( 'AccountCountryCode', $accountCountryCode);
			$simpleXML->XMLTrans->addChild( 'accountName', $accountNickName);
			$simpleXML->XMLTrans->addChild( 'AccountNumber', $accountNumber);
			$simpleXML->XMLTrans->addChild( 'accountType', $accountType);
			$simpleXML->XMLTrans->addChild( 'BankName', $officialBankName);
			$simpleXML->XMLTrans->addChild( 'RoutingNumber', $routingNumber);

			$result =
				$this->_propay_api->setXMLUrl( PROPAY_API_XML_URL )
				                  ->setXMLRequestData( $simpleXML->asXML() )
				                  ->postXML()

				                  ->getXMLRequestObject()->asXML();
			$result = json_encode($this->_propay_api->getXMLResponseObject());
			$this->_primary_bank_account_info = json_decode($result);
		} else {
			$this->_primary_bank_account_info = $this->getBlankBankAccountInfo();
		}
		return $this;
	}


	/**
	 * create or replace primary bank account information
	 * @param string $accountCountryCode - 'USA'
	 * @param string $accountNickName - 'MyBankAccount'
	 * @param int $accountNumber - 123456789
	 * @param string $accountType - 'C' for checking
	 * @param string $officialBankName - 'Wells Fargo'
	 * @param int $routingNumber - 102000076
	 *
	 * @return $this
	 */
	public function editSecondaryBankAccount(
		$accountCountryCode,
		$accountNickName,
		$accountNumber,
		$accountType,
		$officialBankName,
		$routingNumber
	) {
		if ($this->_merchant_data) {
			$data      = "<?xml version='1.0'?>
            <!DOCTYPE Request.dtd>
            <XMLRequest>
            </XMLRequest>";
			$simpleXML = new \SimpleXMLElement( $data );
			$simpleXML->addChild( 'certStr', PROPAY_CERT_STRING );
			$simpleXML->addChild( 'class', 'partner' );
			$simpleXML->addChild( 'XMLTrans' );
			$simpleXML->XMLTrans->addChild( 'transType', 42 );
			$simpleXML->XMLTrans->addChild( 'accountNum', $this->_merchant_data->AccountNumber);
			$simpleXML->XMLTrans->addChild( 'SecondaryAccountCountryCode', $accountCountryCode);
			$simpleXML->XMLTrans->addChild( 'SecondaryAccountName', $accountNickName);
			$simpleXML->XMLTrans->addChild( 'SecondaryAccountNumber', $accountNumber);
			$simpleXML->XMLTrans->addChild( 'SecondaryAccountType', $accountType);
			$simpleXML->XMLTrans->addChild( 'SecondaryBankName', $officialBankName);
			$simpleXML->XMLTrans->addChild( 'SecondaryRoutingNumber', $routingNumber);

			$result =
				$this->_propay_api->setXMLUrl( PROPAY_API_XML_URL )
				                  ->setXMLRequestData( $simpleXML->asXML() )
				                  ->postXML()

				                  ->getXMLRequestObject()->asXML();
			$result = json_encode($this->_propay_api->getXMLResponseObject());
			$this->_secondary_bank_account_info = json_decode($result);
		} else {
			$this->_secondary_bank_account_info = $this->getBlankBankAccountInfo();
		}
		return $this;
	}

	public function resetPropayAccountPassword() {
		if ($this->_merchant_data) {
			$data      = "<?xml version='1.0'?>
            <!DOCTYPE Request.dtd>
            <XMLRequest>
            </XMLRequest>";
			$simpleXML = new \SimpleXMLElement( $data );
			$simpleXML->addChild( 'certStr', PROPAY_CERT_STRING );
			$simpleXML->addChild( 'class', 'partner' );
			$simpleXML->addChild( 'XMLTrans' );
			$simpleXML->XMLTrans->addChild( 'transType', 32 );
			$simpleXML->XMLTrans->addChild( 'accountNum', $this->_merchant_data->AccountNumber);

			$result =
				$this->_propay_api->setXMLUrl( PROPAY_API_XML_URL )
				                  ->setXMLRequestData( $simpleXML->asXML() )
				                  ->postXML()

				                  ->getXMLRequestObject()->asXML();
			$result = json_encode($this->_propay_api->getXMLResponseObject());
			$this->_reset_password_info = json_decode($result);
		} else {
			$this->_reset_password_info = $this->getBlankResetPassword();
		}
		return $this;
	}

	/**
	 * @return $this
	 */
	public function removeAffiliation() {
		if ($this->_merchant_data) {
			$data      = "<?xml version='1.0'?>
            <!DOCTYPE Request.dtd>
            <XMLRequest>
            </XMLRequest>";
			$simpleXML = new \SimpleXMLElement( $data );
			$simpleXML->addChild( 'certStr', PROPAY_CERT_STRING );
			$simpleXML->addChild( 'class', 'partner' );
			$simpleXML->addChild( 'XMLTrans' );
			$simpleXML->XMLTrans->addChild( 'transType', 41);
			$simpleXML->XMLTrans->addChild( 'accountNum', $this->_merchant_data->AccountNumber);

			$result =
				$this->_propay_api->setXMLUrl( PROPAY_API_XML_URL )
				                  ->setXMLRequestData( $simpleXML->asXML() )
				                  ->postXML()

				                  ->getXMLRequestObject()->asXML();
			$result = json_encode($this->_propay_api->getXMLResponseObject());
			$this->_remove_affiliation_info = json_decode($result);
		} else {
			$this->_remove_affiliation_info = $this->getBlankRemoveAffiliation();
		}
		return $this;
	}

	/**
	 * @param int $transactionNumber
	 * @param int $amountToRefundInCents
	 * @param string $invoiceNumbderString
	 *
	 * @return $this
	 */
	public function voidOrRefundCreditCard($transactionNumber, $amountToRefundInCents, $invoiceNumbderString) {
		if ($this->_merchant_data) {
			$data      = "<?xml version='1.0'?>
            <!DOCTYPE Request.dtd>
            <XMLRequest>
            </XMLRequest>";
			$simpleXML = new \SimpleXMLElement( $data );
			$simpleXML->addChild( 'certStr', PROPAY_CERT_STRING );
			$simpleXML->addChild( 'class', 'partner' );
			$simpleXML->addChild( 'XMLTrans' );
			$simpleXML->XMLTrans->addChild( 'transType', 07 );
			$simpleXML->XMLTrans->addChild( 'accountNum', $this->_merchant_data->AccountNumber);
			$simpleXML->XMLTrans->addChild( 'transNum', $transactionNumber );
			$simpleXML->XMLTrans->addChild( 'amount', $amountToRefundInCents );
			$simpleXML->XMLTrans->addChild( 'invNum', $invoiceNumbderString );
			$result =
				$this->_propay_api->setXMLUrl( PROPAY_API_XML_URL )
				                  ->setXMLRequestData( $simpleXML->asXML() )
				                  ->postXML()

				                  ->getXMLRequestObject()->asXML();
			$result = json_encode($this->_propay_api->getXMLResponseObject());
			$this->_void_or_refund_card_info = json_decode($result);
		} else {
			$this->_void_or_refund_card_info = $this->getBlankVoidOrRefundCard();
		}
		return $this;

	}

	/**
	 * @param int $amountToRefundInCents
	 * @param string $invoiceNumbderString
	 * @param int $RoutingNumber
	 * @param string $accountName
	 * @param int $AccountNumber
	 * @param string $StandardEntryClassCode
	 * @param string $AccountCountryCode
	 * @param string $accountType
	 * @param string $comment1
	 * @param string $comment2
	 *
	 * @return $this
	 */
	public function RefundACH(
		$amountToRefundInCents,
		$invoiceNumbderString,
		$RoutingNumber,
		$accountName,
		$AccountNumber,
		$StandardEntryClassCode = 'CCD',
		$AccountCountryCode = 'USA',
		$accountType = 'Checking',
	    $comment1 = '',
	    $comment2 = ''

	) {
		if ($this->_merchant_data) {
			$data      = "<?xml version='1.0'?>
            <!DOCTYPE Request.dtd>
            <XMLRequest>
            </XMLRequest>";
			$simpleXML = new \SimpleXMLElement( $data );
			$simpleXML->addChild( 'certStr', PROPAY_CERT_STRING );
			$simpleXML->addChild( 'class', 'partner' );
			$simpleXML->addChild( 'XMLTrans' );
			$simpleXML->XMLTrans->addChild( 'transType', 40 );
			$simpleXML->XMLTrans->addChild( 'accountNum', $this->_merchant_data->AccountNumber);
			$simpleXML->XMLTrans->addChild( 'amount', $amountToRefundInCents );
			$simpleXML->XMLTrans->addChild( 'RoutingNumber', $RoutingNumber );
			$simpleXML->XMLTrans->addChild( 'AccountNumber', $AccountNumber );
			$simpleXML->XMLTrans->addChild( 'accountName', $accountName );
			$simpleXML->XMLTrans->addChild( 'StandardEntryClassCode', $StandardEntryClassCode );
			$simpleXML->XMLTrans->addChild( 'AccountCountryCode', $AccountCountryCode );
			$simpleXML->XMLTrans->addChild( 'accountType', $accountType );
			$simpleXML->XMLTrans->addChild( 'comment1', $comment1 );
			$simpleXML->XMLTrans->addChild( 'comment2', $comment2 );
			$simpleXML->XMLTrans->addChild( 'invNum', $invoiceNumbderString );
			$result =
				$this->_propay_api->setXMLUrl( PROPAY_API_XML_URL )
				                  ->setXMLRequestData( $simpleXML->asXML() )
				                  ->postXML()

				                  ->getXMLRequestObject()->asXML();
			$result = json_encode($this->_propay_api->getXMLResponseObject());
			$this->_refund_ach_info = json_decode($result);
		} else {
			$this->_refund_ach_info = $this->getBlankRefundACH();
		}
		return $this;

	}

	public function withdrawToPrimaryBankAccount($amountToWithdrawInCents) {
		if ($this->_merchant_data) {
			$data      = "<?xml version='1.0'?>
            <!DOCTYPE Request.dtd>
            <XMLRequest>
            </XMLRequest>";
			$simpleXML = new \SimpleXMLElement( $data );
			$simpleXML->addChild( 'certStr', PROPAY_CERT_STRING );
			$simpleXML->addChild( 'class', 'partner' );
			$simpleXML->addChild( 'XMLTrans' );
			$simpleXML->XMLTrans->addChild( 'transType', 38 );
			$simpleXML->XMLTrans->addChild( 'amount', $amountToWithdrawInCents);
			$simpleXML->XMLTrans->addChild( 'accountNum', $this->_merchant_data->AccountNumber);

			$result =
				$this->_propay_api->setXMLUrl( PROPAY_API_XML_URL )
				                  ->setXMLRequestData( $simpleXML->asXML() )
				                  ->postXML()

				                  ->getXMLRequestObject()->asXML();
			$result = json_encode($this->_propay_api->getXMLResponseObject());
			$this->_withdraw_to_bank_account_info = json_decode($result);
		} else {
			$this->_withdraw_to_bank_account_info = $this->getBlankWithdrawToBankAccount();
		}
		return $this;
	}

	/**
	 *
	 * @return $this
	 */
	public function merchantBalances() {
		if ($this->_merchant_data) {
			$data      = "<?xml version='1.0'?>
            <!DOCTYPE Request.dtd>
            <XMLRequest>
            </XMLRequest>";
			$simpleXML = new \SimpleXMLElement( $data );
			$simpleXML->addChild( 'certStr', PROPAY_CERT_STRING );
			$simpleXML->addChild( 'class', 'partner' );
			$simpleXML->addChild( 'XMLTrans' );
			$simpleXML->XMLTrans->addChild( 'transType', 13 );
			$simpleXML->XMLTrans->addChild( 'accountNum', $this->_merchant_data->AccountNumber);

			$result =
				$this->_propay_api->setXMLUrl( PROPAY_API_XML_URL )
				                  ->setXMLRequestData( $simpleXML->asXML() )
				                  ->postXML()

				                  ->getXMLRequestObject()->asXML();
			$result = json_encode($this->_propay_api->getXMLResponseObject());
			$this->_merchant_balances = json_decode($result);
			if($this->_merchant_balances->XMLTrans->status == '59') {
				$this->_merchant_balances = $this->getBlankMerchantAccountBalances();
			}
		} else {
			$this->_merchant_balances = $this->getBlankMerchantAccountBalances();
		}
		return $this;
	}

	/**
	 * get coinbase balances for a coinbase userId which is our account id
	 * @param $user_account
	 */
	public function cryptoBalances($user_account) {
		$userParts = explode( '_', $user_account );
		if ( count( $userParts ) == 1 ) {
			$userParts[1] = $userParts[0];
		}
		$sql = "SELECT id FROM accounts WHERE accountUrlPrefix='" . $userParts[0] . "';";
		/** @var  CI_DB_mysql_result $result */
		$result = $this->_primaryDatabase->query($sql, []);
		if ($result->num_rows() == 1) {
			$id = (object) $result->_fetch_assoc();
		    $id = $id->id;
		} else {
			$id = 0;
		}
		$this->_crypto_balances->$user_account = json_decode(json_encode( $this->_crypto->DisplayDashBoard($id)));
	}

	public function getBlankMerchantInfo() {
		$data = "<?xml version='1.0'?>
		<XMLResponse>
		<XMLTrans>
		<transType>14</transType>
		<accountNum>000000</accountNum>
		<status>00</status>
		<amount>0</amount>
		<pendingAmount>0</pendingAmount>
		</XMLTrans>
		</XMLResponse>";
		$simpleXML = new \SimpleXMLElement( $data );
		return json_decode(json_encode($simpleXML));
	}

	public function getBlankMerchantToMerchantPaymentInfo() {
		$data = "<?xml version='1.0'?>
		<XMLResponse>
		<XMLTrans>
		<transType>11</transType>
		<accountNum>123456</accountNum>
		<status>59</status>
		<transNum>0</transNum>
		<pending>N</pending>
		</XMLTrans>
		</XMLResponse>";
		$simpleXML = new \SimpleXMLElement( $data );
		return json_decode(json_encode($simpleXML));
	}

	public function getBlankMerchantAccountBalances() {
		$data = "<?xml version='1.0'?>
		<XMLResponse>
		<XMLTrans>
		<transType>13</transType>
		<accountNum>1148111</accountNum>
		<tier>Premium</tier>
		<expiration>11/27/2025 12:00:00 AM</expiration>
		<signupDate>4/17/2008 3:17:00 PM</signupDate>
		<affiliation>SRKUUW9 </affiliation>
		<accntStatus>Ready</accntStatus>
		<addr>123 Anywhere St</addr>
		<city>Lehi</city>
		<state>UT</state>
		<zip>84043</zip>
		<status>00</status>
		<apiReady>Y</apiReady>
		<currencyCode>USD</currencyCode>
		<CreditCardTransactionLimit>0</CreditCardTransactionLimit>
		<CreditCardMonthLimit>0</CreditCardMonthLimit>
		<ACHPaymentPerTranLimit>0</ACHPaymentPerTranLimit>
		<ACHPaymentMonthLimit>0</ACHPaymentMonthLimit>
		<CreditCardMonthlyVolume>0</CreditCardMonthlyVolume>
		<ACHPaymentMonthlyVolume>0</ACHPaymentMonthlyVolume>
		<ReserveBalance>0</ReserveBalance>
		</XMLTrans>
		</XMLResponse>";
		$simpleXML = new \SimpleXMLElement( $data );
		return json_decode(json_encode($simpleXML));
	}

	public function getBlankMerchantAddFundsInfo() {
		$data = "<?xml version='1.0'?>
		<XMLResponse>
		<XMLTrans>
		<transType>37</transType>
		<accountNum>000000</accountNum>
		<status>59</status>
		<transNum>0</transNum>
		</XMLTrans>
		</XMLResponse>";
		$simpleXML = new \SimpleXMLElement( $data );
		return json_decode(json_encode($simpleXML));
	}

    public function getBlankBankAccountInfo() {
	    $data = "<XMLResponse>
		<XMLTrans>
		<transType>42</transType>
		<status>59</status>
		</XMLTrans>
		</XMLResponse>";
	    $simpleXML = new \SimpleXMLElement( $data );
	    return json_decode(json_encode($simpleXML));
    }

	public function getBlankResetPassword() {
		$data = "<XMLResponse>
		<XMLTrans>
		<transType>32</transType>
		<accountNum>000000</accountNum>
		<status>59</status>
		</XMLTrans>
		</XMLResponse>";
		$simpleXML = new \SimpleXMLElement( $data );
		return json_decode(json_encode($simpleXML));
	}

	public function getBlankWithdrawToBankAccount() {
		$data = "<XMLResponse>
		<XMLTrans>
		<transType>38</transType>
		<accountNum>000000</accountNum>
		<status>59</status>
		<transNum>0</transNum>
		</XMLTrans>
		</XMLResponse>";
		$simpleXML = new \SimpleXMLElement( $data );
		return json_decode(json_encode($simpleXML));
	}

	public function getBlankVoidOrRefundCard() {
		$data = "<XMLTrans>
		<transType>07</transType>
		<accountNum>000000</accountNum>
		<transNum>0</transNum>
		<status>59</status>
		</XMLTrans>
		</XMLResponse>";
		$simpleXML = new \SimpleXMLElement( $data );
		return json_decode(json_encode($simpleXML));
	}

	public function getBlankRefundAch() {
		$data = "<XMLResponse>
		<XMLTrans>
		<transType>40</transType>
		<transNum>0</transNum>
		<status>59</status>
		<accountNum>000000</accountNum>
		<invNum>0</invNum>
		</XMLTrans>
		</XMLResponse>";
		$simpleXML = new \SimpleXMLElement( $data );
		return json_decode(json_encode($simpleXML));
	}

	public function getBlankRemoveAffiliation() {
		$data = "<XMLResponse>
		<XMLTrans>
		<transType>41</transType>
		<status>59</status>
		</XMLTrans>
		</XMLResponse>";
		$simpleXML = new \SimpleXMLElement( $data );
		return json_decode(json_encode($simpleXML));
	}
}