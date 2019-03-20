<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 1/10/18
 * Time: 10:20 AM
 *
 * //TODO: need to rework this so that it can match code igniter url to work and get these params in
 * call to api example http://spera.spera.io/api/{$table}/{$id}
 *
 * //TODO: this is a working concept / wireframe tat needs to be retrofitted a little to work with code igniter tables.
 */

class rest {

    /**
     * @var string - $_SERVER['REQUEST_METHOD']
     */
    private $_REQUEST_METHOD;

    /**
     * @var string - $_SERVER['PATH_INFO']
     */
    private $_PATH_INFO;

    /**
     * @var string - file_get_contents('php://input')
     */
    private $_INPUT_DATA;

    private $_STATE_MESSAGE = "SUCCESS";

    private $_STATE_CODE = "00";

    /** @var  CI_DB_mysql_driver */
    private $_primaryDatabase;

    /** @var  CI_DB_mysql_driver */
    private $_accountDatabase;

    /** @var  string */
    private $_decryptedString;

    /** @var  string */
    private $_encryptedString;

    /** @var [] array of objects referenced by array key */
    private $_sub_object_list;

    /** @var  crypto */
    private $_crypto;

    /** @var propay_api */
    private $_propay_api;

    /** @var account */
    private $_account;

    /** @var string */
    private $_accountUrlPrefix;

    /** @var protectpayapi */
    private $_protectpayapi;

	/** @var fullpayment */
    private $_fullpayment;

    /** @var platformaws */
    private $_platformaws;

	/** @var  MY_Email */
	private $_email;

	/** @var  CI_Parser */
	private $_parser;

	/** @var CI_Lang */
	private $_lang;

	/** @var  CI_Config */
	private $_config;

	/** @var string  */
	private $_accountSql;

	/** @var MY_Loader */
	private $load;


	/**
     * rest constructor.
     * @param null $init_array
     */
    public function __construct($init_array = NULL) {
        $this->_primaryDatabase = $init_array['primaryDatabase'];
        $this->_accountDatabase = $init_array['accountDatabase'];
        $this->_REQUEST_METHOD = $init_array['requestMethod'];
        $this->_PATH_INFO = $init_array['pathInfo'];
        $this->_INPUT_DATA = $init_array['inputData'];
        $this->_USER = $init_array['user'];
        $this->_sub_object_list = $init_array['sub_object_list'];
        $this->_accountUrlPrefix = $init_array['accountUrlPrefix'];
    }

    /**
     * @return string
     */
    public function getEncryptedString() {
        return $this->_encryptedString;
    }

    /**
     * @return string
     */
    public function getDecryptedString() {
        return $this->_decryptedString;
    }

    private function _check_login(array $request, $method) {
        if(!isset($request['username']) || !isset($request['password'])) {
            $method = 'error';
            $this->_STATE_MESSAGE = 'Login requires username & password params';
            $this->_STATE_CODE = '01'; //  invalid or not enough parameters
        }
        return $method;
    }

    private function _check_user_info(array $request, $method) {
        if(!isset($request['token'])) {
            $method = 'error';
            $this->_STATE_MESSAGE = 'User Info requires token param';
            $this->_STATE_CODE = '01'; //  invalid or not enough parameters
        }
        return $method;
    }

	/**
	 * @param array $request
	 * @param $method
	 *
	 * @return string
	 */
	private function _check_propay_to_propay(array $request, $method) {
		if(!isset($request['token']) ||
		   !isset($request['amount']) ||
		   !isset($request['recAccntNum'])
		) {
			$method = 'error';
			$this->_STATE_MESSAGE = 'Invalid parameter list, one or more params missing. Required: userId,clientId,fiatCurrency,fiatCurrencyAmount,asset,token';
			$this->_STATE_CODE = '01'; //  invalid or not enough parameters
		}
		return $method;
	}

    private function _check_edit_primary_bank_account(array $request, $method) {
	    if(!isset($request['token']) ||
	       !isset($request['accountCountryCode']) ||
	       !isset($request['accountNickName']) ||
	       !isset($request['accountNumber']) ||
	       !isset($request['accountType']) ||
	       !isset($request['officialBankName']) ||
	       !isset($request['routingNumber'])
	    ) {
		    $method = 'error';
		    $this->_STATE_MESSAGE = 'Invalid parameter list, one or more params missing. Required: userId,clientId,fiatCurrency,fiatCurrencyAmount,asset,token';
		    $this->_STATE_CODE = '01'; //  invalid or not enough parameters
	    }
	    return $method;
    }

	/**
	 * @param array $request
	 * @param string $method
	 *
	 * promoCode is optional
	 * address2 is optional
	 *
	 * @return string
	 *
	 */
	private function _check_board(array $request, $method) {
		if(!isset($request['token']) ||
		   !isset($request['username']) ||
		   !isset($request['BankName']) ||
		   !isset($request['RoutingNumber']) ||
		   !isset($request['BankAccountNumber']) ||
		   !isset($request['AccountType']) ||
		   !isset($request['FirstName']) ||
		   !isset($request['LastName']) ||
		   !isset($request['Address1']) ||
		   !isset($request['City']) ||
		   !isset($request['State']) ||
		   !isset($request['Zip']) ||
		   !isset($request['DayPhone']) ||
		   !isset($request['EveningPhone']) ||
		   !isset($request['DateOfBirth']) ||
		   !isset($request['Email']) ||
		   !isset($request['SocialSecurityNumber']) ||
		   !isset($request['Password']) ||
		   !isset($request['signature']) ||
		   !isset($request['planType'])
		) {
			$method = 'error';
			$this->_STATE_MESSAGE = 'Invalid parameter list, one or more params missing. Required: 
			token,username,planType,Password,username,accountUrlPrefix,BankName,RoutingNumber,BankAccountNumber,AccountType,FirstName,LastName,Address1,City,State,Zip,DayPhone,EveningPhone,DateOfBirth,SourceEmail,SocialSecurityNumber,accountType,officialBankName,signature';
			$this->_STATE_CODE = '01'; //  invalid or not enough parameters
		}
		return $method;
	}

	/**
	 * @param array $request
	 * @param string $method
	 *
	 * @return string
	 */
	private function _check_board_propay(array $request, $method) {
		if(!isset($request['token']) ||
		   !isset($request['username']) ||
		   !isset($request['accountUrlPrefix']) ||
		   !isset($request['BankName']) ||
		   !isset($request['RoutingNumber']) ||
		   !isset($request['BankAccountNumber']) ||
		   !isset($request['AccountType']) ||
		   !isset($request['FirstName']) ||
		   !isset($request['LastName']) ||
		   !isset($request['Address1']) ||
		   !isset($request['City']) ||
		   !isset($request['State']) ||
		   !isset($request['Zip']) ||
		   !isset($request['DayPhone']) ||
		   !isset($request['EveningPhone']) ||
		   !isset($request['DateOfBirth']) ||
		   !isset($request['SourceEmail']) ||
		   !isset($request['SocialSecurityNumber']) ||
		   !isset($request['accountType']) ||
		   !isset($request['officialBankName']) ||
		   !isset($request['signature'])
		) {
			$method = 'error';
			$this->_STATE_MESSAGE = 'Invalid parameter list, one or more params missing. Required: 
			token,username,accountUrlPrefix,BankName,RoutingNumber,BankAccountNumber,AccountType,FirstName,LastName,Address1,City,State,Zip,DayPhone,EveningPhone,DateOfBirth,SourceEmail,SocialSecurityNumber,accountType,officialBankName,signature';
			$this->_STATE_CODE = '01'; //  invalid or not enough parameters
		}
		return $method;
	}

	private function _check_edit_secondary_bank_account(array $request, $method) {
		if(!isset($request['token']) ||
		   !isset($request['accountCountryCode']) ||
		   !isset($request['accountNickName']) ||
		   !isset($request['accountNumber']) ||
		   !isset($request['accountType']) ||
		   !isset($request['officialBankName']) ||
		   !isset($request['routingNumber'])
		) {
			$method = 'error';
			$this->_STATE_MESSAGE = 'Invalid parameter list, one or more params missing. Required: userId,clientId,fiatCurrency,fiatCurrencyAmount,asset,token';
			$this->_STATE_CODE = '01'; //  invalid or not enough parameters
		}
		return $method;
	}

	/**
     * @param array $request
     * @param $method
     * @return string
     *
     * Example Params
     *    $request['userId'], //101,
     *    $request['clientId'], //501,
     *    $request['fiatCurrency'], //'USD',
     *    $request['fiatCurrencyAmount'], //2.00 or more,
     *    $request['asset'] //"BTC"
     *
     */
    private function _check_transfer_to_crypto(array $request, $method) {
        if(!isset($request['token']) ||
            !isset($request['userId']) ||
            !isset($request['clientId']) ||
            !isset($request['fiatCurrency']) ||
            !isset($request['fiatCurrencyAmount']) ||
            !isset($request['asset'])
        ) {
            $method = 'error';
            $this->_STATE_MESSAGE = 'Invalid parameter list, one or more params missing. Required: userId,clientId,fiatCurrency,fiatCurrencyAmount,asset,token';
            $this->_STATE_CODE = '01'; //  invalid or not enough parameters
        }
        return $method;
    }

