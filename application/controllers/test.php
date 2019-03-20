<?php if ( ! defined( 'BASEPATH' ) ) {
    exit( 'No direct script access allowed' );
}
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 11/1/17
 * Time: 9:30 AM
 */
class Test extends MY_Controller
{
    /** @var  account */
    public $account;

    /** @var  platformaws */
    public $platformaws;

    /** @var  MY_Email */
    public $email;

    /** @var  propay_api */
    public $propay_api;

    /** @var  protectpayapi */
    public $protectpayapi;

    /** @var  Invoice */
    public $invoice;

    /** @var  User */
    public $user;

    /** @var  CI_Config */
    public $config;

    /** @var  projecttasks */
    public $projecttasks;

    /** @var crypto */
    public $crypto;

    public function index()
    {
        $parsedAccountUrlPrefix = $_SESSION['accountUrlPrefix'];

        $databaseName = $parsedAccountUrlPrefix . '_' . ENVIRONMENT;

        // @var CI_DB_mysql_driver $primaryDatabase /
        $primaryDatabase = $this->load->database($databaseName, TRUE);

        $params = [
            'databaseName' => $databaseName,
            'primaryDatabase' => $primaryDatabase        ];

        $this->load->library('projecttasks', $params);

        $projectTasks = $this->projecttasks->getProjectTasks();

        $csv = $this->projecttasks->formatCsv($projectTasks);

        echo "<pre>";
        echo $csv;
        die();

        /*
        $core_settings = Setting::first();

        echo "<pre>";

        $this->load->library('platformaws', ['aws_access_key' => $this->config->item('aws_access_key'), 'aws_secret_key' => $this->config->item('aws_secret_key')]);

        $environment = ENVIRONMENT;
        $bucket = "spera-" . $environment;

        //$files = $this->platformaws->getFileList($bucket,$_SESSION['accountUrlPrefix'] . '/assets/blueline/images/backgrounds/');

        $success = $this->platformaws->copyPath(
            $bucket,
            'spera-release',
            'default/',
            'test/'
        );

        */

        /**
         * https://spera-development.s3-us-west-2.amazonaws.com/spera-development/default/blueline/images/logos/logo-lg-bk_preview.png
         */

        /**
         * https://spera-development.s3-us-west-2.amazonaws.com/spera-development/damon/blueline/images/backgrounds/DrStrangeContrastFull.png
         */

        /*
        var_export($success);
        die();
        */

        /*
        $hostedTransactionResponse = '{
            "HostedTransaction":
            {
                "CreationDate": "2016-02-01T16:32:57.9970565",
                "HostedTransactionIdentifier": "3c2d361a-23a7-4ca1-9c4d-4c18e1af7ad1",
                "PayerId": 1045899410011966,
                "TransactionResultMessage": "",
                "AuthCode": "A11111",
                "TransactionHistoryId": 8299869,
                "TransactionId": "338",
                "TransactionResult": "00",
                "AvsResponse": "T",
                "PaymentMethodInfo":
            {
                "PaymentMethodID": "48a5bf91-a076-4719-9615-d1dc630e39ca",
                "PaymentMethodType": "Visa",
                "ObfuscatedAccountNumber": "474747******4747",
                "ExpirationDate": "0117",
                "AccountName": "John Smith",
                "BillingInformation":
            {
                "Address1": "3400 N. Ashton Blvd",
                "Address2": "Suite 200",
                "Address3": "",
                "City": "Lehi",
                "State": "UT",
                "ZipCode": "84043",
                "Country": "USA",
                "TelephoneNumber": "",
                "Email": ""
            },
                "Description": "",
                "Priority": 55,
                "DateCreated": "2016-02-01T16:32:56.307",
                "Protected": true
            },
                "GrossAmt": 1000,
                "NetAmt": 948,
                "PerTransFee": 25,
                "Rate": 2.69,
                "GrossAmtLessNetAmt": 52,
                "CVVResponseCode": "M",
                "CurrencyConversionRate": 1,
                "CurrencyConvertedAmount": 1000,
                "CurrencyConvertedCurrencyCode": 840
            },
            "Result":
            {
                "ResultValue": "SUCCESS",
                "ResultCode": "00",
                "ResultMessage": ""
            }
        }';
        $responseObject = json_decode($hostedTransactionResponse);
        var_export($responseObject);


        if ( ! isset( $responseObject->HostedTransaction ) ) {
            $message = 'Hosted Transaction Identifier was not created.';
        } elseif ( $responseObject->HostedTransaction == null ) {
            $message = 'Hosted Transaction Identifier has no successful payment.';
        } else {
            $grossToPay = (isset($responseObject->HostedTransaction->GrossAmt) && (int) $responseObject->HostedTransaction->GrossAmt > 0) ?
                $responseObject->HostedTransaction->GrossAmt / 100 : 0;
            $netToPay = (isset($responseObject->HostedTransaction->GrossAmt) && (int) $responseObject->HostedTransaction->GrossAmt > 0) ?
                $responseObject->HostedTransaction->NetAmt / 100 : 0;

            //TODO: handle a case where payment was so low that fee ate up all that could be transfered
            if ($grossToPay > 0 && $netToPay > 0) {

                $parsedAccountUrlPrefix = $_SESSION['accountUrlPrefix'];

                $databaseName = $parsedAccountUrlPrefix . '_' . ENVIRONMENT;

                // @var CI_DB_mysql_driver $primaryDatabase /
                $primaryDatabase = $this->load->database('primary', TRUE);

                $params = [
                    'databaseName' => $databaseName,
                    'primaryDatabase' => $primaryDatabase        ];

                $this->load->library('propay_api', $params);

                $params = [
                    'primaryDatabase' => $primaryDatabase,
                ];
                $this->load->library('account', $params);

                $signedUp = $this->propay_api->isSignedUp($parsedAccountUrlPrefix,$parsedAccountUrlPrefix);

                $payeeUsername = $signedUp->username;
                $payeeAccountNumber = $signedUp->AccountNumber;

                $result = $this->propay_api
                    ->setBillerId(PROTECT_PAY_BILLER_ID)
                    ->setAuthToken(PROTECT_PAY_AUTH_TOKEN)
                    ->setPropayApiBaseUrl(explode("/ProtectPay",PROTECT_PAY_API_BASE_URL)[0])
                    ->setTimedPullData([
                        "accountNum" => PROTECT_PAY_PAYER_ACCOUNT_ID,
                        "recAccntNum" => $payeeAccountNumber,
                        "amount" => (int) $netToPay * 100,
                        "transNum" => $responseObject->HostedTransaction->TransactionId, //card transaction number, this payment will occur when card transaction settles
                        "invNum" => 1, //optional
                        "comment1" => $parsedAccountUrlPrefix, //optional
                        "comment2" => $core_settings->email //optional
                    ])
                    ->processTimedPull()
                    ->getTimedPullInfo();

                //{"AccountNumber":0,"Status":"59","TransactionNumber":0}
                $timePullResponseObject = json_decode($result);

                $result = $this->propay_api
                    ->setAccountUrlPrefix($parsedAccountUrlPrefix)
                    ->recordTransaction($responseObject, $_SERVER['REMOTE_ADDR'], $payeeUsername, 1, $timePullResponseObject->Status);

                $this->account
                    ->setAccountUrlPrefix($parsedAccountUrlPrefix)
                    ->storeAccountUserPaymentMethod($responseObject, $parsedAccountUrlPrefix);

                echo "<br><br>";
                var_export($result);

                die();


                //$invoice->status = "Paid";
                //$invoice->paid_date = date('Y-m-d', time());
                //$invoice->paid = $grossToPay;
                //$invoice->save();

                //TODO review this for propay and make appropriate changes
                //$attributes = array('invoice_id' => $invoice->id, 'reference' => $this->authorize_net->getTransactionId(), 'amount' => $grossToPay, 'date' => date('Y-m-d', time()), 'type' => 'credit_card', 'notes' => $this->authorize_net->getApprovalCode());
                //$invoiceHasPayment = InvoiceHasPayment::create($attributes);

                $success = true;
                $message = 'Payment Recorded Successfully.';
            }
        }

        */

        $parsedAccountUrlPrefix = $_SESSION['accountUrlPrefix'];

        $databaseName = $parsedAccountUrlPrefix . '_' . ENVIRONMENT;

        // @var CI_DB_mysql_driver $primaryDatabase /
        $primaryDatabase = $this->load->database('primary', TRUE);

        $params = [
            'databaseName' => $databaseName,
            'primaryDatabase' => $primaryDatabase        ];

        $this->load->library('propay_api', $params);

        $params = [
            'primaryDatabase' => $primaryDatabase,
        ];
        $this->load->library('account', $params);

        $proPayAPI = $this->propay_api;

        $signedUp = $this->propay_api->isSignedUp($parsedAccountUrlPrefix, $parsedAccountUrlPrefix);

        $payeeUsername = $signedUp->username;
        $payeeAccountNumber = $signedUp->AccountNumber;

        $transactionObject = (object) [];
        $transactionObject ->HostedTransaction = (object) [];

        $invoiceId = 4;

        $transactionObject->HostedTransaction->TransactionId = '428';

        $transactionObject->HostedTransaction->HostedTransactionIdentifier = 'ed34bb40-6b33-4090-b159-1ab3cd985919';
        $transactionObject->HostedTransaction->GrossAmt = '100';
        $transactionObject->HostedTransaction->NetAmt = '67';

        echo PROPAY_CERT_STRING . ':' . PROTECT_PAY_TERM_ID;
        echo "<br><br>";
        $data = [
            "accountNum" => PROPAY_ACCOUNT_NUMBER,
            "recAccntNum" => $payeeAccountNumber,
            "amount" => (int) $transactionObject->HostedTransaction->NetAmt,
            "transNum" => $transactionObject->HostedTransaction->TransactionId, //card transaction number, this payment will occur when card transaction settles
            "invNum" => $invoiceId, //optional
            "comment1" => $parsedAccountUrlPrefix, //optional
            "comment2" => $core_settings->email //optional
        ];

        var_export($data);
        echo "<br><br>";

        $result = $this->propay_api
            ->setCertStr(PROPAY_CERT_STRING)
            ->setTermId(PROTECT_PAY_TERM_ID)
            ->setPropayApiBaseUrl(explode("/ProtectPay", PROTECT_PAY_API_BASE_URL)[0])
            ->setTimedPullData($data)
            ->processTimedPull()
            ->getTimedPullInfo();

        //{"AccountNumber":0,"Status":"59","TransactionNumber":0}
        $timePullResponseObject = json_decode($result);

        var_export($timePullResponseObject);


        $result = $this->propay_api
            ->setAccountUrlPrefix($parsedAccountUrlPrefix)
            ->recordTransaction($transactionObject, $_SERVER['REMOTE_ADDR'], $payeeUsername, $invoiceId, $timePullResponseObject->Status);

        //$this->account
        //    ->setAccountUrlPrefix($parsedAccountUrlPrefix)
        //    ->storeAccountUserPaymentMethod($responseObject, $parsedAccountUrlPrefix);

        $invoice = Invoice::find_by_id($invoiceId);

        $invoice->status = "Paid";
        $invoice->paid_date = date('Y-m-d', time());
        $invoice->paid = 1.0;
        $invoice->save();

        //4.3.2, 4.3.3 4.3.4 99-109 propay. HPP    4.4 4.5

        /**
         * <?xml version='1.0'?>
        <!DOCTYPE Request.dtd>
        <XMLRequest>
        <certStr>MyCertStr</certStr>
        <class>partner</class>
        <XMLTrans>
        <transType>13</transType>
        <sourceEmail>jsmith@gmail.com</sourceEmail>
        </XMLTrans>
        </XMLRequest>
         */

        /*$simpleXML = new \SimpleXMLElement($proPayAPI->getDefaultXMLHeader());
        $simpleXML->addChild('certStr',PROTECT_PAY_COMMISSION_DISBURSEMENT_CREDENTIAL);
        $simpleXML->addChild('class','partner');
        $simpleXML->addChild('XMLTrans');
        $simpleXML->XMLTrans->addChild('transType', 13);
        $simpleXML->XMLTrans->addChild('accountNum', '32358477');


        $result =
            $proPayAPI->setXMLUrl(explode("/ProtectPay",PROTECT_PAY_API_BASE_URL)[0] . '/API/PropayAPI.aspx')
                //->setXMLRequestData($simpleXML->asXML())
                ->setXMLRequestData("<?xml version='1.0'?>
        <!DOCTYPE Request.dtd>
        <XMLRequest>
        <certStr>b261326ddba467bbe841cfa6fffc43</certStr>
        <class>partner</class>
        <XMLTrans>
        <transType>13</transType>
        <sourceEmail>damonhogan3@juno.com</sourceEmail>
        </XMLTrans>
        </XMLRequest>")
                ->postXML()
                ->getXMLRequestObject()->asXML();

        $result = $proPayAPI->getXMLResponseObject();
        var_export($result);
        */
        die();
    }

