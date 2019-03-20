<?php

class Propay extends MY_Controller {

    /** @var  propay_api */
    public $propay_api;

    /** @var  protectpayapi */
    public $protectpayapi;

    /** @var  Invoice */
    public $invoice;

    /** @var  account */
    public $account;

	/** @var crypto*/
    public $crypto;

    /** @var  User */
    public $user;

    function hid() {

        //TODO: we need to implement a check to see if we have a stored card, and if we do
        // we don't need to set StoreCard to true if the card hasn't expired and also
        // we need to set the HPP to use the stored card instead of asking for a new one and
        // we probably just go ahead and charge the stored card, and only use HPP if the stored card
        // fails and we mark the stored card inactive or delete it on failure and just go with the new one on the transaction
        // results

        $StoreCard = (isset($_REQUEST['StoreCard']) && $_REQUEST['StoreCard'] == 'true') ? true : false;

        $invoiceId = $_REQUEST['id'];

        $this->load->library('protectpayapi');

        $invoice = Invoice::find($invoiceId);

        $applicationEnv = ENVIRONMENT;


        $parsedAccountUrlPrefix = $_SESSION['accountUrlPrefix'];

        $databaseName = $parsedAccountUrlPrefix . '_' . ENVIRONMENT;

        /** @var CI_DB_mysql_driver $primaryDatabase */
        $primaryDatabase = $this->load->database('primary', TRUE);

        $params = [
            'primaryDatabase' => $primaryDatabase,
        ];
        $this->load->library('account', $params);

        $params = [
            'databaseName' => $databaseName,
            'primaryDatabase' => $primaryDatabase];

        $this->load->library('propay_api', $params);

        $invoiceUser = InvoiceHasUser::find('all',array('conditions' => array('invoice_id=?',$invoiceId)));
        if (count($invoiceUser) > 0) {
            $user = User::find($invoiceUser[0]->user_id);
            $signedUp = $this->propay_api->isSignedUp($parsedAccountUrlPrefix, $user->username);
	        $merchantProfileId = ($this->account->isInvoiceCryptoPayment($invoiceId, $parsedAccountUrlPrefix, $user->username) != false) ? PROTECT_PAY_MERCHANT_PROFILE_ID : $signedUp->ProfileId;
        } else {
            $signedUp = $this->propay_api->isSignedUp($parsedAccountUrlPrefix, $parsedAccountUrlPrefix);
	        $merchantProfileId = ($this->account->isInvoiceCryptoPayment($invoiceId, $parsedAccountUrlPrefix) != false) ? PROTECT_PAY_MERCHANT_PROFILE_ID : $signedUp->ProfileId;
        }

        $data = [
            "Amount" => (int) ($invoice->outstanding * 100), //convert to cents
            "AuthOnly" => false,
            "AvsRequirementType" => 3,
            "CardHolderNameRequirementType" => 2,
            "CssUrl" => "https://spera-" . $applicationEnv . ".s3-us-west-2.amazonaws.com/pmi.css",
            "CurrencyCode" => "USD",
            "InvoiceNumber" => $invoiceId,
            "MerchantProfileId" => $merchantProfileId,
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

        $result = $this->protectpayapi
            ->setApiBaseUrl(PROTECT_PAY_API_BASE_URL)
            ->setBillerId(PROTECT_PAY_BILLER_ID)
            ->setAuthToken(PROTECT_PAY_AUTH_TOKEN)
            ->setHostedTransactionData($data)
            ->createHostedTransaction()
            ->getCreatedHostedTransactionInfo();
        $responseObject = json_decode($result);

        if ( $responseObject ) {
            $_SESSION['paymentType'] = $_REQUEST['paymentType'];
            $_SESSION['HostedTransactionIdentifier'] = $responseObject->HostedTransactionIdentifier;
	        $_SESSION['merchantProfileId'] = $merchantProfileId;
        }
        header('Content-Type: application/json');
        echo json_encode([
            'response'=>$responseObject,
            'success'                     => true,
            'message'                     => 'Hosted Transaction Identifier Created Successfully.',
            'HostedTransactionIdentifier' => $_SESSION['HostedTransactionIdentifier']
        ]);

        die();
    }

	function sperahid() {

		//TODO: we need to implement a check to see if we have a stored card, and if we do
		// we don't need to set StoreCard to true if the card hasn't expired and also
		// we need to set the HPP to use the stored card instead of asking for a new one and
		// we probably just go ahead and charge the stored card, and only use HPP if the stored card
		// fails and we mark the stored card inactive or delete it on failure and just go with the new one on the transaction
		// results

		$StoreCard = (isset($_REQUEST['StoreCard']) && $_REQUEST['StoreCard'] == 'true') ? true : false;

		$amountDue = $_REQUEST['amountDue'];

		$this->load->library('protectpayapi');

		$applicationEnv = ENVIRONMENT;


		$parsedAccountUrlPrefix = $_SESSION['accountUrlPrefix'];

		$databaseName = $parsedAccountUrlPrefix . '_' . ENVIRONMENT;

		/** @var CI_DB_mysql_driver $primaryDatabase */
		$primaryDatabase = $this->load->database('primary', TRUE);

		$params = [
			'primaryDatabase' => $primaryDatabase,
		];
		$this->load->library('account', $params);

		$params = [
			'databaseName' => $databaseName,
			'primaryDatabase' => $primaryDatabase];

		$this->load->library('propay_api', $params);

		$data = [
			"Amount" => (int) $amountDue, //convert to cents
			"AuthOnly" => false,
			"AvsRequirementType" => 3,
			"CardHolderNameRequirementType" => 2,
			"CssUrl" => "https://spera-" . $applicationEnv . ".s3-us-west-2.amazonaws.com/pmi.css",
			"CurrencyCode" => "USD",
			"InvoiceNumber" => $_SESSION['accountUrlPrefix'],
			"MerchantProfileId" => PROTECT_PAY_MERCHANT_PROFILE_ID,
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

		$result = $this->protectpayapi
			->setApiBaseUrl(PROTECT_PAY_API_BASE_URL)
			->setBillerId(PROTECT_PAY_BILLER_ID)
			->setAuthToken(PROTECT_PAY_AUTH_TOKEN)
			->setHostedTransactionData($data)
			->createHostedTransaction()
			->getCreatedHostedTransactionInfo();
		$responseObject = json_decode($result);

		if ( $responseObject ) {
			$_SESSION['paymentType'] = $_REQUEST['paymentType'];
			$_SESSION['HostedTransactionIdentifier'] = $responseObject->HostedTransactionIdentifier;
			$_SESSION['merchantProfileId'] = PROTECT_PAY_MERCHANT_PROFILE_ID;
		}
		header('Content-Type: application/json');
		echo json_encode([
			'success'                     => true,
			'message'                     => $amountDue, //'Hosted Transaction Identifier Created Successfully.',
			'HostedTransactionIdentifier' => $_SESSION['HostedTransactionIdentifier']
		]);

		die();
	}


	function recordpayment() {
        $paymentType = $_SESSION['paymentType'];
        $success = false;
        $message = '';

        $core_settings = Setting::first();

        $invoice = Invoice::find_by_id($_REQUEST['id']);

        $this->load->library('protectpayapi');
        $result        = $this->protectpayapi
            ->setBillerId( PROTECT_PAY_BILLER_ID )
            ->setAuthToken( PROTECT_PAY_AUTH_TOKEN )
            ->setApiBaseUrl( PROTECT_PAY_API_BASE_URL )
            ->setGetHostedTransactionData( $_SESSION['HostedTransactionIdentifier'] )
            ->getHostedTransaction()
            ->getHostedTransactionInfo();

        $responseObject = json_decode( $result );
        if ( ! isset( $responseObject->HostedTransaction ) ) {
            $message = 'Hosted Transaction Identifier was not created.';
        } elseif ( $responseObject->HostedTransaction == null ) {
            $message = 'Hosted Transaction Identifier has no successful payment.';
        } else {
            $grossToPay = (isset($responseObject->HostedTransaction->GrossAmt) && (int) $responseObject->HostedTransaction->GrossAmt > 0) ?
                $responseObject->HostedTransaction->GrossAmt / 100 : 0;
            $netToPay = (isset($responseObject->HostedTransaction->GrossAmt) && (int) $responseObject->HostedTransaction->GrossAmt > 0) ?
                $responseObject->HostedTransaction->NetAmt / 100 : 0;

            if ($paymentType == 'ach') {
                $grossToPay = $invoice->sum;
                $netToPay = $invoice->sum;
            }

            if ($netToPay <=  0) {
                $invoice->status = "Paid";
                $invoice->paid_date = date('Y-m-d', time());
                $invoice->paid = $grossToPay;
                $invoice->save();

                $success = true;
                $message = 'Transaction fee.';

            } else {

                if ($grossToPay > 0 && $netToPay > 0) {

                    $parsedAccountUrlPrefix = $_SESSION['accountUrlPrefix'];

                    $databaseName = $parsedAccountUrlPrefix . '_' . ENVIRONMENT;

                    /** @var CI_DB_mysql_driver $primaryDatabase */
                    $primaryDatabase = $this->load->database('primary', TRUE);

                    $params = [
                        'databaseName' => $databaseName,
                        'primaryDatabase' => $primaryDatabase];

                    $this->load->library('propay_api', $params);

                    $params = [
                        'primaryDatabase' => $primaryDatabase,
                    ];
                    $this->load->library('account', $params);

                    $signedUp = $this->propay_api->isSignedUp($parsedAccountUrlPrefix, $parsedAccountUrlPrefix);

                    $payeeUsername = $signedUp->username;
                    $payeeAccountNumber = $signedUp->AccountNumber;

                    $result = $this->propay_api
                        ->setAccountUrlPrefix($parsedAccountUrlPrefix)
                        ->recordTransaction($responseObject, $_SERVER['REMOTE_ADDR'], $payeeUsername, $_REQUEST['id'], '00');

	                $invoice->outstanding = $invoice->sum - $grossToPay;
	                $invoice->status = "Paid";
	                $invoice->paid_date = date('Y-m-d', time());
	                $invoice->paid = $grossToPay;
	                $invoice->save();

	                $success = true;
	                $message = 'Payment Recorded Successfully.';


	                $isInvoiceCryptoPayment = $this->account->isInvoiceCryptoPayment($invoice->id, $parsedAccountUrlPrefix);


	                if($isInvoiceCryptoPayment != false) {
		                $isInvoiceCryptoPayment = $isInvoiceCryptoPayment[0];
		                $aup_transaction = $this->account->getAccountUsersPropayTransaction( $responseObject->HostedTransactionIdentifier )[0];

		                $this->account->updateCryptoPaymentStatus( $isInvoiceCryptoPayment->id, $aup_transaction, 1 );

		                $params = [
			                'databaseName'     => $databaseName,
			                'primaryDatabase'  => $primaryDatabase,
			                'accountUrlPrefix' => $_SESSION['accountUrlPrefix'],
			                'username'         => ( isset( $this->user->username ) ) ? $this->user->username : '{not logged in}'
		                ];
		                $this->load->library( 'crypto', $params );
		                if ($aup_transaction->NetAmt > 0) {
		                	$account = $this->account->setAccountUrlPrefix($parsedAccountUrlPrefix)
				                ->getAccountData();
			                $result = $this->crypto->cryptoAssetTransfers( $account->id, $this->user->id, 'USD' , $aup_transaction->NetAmt, $isInvoiceCryptoPayment->destination_currency );
			                $data = $this->crypto->getTransferResponseStatus($result);
                            $dataResponse = json_encode($data);
			                $data = json_decode($dataResponse);
			                //$data = json_decode('{"type":"FiatClientPaymentAndCryptoTransfer","transactionDate":1520975363472,"fiatCurrency":"USD","fiatCurrencyAmount":2.99,"clientId":"501","asset":"BTC","assetAmount":0.00021909,"conversionRate":0.00010955,"clientTransactionId":null,"currencyExchangeTransferFeeAmount":0.99,"currencySperaTransferFeeAmount":0}');
			                $this->account->updateCryptoPaymentStatus( $isInvoiceCryptoPayment->id, $data, 2 );
			                $cryptoRequest = json_encode([
				                'account_id' =>	$account->id,
				                'user_id' => $this->user->id,
				                'TransactionId' => $aup_transaction->TransactionId,
				                'fiat_currency' => 'USD',
				                'fiat_amount' => $aup_transaction->NetAmt,
				                'destination_currency' => $isInvoiceCryptoPayment->destination_currency

			                ]);
			                $this->account->logApiCall($_SESSION['accountUrlPrefix'] , $this->user->username, 'crypto_purchase', $cryptoRequest, $dataResponse);
		                }
                    }

		            $this->account
                        ->setAccountUrlPrefix($parsedAccountUrlPrefix)
                        ->storeAccountUserPaymentMethod($responseObject, $parsedAccountUrlPrefix);

                }
            }
        }

        log_message('info', var_export($responseObject,true));

        header('Content-Type: application/json');
        $HostedTransactionIdentifier = null;
        if (isset($_SESSION['HostedTransactionIdentifier'])) $HostedTransactionIdentifier = $_SESSION['HostedTransactionIdentifier'];
        echo json_encode([
            'success'                     => $success,
            'message'                     => $message,
            'HostedTransactionIdentifier' => $HostedTransactionIdentifier,
            'redirectUrl'                 => 'invoices/view/' . $invoice->id
        ]);
        if ($success == true) unset($_SESSION[ 'HostedTransactionIdentifier']);

        die();
    }

	function recordsperapayment() {
		$paymentType = $_SESSION['paymentType'];
		$success = false;
		$message = '';

		$core_settings = Setting::first();

		$amount_due = $_REQUEST['amountDue'];

		$this->load->library('protectpayapi');
		$result        = $this->protectpayapi
			->setBillerId( PROTECT_PAY_BILLER_ID )
			->setAuthToken( PROTECT_PAY_AUTH_TOKEN )
			->setApiBaseUrl( PROTECT_PAY_API_BASE_URL )
			->setGetHostedTransactionData( $_SESSION['HostedTransactionIdentifier'] )
			->getHostedTransaction()
			->getHostedTransactionInfo();

		$responseObject = json_decode( $result );
		log_message('info', var_export($responseObject,true));
		if ( ! isset( $responseObject->HostedTransaction ) ) {
			$message = 'Hosted Transaction Identifier was not created.';
		} elseif ( $responseObject->HostedTransaction == null ) {
			$message = 'Hosted Transaction Identifier has no successful payment.';
		} else {
			$grossToPay = (isset($responseObject->HostedTransaction->GrossAmt) && (int) $responseObject->HostedTransaction->GrossAmt > 0) ?
				$responseObject->HostedTransaction->GrossAmt / 100 : 0;
			$netToPay = (isset($responseObject->HostedTransaction->GrossAmt) && (int) $responseObject->HostedTransaction->GrossAmt > 0) ?
				$responseObject->HostedTransaction->NetAmt / 100 : 0;

			if ($paymentType == 'ach') {
				$grossToPay = $amount_due/100;
				$netToPay = $amount_due/100;
			}

			if ($grossToPay > 0 && $netToPay > 0) {

				$parsedAccountUrlPrefix = $_SESSION['accountUrlPrefix'];

				$databasePrefix = $_SESSION['accountUrlPrefix'];

				if(in_array(substr($databasePrefix,0,1),explode(',','0,1,2,3,4,5,6,7,8,9')))
					$databasePrefix = 'z' . $databasePrefix;

				/** @var CI_DB_mysql_driver $primaryDatabase */
				$primaryDatabase = $this->load->database('primary', TRUE);

				$databaseName = $databasePrefix . '_' . ENVIRONMENT;

				$_SESSION['accountDatabasePrefix'] = $databasePrefix;

				/** @var CI_DB_mysql_driver $accountDatabase */
				$accountDatabase = $this->load->database($databaseName, TRUE);


				$params = [
					'databaseName' => $databaseName,
					'primaryDatabase' => $primaryDatabase,
					'accountDatabase' => $accountDatabase,
					'accountUrlPrefix' => $_SESSION['accountUrlPrefix'],
					'username' => $this->user->username,
					'session' => $this->session

				];

				$this->load->library( 'account', $params );
				//TODO: may need crypto library at some point if paid in crypto
				//$this->load->library( 'crypto', $params );
				$this->load->library( 'propay_api', $params );
				$this->load->library( 'billing', $params );

				$this->account->isSubscriptionDue($_SESSION['accountUrlPrefix'], $this->billing);

				$signedUp = $this->propay_api->isSignedUp($parsedAccountUrlPrefix, $parsedAccountUrlPrefix);

				$payeeUsername = $signedUp->username;
				$payeeAccountNumber = $signedUp->AccountNumber;

				$result = $this->propay_api
					->setAccountUrlPrefix($parsedAccountUrlPrefix)
					->recordTransaction($responseObject, $_SERVER['REMOTE_ADDR'], $payeeUsername, $_REQUEST['id'], '00');

				$_SESSION['billing']['TransactionId'] = $responseObject->HostedTransaction->TransactionId;
				$_SESSION['billing']['HostedTransactionIdentifier'] = $responseObject->HostedTransaction->HostedTransactionIdentifier;
				$_SESSION['billing']['GrossPaid'] = $grossToPay;
				$_SESSION['billing']['NetPaid'] = $netToPay;

				$success = true;
				$message = 'Payment Recorded Successfully.';

				$daysInBillingFrequency = $this->account->getPlanType()->billingFrequencyDays;
				$lastPaidDate = (isset($this->account->getSubscription()->lastBilled)) ? $this->account->getSubscription()->lastBilled : null;

				// first time paid
				if ($lastPaidDate == null) {
					$this->account
						->setAccountUrlPrefix($parsedAccountUrlPrefix)
						->storeAccountUserPaymentMethod($responseObject, $parsedAccountUrlPrefix)
						->updateLastPaymentDate();
				} else {
					//do an exact adjustment on lassBilled based on billing frequency
					// so someone can't work the system by avoiding payment for a number of days
					$db_date = new DateTime($lastPaidDate);
    				$db_date->modify( '+' . $daysInBillingFrequency . ' days' );

					$this->account
						->setAccountUrlPrefix($parsedAccountUrlPrefix)
						->storeAccountUserPaymentMethod($responseObject, $parsedAccountUrlPrefix)
						->updateLastPaymentDate(date_format($db_date, 'Y-m-d H:i:s'));
				}

				$plan_change_status = 0;
				if (isset($_SESSION['billing']['OldPlanType']) && strlen($_SESSION['billing']['OldPlanType']) > 0
				  && $_SESSION['billing']['PlanType'] != $_SESSION['billing']['OldPlanType']
				) {
					$starting_plan = $_SESSION['billing']['OldPlanType'];
					$plan_change_status = 1;
				} else {
					$starting_plan = $_SESSION['billing']['PlanType'];
				}

				$ending_plan = $_SESSION['billing']['PlanType'];
				$change_reason =  (!isset($_SESSION['billing']['UpgradeReason'])) ? '' : $_SESSION['billing']['UpgradeReason'];

				$user_count = $this->account->getBilling()->getUserCount();

				$result = $this->propay_api->recordAccountPayment(
					$responseObject,
					$_SERVER['REMOTE_ADDR'],
					$_REQUEST['id'],
					$starting_plan,
					$ending_plan,
					$plan_change_status,
					$user_count,
					$change_reason
				);


				//$this->view_data['estimate'] = Invoice::find_by_id($id);
				$this->view_data['estimate']->estimate_status = "Accepted";
				$this->view_data['estimate']->estimate_accepted_date = date("Y-m-d");

				$this->view_data['estimate']->save();

				send_user_notification(
					$this->user,
					$core_settings->email,
					'Spera payment [$' . $grossToPay . '] successfully processed.',
					'Thank you for your Payment of $' . $grossToPay . ' for your spera account.',
					false,
					base_url() . 'settings/account_payments/'. $responseObject->HostedTransaction->TransactionId
				);
			}
		}

		header('Content-Type: application/json');
		$HostedTransactionIdentifier = null;
		if (isset($_SESSION['HostedTransactionIdentifier'])) $HostedTransactionIdentifier = $_SESSION['HostedTransactionIdentifier'];
		echo json_encode([
			'success'                     => $success,
			'message'                     => $message,
			'HostedTransactionIdentifier' => $HostedTransactionIdentifier,
			'redirectUrl'                 => 'settings/account_payments'
		]);
		if ($success == true) unset($_SESSION[ 'HostedTransactionIdentifier']);

		die();
	}

	function speraapp() {
		if(!isset($_COOKIE["PHPSESSID"])) session_start();
		$currentCookieParams = session_get_cookie_params();
		$sidvalue = session_id();
		setcookie(
			'PHPSESSID',//name
			$sidvalue,//value
			0,//expires at end of session
			$currentCookieParams['path'],//path
			$currentCookieParams['domain'],//domain
			true //secure
		);

		$hid = $_REQUEST['hid'];
		$_SESSION['HostedTransactionIdentifier'] = $hid;
		$this->theme_view       = 'payment';
		$this->view_data['hid'] = $hid;
		$this->content_view             = 'propay/speraapp';
	}

	function recordapppayment() {
    	if (ENVIRONMENT != 'production') {
		    $success = true;
		    header('Content-Type: application/json');
		    $HostedTransactionIdentifier = null;
		    if (isset($_SESSION['HostedTransactionIdentifier'])) $HostedTransactionIdentifier = $_SESSION['HostedTransactionIdentifier'];
		    $_SESSION['paymentAmount'] = (string) 9.95;
		    $_SESSION['TransactionId'] = (string) rand ( 16385 , 32767 );
		    $_SESSION['paymentType'] = 'card';
		    echo json_encode([
			    'success'                     => $success,
			    'message'                     => '',
			    'HostedTransactionIdentifier' => $HostedTransactionIdentifier,
			    'redirectUrl'                 => 'propay/thankyou'
		    ]);
		    if ($success == true) unset($_SESSION[ 'HostedTransactionIdentifier']);

		    die();
	    }
		$success = false;
		$message = '';

		$this->load->library('protectpayapi');
		$result        = $this->protectpayapi
			->setBillerId( PROTECT_PAY_BILLER_ID )
			->setAuthToken( PROTECT_PAY_AUTH_TOKEN )
			->setApiBaseUrl( PROTECT_PAY_API_BASE_URL )
			->setGetHostedTransactionData( $_SESSION['HostedTransactionIdentifier'] )
			->getHostedTransaction()
			->getHostedTransactionInfo();

		$responseObject = json_decode( $result );
		if ( ! isset( $responseObject->HostedTransaction ) ) {
			$message = 'Hosted Transaction Identifier was not created.';
		} elseif ( $responseObject->HostedTransaction == null ) {
			$message = 'Hosted Transaction Identifier has no successful payment.';
		} else {
			if($responseObject->HostedTransaction->PaymentMethodInfo->PaymentMethodType == 'Checking' ||
			   $responseObject->HostedTransaction->PaymentMethodInfo->PaymentMethodType == 'Savings'
			) {
				$paymentType = 'ach';
			} else {
				$paymentType = 'card';
			}
			$_SESSION['paymentType'] = $paymentType;

			$grossToPay = (isset($responseObject->HostedTransaction->GrossAmt) && (int) $responseObject->HostedTransaction->GrossAmt > 0) ?
				$responseObject->HostedTransaction->GrossAmt / 100 : 0;
			$netToPay = (isset($responseObject->HostedTransaction->GrossAmt) && (int) $responseObject->HostedTransaction->GrossAmt > 0) ?
				$responseObject->HostedTransaction->NetAmt / 100 : 0;

			if ($paymentType == 'ach') {
				$grossToPay = $responseObject->HostedTransaction->GrossAmt / 100;
				$netToPay = $responseObject->HostedTransaction->GrossAmt / 100;
			}

			$_SESSION['paymentAmount'] = $responseObject->HostedTransaction->GrossAmt;
			$_SESSION['TransactionId'] = $responseObject->HostedTransaction->TransactionId;

			if ($netToPay <=  0) {
				$success = true;
				$message = 'Transaction fee.';
			} else {

				if ($grossToPay > 0 && $netToPay > 0) {

					$parsedAccountUrlPrefix = $_SESSION['accountUrlPrefix'];

					$databaseName = $parsedAccountUrlPrefix . '_' . ENVIRONMENT;

					/** @var CI_DB_mysql_driver $primaryDatabase */
					$primaryDatabase = $this->load->database('primary', TRUE);

					$params = [
						'databaseName' => $databaseName,
						'primaryDatabase' => $primaryDatabase];

					$this->load->library('propay_api', $params);

					$params = [
						'primaryDatabase' => $primaryDatabase,
					];
					$this->load->library('account', $params);

					$payeeUsername = $parsedAccountUrlPrefix;

					$result = $this->propay_api
						->setAccountUrlPrefix($parsedAccountUrlPrefix)
						->recordTransaction($responseObject, $_SERVER['REMOTE_ADDR'], $payeeUsername, 0, '00');

					$success = true;
					$message = 'Payment Recorded Successfully.';

					$this->account
						->setAccountUrlPrefix($parsedAccountUrlPrefix)
						->storeAccountUserPaymentMethod($responseObject, $parsedAccountUrlPrefix);
				}
			}
		}

		log_message('info', var_export($responseObject,true));

		header('Content-Type: application/json');
		$HostedTransactionIdentifier = null;
		if (isset($_SESSION['HostedTransactionIdentifier'])) $HostedTransactionIdentifier = $_SESSION['HostedTransactionIdentifier'];
		echo json_encode([
			'success'                     => $success,
			'message'                     => $message,
			'HostedTransactionIdentifier' => $HostedTransactionIdentifier,
			'redirectUrl'                 => 'propay/thankyou'
		]);
		if ($success == true) unset($_SESSION[ 'HostedTransactionIdentifier']);

		die();
	}

	public function thankyou () {
		$this->theme_view       = 'payment';
		$this->content_view             = 'propay/thankyou';
	}

}