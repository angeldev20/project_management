<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 10/16/17
 * Time: 3:22 PM
 */ ?>
<?php if ( ! defined( 'BASEPATH' ) ) {
	exit( 'No direct script access allowed' );
}

class Signup extends MY_Controller
{

	/** @var  account */
	public $account;

	/** @var  platformaws */
	public $platformaws;

	/** @var  MY_Email */
	public $email;

	/** @var  CI_Config */
	public $config;

	/** @var  CI_Parser */
	public $parser;

	/** @var  CI_Session */
	public $session;

	/** @var CI_Lang */
	public $lang;

	/** @var mailchimp */
	public $mailchimp;

    /**
     * Encrypts the string using the server defined SALT string
     * @param string $stringToEncrypt
     * @return string
     */
    public function encryptString($stringToEncrypt) {
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($stringToEncrypt, $cipher, SALT, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, SALT, $as_binary=true);
        return base64_encode( $iv.$hmac.$ciphertext_raw );
    }

	/**
	 * cleans all the post variables for showing again in the form
	 */
	public function cleanPostVariables()
	{
		if(isset($_POST['username'])) $_POST['username']  = trim( htmlspecialchars( $_POST['username'] ) );
        if(isset($_POST['name'])) $_POST['name']      = trim( htmlspecialchars( $_POST['name'] ) );
        if(isset($_POST['email'])) $_POST['email']     = trim( htmlspecialchars( $_POST['email'] ) );
        if(isset($_POST['firstname'])) $_POST['firstname'] = trim( htmlspecialchars( $_POST['firstname'] ) );
        if(isset($_POST['lastname'])) $_POST['lastname']  = trim( htmlspecialchars( $_POST['lastname'] ) );
	}

	public function index()
	{
        $this->view_data['emailSent']       = false;
		$core_settings = Setting::first();

		/** @var CI_DB_mysql_driver $primaryDatabase */
		$primaryDatabase = $this->load->database( 'primary', true );

		$params = [
			'primaryDatabase' => $primaryDatabase,
		];
		$this->load->library( 'account', $params );

		$planTypes                    = $this->account->getPlanTypes();
		$this->view_data['planTypes'] = $planTypes;

        if ( $_POST ) {
            $promoCode = (isset($_POST['promoCode'])) ? strtoupper(trim( htmlspecialchars( $_POST['promoCode'] ) )) : null;
            if ($promoCode == '') {
                $promoCode = null;
                unset($_POST['promoCode']);
            }
            if (($promoCode && $this->account->checkValidPromoCode($promoCode)) || !$promoCode) {

                $this->load->library('parser');
                $this->load->helper('file');
                $this->load->helper('notification');

                if (trim(htmlspecialchars($_POST['email'])) != "" && $_POST['password'] != ""
                    && $_POST['firstname'] != "" && $_POST['lastname'] != ""
                ) {

                    $from_email = EMAIL_FROM; //$core_settings->email
                    $this->email->from($from_email, $core_settings->company);
                    $this->email->to(trim(htmlspecialchars($_POST['email'])));

                    $this->email->subject($this->lang->line('application_your_account_has_been_created'));
                    $parse_data = array(
                        'link' => base_url() . 'signup/confirm?account=' . md5(json_encode($_POST)),
                        'company' => $core_settings->company,
                        'client_firstname' => trim(htmlspecialchars($_POST['firstname'])),
                        'client_lastname' => trim(htmlspecialchars($_POST['lastname'])),
                        'company_reference' => '',
                        // $company->reference, //TODO: find out what this is, and substitute something meaningful here
                        'logo' => '<img style="max-height: 50px; max-width: 200px; width: auto;" src="https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/spera/' . $core_settings->logo . '" alt="' . $core_settings->company . '"/>',
                        'invoice_logo' => '<img style="max-height: 50px; max-width: 200px; width: auto;" src="https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/spera/' . $core_settings->invoice_logo . '" alt="' . $core_settings->company . '"/>'
                    );
                    $email = read_file('./application/views/' . $core_settings->template . '/templates/email_confirmation.html');
                    $message = $this->parser->parse_string($email, $parse_data);
                    $this->email->message($message);
                    $this->email->set_smtp_conn_options(
                        [
                            'ssl' => [
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            ]
                        ]
                    );
                    $this->email->send();
                    $this->view_data['error'] = 'false';
                    $this->view_data['emailSent'] = true;
                    $this->session->set_userdata('register_email_confirmation', $_POST);

                    /** @var CI_DB_mysql_driver $primaryDatabase */
                    $primaryDatabase = $this->load->database( 'primary', true );

                    $params = [
                        'primaryDatabase' => $primaryDatabase,
                    ];

                    $this->load->library( 'account', $params );

                    $this->account->storeSignupInfo($_POST);

                    redirect('signup/email');
                    //TODO: if we decide to switch off email confirmation, take out the above line and put in the next line
                    // this will bypass email confirmation and go directly to domain setup
                    // redirect(base_url() . 'signup/confirm?account=' . md5(json_encode($_POST)) , 'refresh');
                } else {
                    $this->view_data['registerdata'] = array_map( 'htmlspecialchars', $_POST );
                    $this->view_data['error'] = 'error:' . ' Required fields are not filled.';
                    $this->view_data['message'] = 'error:' . ' Required fields are not filled.';
                    $this->session->set_flashdata('message', 'error: Required fields are not filled.');
                }
            } else {
                $this->view_data['registerdata'] = array_map( 'htmlspecialchars', $_POST );
                $this->view_data['error'] = 'error:' . ' Promo Code was not valid.';
                $this->view_data['message'] = 'error:' . ' Promo Code was not valid.';
                $this->session->set_flashdata('message', 'error: Promo Code was not valid.');
            }
		} else {
            if ( isset( $_GET['planType'] ) ) {
                $this->view_data['planType'] = $_GET['planType'];
            }
            if ( isset( $_GET['promoCode'] ) ) {
                $this->view_data['promoCode'] = $_GET['promoCode'];
            }
            $this->view_data['error']       = 'false';
        }
        if ( isset( $_GET['planType'] ) ) {
            $this->view_data['planType'] = $_GET['planType'];
            $this->view_data['form_action'] = 'signup/index?planType=' . $_GET['planType'];
        } else {
            $this->view_data['form_action'] = 'signup/index';
        }
		$this->theme_view               = 'signup';
		$this->content_view             = 'signup/index';

	}