    private function _check_user_balances(array $request, $method) {
        if(!isset($request['token']) ||
            !isset($request['userId'])
        ) {
            $method = 'error';
            $this->_STATE_MESSAGE = 'Invalid parameter list, one or more params missing. Required: userId,token';
            $this->_STATE_CODE = '01'; //  invalid or not enough parameters
        }
        return $method;
    }

    private function _check_user_transactions(array $request, $method) {
	    if(!isset($request['token']) ||
	       !isset($request['userId'])||
	       !isset($request['asset'])
	    ) {
		    $method = 'error';
		    $this->_STATE_MESSAGE = 'Invalid parameter list, one or more params missing. Required: userId,token';
		    $this->_STATE_CODE = '01'; //  invalid or not enough parameters
	    }
	    return $method;
    }

    private function _check_account_balance(array $request, $method) {
	    if(!isset($request['token']) ||
	       !isset($request['accountNum'])
	    ) {
		    $method = 'error';
		    $this->_STATE_MESSAGE = 'Invalid parameter list, one or more params missing. Required: userId,token';
		    $this->_STATE_CODE = '01'; //  invalid or not enough parameters
	    }
	    return $method;
    }

	private function _check_details(array $request, $method) {
		if(!isset($request['token']) ||
		   !isset($request['accountNum'])
		) {
			$method = 'error';
			$this->_STATE_MESSAGE = 'Invalid parameter list, one or more params missing. Required: userId,token';
			$this->_STATE_CODE = '01'; //  invalid or not enough parameters
		}
		return $method;
	}

	private function _check_hid(array $request, $method) {
		if(!isset($request['token']) ||
		   !isset($request['invoiceId']) ||
		   !isset($request['StoreCard']) ||
		   !isset($request['MerchantProfileId']) ||
		   !isset($request['Amount']) ||
		   !isset($request['paymentType'])
		) {
			$method = 'error';
			$this->_STATE_MESSAGE = 'Invalid parameter list, one or more params missing. Required: token, invoiceId, StoreCard, MerchantProfileId, Amount, paymentType';
			$this->_STATE_CODE = '01'; //  invalid or not enough parameters
		}
		return $method;
	}

	private function _check_gethid(array $request, $method) {
		if(!isset($request['token']) ||
		   !isset($request['hid'])
		) {
			$method = 'error';
			$this->_STATE_MESSAGE = 'Invalid parameter list, one or more params missing. Required: token, hid';
			$this->_STATE_CODE = '01'; //  invalid or not enough parameters
		}
		return $method;
	}

	private function _checkRequiredParams(array $request, $method) {
        switch ($method) {
            case 'active':
                break;
            case 'login':
            case 'user_info':
            case 'transfer_to_crypto':
            $code = '$method = $this->_check_' . $method . '($request, $method);';
                eval($code);
                break;
            default:
        }
        return $method;
    }

    private function _error_response() {
        $response = [
            'id' => $this->_PATH_INFO,
            'links' => [
                //todo: link to detailed error information, perhaps usage
            ],
            'response' =>
                [
                    0 =>
                        [
                            'rel' => 'error',
                            'method' => $this->_REQUEST_METHOD,
                            'href' => $this->_PATH_INFO,
                            'expects' =>
                                [
                                    'state' => 'error',
                                    'message' => $this->_STATE_MESSAGE,
                                    'code' => $this->_STATE_CODE
                                ],
                        ],
                ],


        ];
        return $response;
    }

	/**
	 * @return array
	 */
    private function _get_default_response() {
	    $response = [
		    'id' => $this->_PATH_INFO,
		    'links' =>
			    [
				    0 =>
					    [
						    //TODO: run through gravitar
						    'image' => ($this->_USER != null) ? $this->_USER->userpic : 'no-pic.png',
						    'title' => ($this->_USER != null) ? $this->_USER->firstname . ' ' . $this->_USER->lastname : '{firstname} {lastname}',
					    ],
			    ],
		    'operations' =>
			    [
				    0 =>
					    [
						    'rel' => 'status',
						    'method' => $this->_REQUEST_METHOD,
						    'href' => $this->_PATH_INFO,
						    'expects' =>
							    [
							    ]
					    ],
				    1 =>
					    [
						    'rel' => 'login',
						    'method' => $this->_REQUEST_METHOD,
						    'href' => $this->_PATH_INFO,
						    'expects' =>
							    [
								    'method' => 'login',
								    'username' => '{username} or {email}',
								    'password' => '{password}',
							    ],
					    ],
			    ],
	    ];
        return $response;
    }

    /**
     *
     * @param array $request - passed in so this is unit testable
     */
    public function process(array $request)
    {

        $requestMethod = $this->_REQUEST_METHOD;
        $apiRequest = explode('/', trim($this->_PATH_INFO, '/'));

        $method = (!isset($request['method'])) ? 'active' : $request['method'];
        $method = ($this->_checkRequiredParams($request, $method));
        $input = json_decode($this->_INPUT_DATA, true);

        if ($apiRequest[0] == 'api' && $apiRequest[1] == 'rest') {
            switch ($requestMethod) {
                case 'GET':
                    switch ($method) {
                        case 'error':
                            $response = $this->_error_response();
                            break;
                        //push api functions labels that exist into api functions
                        case 'user_info':
                        case 'login':
                        case 'transfer_to_crypto':
	                    case 'user_balances':
	                    case 'user_transactions':
	                    case 'account_balance':
	                    case 'details':
	                    case 'hid':
	                    case 'gethid':
	                    case 'propay_to_propay':
	                    case 'board_propay':
	                    case 'edit_primary_bank_account':
	                    case 'edit_secondary_bank_account':
	                    case 'board':
                            $code = '$response = $this->_' . $method . '($request);';
                            eval($code);
                            break;
                        default:
                            $response = $this->_get_default_response();
                    }
                    break;
                case 'PUT':
	                switch ($method) {
		                case 'propay_to_propay':
			                //$code = '$response = $this->_' . $method . '($request);';
			                //eval($code);
							//TODO:  we have the real execution disabled above, this is a money
							// transfer, we want a spera money transfer token here to prevent
							// unauthorized transfers, just issueing default reponse for now
			                $response = $this->_get_default_response();
			                break;
		                default:
			                $response = $this->_get_default_response();
	                }
                    //$sql = "update `$table` set $set where id=$key";
                    break;
                case 'POST':
                    //$sql = "insert into `$table` set $set";
                    break;
                case 'DELETE':
                    //$sql = "delete `$table` where id=$key";
                    break;
            }


            header('Content-Type: application/json');
            echo json_encode($response);
            die();
        }
        die();
        // get the HTTP method, path and body of the request
        $method = $this->_REQUEST_METHOD;
        $request = explode('/', trim($this->_PATH_INFO, '/'));
        $input = json_decode($this->_INPUT_DATA, true);

        // connect to the mysql database
        //$link = mysqli_connect('localhost', 'user', 'pass', 'dbname');
        //mysqli_set_charset($link, 'utf8');

        // retrieve the table and key from the path
        //$table = preg_replace('/[^a-z0-9_]+/i', '', array_shift($request));
        //$key = array_shift($request) + 0;

        // escape the columns and values from the input object
        //$columns = preg_replace('/[^a-z0-9_]+/i', '', array_keys($input));
        //$values = array_map(function ($value) use ($link) {
        //    if ($value === null) return null;
        //    return mysqli_real_escape_string($link, (string)$value);
        //}, array_values($input));

        // build the SET part of the SQL command
        //$set = '';
        //for ($i = 0; $i < count($columns); $i++) {
        //    $set .= ($i > 0 ? ',' : '') . '`' . $columns[$i] . '`=';
        //    $set .= ($values[$i] === null ? 'NULL' : '"' . $values[$i] . '"');
        //}

        // create SQL based on HTTP method
        switch ($method) {
            case 'GET':
                //$sql = "select * from `$table`" . ($key ? " WHERE id=$key" : '');
                break;
            case 'PUT':
                //$sql = "update `$table` set $set where id=$key";
                break;
            case 'POST':
                //$sql = "insert into `$table` set $set";
                break;
            case 'DELETE':
                //$sql = "delete `$table` where id=$key";
                break;
        }

        // excecute SQL statement
        //$result = mysqli_query($link, $sql);

        // die if SQL statement failed
        //if (!$result) {
        //    http_response_code(404);
        //    die(mysqli_error($link));
        //}

        // print results, insert id or affected row count
        //if ($method == 'GET') {
        //    if (!$key) echo '[';
        //    for ($i = 0; $i < mysqli_num_rows($result); $i++) {
        //        echo ($i > 0 ? ',' : '') . json_encode(mysqli_fetch_object($result));
        //    }
        //    if (!$key) echo ']';
        //} elseif ($method == 'POST') {
        //    echo mysqli_insert_id($link);
        //} else {
        //    echo mysqli_affected_rows($link);
        //}

        // close mysql connection
        //mysqli_close($link);
    }