    public function crypto() {
	    $parsedAccountUrlPrefix = $_SESSION['accountUrlPrefix'];

	    $databaseName = $parsedAccountUrlPrefix . '_' . ENVIRONMENT;

	    // @var CI_DB_mysql_driver $primaryDatabase /
	    $primaryDatabase = $this->load->database('primary', TRUE);

	    $params = [
		    'primaryDatabase' => $primaryDatabase,
	    ];
	    $this->load->library('account', $params);
	    echo "<pre>";
	    $isInvoiceCryptoPayment = $this->account->isInvoiceCryptoPayment(7, $parsedAccountUrlPrefix)[0];

	    $aup_transaction = $this->account->getAccountUsersPropayTransaction( '09480430-40f8-4eab-b62d-b6fe392bb5e5' )[0];

	    var_export($isInvoiceCryptoPayment);
	    $this->account->updateCryptoPaymentStatus( $isInvoiceCryptoPayment->id, $aup_transaction, 1 );
	    $isInvoiceCryptoPayment = $this->account->isInvoiceCryptoPayment(7, $parsedAccountUrlPrefix)[0];
	    $data = json_decode('{"type":"FiatClientPaymentAndCryptoTransfer","transactionDate":1520975363472,"fiatCurrency":"USD","fiatCurrencyAmount":2.99,"clientId":"501","asset":"BTC","assetAmount":0.00021909,"conversionRate":0.00010955,"clientTransactionId":null,"currencyExchangeTransferFeeAmount":0.99,"currencySperaTransferFeeAmount":0}');
	    $this->account->updateCryptoPaymentStatus( $isInvoiceCryptoPayment->id, $data, 2 );
	    $isInvoiceCryptoPayment = $this->account->isInvoiceCryptoPayment(7, $parsedAccountUrlPrefix)[0];

	    $params = [
		    'databaseName'     => $databaseName,
		    'primaryDatabase'  => $primaryDatabase,
		    'accountUrlPrefix' => $_SESSION['accountUrlPrefix'],
		    'username'         => ( isset( $this->user->username ) ) ? $this->user->username : '{not logged in}'
	    ];
	    $this->load->library( 'crypto', $params );
	    var_export($isInvoiceCryptoPayment);
	    $data = $this->crypto->cryptoAssetTransfers(101, 501, 'USD', '2.00', 'BTC');
	    $data = $this->crypto->getTransferResponseStatus($data);
	    var_export($data);
	    die();
    }
}