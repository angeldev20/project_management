<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 11/6/17
 * Time: 9:28 AM
 */
class Testsignup extends MY_Controller
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

    public function index()
    {
        echo "<pre>";

        $core_settings = Setting::first();

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

        echo PROPAY_CERT_STRING . ':' . PROTECT_PAY_TERM_ID;
        echo "<br><br>";

        /*$result = $this->propay_api
            ->setApiBaseUrl(explode("/ProtectPay", PROTECT_PAY_API_BASE_URL)[0])
            ->setCertStr(PROPAY_CERT_STRING)
            ->setTermId(PROTECT_PAY_TERM_ID)
            ->setSignupData($data)
            ->processSignup()
            ->getSignupInfo();*/

        $signupData = [
            'AccountNumber' => 32624929.9999,
            'Password' => '',
            'SourceEmail' => 'kayrena@yahoo.com',
            'Status' => '00',
            'Tier' => 'Premium',
        ];

        $result = json_encode($signupData);
        $this->propay_api->setSignupInfo(json_encode($result));

        $signupInfo = json_decode($result);
        var_export($signupInfo);



            $signature = trim(htmlspecialchars('1'));

            $this->propay_api->setSignupInfo($result);
            $storeSignupInfoStatus = $this->propay_api->storeSignupInfo(
                $_SESSION['accountUrlPrefix'],
                $this->user->username,
                $signature,
                $_SERVER['REMOTE_ADDR'],
                date("Y-m-d H:i:s")
            );
            //$from_email = EMAIL_FROM; //$core_settings->email
            //$this->email->from($from_email, $core_settings->company);
            //$this->email->to($signupInfo->SourceEmail);

            //TODO: translate this
            ////$this->email->subject($this->lang->line('application_your_account_has_been_created'));
            //$this->email->subject('Your payment account has been created.');
            //$this->email->message('<br>Please refer to your email to set your payment password' .
            //    '<br><br>Your account number is :' . $signupInfo->AccountNumber .
            //    '<br>Your temporary password is ' . $signupInfo->Password .
            //    '<br>You can login to your payment account at https://www.propay.com using your email: ' . $signupInfo->SourceEmail
            //);
            //$this->email->send();

            $isSignedUp = $this->propay_api->isSignedUp($parsedAccountUrlPrefix, $this->user->username);

            switch ($signupInfo->Status) {
                case '00':
                    $merchantProfileData = [
                        'ProfileName' => substr($_SESSION['accountUrlPrefix'] . '-' . $this->user->username . '-' . $signupInfo->AccountNumber,0,50),
                        'PaymentProcessor' => 'LegacyProPay',
                        'ProcessorData' =>
                            [

                                [
                                    'ProcessorField' => 'certStr',
                                    'Value' => PROPAY_CERT_STRING,
                                ],

                                [
                                    'ProcessorField' => 'accountNum',
                                    'Value' => $signupInfo->AccountNumber,
                                ],

                                [
                                    'ProcessorField' => 'termId',
                                    'Value' => PROTECT_PAY_TERM_ID,
                                ]
                            ]
                    ];
                    $this->load->library('protectpayapi');
                    $merchantProfileResponse = $this->protectpayapi
                        ->setApiBaseUrl(PROTECT_PAY_API_BASE_URL)
                        ->setBillerId(PROTECT_PAY_BILLER_ID)
                        ->setAuthToken(PROTECT_PAY_AUTH_TOKEN)
                        ->createMerchantProfile($merchantProfileData);
                    $merchantProfileData = json_decode($merchantProfileResponse);
                    echo "<br><br>";
                    var_export($merchantProfileData);
                    $storeMerchantProfileStatus = $this->propay_api->storeMerchantProfile(
                        $_SESSION['accountUrlPrefix'],
                        $this->user->username,
                        $merchantProfileData
                    );
                    break;
                case '87':
                    $this->view_data['error'] = "Email address is already signed up for a payment account!";
                    $this->view_data['registerdata'] = array_map('htmlspecialchars', $_POST);
                    //deal with account alread exists case
                    break;
                default:
                    $this->view_data['error'] = "Unknown error trying to signup! " . var_export($signupInfo, true) ;
                    $this->view_data['registerdata'] = array_map('htmlspecialchars', $_POST);
                    //stdClass::__set_state(array( 'AccountNumber' => 0, 'Password' => NULL, 'SourceEmail' => NULL, 'Status' => '59', 'Tier' => NULL, ))stdClass::__set_state(array( 'AccountNumber' => 0, 'Password' => NULL, 'SourceEmail' => NULL, 'Status' => '59', 'Tier' => NULL, ))
                    //var_export($signupInfo);
                //die();
                // handle unknown error, log or something.
            }



        die();

    }
}