	/**
	 * @param array $request
	 *
	 * @return array
	 */
    private function _hid(array $request) {
	    if ($this->_validateToken($request['token'])) {
		    $this->_propay_api = $this->_sub_object_list['propay_api'];
		    $this->_protectpayapi = $this->_sub_object_list['protectpayapi'];
		    $this->_account = $this->_sub_object_list['account'];
		    //TODO: this needs to be checking the passed MerchantProfileId and looking it up
		    // to get the accountUrlPrefix and user ID to check is signed up, this is about the
		    // person getting paied not the api account
		    $signedUp = $this->_propay_api->isSignedUp($this->_accountUrlPrefix, $this->_accountUrlPrefix);
		    if ($signedUp) {

			    $StoreCard = (isset($request['StoreCard']) && $request['StoreCard'] == 'true') ? true : false;

			    $invoiceId = $request['invoiceId'];

			    //$invoice = Invoice::find($invoiceId);

			    $applicationEnv = ENVIRONMENT;

			    //$invoiceUser = InvoiceHasUser::find('all',array('conditions' => array('invoice_id=?',$invoiceId)));
			    //if (count($invoiceUser) > 0) {
			    //    $user = User::find($invoiceUser[0]->user_id);
			    //    $signedUp = $this->_propay_api->isSignedUp($this->_accountUrlPrefix, $this->_USER->username);
			    //    $merchantProfileId = ($this->_account->isInvoiceCryptoPayment($invoiceId, $this->_accountUrlPrefix, $this->_USER->username) != false) ? PROTECT_PAY_MERCHANT_PROFILE_ID : $signedUp->ProfileId;
			    //} else {
			    //    $signedUp = $this->_propay_api->isSignedUp($this->_accountUrlPrefix, $this->_accountUrlPrefix);
			    //    $merchantProfileId = ($this->_account->isInvoiceCryptoPayment($invoiceId, $this->_accountUrlPrefix) != false) ? PROTECT_PAY_MERCHANT_PROFILE_ID : $signedUp->ProfileId;
			    //}

			    $merchantProfileId = $request['MerchantProfileId'];
			    if ($merchantProfileId == 'null') $merchantProfileId = null;
			    if ($StoreCard == 'true') $StoreCard = true;

			    $data = [
				    "Amount" => (int) $request['Amount'],  //($invoice->outstanding * 100), //convert to cents
				    "AuthOnly" => false,
				    "AvsRequirementType" => 3,
				    "CardHolderNameRequirementType" => 2,
				    "CssUrl" => "https://spera-" . $applicationEnv . ".s3-us-west-2.amazonaws.com/pmi.css",
				    "CurrencyCode" => "USD",
				    "InvoiceNumber" => $invoiceId,
				    "MerchantProfileId" => ($merchantProfileId != null) ? (int) $merchantProfileId : null,
				    "OnlyStoreCardOnSuccessfulProcess" => $StoreCard,
				    "PayerAccountId" => PROTECT_PAY_PAYER_ACCOUNT_ID,
				    "ProcessCard" => true,
				    "Protected" => false,
				    "SecurityCodeRequirementType" => 1,
				    "StoreCard" => $StoreCard,
			    ];

			    if ($_REQUEST['paymentType'] == 'card') {
				    $data["PaymentTypeId"] = "0";
			    } else if ($_REQUEST['paymentType'] == 'ach') {
				    $data["PaymentTypeId"] = "1";
			    }

			    $result = $this->_protectpayapi
				    ->setApiBaseUrl(PROTECT_PAY_API_BASE_URL)
				    ->setBillerId(PROTECT_PAY_BILLER_ID)
				    ->setAuthToken(PROTECT_PAY_AUTH_TOKEN)
				    ->setHostedTransactionData($data)
				    ->createHostedTransaction()
				    ->getCreatedHostedTransactionInfo();
			    $responseObject = json_decode($result);

			    if ( $responseObject ) {
				    $_SESSION['paymentType'] = $request['paymentType'];
				    $_SESSION['HostedTransactionIdentifier'] = $responseObject->HostedTransactionIdentifier;
			    }
			    $result = [
				    'success'                     => true,
				    'message'                     => 'Hosted Transaction Identifier Created Successfully.',
				    'HostedTransactionIdentifier' => $_SESSION['HostedTransactionIdentifier']
			    ];
			    $userpic =
				    ($this->_USER->userpic != 'no-pic.png')
					    ? 'https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION['accountUrlPrefix'] . '/' . "files/media/" . $this->_USER->userpic
					    : 'https:' . get_gravatar($this->_USER->email);
			    $response = [
				    'id' => $this->_PATH_INFO,
				    'links' => [
					    0 =>
						    [
							    'image' => $userpic,
							    'title' => $this->_USER->firstname . ' ' . $this->_USER->lastname,
						    ],
					    1 =>
						    [
							    'link' => '/api/rest/login?username={username}&password={password}',
							    'title' => 'User login to get token'
						    ],
				    ],
				    'response' =>
					    [
						    0 =>
							    [
								    'rel' => 'response',
								    'method' => $this->_REQUEST_METHOD,
								    'href' => $this->_PATH_INFO . '?method=hid&invoiceId={invoice id}&MerchantProfileId=null&paymentType={card|ach}&StoreCard=true&Amount={amount in cents}&token=pGNUrzKsEs0oVy...',
								    'expects' =>
									    [
										    'method' => 'hid',
										    'token' => '{login token}',
										    'invoiceId' => '{invoiceId}[used to get payment details such as amount and also tag invoice id to payment]',
										    'StoreCard' => '{true||false}',
										    'paymentType' => '{card||ach}',
										    'Amount' => '{amount in cents}'
									    ],
								    'data' => $result
							    ],
					    ],


			    ];
		    } else {
			    $this->_STATE_MESSAGE = 'No merchant account:  account has not signed up or entered a mercant account';
			    $this->_STATE_CODE = '03'; //no merchant account
			    $response = $this->_error_response();
		    }
	    } else {
		    $this->_STATE_MESSAGE = 'Token credentials invalid:  obtain valid non-expired token';
		    $this->_STATE_CODE = '02'; //credentials invalid
		    $response = $this->_error_response();
	    }
	    return $response;
    }

	/**
	 * @param array $request
	 *
	 * @return array
	 */
	private function _gethid(array $request) {
		if ($this->_validateToken($request['token'])) {
			$this->_propay_api = $this->_sub_object_list['propay_api'];
			$this->_protectpayapi = $this->_sub_object_list['protectpayapi'];
			$this->_account = $this->_sub_object_list['account'];
			$result = $this->_protectpayapi
				->setApiBaseUrl(PROTECT_PAY_API_BASE_URL)
				->setBillerId(PROTECT_PAY_BILLER_ID)
				->setAuthToken(PROTECT_PAY_AUTH_TOKEN)
				->setGetHostedTransactionData($request['hid'])
				->getHostedTransaction()
				->getHostedTransactionInfo();
			$responseArray = json_decode($result, true);
			$userpic =
				($this->_USER->userpic != 'no-pic.png')
					? 'https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION['accountUrlPrefix'] . '/' . "files/media/" . $this->_USER->userpic
					: 'https:' . get_gravatar($this->_USER->email);
			$response = [
				'id' => $this->_PATH_INFO,
				'links' => [
					0 =>
						[
							'image' => $userpic,
							'title' => $this->_USER->firstname . ' ' . $this->_USER->lastname,
						],
					1 =>
						[
							'link' => '/api/rest/login?username={username}&password={password}',
							'title' => 'User login to get token'
						],
				],
				'response' =>
					[
						0 =>
							[
								'rel' => 'response',
								'method' => $this->_REQUEST_METHOD,
								'href' => $this->_PATH_INFO . '?method=gethid&token={token}&hid=7a8451...',
								'expects' =>
									[
										'method' => 'gethid',
										'token' => '{login token}',
										'hid' => '{hid}'
									],
								'data' => //$responseArray
									array (
										'HostedTransaction' => (ENVIRONMENT == 'production') ? $responseArray :
											array (
												'CreationDate' => '2016-02-01T16:32:57.9970565',
												'HostedTransactionIdentifier' => $request['hid'],
												'PayerId' => 1045899410011966,
												'TransactionResultMessage' => '',
												'AuthCode' => 'A11111',
												'TransactionHistoryId' => 8299869,
												'TransactionId' => '338',
												'TransactionResult' => '00',
												'AvsResponse' => 'T',
												'PaymentMethodInfo' =>
													array (
														'PaymentMethodID' => '48a5bf91-a076-4719-9615-d1dc630e39ca',
														'PaymentMethodType' => 'Visa',
														'ObfuscatedAccountNumber' => '474747******4747',
														'ExpirationDate' => '0117',
														'AccountName' => 'JohnSmith',
														'BillingInformation' =>
															array (
																'Address1' => '3400N.AshtonBlvd',
																'Address2' => 'Suite200',
																'Address3' => '',
																'City' => 'Lehi',
																'State' => 'UT',
																'ZipCode' => '84043',
																'Country' => 'USA',
																'TelephoneNumber' => '',
																'Email' => '',
															),
														'Description' => '',
														'Priority' => 55,
														'DateCreated' => '2016-02-01T16:32:56.307',
														'Protected' => true,
													),
												'GrossAmt' => 1000,
												'NetAmt' => 948,
												'PerTransFee' => 25,
												'Rate' => 2.69,
												'GrossAmtLessNetAmt' => 52,
												'CVVResponseCode' => 'M',
												'CurrencyConversionRate' => 1,
												'CurrencyConvertedAmount' => 1000,
												'CurrencyConvertedCurrencyCode' => 840,
											),
										'Result' =>
											array (
												'ResultValue' => 'SUCCESS',
												'ResultCode' => '00',
												'ResultMessage' => '',
											),
									)
							],
					],


			];
		} else {
			$this->_STATE_MESSAGE = 'Token credentials invalid:  obtain valid non-expired token';
			$this->_STATE_CODE = '02'; //credentials invalid
			$response = $this->_error_response();
		}
		return $response;
	}

