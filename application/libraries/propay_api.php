<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 10/4/17
 * Time: 4:37 PM
 */

/**
 * Class propay_api
 */
class propay_api {

    /** @var  string */
    private $_databaseName;

    /** @var  CI_DB_mysql_driver */
    private $_primaryDatabase;

    /** @var string */
    private $_encryptedString;

    /** @var string */
    private $_decryptedString;

    /** @var string */
    private $_accountUrlPrefix;

    /**
     * protect pay url
     * @var  string
     */
    private $_protect_pay_api_base_url;

    /** @var  string */
    private $_protect_pay_hosted_transaction_base_url;

    /** @var  string */
    private $_encrypted_protect_pay_biller_id;

    /** @var  string */
    private $_encrypted_protect_pay_auth_token;

    /** @var  string */
    private $_encrypted_protect_pay_merchant_profile_id;

    /** @var  string */
    private $_encrypted_protect_pay_commission_disbursement_credential;

    /** @var  string */
    private $_encrypted_protect_pay_payer_account_id;

    /* change this to the production url for going live after testing https://api.propay.com */
    //test 'https://xmltestapi.propay.com';
    private $_apiBaseUrl;
    /* credentials that would normally be set from database values or a config value */
    private $_billerId;
    private $_authToken;

    /* for signups */
    private $_signupData;
    private $_signupInfo;

    private $_certStr;
    private $_termId;

    /** for timed pull */
    private $_timedPullData;
    private $_timedPullInfo;

    /* for creating payer ID */
    private $_createPayerIdData;
    private $_createPayerIdInfo;

    /** @var  \SimpleXMLElement */
    private $_xmlRequestObject;
    /** @var  \SimpleXMLElement */
    private $_xmlResponseObject;
    /** @var  string */
    private $_xmlUrl;

	/** @var array */
    private $_propayToPropayData;
    private $_propayToPropayInfo;

    /**
     * account constructor.
     * @param null|array $init_array
     */
    public function __construct($init_array = NULL)
    {

        if (!is_null($init_array)) {
            $this->_databaseName = $init_array['databaseName'];
            $this->_primaryDatabase = $init_array['primaryDatabase'];
        }
    }

    /**
     * Used to set the base url for either testing or production
     * @param string $apiBaseUrl
     * @return $this
     */
    public function setApiBaseUrl($apiBaseUrl) {
        $this->_apiBaseUrl = $apiBaseUrl;
        return $this;
    }

    /**
     * @param string $billerId
     * @return $this
     */
    public function setBillerId($billerId) {
        $this->_billerId = $billerId;
        return $this;
    }

    /**
     * @param string $authToken
     * @return $this
     */
    public function setAuthToken($authToken) {
        $this->_authToken = $authToken;
        return $this;
    }