	public function email() {
        $this->theme_view               = 'signup';
        $this->content_view             = 'signup/email';
        $this->view_data['form_action'] = 'signup/email';
    }

	public function confirm()
	{

		$core_settings = Setting::first();
		$signup_data    = $this->session->userdata( 'register_email_confirmation' );
		//TODO: we need to query the plans in the DB to make sure the plan is still one that is offered
        if(!isset($signup_data['planType']))
            redirect('https://spera.io' , 'refresh');

		/** @var CI_DB_mysql_driver $primaryDatabase */
		$primaryDatabase = $this->load->database( 'primary', true );

		$params = [
			'primaryDatabase' => $primaryDatabase,
		];

		$this->load->library( 'account', $params );

		$planTypes = $this->account->getPlanTypes();

		$this->view_data['planTypes'] = $planTypes;

		if ( ! $signup_data ) {
			redirect( 'signup/index' );
		}

        if (!$_POST ) {
            if ( md5(json_encode($signup_data)) != $_REQUEST['account']) {
                redirect( 'signup/index' );
            }
            $this->view_data['error']       = 'false';
            $this->theme_view               = 'signup';
            $this->content_view             = 'signup/confirm';
            $this->view_data['form_action'] = 'signup/confirm';
            $this->view_data['planType'] = $signup_data['planType'];
            if(isset($signup_data['promoCode'])) $this->view_data['promoCode'] = $signup_data['promoCode'];
        } else {
			$this->load->library( 'parser' );
			$this->load->helper( 'file' );
			$this->load->helper( 'notification' );


			if($signup_data) {
				$_POST = array_merge( $_POST, $signup_data );
			}

			$username         = strtolower( str_replace( ' ', '', trim( htmlspecialchars( $_POST['username'] ) ) ) );
			$password         = trim( htmlspecialchars( $_POST['password'] ) );
			$modifiedUsername = $username;

			$accountExists = $this->account->accountExists( $username );
			if ( in_array( substr( $username, 0, 1 ), explode( ',', '0,1,2,3,4,5,6,7,8,9' ) ) ) {
				$modifiedUsername = 'z' . $username;
			}

			if ( ! $accountExists && trim( htmlspecialchars( $_POST['username'] ) ) != "" ) {
				if ( ! $this->account->mysqlUserExists( ( ENVIRONMENT == 'production' ) ? $modifiedUsername : $modifiedUsername . '_' . ENVIRONMENT ) ) {

					$this->load->library( 'platformaws', [
						'aws_access_key' => $this->config->item( 'aws_access_key' ),
						'aws_secret_key' => $this->config->item( 'aws_secret_key' )
					] );

					$environment = ENVIRONMENT;
					$bucket      = "spera-" . $environment;
					$keyname     = "platform_312_default.min.mysql";

					/** @var Aws\S3\S3Client $s3 */
					$s3 = $this->platformaws->getS3Client();

					try {
						$accountSql = (string) $this->platformaws->getObject( $bucket, $keyname )['Body'];
					} catch ( Exception $e ) {
						echo $e->getMessage() . "\n";
						die();
					}

					$databasePrefix = strtolower( str_replace( ' ', '', trim( htmlspecialchars( $_POST['username'] ) ) ) );

					$accountUrlPrefix = $databasePrefix;

					if ( in_array( substr( $databasePrefix, 0, 1 ), explode( ',', '0,1,2,3,4,5,6,7,8,9' ) ) ) {
						$databasePrefix = 'z' . $databasePrefix;
					}

					if ( strlen( $accountUrlPrefix ) < strlen( $databasePrefix ) && strlen( $databasePrefix ) > 16 ) {
						$this->theme_view               = 'signup';
						$this->content_view             = 'signup/confirm';
						$this->view_data['form_action'] = 'signup/confirm';
						$this->cleanPostVariables();
						$this->view_data['registerdata'] = array_map( 'htmlspecialchars', $_POST );
						$this->view_data['error']        = "Usernames starting with a number must be 15 characters or less!";
					} else {
					    $subPost = $_POST;
                        unset($subPost['planType']);
                        unset($subPost['firstname']);
                        unset($subPost['lastname']);
                        $encryptedLogin = urlencode($this->encryptString(json_encode($subPost)));

						$databaseName = $databasePrefix . '_' . ENVIRONMENT;

						$sql = "DROP DATABASE IF EXISTS " . $databaseName . ";";
						$primaryDatabase->query( $sql, [] );

						$sql = "CREATE DATABASE " . $databaseName . ";";
						$primaryDatabase->query( $sql, [] );

						$_SESSION['accountDatabasePrefix'] = $databasePrefix;

						/** @var CI_DB_mysql_driver $accountDatabase */
						$accountDatabase = $this->load->database( $databaseName, true );

						$this->account
							->setAccountDatabase( $accountDatabase )
							->setDatabaseName( $databaseName )
							->setAccountSql( $accountSql );

						$response = $this->account->create();

						$signupData = [
							'Username'                => $username,
							'Firstname'               => trim( htmlspecialchars( $_POST['firstname'] ) ),
							'Lastname'                => trim( htmlspecialchars( $_POST['lastname'] ) ),
							'Password'                => $password,
							'Email'                   => trim( htmlspecialchars( $_POST['email'] ) ),
							'AccountName'             => $databasePrefix, //trim( htmlspecialchars( $_POST['name'] ) ),
							//could be company name or individual name here
							'AccountContactFirstName' => trim( htmlspecialchars( $_POST['firstname'] ) ),
							'AccountContactLastName'  => trim( htmlspecialchars( $_POST['lastname'] ) ),
						];

						if(isset($_POST['promoCode'])) $signupData['promoCode'] = $_POST['promoCode'];

						unset( $_SESSION['accountDatabasePrefix'] );

						if ( ! $this->account->signup( $accountUrlPrefix, $databaseName, $signupData ) ) {
							//TODO: set error messages appropriately here, there could be many points of failure
							$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_registration_error' ) );
							$this->view_data['error'] = "Account name is already taken!";
						} else {

							$this->load->library( 'mailchimp', null);

							if ( isset( $_POST['planType'] ) ) {
								$this->account->storeAccountPlan( trim( htmlspecialchars( $_POST['planType'] ) ) );
							}
							$success    = $this->platformaws->copyPath(
								$bucket,
								$bucket,
								'default/',
								$username . '/'
							);
							$from_email = EMAIL_FROM; //$core_settings->email
							$this->email->from( $from_email, $core_settings->company );
							$this->email->to( trim( htmlspecialchars( $_POST['email'] ) ) );

							$this->email->subject( $this->lang->line( 'application_your_account_has_been_created' ) );
                            $accountName = strtolower( str_replace( ' ', '', trim( htmlspecialchars( $_POST['username'] ) ) ) );
                            $domainParts = explode( '.', $_SERVER['HTTP_HOST'] );
                            $domain      = $domainParts[ count( $domainParts ) - 2 ] . '.' . $domainParts[ count( $domainParts ) - 1 ];
							$parse_data = array(
								'link'              => 'https://' . $accountName . '.' . $domain . '/login?login=' . $encryptedLogin,
								'company'           => $core_settings->company,
								'client_company'    => $databasePrefix, //trim( htmlspecialchars( $_POST['name'] ) ),
								'company_reference' => '',
								// $company->reference, //TODO: find out what this is, and substitute something meaningful here
								'logo'              => '<img style="max-height: 50px; max-width: 200px; width: auto;" src="https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/spera/' . $core_settings->logo . '" alt="' . $core_settings->company . '"/>',
								'invoice_logo'      => '<img style="max-height: 50px; max-width: 200px; width: auto;" src="https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/spera/' . $core_settings->invoice_logo . '" alt="' . $core_settings->company . '"/>'
							);
							$email      = read_file( './application/views/' . $core_settings->template . '/templates/email_create_account.html' );
							$message    = $this->parser->parse_string( $email, $parse_data );
							$this->email->message( $message );
                            $this->email->set_smtp_conn_options(
                                [
                                    'ssl' => [
                                        'verify_peer' => false,
                                        'verify_peer_name' => false,
                                        'allow_self_signed' => true
                                    ]
                                ]
                            );
							$this->email->send();
							send_notification( $core_settings->email, $this->lang->line( 'application_new_client_has_registered' ), $this->lang->line( 'application_new_client_has_registered' ) .$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_registration_success' )));

							$this->session->set_flashdata( 'message', 'success:test' );
							$this->session->unset_userdata( 'register_step1_data' );

							if (ENVIRONMENT == 'production') {
								$response = $this->mailchimp->setFirstName( trim( htmlspecialchars( $_POST['firstname'] ) ) )
								                            ->setLastName( trim( htmlspecialchars( $_POST['lastname'] ) ) )
								                            ->setEmail( trim( htmlspecialchars( $_POST['email'] ) ) )
								                            ->addEmailToSubscriberList();
							}
							redirect('https://' . $accountName . '.' . $domain . '/login?login=' . $encryptedLogin , 'refresh');
						}
					}
				} else {
					$this->theme_view               = 'signup';
					//TODO: restore the md5 link or session at this point?
					$this->content_view             = 'signup/confirm';
					$this->view_data['form_action'] = 'signup/confirm';
					$this->cleanPostVariables();
					$this->view_data['registerdata'] = array_map( 'htmlspecialchars', $_POST );
					$this->view_data['error']        = "Username already exists!";
				}
			} else {
				if ( $accountExists ) {
					$this->view_data['error'] = "Username is already taken!";
				}
				$this->theme_view               = 'signup';
				$this->content_view             = 'signup/confirm';
				$this->view_data['form_action'] = 'signup/confirm';
				$this->cleanPostVariables();
				$this->view_data['registerdata'] = array_map( 'htmlspecialchars', $_POST );
			}
		}
	}

	public function thankyou() {
		$this->theme_view               = 'thankyou';
		$this->content_view             = 'thankyou/thankyou';
		$this->view_data['form_action'] = 'thankyou/thankyou';
	}
}