	/**
	 * @param array $request
	 *
	 * 	 * success gives xml response like
	 * 		    <XMLResponse>
	 *<XMLTrans>
	 *<transType>13</transType>
	 *<accountNum>1148111</accountNum>
	 *<tier>Premium</tier>
	 *<expiration>11/27/2025 12:00:00 AM</expiration>
	 *<signupDate>4/17/2008 3:17:00 PM</signupDate>
	 *<affiliation>SRKUUW9 </affiliation>
	 *<accntStatus>Ready</accntStatus>
	 *<addr>123 Anywhere St</addr>
	 *<city>Lehi</city>
	 *<state>UT</state>
	 *<zip>84043</zip>
	 *<status>00</status>
	 *<apiReady>Y</apiReady>
	 *<currencyCode>USD</currencyCode>
	 *<CreditCardTransactionLimit>65000</CreditCardTransactionLimit>
	 *<CreditCardMonthLimit>250000</CreditCardMonthLimit>
	 *<ACHPaymentPerTranLimit>1000</ACHPaymentPerTranLimit>
	 *<ACHPaymentMonthLimit>5000</ACHPaymentMonthLimit>
	 *<CreditCardMonthlyVolume>0</CreditCardMonthlyVolume>
	 *<ACHPaymentMonthlyVolume>0</ACHPaymentMonthlyVolume>
	 *<ReserveBalance>0</ReserveBalance>
	 *</XMLTrans>
	 *</XMLResponse>
	 *
	 * not authorized returns
	 * array (
	 *'XMLTrans' =>
	 *array (
	 *'transType' => '13',
	 *'status' => '59', // not authorized
	 *),
	 *)
	 *
	 *
	 * @return array
	 */
    private function _details(array $request) {
	    if ($this->_validateToken($request['token'])) {
		    $this->_propay_api = $this->_sub_object_list['propay_api'];
		    $signedUp = $this->_propay_api->isSignedUp($this->_accountUrlPrefix, $this->_accountUrlPrefix);
		    if ($signedUp) {

			    $this->_account = $this->_sub_object_list['account'];

			    $data      = "<?xml version='1.0'?>
            <!DOCTYPE Request.dtd>
            <XMLRequest>
            </XMLRequest>";
			    $simpleXML = new \SimpleXMLElement( $data );
			    $simpleXML->addChild( 'certStr', PROPAY_CERT_STRING );
			    $simpleXML->addChild( 'class', 'partner' );
			    $simpleXML->addChild( 'XMLTrans' );
			    $simpleXML->XMLTrans->addChild( 'transType', 13 );
			    $simpleXML->XMLTrans->addChild( 'accountNum', $signedUp->AccountNumber);//32291150 );
			    $result =
				    $this->_propay_api->setXMLUrl( PROPAY_API_XML_URL )
				                      ->setXMLRequestData( $simpleXML->asXML() )
				                      ->postXML()
				                      ->getXMLRequestObject()->asXML();

			    $result = json_encode($this->_propay_api->getXMLResponseObject(),true);

			    $userpic =
				    ($this->_USER->userpic != 'no-pic.png')
					    ? 'https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION['accountUrlPrefix'] . '/' . "files/media/" . $this->_USER->userpic
					    : 'https:' . get_gravatar($this->_USER->email);
			    $response = [
				    'id' => $this->_PATH_INFO,
				    'links' => [
					    0 =>
						    [
							    'image' => $userpic,
							    'title' => $this->_USER->firstname . ' ' . $this->_USER->lastname,
						    ],
					    1 =>
						    [
							    'link' => '/api/rest/login?username={username}&password={password}',
							    'title' => 'User login to get token'
						    ],
				    ],
				    'response' =>
					    [
						    0 =>
							    [
								    'rel' => 'response',
								    'method' => $this->_REQUEST_METHOD,
								    'href' => $this->_PATH_INFO . '?method=details&token={login_token}',
								    'expects' =>
									    [
										    'method' => 'details',
										    'token' => '{login token}'
									    ],
								    'data' => json_decode($result,true)
							    ],
					    ],


			    ];
		    } else {
			    $this->_STATE_MESSAGE = 'No merchant account:  account has not signed up or entered a mercant account';
			    $this->_STATE_CODE = '03'; //no merchant account
			    $response = $this->_error_response();
		    }
	    } else {
		    $this->_STATE_MESSAGE = 'Token credentials invalid:  obtain valid non-expired token';
		    $this->_STATE_CODE = '02'; //credentials invalid
		    $response = $this->_error_response();
	    }

	    return $response;
    }

	/**
	 * @param array $request
	 *
	 * success gives xml response like
	 * 		    <XMLResponse>
	 *<XMLTrans>
	 *<transType>14</transType>
	 *<accountNum>123456</accountNum>
	 *<status>00</status>
	 *<amount>10000</amount>
	 *<pendingAmount>15300</pendingAmount>
	 *</XMLTrans>
	 *</XMLResponse>
	 *
	 * not authorized returns
	 * array (
	 *'XMLTrans' =>
	 *array (
	 *'transType' => '14',
	 *'status' => '59', // not authorized
	 *),
	 *)
	 *
	 * @return array
	 */
    private function _account_balance(array $request) {
	    if ($this->_validateToken($request['token'])) {
		    $this->_propay_api = $this->_sub_object_list['propay_api'];
		    $signedUp = $this->_propay_api->isSignedUp($this->_accountUrlPrefix, $this->_accountUrlPrefix);
		    if ($signedUp) {

			    $this->_account = $this->_sub_object_list['account'];

			    $data      = "<?xml version='1.0'?>
            <!DOCTYPE Request.dtd>
            <XMLRequest>
            </XMLRequest>";
			    $simpleXML = new \SimpleXMLElement( $data );
			    $simpleXML->addChild( 'certStr', PROPAY_CERT_STRING );
			    $simpleXML->addChild( 'class', 'partner' );
			    $simpleXML->addChild( 'XMLTrans' );
			    $simpleXML->XMLTrans->addChild( 'transType', 14 );
			    $simpleXML->XMLTrans->addChild( 'accountNum', $signedUp->AccountNumber);//32291150 );
			    $result =
				    $this->_propay_api->setXMLUrl( PROPAY_API_XML_URL )
				                      ->setXMLRequestData( $simpleXML->asXML() )
				                      ->postXML()
				                      ->getXMLRequestObject()->asXML();

			    $result = json_encode($this->_propay_api->getXMLResponseObject(),true);

			    $userpic =
				    ($this->_USER->userpic != 'no-pic.png')
					    ? 'https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION['accountUrlPrefix'] . '/' . "files/media/" . $this->_USER->userpic
					    : 'https:' . get_gravatar($this->_USER->email);
			    $response = [
				    'id' => $this->_PATH_INFO,
				    'links' => [
					    0 =>
						    [
							    'image' => $userpic,
							    'title' => $this->_USER->firstname . ' ' . $this->_USER->lastname,
						    ],
					    1 =>
						    [
							    'link' => '/api/rest/login?username={username}&password={password}',
							    'title' => 'User login to get token'
						    ],
				    ],
				    'response' =>
					    [
						    0 =>
							    [
								    'rel' => 'response',
								    'method' => $this->_REQUEST_METHOD,
								    'href' => $this->_PATH_INFO . '?method=account_balance&token={login_token}',
								    'expects' =>
									    [
										    'method' => 'account_balance',
										    'token' => '{login token}'
									    ],
								    'data' => json_decode($result,true)
							    ],
					    ],


			    ];
		    } else {
			    $this->_STATE_MESSAGE = 'No merchant account:  account has not signed up or entered a mercant account';
			    $this->_STATE_CODE = '03'; //no merchant account
			    $response = $this->_error_response();
		    }
	    } else {
		    $this->_STATE_MESSAGE = 'Token credentials invalid:  obtain valid non-expired token';
		    $this->_STATE_CODE = '02'; //credentials invalid
		    $response = $this->_error_response();
	    }

	    return $response;
    }

	/**
	 * @param string $username
	 *
	 * @return string
	 */
    private function _createAndPopulateNewAccountDatabase($username) {
	    $databasePrefix = $this->_populateBoardingS3Folder($username);

	    $databaseName = $databasePrefix . '_' . ENVIRONMENT;

	    $sql = "DROP DATABASE IF EXISTS " . $databaseName . ";";
	    $this->_primaryDatabase->query( $sql, [] );

	    $sql = "CREATE DATABASE " . $databaseName . ";";
	    $this->_primaryDatabase->query( $sql, [] );

	    /** @var CI_DB_mysql_driver $accountDatabase */
	    $this->_accountDatabase = $this->load->database( $databaseName, true );

	    $this->_account
		    ->setAccountDatabase( $this->_accountDatabase )
		    ->setDatabaseName( $databaseName )
		    ->setAccountSql( $this->_accountSql );

	    $response = $this->_account->create();
	    return $databasePrefix;
    }