    /**
     * @param array $payerIdData
     * @return $this\
     */
    public function setCreatePayerIdData($payerIdData) {
        $this->_createPayerIdData = $payerIdData;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatePayerIdInfo() {
        return $this->_createPayerIdInfo;
    }


    /**
     * Creates a payer id
     * @return $this
     *
     * USAGE:
     * $result = $this->propay_api
     *     ->setApiBaseUrl(PROTECT_PAY_API_BASE_URL)
     *     ->setBillerId(PROTECT_PAY_BILLER_ID)
     *     ->setAuthToken(PROTECT_PAY_AUTH_TOKEN)
     *     ->setCreatePayerIdData($accountData)
     *     ->createPayerId()
     *     ->getCreatePayerIdInfo();
     */
    public function createPayerId()
    {
        $data_string = json_encode($this->_createPayerIdData);

        $ch = curl_init($this->_apiBaseUrl . '/Payers');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->_getProtectPayAuth());
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        ));

        $this->_createPayerIdInfo = curl_exec($ch);
        return $this;
    }

    /**
     * @param string $termId
     * @return $this
     */
    public function setTermId($termId) {
        $this->_termId = $termId;
        return $this;
    }

    /**
     * @param string $certStr
     * @return $this
     */
    public function setCertStr($certStr) {
        $this->_certStr = $certStr;
        return $this;
    }

    /**
     * @return string
     */
    private function _getProtectPayAuth() {
        return $this->_billerId . ':' . $this->_authToken;
    }

    /**
     * @return string
     */
    private function _getAuth() {
        return $this->_certStr . ':' . $this->_termId;
    }

    /**
     * @param string $protectPayApiBaseUrl
     * @return $this
     */
    public function setProtectPayApiBaseUrl($protectPayApiBaseUrl) {
        $this->_protect_pay_api_base_url = $protectPayApiBaseUrl;
        return $this;
    }

    /**
     * @param string $propayApiBaseUrl
     * @return $this
     */
    public function setPropayApiBaseUrl($propayApiBaseUrl) {
        $this->_apiBaseUrl = $propayApiBaseUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getProtectPayApiBaseUrl() {
        return $this->_protect_pay_api_base_url;
    }

    /**
     * @param array $signupData
     * @return $this
     */
    public function setSignupData($signupData) {
        $this->_signupData = $signupData;
        return $this;
    }

    /*
        $data = [
            "PersonalData" => [
        R 55    "SourceEmail" => "damonhogan2@juno.com",
        R 20    "FirstName" => "Damon",
        O 2     "MiddleInitial" => "Q",
        R 25    "LastName" => "Hogan",
        R 10    "DateOfBirth" => "1/19/1997",
        R 9     "SocialSecurityNumber" => "111111111",
                "PhoneInformation" => [
        R 10        "DayPhone" => "8601233421",
        R 10        "EveningPhone" => "8601233421"
                ]
            ],

            "SignupAccountData" => [
        O       //"ExternalId" => "3212157",
        R 40    "Tier" => "", //blank = lowest cost 'Premium', 'Merchant' etc
        O 4     //"PhonePIN" => "1234",
            ],

            //"BusinessData" => [
            //    "BusinessLegalName" => "ProPay Partner",
            //    "DoingBusinessAs" => "PPA",
            //],

            "Address" => [
        O 100   "ApartmentNumber" => null,
        R 100   "Address1" => "100 Main Street",
        R 100   "Address2" => null,
        R 30    "City" => "Rocky Hill",
        R 3     "State" => "CT",
        O 3     "Country" => "USA", //Question on if the field name changed.
        R 9     "Zip" => "06067" //5 or 9 characters without dash.
            ],

            "BankAccount" => [
        R 3     "AccountCountryCode" => "USA",
        R 15    "AccountOwnershipType" => "Personal",
        R 1     "AccountType" => "C", //C.hecking S.avings G.General Ledger
        R 25    "BankAccountNumber" => "123456789",
        R 50    "BankName" => "Wells Fargo",
        R 50    "RoutingNumber" => "102000076"
            ],

            //"BusinessAddress" => [
            //    "Address1" => "101 Main Street",
            //    "Address2" => "Ste. 200",
            //    "City" => "Rocky Hill",
            //    "State" => "CT",
            //    "Country" => "USA",
            //    "Zip" => "06067"
            //]

        ];

     */

    /**
     * Processes the signup process through the rest api
     *
     * USAGE example
     *
     * $this->propay_api
     *     ->setApiBaseUrl(PROTECT_PAY_HOSTED_TRANSACTION_BASE_URL)
     *     ->setCertStr(PROTECT_PAY_COMMISSION_DISBURSEMENT_CREDENTIAL)
     *     ->setTermId(PROTECT_PAY_TERM_ID)
     *     ->setSignupData($data)
     *     ->processSignup()
     *     ->getSignupInfo();
     *
     * @return $this
     */
    public function processSignup() {
        $data_string = json_encode($this->_signupData);
        $url = $this->_apiBaseUrl . '/ProPayAPI/signup';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->_getAuth());
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        ));

        $this->_signupInfo = curl_exec($ch);
        return $this;
    }

	/**
	 * @param array $propayToPropayData
	 *
	 * 	 * Sample JSON request data:
	 * {
	 *     "amount": 100,
	 *     "invNum": "",
	 *     "recAccntNum": "123456"
	 * }
	 *
	 *
	 * @return $this
	 */
    public function setPropayToPropayData($propayToPropayData) {
    	$this->_propayToPropayData = $propayToPropayData;
    	return $this;
    }

	/**
	 * returns a json string on success, or bool, or error message
	 * @return mixed
	 *
	 *  * 	Sample JSON response data:
	 * {
	 *     "AccountNumber": 123456,
	 *     "Status": "00",
	 *     "TransactionNumber": 1
	 * }
	 */
    public function getPropayToPropayInfo() {
    	return $this->_propayToPropayInfo;
    }

	/**
	 * @return $this
	 *
	 * Sample JSON request data:
	 * {
	 *     "amount": 100,
	 *     "invNum": "",
	 *     "recAccntNum": "123456"
	 * }
	 *
	 * 	Sample JSON response data:
	 * {
	 *     "AccountNumber": 123456,
	 *     "Status": "00",
	 *     "TransactionNumber": 1
	 * }
	 *
	 */
	public function processPropayToPropayTransfer() {
		$data_string = json_encode($this->_propayToPropayData);
		$url = $this->_apiBaseUrl . '/ProPayAPI/ProPayToProPay';

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $this->_getAuth());
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data_string)
		));

		$this->_propayToPropayInfo = curl_exec($ch);
		return $this;
	}

	/**
     * @param string $protectPayHostedTransactionBaseUrl
     * @return $this
     */
    public function setProtectPayHostedTransactionBaseUrl($protectPayHostedTransactionBaseUrl) {
        $this->_protect_pay_hosted_transaction_base_url = $protectPayHostedTransactionBaseUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getProtectPayHostedTransactionBaseUrl() {
        return $this->_protect_pay_hosted_transaction_base_url;
    }

    /**
     * @param string $protectPayBillerId
     * @return $this
     */
    public function setEncryptedProtectPayBillerId($protectPayBillerId) {
        $this->_encrypted_protect_pay_biller_id = $this->encryptString($protectPayBillerId)->getEncryptedString();
        $this->_encryptedString = null;
        return $this;
    }

    /**
     * @return string
     */
    public function getEncryptedProtectPayBillerId() {
        return $this->decryptString($this->_encrypted_protect_pay_biller_id)->getEncryptedString();
    }

    /**
     * @param string $protectPayAuthToken
     * @return $this
     */
    public function setEncryptedProtectPayAuthToken($protectPayAuthToken) {
        $this->_encrypted_protect_pay_auth_token = $this->encryptString($protectPayAuthToken)->getEncryptedString();
        $this->_encryptedString = null;
        return $this;
    }

    /**
     * @return string
     */
    public function getEncryptedProtectPayAuthToken() {
        return $this->decryptString($this->_encrypted_protect_pay_auth_token)->getEncryptedString();
    }

    /**
     * @param string $protectPayMerchantProfileId
     * @return $this
     */
    public function setEncryptedProtectPayMerchantProfileId($protectPayMerchantProfileId) {
        $this->_encrypted_protect_pay_merchant_profile_id = $this->encryptString($protectPayMerchantProfileId)->getEncryptedString();
        $this->_encryptedString = null;
        return $this;
    }

    /**
     * @return string
     */
    public function getEncryptedProtectPayMerchantProfileId() {
        return $this->decryptString($this->_encrypted_protect_pay_merchant_profile_id)->getEncryptedString();
    }

    /**
     * @param string $protectPayCommissionDisbursementCredential
     * @return $this
     */
    public function setEncryptedProtectPayCommissionDisbursementCredential($protectPayCommissionDisbursementCredential) {
        $this->_encrypted_protect_pay_commission_disbursement_credential =
            $this->encryptString($protectPayCommissionDisbursementCredential)->getEncryptedString();
        $this->_encryptedString = null;
        return $this;
    }

    /**
     * @return string
     */
    public function getEncryptedProtectPayCommissionDisbursementCredential() {
        return $this->decryptString($this->_encrypted_protect_pay_commission_disbursement_credential)->getEncryptedString();
    }

    /**
     * @param string $protectPayPayerAccountId
     * @return $this
     */
    public function setEncryptedProtectPayPayerAccountId($protectPayPayerAccountId) {
        $this->_encrypted_protect_pay_payer_account_id =
            $this->encryptString($protectPayPayerAccountId)->getEncryptedString();
        $this->_encryptedString = null;
        return $this;
    }

    /**
     * @return string
     */
    public function getEncryptedProtectPayPayerAccountId() {
        return $this->decryptString($this->_encrypted_protect_pay_payer_account_id)->getEncryptedString();
    }

    /**
     * @param string $accountUrlPrefix
     * @return $this
     */
    public function setAccountUrlPrefix($accountUrlPrefix) {
        $this->_accountUrlPrefix = strtolower($accountUrlPrefix);
        return $this;
    }

    /**
     * returns all propay data
     * @return bool|object
     */
    public function getAccountData() {
        $sql = "SELECT * FROM account_propay WHERE accountUrlPrefix='" . $this->_accountUrlPrefix . "';";
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
     * checks to see if we have an account record for this urlPrefix already
     * @param string $urlPrefix
     * @return bool
     */
    public function validateUrlPrefix($urlPrefix) {
        $isAccount = false;
        switch ($urlPrefix) {
            case null:
            case 'www':
            case 'app':
                break;
            default:
                $account = $this->checkForActiveAccount($urlPrefix);
                if ($account) {
                    if($this->databaseExists($urlPrefix . '_' . ENVIRONMENT )) {
                        $isAccount = true;
                    }
                }
        }
        return $isAccount;
    }

    /**
     * @return bool
     */
    public function createAccountPropayEntry() {

        $activeAccount = $this->checkForActiveAccount($this->_accountUrlPrefix);

        if (!$activeAccount) {
            $sql = "INSERT INTO account_propay (
                    accountUrlPrefix,
                    protect_pay_api_base_url,
                    protect_pay_hosted_transaction_base_url,
                    protect_pay_biller_id,
                    protect_pay_auth_token,
                    protect_pay_merchant_profile_id,
                    protect_pay_commission_disbursement_credendial,
                    protect_pay_payer_account_id
                ) VALUES (
                    '" . $this->_accountUrlPrefix . "',
                    '" . $this->_protect_pay_api_base_url . "',
                    '" . $this->_protect_pay_hosted_transaction_base_url . "',
                    '" . $this->_encrypted_protect_pay_biller_id . "',
                    '" . $this->_encrypted_protect_pay_auth_token . "',
                    '" . $this->_encrypted_protect_pay_merchant_profile_id . "',
                    '" . $this->_encrypted_protect_pay_commission_disbursement_credential . "',
                    '" . $this->_encrypted_protect_pay_payer_account_id . "'
                    );";
            $result = $this->_primaryDatabase->query($sql, []);
        } else {
            return false;
            //TODO: build in update logic if this already exists
        }
        return true;
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
     * Gets a json string of the signupInfo of the tier that was just signed up.  A signed up response
     * looks like
     * {"AccountNumber":32299999,"Password":"$#GD!ADXv2","SourceEmail":"someuser@somedomain.com","Status":"00","Tier":"Platinum"}
     * @return mixed
     */
    public function getSignupInfo() {
        return $this->_signupInfo;
    }

    /**
     * This function in provided for testing only, as such it won't work in production
     * @param $signupInfoJsonString
     */
    public function setSignupInfo($signupInfoJsonString) {
        if(ENVIRONMENT == 'development' || ENVIRONMENT == 'release') $this->_signupInfo = $signupInfoJsonString;
    }

    /**
     * Processes the timed pull with the provided data
     * @return $this
     *
     * USAGE:
     *
     * $data = [
     *     "accountNum" => 123456,
     *     "recAccntNum" => 987654,
     *     "amount" => 100,
     *     "transNum" => 2, //card transaction number, this payment will occur when card transaction settles
     *     "invNum" => "Invoice", //optional
     *     "comment1" => "Test Comments", //optional
     *     "comment2" => "Test Comments2" //optional
     * ];
     *
     * $proPayAPI = new ProPayApi();
     * $result = $proPayAPI->setCertStr('thecertstringgoesin here') //PROTECT_PAY_COMMISSION_DISBURSEMENT_CREDENTIAL
     *     ->setTermId('termid goes here') //BillerAccountId
     *     ->setTimedPullData($data)
     *     ->processTimedPull()
     *     ->getTimedPullInfo();
     *
     */
    public function processTimedPull() {
        $data_string = json_encode($this->_timedPullData);

        $url = $this->_apiBaseUrl . '/ProPayAPI/timedPull';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->_getAuth());
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        ));

        $this->_timedPullInfo = curl_exec($ch);
        return $this;
    }

    /**
     * @param array $timedPullData
     * @return $this
     */
    public function setTimedPullData($timedPullData) {
        $this->_timedPullData = $timedPullData;
        return $this;
    }

    /**
     * gets the timed pull info after processing, looks something like...
     * @return mixed
     * {
     *     "AccountNumber": 123456,
     *     "Status": "00",
     *     "TransactionNumber": 1
     * }
     */
    public function getTimedPullInfo() {
        return $this->_timedPullInfo;
    }

    /**
     * sets the url for the XML request
     * @param string $xmlUrl
     * @return $this
     */
    public function setXMLUrl($xmlUrl) {
        $this->_xmlUrl = $xmlUrl;
        return $this;
    }

    /**
     * sets the xml request object
     * @param string $xmlData - containing XML
     * @return $this
     */
    public function setXMLRequestData($xmlData) {
        $this->_xmlRequestObject = simplexml_load_string($xmlData);
        return $this;
    }

    /**
     * returns a xml version 1.0 header for use in generating a simple XML object
     * @return string
     */
    public function getDefaultXMLHeader() {
        return "<?xml version='1.0'?>
            <!DOCTYPE Request.dtd>
            <XMLRequest>
            </XMLRequest>";
    }

    /**
     * posts XML to the server
     * @return $this
     */
    public function postXML($postX509 = false) {
        $header = [
            "Content-type:text/xml; charset=\"utf-8\"",
            "Accept: text/xml"
        ];

        if ($postX509) $header[] = "X509Certificate: " . PROPAY_X509_CERT;

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => $this->_xmlUrl,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $this->_xmlRequestObject->asXML(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $header,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_HTTPAUTH => CURLAUTH_ANY
        ]);
        $this->_xmlResponseObject = simplexml_load_string(curl_exec($curl));
        curl_close($curl);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getXMLRequestObject() {
        return $this->_xmlRequestObject;
    }

    /**
     * @return mixed
     */
    public function getXMLResponseObject() {
        return $this->_xmlResponseObject;
    }

    /**
     * Stores the propay signup important data so we can look up a user when it's time to pay them
     * @param string $accountUrlPrefix
     * @param int $username
     * @param int $signature 1 = agreed, 0 means didn't agree which shouldn't ever happen unless data was inserted and they never agreed
     * @param string $signatureIpAddress
     * @param string $signatureDateTime - mysql timestamp formate yyyy-mm-dd hh:mm:ss
     * @param bool $existing
     * @param null|int $propayAccountNumber - required if existing is set to 2
     * @return bool
     * assumed _signupInfo containts something like
     * {"AccountNumber":32299999,"Password":"$#GD!ADXv2","SourceEmail":"someuser@somedomain.com","Status":"00","Tier":"Platinum"}
     */
    public function storeSignupInfo($accountUrlPrefix, $username, $signature, $signatureIpAddress, $signatureDateTime, $existing = false, $propayAccountNumber = null) {
        $success = false;
        if (!$existing) {
            $signupInfo = json_decode($this->_signupInfo);
        } else {
            $signupInfo = (object) [
                'Status' => '00',
                'AccountNumber' => $propayAccountNumber,
                'Tier' => 'Premium'
            ];
        }
        $bankingStatus = (isset($signupInfo->BankAccount)) ? 1 : 0;
        if ($signupInfo->Status == "00") {
            $sql = "SELECT * FROM accounts_users_propay WHERE 
                        accountUrlPrefix = '" . $accountUrlPrefix . "' AND
                        username='" . $username . "';";
            /** @var  CI_DB_mysql_result $result */
            $result = $this->_primaryDatabase->query($sql, []);
            if ($result->num_rows() == 1) {
                $sql = "UPDATE accounts_users_propay set Status='" . $signupInfo->Status . "',AccountNumber='" . $signupInfo->AccountNumber . "',Tier='" . $signupInfo->Tier . "',bankingStatus=" . $bankingStatus . ",signature=" . $signature . ",signatureIpAddress='" . $signatureIpAddress . "',signatureDateTime='" . $signatureDateTime . "'  WHERE 
                        accountUrlPrefix = '" . $accountUrlPrefix . "' AND
                        username='" . $username . "';";
                $updateResult = $this->_primaryDatabase->query($sql, []);
            } else {
                $sql = "INSERT INTO 
                            accounts_users_propay (accountUrlPrefix,username,AccountNumber,Status,Tier,bankingStatus,signature,signatureIpAddress,signatureDateTime) VALUES 
                            ('" . $accountUrlPrefix  . "','" . $username . "','" .
                            $signupInfo->AccountNumber . "','" .
                            $signupInfo->Status . "','" .
                            $signupInfo->Tier . "'," .
                            $bankingStatus . "," .
                            $signature . ",'" .
                            $signatureIpAddress . "','" .
                            $signatureDateTime . "'
                            );";
                $insertResult = $this->_primaryDatabase->query($sql, []);
            }
            $success = true;
        }
        return $success;
    }

    /**
     * @param string $accountUrlPrefix
     * @param int $username
     * @param \stdClass $merchantProfileData
     * @return int - > 1 = ProfileId = success ProfileId Generated, 0 = profile Id was not generated or not saved,
     */
    public function storeMerchantProfile($accountUrlPrefix, $username, $merchantProfileData) {
        if ($merchantProfileData->RequestResult->ResultCode != '00') {
            $status = 0;
        } else {
            $sql = "SELECT * FROM accounts_users_propay WHERE 
                        accountUrlPrefix = '" . $accountUrlPrefix . "' AND
                        username='" . $username . "';";
            /** @var  CI_DB_mysql_result $result */
            $result = $this->_primaryDatabase->query($sql, []);
            if ($result->num_rows() == 1) {
                $sql = "UPDATE accounts_users_propay set ProfileId=" . $merchantProfileData->ProfileId . " WHERE 
                        accountUrlPrefix = '" . $accountUrlPrefix . "' AND
                        username='" . $username . "';";
                $updateResult = $this->_primaryDatabase->query($sql, []);
                $status = $merchantProfileData->ProfileId;
            }
        }

    }

    /**
     * Return the propay signup info if already existing otherwise return false
     * @param string $accountUrlPrefix
     * @param string $username
     * @return bool|object
     */
    public function isSignedUp($accountUrlPrefix, $username) {
        $exists = false;
        $sql = "SELECT * FROM accounts_users_propay WHERE 
                        accountUrlPrefix = '" . $accountUrlPrefix . "' AND
                        username='" . $username . "';";
        /** @var  CI_DB_mysql_result $result */
        $result = $this->_primaryDatabase->query($sql, []);
        if ($result->num_rows() == 1) {
            $databaseRow = (object) $result->_fetch_assoc();
            if ($databaseRow->Status == "00" && $databaseRow->AccountNumber > "0") {
                $exists = $databaseRow;
            }
        }
        return $exists;
    }

    /**
     * @param \stdClass $transactionObject
     * @param int|null $invoiceId
     * @param string $timedPullStatus - i.e. "00" = success
     * @return bool
     */
    public function recordTransaction(
        $transactionObject,
        $payorUsername,
        $payeeUsername,
        $invoiceId = null,
        $timedPullStatus = "00"
    ) {
        $sql = "SELECT * FROM accounts_users_propay_transactions WHERE
                    TransactionId='" . $transactionObject->HostedTransaction->TransactionId  . "'
                    AND payorAccountUrlPrefix='" . $this->_accountUrlPrefix  . "'
                    AND payeeAccountUrlPrefix='" . $this->_accountUrlPrefix  . "'
                    AND payorUsername='" . $payorUsername  . "'
                    AND payeeUsername='" . $payeeUsername  . "'
                    AND HostedTransactionIdentifier='" . $transactionObject->HostedTransaction->HostedTransactionIdentifier  . "';
        ";
        $invoiceId = ($invoiceId == NULL) ? 'NULL' : "'" . $invoiceId . "'";
        /** @var  CI_DB_mysql_result $result */
        $result = $this->_primaryDatabase->query($sql, []);
        if ($result->num_rows() == 1) {
            $sql = "UPDATE accounts_users_propay_transactions SET
                        invoiceNumber=" . $invoiceId . ",
                        TimedPullStatus='" . $timedPullStatus . "' WHERE TransactionId='" . $transactionObject->HostedTransaction->TransactionId  . "';
            ";
            $result = $this->_primaryDatabase->query($sql, []);
        } else {
            //TODO: if we allow across account payments, we need to not use current accountUrlPrefix for both sending and receiving account
            $sql = "INSERT INTO accounts_users_propay_transactions (
                        payorAccountUrlPrefix,
                        payorUsername,
                        payeeAccountUrlPrefix,
                        payeeUsername,
                        invoiceNumber,
                        HostedTransactionIdentifier,
                        GrossAmt,
                        NetAmt,
                        TransactionId,
                        TimedPullStatus,
                        created            
            )       VALUES (
                        '". $this->_accountUrlPrefix . "',
                        '". $payorUsername . "',
                        '".  $this->_accountUrlPrefix . "',
                        '". $payeeUsername . "',
                        ". $invoiceId . ",
                        '". $transactionObject->HostedTransaction->HostedTransactionIdentifier . "',
                        ". $transactionObject->HostedTransaction->GrossAmt . ",
                        ". $transactionObject->HostedTransaction->NetAmt . ",
                        '". $transactionObject->HostedTransaction->TransactionId . "',
                        '". $timedPullStatus . "',
                        '". date("Y-m-d H:i:s") . "'
            );";
            $result = $this->_primaryDatabase->query($sql, []);
        }
        return $transactionObject->HostedTransaction->TransactionId;
    }

	/**
	 * @param \stdClass $transactionObject
	 * @param string $payorUsername
	 * @param int|null $invoiceId
	 *
	 * @return mixed
	 */
	//TODO: we may want to include an actual payee account and payee username when we have cross account payments to each other
    public function recordAccountPayment(
	    $transactionObject,
	    $payorUsername,
	    $invoiceId = null,
	    $starting_plan,
        $ending_plan,
        $plan_change_status,
        $user_count,
        $change_reason
    ) {
	    $sql = "INSERT INTO accounts_payments (
                        payorAccountUrlPrefix,
                        payorUsername,
                        payeeAccountUrlPrefix,
                        payeeUsername,
                        invoiceNumber,
                        HostedTransactionIdentifier,
                        GrossAmt,
                        NetAmt,
                        TransactionId,
                        PaymentMethodID,
                        created,
                        updated,
                        starting_plan,
                        ending_plan,
                        plan_change_status,
                        user_count,
                        change_reason            
            )       VALUES (
                        '". $this->_accountUrlPrefix . "',
                        '". $payorUsername . "',
                        'spera',
                        'spera',
                        ". $invoiceId . ",
                        '". $transactionObject->HostedTransaction->HostedTransactionIdentifier . "',
                        ". $transactionObject->HostedTransaction->GrossAmt . ",
                        ". $transactionObject->HostedTransaction->NetAmt . ",
                        '". $transactionObject->HostedTransaction->TransactionId . "',
                        '". $transactionObject->PaymentMethodInfo->PaymentMethodID . "',
                        NOW(),
                        NOW(),
                        '" . $starting_plan . "',
                        '" . $ending_plan . "',
                        " . $plan_change_status . ",
                        " . $user_count . ",
                        '" . $change_reason . "'
                        
            );";
	    $result = $this->_primaryDatabase->query($sql, []);
	    return $transactionObject->HostedTransaction->TransactionId;
    }

}
