<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 1/2/18
 * Time: 10:25 AM
 */?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class pInvoices extends MY_Controller
{

    /* current first (user) */
    public $public;

    /** @var  account */
    public $account;

    /** @var  propay_api */
    public $propay_api;

    function __construct()
    {

    	parent::__construct();

        //if (!$this->user) redirect('login');
        $firstUser = User::first();
        //if(isset($this->user->username) && $firstUser->username != $this->user->username) {
            $this->public = clone $firstUser;
        //}

        //$access = FALSE;
        //if ($this->public) {
        //    foreach ($this->view_data['menu'] as $key => $value) {
        //        if ($value->link == "tinvoices") {
        //            $access = TRUE;
        //        }
        //    }

        //    if (!$access) {
        //        redirect('login');
        //    }
        //}
        $this->view_data['submenu'] = array(
            $this->lang->line('application_all_invoices') => 'tinvoices',
        );
    }

    function calc()
    {
        $invoices = Invoice::find( 'all', array( 'conditions' => array( 'estimate != ?', 1 ) ) );
        foreach ( $invoices as $invoice ) {

            $settings = Setting::first();

            $items = InvoiceHasItem::find( 'all', array( 'conditions' => array( 'invoice_id=?', $invoice->id ) ) );

            //calculate sum
            $i   = 0;
            $sum = 0;
            foreach ( $items as $value ) {
                $sum = $sum + $invoice->invoice_has_items[ $i ]->amount * $invoice->invoice_has_items[ $i ]->value;
                $i ++;
            }
            if ( substr( $invoice->discount, - 1 ) == "%" ) {
                $discount = sprintf( "%01.2f", round( ( $sum / 100 ) * substr( $invoice->discount, 0, - 1 ), 2 ) );
            } else {
                $discount = $invoice->discount;
            }
            $sum = $sum - $discount;

            if ( $invoice->tax != "" ) {
                $tax_value = $invoice->tax;
            } else {
                $tax_value = $settings->tax;
            }

            if ( $invoice->second_tax != "" ) {
                $second_tax_value = $invoice->second_tax;
            } else {
                $second_tax_value = $settings->second_tax;
            }

            $tax        = sprintf( "%01.2f", round( ( $sum / 100 ) * $tax_value, 2 ) );
            $second_tax = sprintf( "%01.2f", round( ( $sum / 100 ) * $second_tax_value, 2 ) );

            $sum = sprintf( "%01.2f", round( $sum + $tax + $second_tax, 2 ) );


            $invoice->sum = $sum;
            $invoice->save();

        }
        redirect( 'pinvoices' );

    }

    function view($id = FALSE)
    {
	    //$_REQUEST['accessCode'] = '2ca1ecaf9b38bd92ce6e74fc35497fe7'; //md5
	    $requestAccessCode = (isset($_REQUEST['accessCode'])) ? $_REQUEST['accessCode'] : null;
	    $accessCode = md5( $_SESSION['accountUrlPrefix'] . $id);

	    if ($requestAccessCode != $accessCode) redirect('login');

    	$this->view_data['submenu'] = array(
            $this->lang->line('application_back') => 'invoices',
        );
        $this->view_data['invoice'] = Invoice::find($id);
        $invoice = $this->view_data['invoice'];

        $parsedAccountUrlPrefix = $_SESSION['accountUrlPrefix'];

        $databaseName = $parsedAccountUrlPrefix . '_' . ENVIRONMENT;

        /** @var CI_DB_mysql_driver $primaryDatabase */
        $primaryDatabase = $this->load->database('primary', TRUE);

        $params = [
            'databaseName' => $databaseName,
            'primaryDatabase' => $primaryDatabase        ];

        $this->load->library('propay_api', $params);



        $invoiceUser = InvoiceHasUser::find('all',array('conditions' => array('invoice_id=?',$invoice->id)));
        if (count($invoiceUser) > 0) {
            $user = User::find($invoiceUser[0]->user_id);
            $signedUp = $this->propay_api->isSignedUp($parsedAccountUrlPrefix, $user->username);
        } else {
            $signedUp = $this->propay_api->isSignedUp($parsedAccountUrlPrefix, $parsedAccountUrlPrefix);
        }

        $this->view_data['isSignedUp'] = $this->propay_api->isSignedUp($parsedAccountUrlPrefix, $this->public->username);

        $data["core_settings"] = Setting::first();
        $this->view_data['items'] = $invoice->invoice_has_items;

        //calculate sum
        $i = 0; $sum = 0;
        foreach ($this->view_data['items'] as $value){
            $sum = $sum+$invoice->invoice_has_items[$i]->amount*$invoice->invoice_has_items[$i]->value; $i++;
        }
        if(substr($invoice->discount, -1) == "%"){
            $discount = sprintf("%01.2f", round(($sum/100)*substr($invoice->discount, 0, -1), 2));
        }
        else{
            $discount = $invoice->discount;
        }
        $sum = $sum-$discount;

        if($invoice->tax != ""){
            $tax_value = $invoice->tax;
        }else{
            $tax_value = $data["core_settings"]->tax;
        }

        if($invoice->second_tax != ""){
            $second_tax_value = $invoice->second_tax;
        }else{
            $second_tax_value = $data["core_settings"]->second_tax;
        }

        $tax = sprintf("%01.2f", round(($sum/100)*$tax_value, 2));
        $second_tax = sprintf("%01.2f", round(($sum/100)*$second_tax_value, 2));

        $sum = sprintf("%01.2f", round($sum+$tax+$second_tax, 2));

        $payment = 0;
        $i = 0;
        $payments = $invoice->invoice_has_payments;
        if(isset($payments)){
            foreach ($payments as $value) {
                $payment = sprintf("%01.2f", round($payment+$payments[$i]->amount, 2));
                $i++;
            }
            $invoice->paid = $payment;
            $invoice->outstanding = sprintf("%01.2f", round($sum-$payment, 2));
        }

        $invoice->sum = $sum;
        $invoice->save();

        //$invoiceUser = InvoiceHasUser::find('all',array('conditions' => array('user_id=? AND invoice_id=?',$this->user->id,$id)));
        //if($this->user->admin != 1) {
        //    if (!count($invoiceUser) > 0 || count($invoiceUser) > 0 && $invoiceUser[0]->user_id != $this->user->id) {
        //        echo "<pre>";
        //        var_export($id);
        //        die();
        //        //redirect('tinvoices');
        //    }
        //}
        $this->theme_view   = 'application_public';
        $this->content_view = 'invoices/public_views/view';
        $this->view_data['user'] = $this->user;
    }

    function propay( $id = false, $sum = false)
    {
        $paymentType = $_REQUEST['paymentType'];
        $this->view_data['invoices'] = Invoice::find_by_id( $id );

        $this->view_data['id']  = $id;
        $this->view_data['sum'] = $sum;
        $this->view_data['paymentType'] = $paymentType;
        $this->theme_view       = 'modal';

        $this->view_data['form_action'] = 'pinvoices/propay';
        if ($paymentType == 'ach') {
            $this->view_data['title'] = $this->lang->line('application_pay_with_ach');
        } else {
            $this->view_data['title'] = $this->lang->line('application_pay_with_credit_card');
        }
        //$this->view_data['title'] = var_export($_REQUEST, true);
        $this->content_view             = 'invoices/_propay_hpp';
    }

    function propay_signup() {
        $core_settings = Setting::first();
        $isSignedUp    = false;

        $this->view_data['breadcrumb']    = $this->lang->line( 'application_payments' );
        $this->view_data['breadcrumb_id'] = "payments";
        $this->view_data['error']         = false;

        if ( ! $this->user ) {
            redirect( 'login' );
        }

        if ( isset( $_SESSION['accountUrlPrefix'] ) ) {
            $parsedAccountUrlPrefix = $_SESSION['accountUrlPrefix'];

            $databaseName = $parsedAccountUrlPrefix . '_' . ENVIRONMENT;

            /** @var CI_DB_mysql_driver $primaryDatabase */
            $primaryDatabase = $this->load->database( 'primary', true );

            $params = [
                'databaseName'    => $databaseName,
                'primaryDatabase' => $primaryDatabase
            ];

            $this->load->library( 'propay_api', $params );

            $isSignedUp = $this->propay_api->isSignedUp( $parsedAccountUrlPrefix, $this->user->username );

            $this->view_data['isSignedUp'] = $isSignedUp;
            if ( $isSignedUp ) {
                $this->view_data['registerdata'] = array_map( 'htmlspecialchars', [ 'PropayAccountNumber' => $isSignedUp->AccountNumber ] );
            }
            if ( $_POST ) {
                if ( isset( $_POST['PropayAccountNumber'] ) ) {
                    $signature             = trim( htmlspecialchars( $_POST['signature'] ) );
                    $storeSignupInfoStatus = $this->propay_api->storeSignupInfo(
                        $_SESSION['accountUrlPrefix'],
                        $this->user->username,
                        $signature,
                        $_SERVER['REMOTE_ADDR'],
                        date( "Y-m-d H:i:s" ),
                        true,
                        trim( htmlspecialchars( $_POST['PropayAccountNumber'] ) )
                    );


                    $signupData = [
                        'AccountNumber' => trim( htmlspecialchars( $_POST['PropayAccountNumber'] ) ),
                        'Password'      => '',
                        'SourceEmail'   => $core_settings->email,
                        'Status'        => '00',
                        'Tier'          => 'Premium',
                    ];

                    $result = json_encode( $signupData );
                    $this->propay_api->setSignupInfo( json_encode( $result ) );

                    $signupInfo = json_decode( $result );

                    //TODO: reduce this code down to a function as we are using it in multiple places

                    $merchantProfileData = [
                        'ProfileName'      => substr( $_SESSION['accountUrlPrefix'] . '-' . $this->user->username . '-' . $signupInfo->AccountNumber, 0, 50 ),
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
                    $this->load->library( 'protectpayapi' );
                    $merchantProfileResponse    = $this->protectpayapi
                        ->setApiBaseUrl( PROTECT_PAY_API_BASE_URL )
                        ->setBillerId( PROTECT_PAY_BILLER_ID )
                        ->setAuthToken( PROTECT_PAY_AUTH_TOKEN )
                        ->createMerchantProfile( $merchantProfileData );
                    $merchantProfileData        = json_decode( $merchantProfileResponse );
                    $storeMerchantProfileStatus = $this->propay_api->storeMerchantProfile(
                        $_SESSION['accountUrlPrefix'],
                        $this->user->username,
                        $merchantProfileData
                    );


                    $this->session->set_flashdata( 'message', 'success:Payment settings updated successfully.' );
                    redirect( 'tinvoices' );
                } else {
                    if ( ! $isSignedUp ) {

                        $data = [
                            "PersonalData" => [
                                "SourceEmail"          => trim( htmlspecialchars( $_POST['SourceEmail'] ) ),
                                //  required
                                "FirstName"            => trim( htmlspecialchars( $_POST['FirstName'] ) ),
                                //20 required
                                "LastName"             => trim( htmlspecialchars( $_POST['LastName'] ) ),
                                //25 required
                                //TODO: this may need to be converted from what the front end provides
                                "DateOfBirth"          => trim( htmlspecialchars( $_POST['DateOfBirth'] ) ),
                                //10 required 1/19/1997
                                "SocialSecurityNumber" => trim( htmlspecialchars( $_POST['SocialSecurityNumber'] ) ),
                                //9 required
                                "PhoneInformation"     => [
                                    "DayPhone"     => trim( htmlspecialchars( $_POST['DayPhone'] ) ), //10 required
                                    "EveningPhone" => trim( htmlspecialchars( $_POST['EveningPhone'] ) ), //10 required
                                ]
                            ],

                            "SignupAccountData" => [
                                //"ExternalId" => "3212157",
                                "Tier" => "", // required '' = lowest cost 'Premium', 'Merchant' etc
                                //"PhonePIN" => "1234",
                            ],

                            //TODO: patch this if we decide we are allowing business accounts
                            //"BusinessData" => [
                            //    "BusinessLegalName" => "ProPay Partner",
                            //    "DoingBusinessAs" => "PPA",
                            //],

                            "Address" => [
                                "ApartmentNumber" => null,
                                "Address1"        => trim( htmlspecialchars( $_POST['Address1'] ) ),
                                //100 required
                                "Address2"        => trim( htmlspecialchars( $_POST['Address2'] ) ),
                                //100 required can be null
                                "City"            => trim( htmlspecialchars( $_POST['City'] ) ),
                                //30 required
                                "State"           => trim( htmlspecialchars( $_POST['State'] ) ),
                                //3 required
                                "Country"         => "USA",
                                //3 optional
                                "Zip"             => trim( htmlspecialchars( $_POST['Zip'] ) )
                                //5 or 9 characters required
                            ],

                            //TODO: patch this if we decide we are allowing business accounts
                            //"BusinessAddress" => [
                            //    "Address1" => "101 Main Street",
                            //    "Address2" => "Ste. 200",
                            //    "City" => "Rocky Hill",
                            //    "State" => "CT",
                            //    "Country" => "USA",
                            //    "Zip" => "06067"
                            //]

                        ];

                        if ( isset( $_POST['BankAccountNumber'] ) && isset( $_POST['BankName'] ) && isset( $_POST['RoutingNumber'] ) &&
                            trim( htmlspecialchars( $_POST['AccountType'] ) ) &&
                            trim( htmlspecialchars( $_POST['BankName'] ) ) &&
                            trim( htmlspecialchars( $_POST['BankAccountNumber'] ) ) &&
                            trim( htmlspecialchars( $_POST['RoutingNumber'] ) )
                        ) {
                            $data["BankAccount"] = [
                                //propay isn't international yet.
                                "AccountCountryCode"   => "USA",
                                "AccountOwnershipType" => "Personal",
                                //business would require other fields, existing design does not have this
                                "AccountType"          => trim( htmlspecialchars( $_POST['AccountType'] ) ),
                                //C.hecking S.avings G.General Ledger
                                "BankAccountNumber"    => trim( htmlspecialchars( $_POST['BankAccountNumber'] ) ),
                                //required
                                "BankName"             => trim( htmlspecialchars( $_POST['BankName'] ) ),
                                //50 required
                                "RoutingNumber"        => trim( htmlspecialchars( $_POST['RoutingNumber'] ) )
                                //required
                            ];
                        }

                        $result = $this->propay_api
                            ->setApiBaseUrl( explode( "/ProtectPay", PROTECT_PAY_API_BASE_URL )[0] )
                            ->setCertStr( PROPAY_CERT_STRING )
                            ->setTermId( PROTECT_PAY_TERM_ID )
                            ->setSignupData( $data )
                            ->processSignup()
                            ->getSignupInfo();

                        $signupInfo = json_decode( $result );

                        if ( $signupInfo->AccountNumber != 0 ) {

                            $signature = trim( htmlspecialchars( $_POST['signature'] ) );

                            $storeSignupInfoStatus = $this->propay_api->storeSignupInfo(
                                $_SESSION['accountUrlPrefix'],
                                $this->user->username,
                                $signature,
                                $_SERVER['REMOTE_ADDR'],
                                date( "Y-m-d H:i:s" )
                            );

                            $merchantProfileData = [
                                'ProfileName'      => substr( $_SESSION['accountUrlPrefix'] . '-' . $this->user->username . '-' . $signupInfo->AccountNumber, 0, 50 ),
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
                            $this->load->library( 'protectpayapi' );
                            $merchantProfileResponse    = $this->protectpayapi
                                ->setApiBaseUrl( PROTECT_PAY_API_BASE_URL )
                                ->setBillerId( PROTECT_PAY_BILLER_ID )
                                ->setAuthToken( PROTECT_PAY_AUTH_TOKEN )
                                ->createMerchantProfile( $merchantProfileData );
                            $merchantProfileData        = json_decode( $merchantProfileResponse );
                            $storeMerchantProfileStatus = $this->propay_api->storeMerchantProfile(
                                $_SESSION['accountUrlPrefix'],
                                $this->user->username,
                                $merchantProfileData
                            );

                            $from_email = EMAIL_FROM; //$core_settings->email
                            $this->email->from( $from_email, $core_settings->company );
                            $this->email->to( $signupInfo->SourceEmail );

                            //TODO: translate this
                            //$this->email->subject($this->lang->line('application_your_account_has_been_created'));
                            $this->email->subject( 'Your payment account has been created.' );
                            $this->email->message( '<br>Please refer to your email to set your payment password' .
                                '<br><br>Your account number is :' . $signupInfo->AccountNumber .
                                '<br>Your temporary password is ' . $signupInfo->Password .
                                '<br>You can login to your payment account at https://www.propay.com using your email: ' . $signupInfo->SourceEmail
                            );
                            $this->email->send();

                            $isSignedUp = $this->propay_api->isSignedUp( $parsedAccountUrlPrefix, $this->user->username );

                            $this->view_data['isSignedUp'] = $isSignedUp;
                        } else {
                            //TODO: we need to handle the case where the account already exists.
                            switch ( $signupInfo->Status ) {
                                case '00':
                                    break;
                                case '32':
                                    $this->view_data['error']        = "Invalid Zip Code";
                                    $this->view_data['registerdata'] = array_map( 'htmlspecialchars', $_POST );
                                    //deal with account alread exists case
                                    break;
                                case '87':
                                    $this->view_data['error']        = "Email address is already signed up for a payment account!";
                                    $this->view_data['registerdata'] = array_map( 'htmlspecialchars', $_POST );
                                    //deal with account alread exists case
                                    break;
                                default:
                                    $this->view_data['error']        = "Unknown error trying to signup! " . var_export( $signupInfo, true );
                                    $this->view_data['registerdata'] = array_map( 'htmlspecialchars', $_POST );
                                //stdClass::__set_state(array( 'AccountNumber' => 0, 'Password' => NULL, 'SourceEmail' => NULL, 'Status' => '59', 'Tier' => NULL, ))stdClass::__set_state(array( 'AccountNumber' => 0, 'Password' => NULL, 'SourceEmail' => NULL, 'Status' => '59', 'Tier' => NULL, ))
                                //var_export($signupInfo);
                                //die();
                                // handle unknown error, log or something.
                            }
                        }
                    } else {
                        $this->view_data['error'] = "You already have a propay account signed up for you in our system!";
                    }
                    //TODO: work with Propay to streamline notifications so that their email generated is whitelabeled so user
                    // never knows it's coming from propay
                    //TODO: redirect somewhere when complete
                }
                if ( $this->view_data['error'] ) {
                    $this->session->set_flashdata( 'message', 'error:' . $this->view_data['error'] );
                } else {
                    $this->session->set_flashdata( 'message', 'success:Payment settings updated successfully.' );
                    redirect('pinvoices');
                }
            }
            $this->view_data['isSignedUp']  = $isSignedUp;
            $this->view_data['settings']    = Setting::first();
            $this->content_view             = 'invoices/_propay_signup';
            $this->view_data['form_action'] = 'pinvoices/propay_signup';
            $this->view_data['title']       = $this->lang->line( 'application_payment_id' );
            $this->theme_view       = 'modal';
        }
    }

    function download($id = FALSE){
        $this->load->helper(array('dompdf', 'file'));
        $this->load->library('parser');
        $data["invoice"] = Invoice::find($id);
        $data['items'] = InvoiceHasItem::find('all',array('conditions' => array('invoice_id=?',$id)));
        $invoiceUser = InvoiceHasUser::find('all',array('conditions' => array('user_id=? AND invoice_id=?',$this->public->id,$id)));
        if (!count($invoiceUser) > 0 || count($invoiceUser) > 0 && $invoiceUser[0]->user_id != $this->public->id)
            redirect('pinvoices');
        $data["core_settings"] = Setting::first();
        $due_date = date($data["core_settings"]->date_format, human_to_unix($data["invoice"]->due_date.' 00:00:00'));
        $parse_data = array(
            'due_date' => $due_date,
            'invoice_id' => $data["core_settings"]->invoice_prefix.$data["invoice"]->reference,
            'client_link' => $data["core_settings"]->domain . '/pinvoices/view/' . $id . '?accessCode=' . md5( $_SESSION['accountUrlPrefix'] . $id),
            'company' => $data["core_settings"]->company,
        );
        $html = $this->load->view($data["core_settings"]->template. '/' .$data["core_settings"]->invoice_pdf_template, $data, true);
        $html = $this->parser->parse_string($html, $parse_data);
        $filename = $this->lang->line('application_invoice').'_'.$data["core_settings"]->invoice_prefix.$data["invoice"]->reference;
        pdf_create($html, $filename, TRUE);

    }

    function item( $id = false )
    {
        if ( $_POST ) {
            unset( $_POST['send'] );
            $_POST = array_map( 'htmlspecialchars', $_POST );
            if ( $_POST['name'] != "" ) {
                $_POST['name']  = $_POST['name'];
                $_POST['value'] = str_replace( ",", ".", $_POST['value'] );
                $_POST['type']  = $_POST['type'];
            } else {
                if ( $_POST['item_id'] == "-" ) {
                    $this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_add_item_error' ) );
                    redirect( 'pinvoices/view/' . $_POST['invoice_id'] );

                } else {
                    $rebill = explode( "_", $_POST['item_id'] );
                    if ( $rebill[0] == "rebill" ) {
                        $itemvalue             = Expense::find_by_id( $rebill[1] );
                        $_POST['name']         = $itemvalue->description;
                        $_POST['type']         = $_POST['item_id'];
                        $_POST['value']        = $itemvalue->value;
                        $itemvalue->rebill     = 2;
                        $itemvalue->invoice_id = $_POST['invoice_id'];
                        $itemvalue->save();
                    } else {
                        $itemvalue      = Item::find_by_id( $_POST['item_id'] );
                        $_POST['name']  = $itemvalue->name;
                        $_POST['type']  = $itemvalue->type;
                        $_POST['value'] = $itemvalue->value;
                    }

                }
            }

            $item = InvoiceHasItem::create( $_POST );
            if ( ! $item ) {
                $this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_add_item_error' ) );
            } else {
                $this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_add_item_success' ) );
            }
            redirect( 'pinvoices/view/' . $_POST['invoice_id'] );

        } else {
            $this->view_data['invoice'] = Invoice::find( $id );
            $this->view_data['items']   = Item::find( 'all', array( 'conditions' => array( 'inactive=?', '0' ) ) );
            $this->view_data['rebill']  = Expense::find( 'all', array(
                'conditions' => array(
                    'project_id=? and (rebill=? or invoice_id=?)',
                    $this->view_data['invoice']->project_id,
                    1,
                    $id
                )
            ) );


            $this->theme_view               = 'modal';
            $this->view_data['title']       = $this->lang->line( 'application_add_item' );
            $this->view_data['form_action'] = 'pinvoices/item';
            $this->content_view             = 'invoices/_item';
        }
    }

    function item_update( $id = false )
    {
        if ( $_POST ) {
            unset( $_POST['send'] );
            $_POST          = array_map( 'htmlspecialchars', $_POST );
            $_POST['value'] = str_replace( ",", ".", $_POST['value'] );
            $item           = InvoiceHasItem::find( $_POST['id'] );
            $item           = $item->update_attributes( $_POST );
            if ( ! $item ) {
                $this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_save_item_error' ) );
            } else {
                $this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_save_item_success' ) );
            }
            redirect( 'pinvoices/view/' . $_POST['invoice_id'] );

        } else {
            $this->view_data['invoice_has_items'] = InvoiceHasItem::find( $id );
            $this->theme_view                     = 'modal';
            $this->view_data['title']             = $this->lang->line( 'application_edit_item' );
            $this->view_data['form_action']       = 'pinvoices/item_update';
            $this->content_view                   = 'invoices/_item';
        }
    }

    function item_delete( $id = false, $invoice_id = false )
    {
        $item = InvoiceHasItem::find( $id );
        $item->delete();
        $this->content_view = 'invoices/view';
        if ( ! $item ) {
            $this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_delete_item_error' ) );
        } else {
            $this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_delete_item_success' ) );
        }
        redirect( 'pinvoices/view/' . $invoice_id );
    }

    function preview( $id = false, $attachment = false )
    {
        $this->load->helper( array( 'dompdf', 'file' ) );
        $this->load->library( 'parser' );
        $data["invoice"]       = Invoice::find( $id );
        $data['items']         = InvoiceHasItem::find( 'all', array( 'conditions' => array( 'invoice_id=?', $id ) ) );
        $data["core_settings"] = Setting::first();

        $due_date   = date( $data["core_settings"]->date_format, human_to_unix( $data["invoice"]->due_date . ' 00:00:00' ) );
        $parse_data = array(
            'due_date'    => $due_date,
            'invoice_id'  => $data["core_settings"]->invoice_prefix . $data["invoice"]->reference,
            'client_link' => $data["core_settings"]->domain . '/pinvoices/view/' . $id . '?accessCode=' . md5( $_SESSION['accountUrlPrefix'] . $id),
            'company'     => $data["core_settings"]->company,
            'client_id'   => $data["invoice"]->company->reference,
        );
        $html       = $this->load->view( $data["core_settings"]->template . '/' . $data["core_settings"]->invoice_pdf_template, $data, true );
        $html       = $this->parser->parse_string( $html, $parse_data );

        $filename = $this->lang->line( 'application_invoice' ) . '_' . $data["core_settings"]->invoice_prefix . $data["invoice"]->reference;
        pdf_create( $html, $filename, true, $attachment );
    }

    function previewHTML( $id = false )
    {
        $this->load->helper( array( 'file' ) );
        $this->load->library( 'parser' );
        $data["htmlPreview"]   = true;
        $data["invoice"]       = Invoice::find( $id );
        $data['items']         = InvoiceHasItem::find( 'all', array( 'conditions' => array( 'invoice_id=?', $id ) ) );
        $data["core_settings"] = Setting::first();

        $due_date           = date( $data["core_settings"]->date_format, human_to_unix( $data["invoice"]->due_date . ' 00:00:00' ) );
        $parse_data         = array(
            'due_date'    => $due_date,
            'invoice_id'  => $data["core_settings"]->invoice_prefix . $data["invoice"]->reference,
            'client_link' => $data["core_settings"]->domain . '/pinvoices/view/' . $id . '?accessCode=' . md5( $_SESSION['accountUrlPrefix'] . $id),
            'company'     => $data["core_settings"]->company,
            'client_id'   => $data["invoice"]->company->reference,
        );
        $html               = $this->load->view( $data["core_settings"]->template . '/' . $data["core_settings"]->invoice_pdf_template, $data, true );
        $html               = $this->parser->parse_string( $html, $parse_data );
        $this->theme_view   = 'blank';
        $this->content_view = 'invoices/_preview';
    }

    function sendinvoice( $id = false )
    {
        $this->load->helper( array( 'dompdf', 'file' ) );
        $this->load->library( 'parser' );

        $data["invoice"]       = Invoice::find( $id );
        $data['items']         = InvoiceHasItem::find( 'all', array( 'conditions' => array( 'invoice_id=?', $id ) ) );
        $data["core_settings"] = Setting::first();
        $due_date              = date( $data["core_settings"]->date_format, human_to_unix( $data["invoice"]->due_date . ' 00:00:00' ) );
        //Set parse values
        $parse_data = array(
            'client_contact' => $data["invoice"]->company->client->firstname . ' ' . $data["invoice"]->company->client->lastname,
            'client_company' => $data["invoice"]->company->name,
            'due_date'       => $due_date,
            'invoice_id'     => $data["core_settings"]->invoice_prefix . $data["invoice"]->reference,
            'invoice_value'  => $data["invoice"]->sum,
            'client_link'    => $data["core_settings"]->domain . '/pinvoices/view/' . $id . '?accessCode=' . md5( $_SESSION['accountUrlPrefix'] . $id),
            'company'        => $data["core_settings"]->company,
            'logo'           => '<img style="max-height: 50px; max-width: 200px; width: auto;" src="https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION["accountUrlPrefix"] . '/' . $data["core_settings"]->logo . '" alt="' . $data["core_settings"]->company . '"/>',
            'invoice_logo'   => '<img style="max-height: 50px; max-width: 200px; width: auto;" src="https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION["accountUrlPrefix"] . '/' . $data["core_settings"]->invoice_logo . '" alt="' . $data["core_settings"]->company . '"/>'
        );
        // Generate PDF
        $html     = $this->load->view( $data["core_settings"]->template . '/' . $data["core_settings"]->invoice_pdf_template, $data, true );
        $html     = $this->parser->parse_string( $html, $parse_data );
        $filename = $this->lang->line( 'application_invoice' ) . '_' . $data["core_settings"]->invoice_prefix . $data["invoice"]->reference;
        pdf_create( $html, $filename, false );
        //email
        $subject = $this->parser->parse_string( $data["core_settings"]->invoice_mail_subject, $parse_data );
        $from_email = EMAIL_FROM; //$data["core_settings"]->email
        $this->email->from( $from_email, $data["core_settings"]->company );
        if ( ! is_object( $data["invoice"]->company->client ) && $data["invoice"]->company->client->email == "" ) {
            $this->session->set_flashdata( 'message', 'error:This client company has no primary contact! Just add a primary contact.' );
            redirect( 'pinvoices/view/' . $id );
        }
        $this->email->to( $data["invoice"]->company->client->email );
        $this->email->reply_to( $data["core_settings"]->email, $data["core_settings"]->company );
        $this->email->subject( $subject );
        $this->email->attach( "files/temp/" . $filename . ".pdf" );


        $email_invoice = read_file( './application/views/' . $data["core_settings"]->template . '/templates/email_invoice.html' );
        $message       = $this->parser->parse_string( $email_invoice, $parse_data );
        $this->email->message( $message );
        if ( $this->email->send() ) {
            $this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_send_invoice_success' ) );
            if ( $data["invoice"]->status == "Open" ) {
                $data["invoice"]->update_attributes( array( 'status' => 'Sent', 'sent_date' => date( "Y-m-d" ) ) );
            }
            log_message( 'error', 'Invoice #' . $data["core_settings"]->invoice_prefix . $data["invoice"]->reference . ' has been send to ' . $data["invoice"]->company->client->email );
        } else {
            $this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_send_invoice_error' ) );
            log_message( 'error', 'ERROR: Invoice #' . $data["core_settings"]->invoice_prefix . $data["invoice"]->reference . ' has not been send to ' . $data["invoice"]->company->client->email . '. Please check your servers email settings.' );
        }
        unlink( "files/temp/" . $filename . ".pdf" );
        redirect( 'pinvoices/view/' . $id );
    }

    function update( $id = false, $getview = false )
    {
        if ( $_POST ) {
            unset( $_POST['send'] );
            unset( $_POST['_wysihtml5_mode'] );
            unset( $_POST['files'] );
            $id   = $_POST['id'];
            $view = false;
            if ( isset( $_POST['view'] ) ) {
                $view = $_POST['view'];
            }
            unset( $_POST['view'] );
            $invoice = Invoice::find( $id );
            if ( $_POST['status'] == "Paid" && ! isset( $_POST['paid_date'] ) ) {
                $_POST['paid_date'] = date( 'Y-m-d', time() );
            }
            if ( $_POST['status'] == "Sent" && $invoice->status != "Sent" && ! isset( $_POST['sent_date'] ) ) {
                $_POST['sent_date'] = date( 'Y-m-d', time() );
            }

            if ( empty( $_POST['discount'] ) ) {
                $_POST['discount'] = 0;
            }

            if ( empty( $_POST['tax'] ) ) {
                $_POST['tax'] = 0;
            }

            if ( empty( $_POST['second_tax'] ) ) {
                $_POST['second_tax'] = 0;
            }


            $invoice->update_attributes( $_POST );

            if ( ! $invoice ) {
                $this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_save_invoice_error' ) );
            } else {
                $this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_save_invoice_success' ) );
            }
            if ( $view == 'true' ) {
                redirect( 'pinvoices/view/' . $id );
            } else {
                redirect( 'pinvoices' );
            }

        } else {
            $this->view_data['invoice'] = Invoice::find( $id );
            if ( $this->user->admin != 1 ) {
                $comp_array = array();
                foreach ( $this->user->companies as $value ) {
                    array_push( $comp_array, $value->id );
                }
                $this->view_data['companies'] = $this->user->companies;
            } else {
                $this->view_data['companies'] = Company::find( 'all', array(
                    'conditions' => array(
                        'inactive=?',
                        '0'
                    )
                ) );
            }
            //$this->view_data['projects'] = Project::all();
            //$this->view_data['companies'] = Company::find('all',array('conditions' => array('inactive=?','0')));
            if ( $getview == "view" ) {
                $this->view_data['view'] = "true";
            }
            $this->theme_view               = 'modal';
            $this->view_data['title']       = $this->lang->line( 'application_edit_invoice' );
            $this->view_data['form_action'] = 'pinvoices/update';
            $this->content_view             = 'invoices/_invoice';

            /** @var CI_DB_mysql_driver $primaryDatabase */
            $primaryDatabase = $this->load->database('primary', TRUE);
            $params = [
                'primaryDatabase' => $primaryDatabase,
            ];
            $this->load->library('account', $params);
            $this->view_data['currencies'] = $this->account->getCurrencies();
            $this->view_data['selectedCurrency'] = $this->view_data['currencies'][0];
        }
    }

    function payment( $id = false )
    {

        if ( $_POST ) {
            unset( $_POST['send'] );
            unset( $_POST['_wysihtml5_mode'] );
            unset( $_POST['files'] );
            $_POST['user_id']  = $this->user->id;
            $_POST['amount']   = str_replace( ",", ".", $_POST['amount'] );
            $invoice           = Invoice::find_by_id( $_POST['invoice_id'] );
            $invoiceHasPayment = InvoiceHasPayment::create( $_POST );

            if ( $invoice->outstanding == $_POST['amount'] ) {
                $new_status   = "Paid";
                $payment_date = $_POST['date'];
            } else {
                $new_status = "PartiallyPaid";
            }

            $invoice->update_attributes( array( 'status' => $new_status ) );
            if ( isset( $payment_date ) ) {
                $invoice->update_attributes( array( 'paid_date' => $payment_date ) );
            }
            if ( ! $invoiceHasPayment ) {
                $this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_create_payment_error' ) );
            } else {
                $this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_create_payment_success' ) );
            }
            redirect( 'pinvoices/view/' . $_POST['invoice_id'] );
        } else {
            $this->view_data['invoice']           = Invoice::find_by_id( $id );
            $this->view_data['payment_reference'] = InvoiceHasPayment::count( array( 'conditions' => 'invoice_id = ' . $id ) ) + 1;
            $this->view_data['sumRest']           = sprintf( "%01.2f", round( $this->view_data['invoice']->sum - $this->view_data['invoice']->paid, 2 ) );


            $this->theme_view               = 'modal';
            $this->view_data['title']       = $this->lang->line( 'application_add_payment' );
            $this->view_data['form_action'] = 'pinvoices/payment';
            $this->content_view             = 'invoices/_payment';
        }
    }

    function payment_update( $id = false )
    {

        if ( $_POST ) {
            unset( $_POST['send'] );
            unset( $_POST['_wysihtml5_mode'] );
            unset( $_POST['files'] );
            $_POST['amount'] = str_replace( ",", ".", $_POST['amount'] );

            $payment    = InvoiceHasPayment::find_by_id( $_POST['id'] );
            $invoice_id = $payment->invoice_id;
            $payment    = $payment->update_attributes( $_POST );


            $invoice  = Invoice::find_by_id( $invoice_id );
            $payment  = 0;
            $i        = 0;
            $payments = $invoice->invoice_has_payments;
            if ( isset( $payments ) ) {
                foreach ( $payments as $value ) {
                    $payment = sprintf( "%01.2f", round( $payment + $payments[ $i ]->amount, 2 ) );
                    $i ++;
                }

            }
            $paymentsum = sprintf( "%01.2f", round( $payment + $_POST['amount'], 2 ) );
            if ( $invoice->sum <= $paymentsum ) {
                $new_status   = "Paid";
                $payment_date = $_POST['date'];

            } else {
                $new_status = "PartiallyPaid";
            }
            $invoice->update_attributes( array( 'status' => $new_status ) );
            if ( isset( $payment_date ) ) {
                $invoice->update_attributes( array( 'paid_date' => $payment_date ) );
            }
            if ( ! $payment ) {
                $this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_edit_payment_error' ) );
            } else {
                $this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_edit_payment_success' ) );
            }
            redirect( 'pinvoices/view/' . $_POST['invoice_id'] );

        } else {
            $this->view_data['payment']     = InvoiceHasPayment::find_by_id( $id );
            $this->view_data['invoice']     = Invoice::find_by_id( $this->view_data['payment']->invoice_id );
            $this->theme_view               = 'modal';
            $this->view_data['title']       = $this->lang->line( 'application_add_payment' );
            $this->view_data['form_action'] = 'pinvoices/payment_update';
            $this->content_view             = 'invoices/_payment';
        }
    }


    function changestatus( $id = false, $status = false )
    {
        $invoice = Invoice::find_by_id( $id );
        if ( $this->user->admin != 1 ) {
            $comp_array = array();
            foreach ( $this->user->companies as $value ) {
                array_push( $comp_array, $value->id );
            }
            if ( ! in_array( $invoice->company_id, $comp_array ) ) {
                return false;
            }
        }
        switch ( $status ) {
            case "Sent":
                $invoice->sent_date = date( "Y-m-d", time() );
                break;
            case "Paid":
                $invoice->paid_date = date( "Y-m-d", time() );
                break;
        }
        $invoice->status = $status;
        $invoice->save();
        die();
    }

}