	/**
	 * @param array $request
	 */
    private function _sendSignupNotification($request, $databasePrefix) {
    	$core_settings = Setting::first();

	    $from_email = EMAIL_FROM; //$core_settings->email
	    $this->_email->from( $from_email, $core_settings->company );
	    $this->_email->to( trim( htmlspecialchars( $request['Email'] ) ) );

	    $this->_email->subject( $this->_lang->line( 'application_your_account_has_been_created' ) );
	    $accountName = strtolower( str_replace( ' ', '', trim( htmlspecialchars( $request['username'] ) ) ) );
	    $domainParts = explode( '.', $_SERVER['HTTP_HOST'] );
	    $domain      = $domainParts[ count( $domainParts ) - 2 ] . '.' . $domainParts[ count( $domainParts ) - 1 ];
	    $parse_data = array(
		    'link'              => 'https://' . $accountName . '.' . $domain . '/login',
		    'company'           => $core_settings->company,
		    'client_company'    => $databasePrefix, //trim( htmlspecialchars( $request['name'] ) ),
		    'company_reference' => '',
		    // $company->reference, //TODO: find out what this is, and substitute something meaningful here
		    'logo'              => '<img style="max-height: 50px; max-width: 200px; width: auto;" src="https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/spera/' . $core_settings->logo . '" alt="' . $core_settings->company . '"/>',
		    'invoice_logo'      => '<img style="max-height: 50px; max-width: 200px; width: auto;" src="https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/spera/' . $core_settings->invoice_logo . '" alt="' . $core_settings->company . '"/>'
	    );
	    $email      = read_file( './application/views/' . $core_settings->template . '/templates/email_create_account.html' );
	    $message    = $this->_parser->parse_string( $email, $parse_data );
	    $this->_email->message( $message );
	    $this->_email->set_smtp_conn_options(
		    [
			    'ssl' => [
				    'verify_peer' => false,
				    'verify_peer_name' => false,
				    'allow_self_signed' => true
			    ]
		    ]
	    );
	    $this->_email->send();
	    send_notification( $core_settings->email, $this->lang->line( 'application_new_client_has_registered' ), $this->lang->line( 'application_new_client_has_registered' ) .
	                                                                                                            ': <br><strong>' . $databasePrefix . '</strong><br>' .
	                                                                                                            trim( htmlspecialchars( $request['FirstName'] ) ) . ' ' . trim( htmlspecialchars( $request['LastName'] ) ) . '<br>' . trim( htmlspecialchars( $request['Email'] ) ) );

    }

    private function _board_propay(array $request) {
	    if ($this->_validateToken($request['token'])) {
		    $this->_propay_api = $this->_sub_object_list['propay_api'];
		    $this->_account = $this->_sub_object_list['account'];
		    $this->_parser = $this->_sub_object_list['parser'];
		    $this->_lang = $this->_sub_object_list['lang'];
		    $this->_platformaws = $this->_sub_object_list['platformaws'];
		    $this->_protectpayapi = $this->_sub_object_list['protectpayapi'];
		    $this->_email = $this->_sub_object_list['email'];

		    $request['Address2'] = (isset($request['Address2'])) ? $request['Address2'] : '';
		    $data = [
			    "PersonalData" => [
				    "SourceEmail"          => trim( htmlspecialchars( $request['SourceEmail'] ) ),
				    "FirstName"            => trim( htmlspecialchars( $request['FirstName'] ) ),
				    "LastName"             => trim( htmlspecialchars( $request['LastName'] ) ),
				    "DateOfBirth"          => trim( htmlspecialchars( $request['DateOfBirth'] ) ),
				    //10 required 1/19/1997
				    "SocialSecurityNumber" => trim( htmlspecialchars( $request['SocialSecurityNumber'] ) ),
				    "PhoneInformation"     => [
					    "DayPhone"     => trim( htmlspecialchars( $request['DayPhone'] ) ), //10 required
					    "EveningPhone" => trim( htmlspecialchars( $request['EveningPhone'] ) ), //10 required
				    ]
			    ],

			    "SignupAccountData" => [
				    "Tier" => "", // required '' = lowest cost 'Premium', 'Merchant' etc
			    ],

			    "Address" => [
				    "ApartmentNumber" => null,
				    "Address1"        => trim( htmlspecialchars( $request['Address1'] ) ),
				    "Address2"        => trim( htmlspecialchars( $request['Address2'] ) ),
				    "City"            => trim( htmlspecialchars( $request['City'] ) ),
				    "State"           => trim( htmlspecialchars( $request['State'] ) ),
				    "Country"         => "USA",
				    "Zip"             => trim( htmlspecialchars( $request['Zip'] ) )
			    ],
		    ];

		    if ( isset( $request['BankAccountNumber'] ) && isset( $request['BankName'] ) && isset( $request['RoutingNumber'] ) &&
		         trim( htmlspecialchars( $request['AccountType'] ) ) &&
		         trim( htmlspecialchars( $request['BankName'] ) ) &&
		         trim( htmlspecialchars( $request['BankAccountNumber'] ) ) &&
		         trim( htmlspecialchars( $request['RoutingNumber'] ) )
		    ) {
			    $data["BankAccount"] = [
				    "AccountCountryCode"   => "USA",
				    "AccountOwnershipType" => "Personal",
				    "AccountType"          => trim( htmlspecialchars( $request['AccountType'] ) ),
				    "BankAccountNumber"    => trim( htmlspecialchars( $request['BankAccountNumber'] ) ),
				    "BankName"             => trim( htmlspecialchars( $request['BankName'] ) ),
				    "RoutingNumber"        => trim( htmlspecialchars( $request['RoutingNumber'] ) )
			    ];
		    }

		    $result = $this->_propay_api
			    ->setApiBaseUrl( explode( "/ProtectPay", PROTECT_PAY_API_BASE_URL )[0] )
			    ->setCertStr( PROPAY_CERT_STRING )
			    ->setTermId( PROTECT_PAY_TERM_ID )
			    ->setSignupData( $data )
			    ->processSignup()
			    ->getSignupInfo();

		    $signupInfo = json_decode( $result );
		    $response = $result;
		    if ( $signupInfo->AccountNumber != 0 ) {

			    $signature = trim( htmlspecialchars( $request['signature'] ) );

			    $storeSignupInfoStatus = $this->_propay_api->storeSignupInfo(
				    $request['accountUrlPrefix'],
				    $request['username'],
				    $signature,
				    $_SERVER['REMOTE_ADDR'],
				    date( "Y-m-d H:i:s" )
			    );

			    $merchantProfileData = [
				    'ProfileName'      => substr( $request['accountUrlPrefix'] . '-' . $request['username'] . '-' . $signupInfo->AccountNumber, 0, 50 ),
				    'PaymentProcessor' => 'LegacyProPay',
				    'ProcessorData'    =>
					    [

						    [
							    'ProcessorField' => 'certStr',
							    'Value'          => PROPAY_CERT_STRING,
						    ],

						    [
							    'ProcessorField' => 'accountNum',
							    'Value'          => $signupInfo->AccountNumber,
						    ],

						    [
							    'ProcessorField' => 'termId',
							    'Value'          => PROTECT_PAY_TERM_ID,
						    ]
					    ]
			    ];
			    $merchantProfileResponse    = $this->_protectpayapi
				    ->setApiBaseUrl( PROTECT_PAY_API_BASE_URL )
				    ->setBillerId( PROTECT_PAY_BILLER_ID )
				    ->setAuthToken( PROTECT_PAY_AUTH_TOKEN )
				    ->createMerchantProfile( $merchantProfileData );
			    $merchantProfileData        = json_decode( $merchantProfileResponse );
			    $storeMerchantProfileStatus = $this->_propay_api->storeMerchantProfile(
				    $request['accountUrlPrefix'],
				    $request['username'],
				    $merchantProfileData
			    );


			    $this->_sendPropaySignupNotification($signupInfo);
			    unset($data["PersonalData"]["SocialSecurityNumber"]);
			    $this->_account->logApiCall($request['accountUrlPrefix'] , $request['username'], 'propay_signup', json_encode($data), $merchantProfileResponse);
		    } else {
			    //TODO: we need to handle the case where the account already exists.
			    switch ( $signupInfo->Status ) {
				    case '00':
					    break;
				    case '32':
					    //$this->view_data['error']        = "Invalid Zip Code";
					    //$this->view_data['registerdata'] = array_map( 'htmlspecialchars', $_POST );
					    //deal with account alread exists case
					    break;
				    case '87':
					    //$this->view_data['error']        = "Email address is already signed up for a payment account!";
					    //$this->view_data['registerdata'] = array_map( 'htmlspecialchars', $_POST );
					    //deal with account alread exists case
					    break;
				    default:
					    //$this->view_data['error']        = "Unknown error trying to signup! " . var_export( $signupInfo, true );
					    //$this->view_data['registerdata'] = array_map( 'htmlspecialchars', $_POST );
				    //stdClass::__set_state(array( 'AccountNumber' => 0, 'Password' => NULL, 'SourceEmail' => NULL, 'Status' => '59', 'Tier' => NULL, ))stdClass::__set_state(array( 'AccountNumber' => 0, 'Password' => NULL, 'SourceEmail' => NULL, 'Status' => '59', 'Tier' => NULL, ))
			    }
		    }

	    } else {
		    $this->_STATE_MESSAGE = 'Token credentials invalid:  obtain valid non-expired token';
		    $this->_STATE_CODE = '02'; //credentials invalid
		    $response = $this->_error_response();
	    }
	    return $response;
    }

    private function _sendPropaySignupNotification($signupInfo) {
	    $from_email = EMAIL_FROM; //$core_settings->email
	    $this->_email->from( $from_email, 'Spera' );
	    $this->_email->to( $signupInfo->SourceEmail );

	    //TODO: translate this
	    //$this->email->subject($this->lang->line('application_your_account_has_been_created'));
	    $this->_email->subject( 'Your payment account has been created.' );
	    $this->_email->message( '<br>Please refer to your email to set your payment password' .
	                            '<br><br>Your account number is :' . $signupInfo->AccountNumber .
	                            '<br>Your temporary password is ' . $signupInfo->Password .
	                            '<br>You can login to your payment account at https://www.propay.com using your email: ' . $signupInfo->SourceEmail
	    );
	    $this->_email->send();

    }

    private function _board(array $request) {
	    if ($this->_validateToken($request['token'])) {
	    	$this->_propay_api = $this->_sub_object_list['propay_api'];
		    $this->_account = $this->_sub_object_list['account'];
		    $this->_parser = $this->_sub_object_list['parser'];
		    $this->_lang = $this->_sub_object_list['lang'];
		    $this->_config = $this->_sub_object_list['config'];
		    $this->_platformaws = $this->_sub_object_list['platformaws'];
		    $this->_protectpayapi = $this->_sub_object_list['protectpayapi'];
		    $this->_email = $this->_sub_object_list['email'];
		    $this->load = $this->_sub_object_list['load'];

		    $request['accountUrlPrefix'] = (isset($request['accountUrlPrefix'])) ? $request['accountUrlPrefix'] : $request['username'];

		    $boardingType = isset($request['boardingType']) ? (int) $request['boardingType'] : 2;
            $username = $request['username'];

            if ($boardingType == 1) {
	            /** @var CI_DB_mysql_driver $accountDatabase */
	            $databasePrefix = $this->_createAndPopulateNewAccountDatabase($username);
	            $isSperaApp = false;
            } else {
	            $databasePrefix = strtolower( str_replace( ' ', '', trim( htmlspecialchars( $username ) ) ) );
	            if ( in_array( substr( $databasePrefix, 0, 1 ), explode( ',', '0,1,2,3,4,5,6,7,8,9' ) ) ) {
		            $databasePrefix = 'z' . $databasePrefix;
	            }
	            $isSperaApp = true;
            }

		    $databaseName = $databasePrefix . '_' . ENVIRONMENT;

		    $signupData = [
			    'Username'                => $username,
			    'Firstname'               => trim( htmlspecialchars( $request['FirstName'] ) ),
			    'Lastname'                => trim( htmlspecialchars( $request['LastName'] ) ),
			    'Password'                => $request['Password'],
			    'Email'                   => trim( htmlspecialchars( $request['Email'] ) ),
			    'AccountName'             => $databasePrefix, //trim( htmlspecialchars( $_POST['name'] ) ),
			    //could be company name or individual name here
			    'AccountContactFirstName' => trim( htmlspecialchars( $request['FirstName'] ) ),
			    'AccountContactLastName'  => trim( htmlspecialchars( $request['LastName'] ) ),
		    ];

		    if(isset($request['promoCode'])) $signupData['promoCode'] = $request['promoCode'];

		    unset( $_SESSION['accountDatabasePrefix'] );

		    $response = [
			    'id' => $this->_PATH_INFO,
			    'links' => [
				    0 =>
					    [
						    'image' => $this->_USER->userpic,
						    'title' => $this->_USER->firstname . ' ' . $this->_USER->lastname,
					    ],
				    1 =>
					    [
						    'link' => '/api/rest/login?username={username}&password={password}',
						    'title' => 'User login to get token'
					    ],
			    ],
			    'response' =>
				    [
					    0 =>
						    [
							    'rel' => 'response',
							    'method' => $this->_REQUEST_METHOD,
							    'href' => $this->_PATH_INFO . '?method=board&Email=dhogan@spera.io...',
							    'expects' =>
								    [
									    'method' => 'board',
									    'Email' => '{email address}',
									    'token' => '{login token}'
								    ],
							    //TODO: get real propay boarding here
							    'data' => [
								    'AccountNumber' => 123456,
								    'Password' => 'TempPassw0rd ',
								    'SourceEmail' => $_REQUEST['Email'],
								    'Status' => '00',
								    'Tier' => 'Business',
								    'ProfileId' => null
							    ]
						    ],
				    ],


		    ];

            if (ENVIRONMENT == 'production') {

	            //TODO: need to signup someone with active = 2 if they are a Spera App account and not try to update thier
	            //database settings, which won't exist, if they are
	            if ( ! $this->_account->signup( $username, $databaseName, $signupData , $isSperaApp) ) {
		            $this->_STATE_MESSAGE = 'Spera Account name is already taken!';
		            $this->_STATE_CODE    = '04'; //credentials invalid
		            $response             = $this->_error_response();
	            } else {
		            if ( isset( $request['planType'] ) ) {
			            $this->_account->storeAccountPlan( trim( htmlspecialchars( $request['planType'] ) ) );
		            }
		            $this->_sendSignupNotification( $request, $databasePrefix );

		            //TODO: change response data to be propay boarding response here
		            if (ENVIRONMENT != 'production') {
			            $response['response'][0]['data'] = [
				            'AccountNumber' => 123456,
				            'Password'      => 'TempPassw0rd ',
				            'SourceEmail'   => $_REQUEST['Email'],
				            'Status'        => '00',
				            'Tier'          => 'Business',
				            'ProfileId'     => null
			            ];
		            }  else {
			            $response['response'][0]['data'] = $this->_board_propay($request);
		            }
	            }
            }
	    } else {
		    $this->_STATE_MESSAGE = 'Token credentials invalid:  obtain valid non-expired token';
		    $this->_STATE_CODE = '02'; //credentials invalid
		    $response = $this->_error_response();
	    }

	    return $response;
    }

	/**
	 * @param string $username
     *
	 * @return string - resolved database previx possibly with z preceding if account url starts with number
	 */
    private function _populateBoardingS3Folder($username) {
	    $environment = ENVIRONMENT;
	    $bucket      = "spera-" . $environment;
	    $keyname     = "platform_312_default.min.mysql";

	    /** @var Aws\S3\S3Client $s3 */
	    $s3 = $this->_platformaws->getS3Client();

	    try {
		    $this->_accountSql = (string) $this->_platformaws->getObject( $bucket, $keyname )['Body'];
	    } catch ( Exception $e ) {
		    echo $e->getMessage() . "\n";
		    die();
	    }

	    /** @var Aws\S3\S3Client $s3 */
	    $s3 = $this->_platformaws->getS3Client();

	    try {
		    $accountSql = (string) $this->_platformaws->getObject( $bucket, $keyname )['Body'];
	    } catch ( Exception $e ) {
		    echo $e->getMessage() . "\n";
		    die();
	    }

	    $databasePrefix = strtolower( str_replace( ' ', '', trim( htmlspecialchars( $username ) ) ) );

	    if ( in_array( substr( $databasePrefix, 0, 1 ), explode( ',', '0,1,2,3,4,5,6,7,8,9' ) ) ) {
		    $databasePrefix = 'z' . $databasePrefix;
	    }

	    return $databasePrefix;
    }

    /**
     * Return all user coinbase cryptocurrency balances
     * @param array $request
     * @return array
     */
    private function _user_balances(array $request) {
        $this->_crypto = $this->_sub_object_list['crypto'];
        if ($this->_validateToken($request['token'])) {
            $result = json_encode($this->_crypto->DisplayDashBoard($request['userId']));
            if(json_decode($result) == null) {
                $result = '{"type":"error","message":"' . $result . '","response":"' . $this->_crypto->DisplayDashBoard($request['userId']) . '"}';
            }
            $userpic =
                ($this->_USER->userpic != 'no-pic.png')
                    ? 'https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION['accountUrlPrefix'] . '/' . "files/media/" . $this->_USER->userpic
                    : 'https:' . get_gravatar($this->_USER->email);

            $response = [
                'id' => $this->_PATH_INFO,
                'links' => [
                    0 =>
                        [
                            'image' => $userpic,
                            'title' => $this->_USER->firstname . ' ' . $this->_USER->lastname,
                        ],
                    1 =>
                        [
                            'link' => '/api/rest/login?username={username}&password={password}',
                            'title' => 'User login to get token'
                        ],
                ],
                'response' =>
                    [
                        0 =>
                            [
                                'rel' => 'response',
                                'method' => $this->_REQUEST_METHOD,
                                'href' => $this->_PATH_INFO . '?method=user_balances&userId={coinbaseUserId}&token={login_token}',
                                'expects' =>
                                    [
                                        'method' => 'user_balances',
                                        'userId' => '{coinbase user id i.e. 101 etc}',
                                        'token' => '{login token}'
                                    ],
                                'data' => json_decode($result,true)
                            ],
                    ],


            ];
            return $response;
        } else {
            $this->_STATE_MESSAGE = 'Token credentials invalid:  obtain valid non-expired token';
            $this->_STATE_CODE = '02'; //credentials invalid
            $response = $this->_error_response();
        }

        return $response;
    }

    /**
     * @param $token
     * @return array|bool|mixed|object
     */
    private function _validateToken($token) {
	    $returnValue = false;
        $this->decryptString($token);
	    error_reporting(0);
        $tokenParts = json_decode($this->_decryptedString);
	    error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	    if ($tokenParts) {
		    $this->_USER = User::validate_token($tokenParts->password, $tokenParts->username, $tokenParts->expires);
		    if ($tokenParts->password == $this->_USER->hashed_password && $tokenParts->expires > time()) {
			    $returnValue = clone $tokenParts;
		    }
	    } else {
		    $this->_USER = null;
	    }
        return $returnValue;
    }

    private function _getDefaultLinks() {
        $userpic =
            ($this->_USER->userpic != 'no-pic.png')
                ? 'https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION['accountUrlPrefix'] . '/' . "files/media/" . $this->_USER->userpic
                : 'https:' . get_gravatar($this->_USER->email);

        return [
            0 =>
                [
                    'image' => $userpic,
                    'title' => $this->_USER->firstname . ' ' . $this->_USER->lastname,
                ],
            1 =>
                [
                    'link' => '/api/rest/login?username={username}&password={password}',
                    'title' => 'User login to get token'
                ],
        ];
    }

	/**	 * Sample JSON request data:
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
	 * @param array $request
	 * @return array
	 */
    private function _propay_to_propay(array $request) {
    	$invoiceNumber = (isset($request['invNum'])) ? $request['invNum'] : '';
	    if ($this->_validateToken($request['token'])) {
		    $data = $this->_propay_api->setPropayToPropayData([
			    'amount' => $request['amount'],
			    'invNum' => $invoiceNumber,
			    'recAccntNum' => $request['recAccntNum']
		    ])->processPropayToPropayTransfer()->getPropayToPropayInfo();
	    } else {
		    $this->_STATE_MESSAGE = 'Token credentials invalid:  obtain valid non-expired token';
		    $this->_STATE_CODE = '02'; //credentials invalid
		    $response = $this->_error_response();
	    }

	    return $response;
    }

	/**
	 * @param array $request
	 *
	 * @return array
	 */
    private function _edit_primary_bank_account(array $request) {
	    if ($this->_validateToken($request['token'])) {
		    $this->_fullpayment = $this->_sub_object_list['fullpayment'];
		    $data = $this->_fullpayment->editPrimaryBankAccount(
			    $request['accountCountryCode'],
			    $request['accountNickName'],
			    $request['accountNumber'],
			    $request['accountType'],
			    $request['officialBankName'],
			    $request['routingNumber']);
		    $result = json_decode($data, true);
		    $response = [
			    'id' => $this->_PATH_INFO,
			    'links' => $this->_getDefaultLinks(),
			    'response' =>
				    [
					    0 =>
						    [
							    'rel' => 'edit_primary_bank_account',
							    'method' => $this->_REQUEST_METHOD,
							    'href' => $this->_PATH_INFO,
							    'expects' =>
								    [
									    'method' => 'edit_primary_bank_account',
									    'accountCountryCode' => '{USA}',
									    'accountNickName' => '{MyBankAccount}',
									    'accountNumber' => '{bank account number}',
									    'accountType' => '{C -  for checking}',
									    'officialBankName' => '{i.e. Wells Fargo Inc}',
									    'routingNumber' => '{routing number}',
								    ],
							    'data' => $result
						    ],
				    ],
		    ];
		    return $response;

	    } else {
		    $this->_STATE_MESSAGE = 'Token credentials invalid:  obtain valid non-expired token';
		    $this->_STATE_CODE = '02'; //credentials invalid
		    $response = $this->_error_response();
	    }
    }

	/**
	 * @param array $request
	 *
	 * @return array
	 */
	private function _edit_secondary_bank_account(array $request) {
		if ($this->_validateToken($request['token'])) {
			$this->_fullpayment = $this->_sub_object_list['fullpayment'];
			$data = $this->_fullpayment->editSecondaryBankAccount(
				$request['accountCountryCode'],
				$request['accountNickName'],
				$request['accountNumber'],
				$request['accountType'],
				$request['officialBankName'],
				$request['routingNumber']);
			$result = json_decode($data, true);
			$response = [
				'id' => $this->_PATH_INFO,
				'links' => $this->_getDefaultLinks(),
				'response' =>
					[
						0 =>
							[
								'rel' => 'edit_secondary_bank_account',
								'method' => $this->_REQUEST_METHOD,
								'href' => $this->_PATH_INFO,
								'expects' =>
									[
										'method' => 'edit_secondary_bank_account',
										'accountCountryCode' => '{USA}',
										'accountNickName' => '{MyBankAccount}',
										'accountNumber' => '{bank account number}',
										'accountType' => '{C -  for checking}',
										'officialBankName' => '{i.e. Wells Fargo Inc}',
										'routingNumber' => '{routing number}',
									],
								'data' => $result
							],
					],
			];
			return $response;

		} else {
			$this->_STATE_MESSAGE = 'Token credentials invalid:  obtain valid non-expired token';
			$this->_STATE_CODE = '02'; //credentials invalid
			$response = $this->_error_response();
		}
	}

	/**
     * @param array $request
     * @return mixed
     * example:
     * https://damon.damonhogan.com/api/rest?method=transfer_to_crypto
     * &userId=101
     * &clientId=501
     * &fiatCurrency=USD
     * &fiatCurrencyAmount=2.00  //must be at least $2.00
     * &asset=BTC
     * &token=iRyx6iW87vumDqXpRM4QfBPhQtQX7abmWn14FkvXcxknC9o+hEUXkqeJ6GQ3XWC2rTkWn5V5
     */
    private function _transfer_to_crypto(array $request) {
        $this->_crypto = $this->_sub_object_list['crypto'];
        if ($this->_validateToken($request['token'])) {
            $result = $this->_crypto->cryptoAssetTransfers(
                $request['userId'], //101,
                $request['clientId'], //501,
                $request['fiatCurrency'], //'USD',
                $request['fiatCurrencyAmount'], //2.00
                $request['asset'] //"BTC"
            );
	        $data = $this->_crypto->getTransferResponseStatus($result);
            //$result ='{"type":"FiatClientPaymentAndCryptoTransfer","transactionDate":1520292962999,"fiatCurrency":"USD","fiatCurrencyAmount":2.99,"clientId":"501","asset":"BTC","assetAmount":0.00017206,"conversionRate":0.00008603,"clientTransactionId":null,"currencyExchangeTransferFeeAmount":0.99,"currencySperaTransferFeeAmount":0}';
	        $dataResponse = json_encode($data);
	        $cryptoRequest = json_encode([
		        'user_id' => $request['userId'],
		        'fiat_currency' => $request['fiatCurrency'],
		        'fiat_amount' => $request['fiatCurrencyAmount'],
		        'destination_currency' => $request['asset']

	        ]);
	        $this->_account->logApiCall($_SESSION['accountUrlPrefix'] , $this->_USER->username, 'crypto_purchase', $cryptoRequest, $dataResponse);


            $response = [
                'id' => $this->_PATH_INFO,
                'links' => $this->_getDefaultLinks(),
                'response' =>
                    [
                        0 =>
                            [
                                'rel' => 'transfer_to_crypto',
                                'method' => $this->_REQUEST_METHOD,
                                'href' => $this->_PATH_INFO,
                                'expects' =>
                                    [
                                        'method' => 'transfer_to_crypto',
                                        'userId' => '{coinbase user id i.e. 101 etc}',
                                        'clientId' => '{coinbase client id i.e. 501 etc}',
                                        'fiatCurrency' => '{currency code i.e. USD}',
                                        'fiatCurrencyAmount' => '{some number in dollars and cents at least 2.00}',
                                        'asset' => '{BTC|ETH|LTC}',
                                        'token' => '{login token}'
                                    ],
                                'data' => $data
                            ],
                    ],


            ];
            return $response;

        } else {
            $this->_STATE_MESSAGE = 'Token credentials invalid:  obtain valid non-expired token';
            $this->_STATE_CODE = '02'; //credentials invalid
            $response = $this->_error_response();
        }

        return $response;
    }

    /**
     * create a session for the user
     * @param array $request
     * @return array
     */
    private function _login(array $request) {
        $user = User::validate_login($request['username'], $request['password']);
        if (!$user) {
            $this->_STATE_MESSAGE = 'Login credentials invalid:  check username or password';
            $this->_STATE_CODE = '02'; //credentials invalid
            $response = $this->_error_response();
        } else {
        	$this->_USER = $user;
            $baseUrl = 'https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION['accountUrlPrefix'] . '/';
            $userpic =
                ($user->userpic != 'no-pic.png')
                    ? 'https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION['accountUrlPrefix'] . '/' . "files/media/" . $user->userpic
                    : 'https:' . get_gravatar($user->email);

            $response = [
                'id' => $this->_PATH_INFO,
                'links' => $this->_getDefaultLinks(),
                'operations' =>
                    [
                        0 =>
                            [
                                'rel' => 'status',
                                'method' => $this->_REQUEST_METHOD,
                                'href' => $this->_PATH_INFO,
                                'expects' =>
                                    [
                                    ]
                            ],
                        1 =>
                            [
                                'rel' => 'login',
                                'method' => $this->_REQUEST_METHOD,
                                'href' => $this->_PATH_INFO . '?',
                                'expects' =>
                                    [
                                        'method' => 'login',
                                        'username' => '{username} or {email}',
                                        'password' => '{password}',
                                    ],
                            ],
                        2 =>
                            [
                                'rel' => 'user_balances',
                                'method' => $this->_REQUEST_METHOD,
                                'href' => $this->_PATH_INFO,
                                'expects' =>
                                    [
                                        'method' => 'user_balances',
                                        'userId' => '{coinbase user id i.e. 101 etc}',
                                        'token' => '{login token}'
                                    ]
                            ],
                        3 =>
                            [
                                'rel' => 'transfer_to_crypto',
                                'method' => $this->_REQUEST_METHOD,
                                'href' => $this->_PATH_INFO,
                                'expects' =>
                                    [
                                        'method' => 'transfer_to_crypto',
                                        'userId' => '{coinbase user id i.e. 101 etc}',
                                        'clientId' => '{coinbase client id i.e. 501 etc}',
                                        'fiatCurrency' => '{currency code i.e. USD}',
                                        'fiatCurrencyAmount' => '{some number in dollars and cents at least 2.00}',
                                        'asset' => '{BTC|ETH|LTC}',
                                        'token' => '{login token}'
                                    ]
                            ],
                        4 =>
	                        [
		                        'rel' => 'user_balances',
		                        'method' => $this->_REQUEST_METHOD,
		                        'href' => $this->_PATH_INFO,
		                        'expects' =>
			                        [
				                        'method' => 'user_balances',
				                        'userId' => '{coinbase user id i.e. 101 etc}',
				                        'token' => '{login token}'
			                        ]
	                        ],
                        5 =>
	                        [
		                        'rel' => 'account_balance',
		                        'method' => $this->_REQUEST_METHOD,
		                        'href' => $this->_PATH_INFO,
		                        'expects' =>
			                        [
				                        'method' => 'account_balance',
				                        'token' => '{login token}'
			                        ]
	                        ],
                        6 =>
	                        [
		                        'rel' => 'details',
		                        'method' => $this->_REQUEST_METHOD,
		                        'href' => $this->_PATH_INFO,
		                        'expects' =>
			                        [
				                        'method' => 'details',
				                        'token' => '{login token}'
			                        ]
	                        ],
                        7 =>
	                        [
		                        'rel' => 'hid',
		                        'method' => $this->_REQUEST_METHOD,
		                        'href' => $this->_PATH_INFO. '?method=hid&token={login_token}&invoiceId={invoice id}&StoreCard=true&paymentType={card||ach}',
		                        'expects' =>
			                        [
				                        'method' => 'hid',
				                        'token' => '{login token}',
				                        'invoiceId' => '{invoiceId}[used to get payment details such as amount and also tag invoice id to payment]',
				                        'StoreCard' => '{true||false}',
				                        'paymentType' => '{card||ach}'
			                        ]
	                        ],
                        8 =>
	                        [
		                        'rel' => 'edit_primary_bank_account',
		                        'method' => $this->_REQUEST_METHOD,
		                        'href' => $this->_PATH_INFO. '?method=hid&token={login_token}
		                        &accountCountryCode={USA}
		                        &accountNickName={MyBankAccount}
		                        &accountNumber={bank account number}
		                        &accountType={C -  for checking}
		                        &officialBankName={i.e. Wells Fargo Inc}
		                        &routingNumber={routing number}
		                        ',
		                        'expects' =>
			                        [
				                        'method' => 'edit_primary_bank_account',
				                        'token' => '{login token}',
				                        'accountCountryCode' => '{USA}',
				                        'accountNickName' => '{MyBankAccount}',
				                        'accountNumber' => '{bank account number}',
				                        'accountType' => '{C -  for checking}',
				                        'officialBankName' => '{i.e. Wells Fargo Inc}',
				                        'routingNumber' => '{routing number}',
			                        ]
	                        ],
                        9 =>
	                        [
		                        'rel' => 'edit_secondary_bank_account',
		                        'method' => $this->_REQUEST_METHOD,
		                        'href' => $this->_PATH_INFO. '?method=hid&token={login_token}
		                        &accountCountryCode={USA}
		                        &accountNickName={MyBankAccount}
		                        &accountNumber={bank account number}
		                        &accountType={C -  for checking}
		                        &officialBankName={i.e. Wells Fargo Inc}
		                        &routingNumber={routing number}
		                        ',
		                        'expects' =>
			                        [
				                        'method' => 'edit_secondary_bank_account',
				                        'token' => '{login token}',
				                        'accountCountryCode' => '{USA}',
				                        'accountNickName' => '{MyBankAccount}',
				                        'accountNumber' => '{bank account number}',
				                        'accountType' => '{C -  for checking}',
				                        'officialBankName' => '{i.e. Wells Fargo Inc}',
				                        'routingNumber' => '{routing number}',
			                        ]
	                        ],
                    ],
                'response' =>
                    [
                        0 => [
                            'id' => $user->id,
                            'username' => $user->username,
                            'firstname' => $user->firstname,
                            'lastname' => $user->lastname,
                            'token' => $this->encryptString(json_encode(
                                [
                                    'password' => $user->hashed_password,
                                    'expires' => time() + 5184000, //expires in 60 days  consider 2 hours here 7200?
                                    'accountUrlPrefix' => $_SESSION['accountUrlPrefix'],
                                    'username' => $user->username
                                ]
                            ))->getEncryptedString(),
                            'email' => $user->email,
                            'status' => $user->status,
                            'admin' => $user->admin,
                            'userpic' => $userpic
                        ]
                    ],

            ];
        }
        return $response;
    }

    private function _user_transactions(array $request) {
	    $token = $this->_validateToken($request['token']);
	    if (($this->_USER && $this->_USER->username != $token->username) || !$this->_USER) {
		    $this->_STATE_MESSAGE = 'Token credentials invalid: get a new token, it could be expired';
		    $this->_STATE_CODE = '02'; //credentials invalid
		    $response = $this->_error_response();
	    } else {
		    $this->_crypto = $this->_sub_object_list['crypto'];
		    if ($this->_validateToken($request['token'])) {
			    $result   = $this->_crypto->GetTransactions(
				    $request['userId'], //101,
				    $request['asset'] //BTC|ETH|LTC
			    );
			    $response = [
				    'id'       => $this->_PATH_INFO,
				    'links'    => $this->_getDefaultLinks(),
				    'response' =>
					    [
						    0 =>
							    [
								    'rel'     => 'user_transactions',
								    'method'  => $this->_REQUEST_METHOD,
								    'href'    => $this->_PATH_INFO,
								    'expects' =>
									    [
										    'method' => 'user_transactions',
										    'userId' => '{coinbase user id i.e. 101 etc}',
										    'asset'  => '{BTC|ETH|LTC}',
										    'token'  => '{login token}'
									    ],
								    'data'    => json_decode( $result, true )
							    ],
					    ],


			    ];
		    }
	    }
	    return $response;
    }

    /**
     * @param array $request
     * @return array
     */
    private function _user_info(array $request)
    {
	    $token = $this->_validateToken($request['token']);
	    if (($this->_USER && $this->_USER->username != $token->username) || !$this->_USER) {
            $this->_STATE_MESSAGE = 'Token credentials invalid: get a new token, it could be expired';
            $this->_STATE_CODE = '02'; //credentials invalid
            $response = $this->_error_response();
        } else {
            $baseUrl = 'https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION['accountUrlPrefix'] . '/';
            $userpic =
                ($this->_USER->userpic != 'no-pic.png')
                    ? 'https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION['accountUrlPrefix'] . '/' . "files/media/" . $this->user->userpic
                    : 'https:' . get_gravatar($this->_USER->email);

            $response = [
                'id' => $this->_PATH_INFO,
                'links' => $this->_getDefaultLinks(),
                'operations' =>
                    [
                        0 =>
                            [
                                'rel' => 'user_info',
                                'method' => $this->_REQUEST_METHOD,
                                'href' => $this->_PATH_INFO,
                                'expects' =>
                                    [
                                        'token' => '{token issued from login page}',
                                    ]
                            ],
                    ],
                'response' =>
                    [
                        0 => [
                            'id' => $this->_USER->id,
                            'username' => $this->_USER->username,
                            'firstname' => $this->_USER->firstname,
                            'lastname' => $this->_USER->lastname,
                            'email' => $this->_USER->email,
                            'status' => $this->_USER->status,
                            'admin' => $this->_USER->admin,
                            'userpic' => $userpic
                        ]
                    ],

            ];

        }
        return $response;
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
        //$c = base64_decode('8fDq8gMWVn16ef4DsGQ5BUEnhVUDq97KozDM2vhEoJUiA2MHllzwtXs972rxqCfZCw4qMZDhr0vIMRjRI4KIGEnrJhYM4zkunE6bMxYvDIodV1Nz8k/7Bh2lFXT+sKewT4t/ALcB0lRe9EY3V1tSloMd2y7Ikfa2GamoBTJy0LZpROo03FiNLCpsIIrwk3Ao7mUvdmUO513ScYOzyqFEIMxn7JPXLnDnXhT3VF0O7xyEJrUEFVZRl4focvf3ZYKFijWKsKljNfCTQ8LRMxrb8UuoJuErslpbeyy7lZOfAGEbqH5TPxaVu3gXLq16hC+p4Bxd9CNL3OczmKwLaaCLfVklRcHkFcf8G3W8AEtK3z0=');
        $c = base64_decode($stringToDecrypt);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
	    error_reporting(0);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, SALT, $options=OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, SALT, $as_binary=true);
        if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
        {
            $this->_decryptedString = $original_plaintext;
        } else {
	        $this->_decryptedString = null;
        }
	    error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
        return $this;
    }

}