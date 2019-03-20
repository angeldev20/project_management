<?php if ( ! defined( 'BASEPATH' ) ) {
	exit( 'No direct script access allowed' );
}
error_reporting( E_ALL ^ E_DEPRECATED );

class Settings extends MY_Controller
{

	/** @var  propay_api */
	public $propay_api;

	/** @var  protectpayapi */
	public $protectpayapi;

	/** @var  MY_Email */
	public $email;

	/** @var  CI_Session */
	public $session;

	/** @var  CI_Lang */
	public $lang;

	/** @var  CI_Config */
	public $config;

	/** @var  CI_Upload */
	public $upload;

    /** @var  platformaws */
    public $platformaws;

    /** @var account */
    public $account;

	function __construct()
	{
		parent::__construct();
		$access = false;
		unset( $_POST['DataTables_Table_0_length'] );
		if ( $this->client ) {
			redirect( 'cprojects' );
		} elseif ( $this->user ) {
			foreach ( $this->view_data['menu'] as $key => $value ) {
				if ( $value->link == "settings" ) {
					$access = true;
				}
			}
			if ( ! $access ) {
				redirect( 'login' );
			}
		} else {
			redirect( 'login' );
		}
		if ( ! $this->user->admin ) {
			$this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_no_access' ) );
			redirect( 'dashboard' );
		}
		$this->view_data['submenu'] = array(
			$this->lang->line( 'application_settings' )          => 'settings',
			$this->lang->line( 'application_templates' )         => 'settings/templates',
			$this->lang->line( 'application_pdf_templates' )     => 'settings/invoice_templates',
			$this->lang->line( 'application_calendar' )          => 'settings/calendar',
			//$this->lang->line( 'application_paypal' )               => 'settings/paypal',
			$this->lang->line( 'application_payments' )          => 'settings/merchant',
			$this->lang->line( 'application_payments_received')  => 'settings/account_payments',
			$this->lang->line( 'application_bank_transfer' )     => 'settings/bank_transfer',
			$this->lang->line( 'application_users' )             => 'settings/users',
			$this->lang->line( 'application_registration' )      => 'settings/registration',
			$this->lang->line( 'application_system_updates' )    => 'settings/updates',
			$this->lang->line( 'application_backup' )            => 'settings/backup',
			$this->lang->line( 'application_cronjob' )           => 'settings/cronjob',
			$this->lang->line( 'application_ticket' )            => 'settings/ticket',
			$this->lang->line( 'application_customize' )         => 'settings/customize',
			$this->lang->line( 'application_theme_options' )     => 'settings/themeoptions',
			$this->lang->line( 'application_smtp_settings' )     => 'settings/smtp_settings',
			$this->lang->line( 'application_logs' )              => 'settings/logs',
            $this->lang->line( 'application_links' )             => 'settings/links',

		);
		$this->config->load( 'defaults' );
		$settings                        = Setting::first();
		$this->view_data['update_count'] = false;
	}

	function index()
	{
		$this->view_data['breadcrumb']    = $this->lang->line( 'application_settings' );
		$this->view_data['breadcrumb_id'] = "settings";

		$this->view_data['settings']    = Setting::first();
		$this->view_data['form_action'] = 'settings/settings_update';
		$this->content_view             = 'settings/settings_all';

		$this->load->helper( 'curl' );
		$object = remote_get_contents( 'http://fc2.luxsys-apps.com/updates/xml.php?code=' . $this->view_data['settings']->pc, 1 );
		$object = json_decode( $object );

		if ( isset( $object->error ) && isset( $object->lastupdate ) ) {
			if ( $object->error == false && $object->lastupdate > $this->view_data['settings']->version ) {
				$this->view_data['update_count'] = "1";
			}
		}
	}

	function settings_update()
	{
		if ( $_POST ) {
            $this->load->library('platformaws', ['aws_access_key' => $this->config->item('aws_access_key'), 'aws_secret_key' => $this->config->item('aws_secret_key')]);
            $environment = ENVIRONMENT;
            $bucket = "spera-" . $environment;

			$has_errors              = false;
			$config['upload_path']   = './files/media/';
			$config['allowed_types'] = 'gif|jpg|png|ico';
			$config['max_size']      = 1024 * 1024 * 1024; // 1gb
			//$config['max_width']     = '300';
			//$config['max_height']    = '300';

			$this->load->library( 'upload', $config );

            if ( $_FILES['userfile']['size'] > 0 ) {
                if (!$this->upload->do_upload('userfile')) {
                    $error = $this->upload->display_errors('', ' ');
                    if ($error != "You did not select a file to upload. ") {
                        $this->session->set_flashdata('message', 'error:' . $error);
                        $has_errors = true;
                    }
                } else {
                    $data = array('upload_data' => $this->upload->data());
                    $_POST['logo'] = explode('./', $config['upload_path'])[1] . $data['upload_data']['file_name'];
                    $s3UploadPathAndFile = $_SESSION['accountUrlPrefix'] .
                        '/' . $_POST['logo'];
                    $result = $this->platformaws->putObjectFile(
                        $bucket,
                        $s3UploadPathAndFile,
                        './' . $_POST['logo']
                    );
                    unlink('./' . $_POST['logo']);

                }
            }

			if ( $_FILES['userfile2']['size'] > 0 ) {
				if ( ! $this->upload->do_upload( "userfile2" ) ) {
					$error = $this->upload->display_errors( '', ' ' );
					if ( $error != "You did not select a file to upload. " ) {
						$this->session->set_flashdata( 'message', 'error:' . $error );
						$has_errors = true;
					}
				} else {
                    $data                  = array( 'upload_data' => $this->upload->data() );
                    $_POST['invoice_logo'] = explode('./',$config['upload_path'])[1] . $data['upload_data']['file_name'];
                    $s3UploadPathAndFile = $_SESSION['accountUrlPrefix'] .
                        '/' . $_POST['invoice_logo'];
                    $result = $this->platformaws->putObjectFile(
                        $bucket,
                        $s3UploadPathAndFile,
                        './' . $_POST['invoice_logo']
                    );
                    unlink('./' . $_POST['invoice_logo']);
                }
			}

			if ( $_FILES['favicon']['size'] > 0 ) {
				if ( ! $this->upload->do_upload( "favicon" ) ) {
					$error = $this->upload->display_errors( '', ' ' );
					if ( $error != "You did not select a file to upload. " ) {
						$this->session->set_flashdata( 'message', 'error:' . $error );
						$has_errors = true;
					}
				} else {
					$data             = array( 'upload_data' => $this->upload->data() );
					$_POST['favicon'] = "files/media/" . $data['upload_data']['file_name'];
                    $s3UploadPathAndFile = $_SESSION['accountUrlPrefix'] .
                        '/' . $_POST['favicon'];
                    $result = $this->platformaws->putObjectFile(
                        $bucket,
                        $s3UploadPathAndFile,
                        './' . $_POST['favicon']
                    );
                    unlink('./' . $_POST['favicon']);
				}
			}

			unset( $_POST['userfile'] );
			unset( $_POST['userfile2'] );
			unset( $_POST['file-name'] );
			unset( $_POST['file-name2'] );
			unset( $_POST['_wysihtml5_mode'] );
			unset( $_POST['send'] );

			if ( ! $has_errors ) {
				$settings = Setting::first();
				$settings->update_attributes( $_POST );
				$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_save_settings_success' ) );
			}
			redirect( 'settings' );
		} else {
			$this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_save_settings_error' ) );
			redirect( 'settings' );
		}
	}

	function create_favicon() {
		if ( $_POST ) {
			$this->load->library('platformaws', ['aws_access_key' => $this->config->item('aws_access_key'), 'aws_secret_key' => $this->config->item('aws_secret_key')]);
			$environment = ENVIRONMENT;
			$bucket = "spera-" . $environment;

			$has_errors              = false;
			$config['upload_path']   = './files/media/';
			$config['allowed_types'] = 'gif|jpg|png|ico';
			$config['max_size']      = 1024 * 1024 * 1024; // 1gb
			//$config['max_width']     = '300';
			//$config['max_height']    = '300';

			$this->load->library( 'upload', $config );

			if ( $_FILES['favicon']['size'] > 0 ) {
				if ( ! $this->upload->do_upload( "favicon" ) ) {
					$error = $this->upload->display_errors( '', ' ' );
					if ( $error != "You did not select a file to upload. " ) {
						$this->session->set_flashdata( 'message', 'error:' . $error );
						$has_errors = true;
					}
				} else {
					$data             = array( 'upload_data' => $this->upload->data() );
					$_POST['favicon'] = "files/media/" . $data['upload_data']['file_name'];
					$s3UploadPathAndFile = $_SESSION['accountUrlPrefix'] .
					                       '/' . $_POST['favicon'];
					$result = $this->platformaws->putObjectFile(
						$bucket,
						$s3UploadPathAndFile,
						'./' . $_POST['favicon']
					);
					unlink('./' . $_POST['favicon']);
				}
			}

			unset( $_POST['userfile'] );
			unset( $_POST['userfile2'] );
			unset( $_POST['file-name'] );
			unset( $_POST['file-name2'] );
			unset( $_POST['_wysihtml5_mode'] );
			unset( $_POST['send'] );

			if ( ! $has_errors ) {
				$settings = Setting::first();
				$settings->update_attributes( $_POST );
			}
			echo json_encode( [
				'status' => 1
			] );
			die();
		} else {
			echo json_encode( [
				'status' => 0
			] );
			die();

		}

	}

	function remove_favicon() {
		$settings = Setting::first();
		$settings->update_attributes(['favicon' => 'assets/blueline/img/favicon.ico']);
		echo json_encode( [
			'status' => 1
		] );
		die();
	}

	function settings_reset( $template = false )
	{
		$this->load->helper( 'file' );
		$settings = Setting::first();
		if ( $template ) {
			$data = read_file( './application/views/' . $settings->template . '/templates/default/' . $template . '.html' );
			if ( write_file( './application/views/' . $settings->template . '/templates/' . $template . '.html', $data ) ) {
				$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_reset_mail_body_success' ) );
				redirect( 'settings/templates' );
			}


		}

	}

	function templates( $template = "invoice" )
	{
		$this->load->helper( 'file' );
		$settings                       = Setting::first();
		$filename                       = './application/views/' . $settings->template . '/templates/email_' . $template . '.html';
		$this->view_data['folder_path'] = '/application/views/' . $settings->template . '/templates/ ';
		if ( ! is_writable( $filename ) ) {
			$this->view_data['not_writable'] = true;
		} else {
			$this->view_data['not_writable'] = false;
		}
		$this->view_data['breadcrumb']    = $this->lang->line( 'application_templates' );
		$this->view_data['breadcrumb_id'] = "templates";

		$this->view_data['breadcrumb_sub']    = $this->lang->line( 'application_' . $template );
		$this->view_data['breadcrumb_sub_id'] = $template;

		if ( $_POST ) {
			$data = html_entity_decode( $_POST["mail_body"] );

			unset( $_POST["mail_body"] );
			unset( $_POST["send"] );

			$settings->update_attributes( $_POST );
			if ( write_file( './application/views/' . $settings->template . '/templates/email_' . $template . '.html', $data ) ) {
				$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_save_template_success' ) );
				redirect( 'settings/templates/' . $template );
			} else {
				$this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_save_template_error' ) );
				redirect( 'settings/templates/' . $template );
			}
		} else {

			$this->view_data['email']          = read_file( './application/views/' . $settings->template . '/templates/email_' . $template . '.html' );
			$this->view_data['template']       = $template;
			$this->view_data['template_files'] = get_filenames( './application/views/' . $settings->template . '/templates/default/' );
			$this->view_data['template_files'] = str_replace( '.html', '', $this->view_data['template_files'] );
			$this->view_data['template_files'] = str_replace( 'email_', '', $this->view_data['template_files'] );

			$this->view_data['settings']    = Setting::first();
			$this->view_data['form_action'] = 'settings/templates/' . $template;
			$this->content_view             = 'settings/templates';
		}

	}

	function invoice_templates( $dest = false, $template = false )
	{
		$this->load->helper( 'file' );
		$settings                       = Setting::first();
		$filename                       = './application/views/' . $settings->template . '/templates/invoice/default.php';
		$this->view_data['folder_path'] = '/application/views/' . $settings->template . '/templates/ ';

		$this->view_data['breadcrumb']    = $this->lang->line( 'application_pdf_templates' );
		$this->view_data['breadcrumb_id'] = "pdf_templates";
		if ( $_POST ) {

			unset( $_POST["send"] );
			if ( ! isset( $_POST["pdf_path"] ) ) {
				$_POST["pdf_path"] = 0;
			}
			$settings->update_attributes( $_POST );
			if ( $settings ) {
				$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_save_template_success' ) );
				redirect( 'settings/invoice_templates/' );
			} else {
				$this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_save_template_error' ) );
				redirect( 'settings/invoice_templates/' );
			}
		} else {
			if ( $dest && $template ) {
				$DBdest          = $dest . "_pdf_template";
				$attr            = array();
				$attr[ $DBdest ] = 'templates/' . $dest . '/' . $template;
				$settings->update_attributes( $attr );
				redirect( 'settings/invoice_templates' );
			} else {


				$this->view_data['invoice_template_files']  = get_filenames( './application/views/' . $settings->template . '/templates/invoice/' );
				$this->view_data['invoice_template_files']  = str_replace( '.php', '', $this->view_data['invoice_template_files'] );
				$this->view_data['estimate_template_files'] = get_filenames( './application/views/' . $settings->template . '/templates/estimate/' );
				$this->view_data['estimate_template_files'] = str_replace( '.php', '', $this->view_data['estimate_template_files'] );

				$this->view_data['settings']        = Setting::first();
				$invoice_templates                  = explode( "/", $this->view_data['settings']->invoice_pdf_template );
				$active_template                    = end( $invoice_templates );
				$this->view_data['active_template'] = str_replace( '.php', '', $active_template );

				$estimate_templates                          = explode( "/", $this->view_data['settings']->estimate_pdf_template );
				$active_estimate_template                    = end( $estimate_templates );
				$this->view_data['active_estimate_template'] = str_replace( '.php', '', $active_estimate_template );

				$this->view_data['form_action'] = 'settings/invoice_templates/' . $template;
				$this->content_view             = 'settings/invoice_templates';
			}
		}

	}

	function paypal()
	{
		$this->view_data['breadcrumb']    = $this->lang->line( 'application_paypal' );
		$this->view_data['breadcrumb_id'] = "paypal";

		if ( $_POST ) {

			unset( $_POST['send'] );
			if ( isset( $_POST['paypal'] ) ) {
				if ( $_POST['paypal'] != "1" ) {
					$_POST['paypal'] = "0";
				}
			} else {
				$_POST['paypal'] = "0";
			}
			$settings = Setting::first();
			$settings->update_attributes( $_POST );
			if ( $settings ) {
				$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_save_settings_success' ) );
				redirect( 'settings/paypal' );
			} else {
				$this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_save_settings_error' ) );
				redirect( 'settings/paypal' );
			}
		} else {

			$this->view_data['settings']    = Setting::first();
			$this->view_data['form_action'] = 'settings/paypal';
			$this->content_view             = 'settings/paypal';
		}
	}

	public function merchant()
	{
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
			$this->load->library('account', $params);

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

					//TODO: reduce this code down to a functon as we are using it in multiple places

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
					$this->account->logApiCall($_SESSION['accountUrlPrefix'] , $this->user->username, 'crypto_purchase', json_encode($signupInfo), $merchantProfileResponse);
					redirect( 'settings/merchant' );
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
							unset($data["PersonalData"]["SocialSecurityNumber"]);
							$this->account->logApiCall($_SESSION['accountUrlPrefix'] , $this->user->username, 'propay_signup', json_encode($data), $merchantProfileResponse);
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
					redirect( 'settings/merchant' );
				}
			}
			$this->view_data['isSignedUp']  = $isSignedUp;
			$this->view_data['settings']    = Setting::first();
			$this->content_view             = 'settings/merchant';
			$this->view_data['form_action'] = 'settings/merchant';
		}
	}

	function calendar()
	{
		$this->view_data['breadcrumb']    = $this->lang->line( 'application_calendar' );
		$this->view_data['breadcrumb_id'] = "calendar";

		if ( $_POST ) {

			unset( $_POST['send'] );

			$settings = Setting::first();
			$settings->update_attributes( $_POST );
			if ( $settings ) {
				$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_save_settings_success' ) );
				redirect( 'settings/calendar' );
			} else {
				$this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_save_settings_error' ) );
				redirect( 'settings/calendar' );
			}
		} else {

			$this->view_data['settings']    = Setting::first();
			$this->view_data['form_action'] = 'settings/calendar';
			$this->content_view             = 'settings/calendar';
		}
	}

	function payments()
	{
		$this->view_data['breadcrumb']    = $this->lang->line( 'application_payments' );
		$this->view_data['breadcrumb_id'] = "payments";

		if ( $_POST ) {

			unset( $_POST['send'] );
			if ( isset( $_POST['stripe'] ) ) {
				if ( $_POST['stripe'] != "1" ) {
					$_POST['stripe'] = "0";
				}
				if ( $_POST['stripe_ideal'] != "1" ) {
					$_POST['stripe_ideal'] = "0";
				}
			} else {
				$_POST['stripe'] = "0";
			}

			if ( isset( $_POST['authorize_net'] ) ) {
				if ( $_POST['authorize_net'] != "1" ) {
					$_POST['authorize_net'] = "0";
				}
			} else {
				$_POST['authorize_net'] = "0";
			}

			if ( isset( $_POST['twocheckout'] ) ) {
				if ( $_POST['twocheckout'] != "1" ) {
					$_POST['twocheckout'] = "0";
				}
			} else {
				$_POST['twocheckout'] = "0";
			}

			$settings = Setting::first();
			$settings->update_attributes( $_POST );
			if ( $settings ) {
				$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_save_settings_success' ) );
				redirect( 'settings/payments' );
			} else {
				$this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_save_settings_error' ) );
				redirect( 'settings/payments' );
			}
		} else {

			$this->view_data['settings']    = Setting::first();
			$this->view_data['form_action'] = 'settings/payments';
			$this->content_view             = 'settings/stripe';
		}
	}

	function account_payments( $TransactionId = false) {
		$parsedAccountUrlPrefix = $_SESSION['accountUrlPrefix'];

		if(in_array(substr($parsedAccountUrlPrefix,0,1),explode(',','0,1,2,3,4,5,6,7,8,9')))
			$parsedAccountUrlPrefix = 'z' . $parsedAccountUrlPrefix;

		$databaseName = $parsedAccountUrlPrefix . '_' . ENVIRONMENT;

		/** @var CI_DB_mysql_driver $primaryDatabase */
		$primaryDatabase = $this->load->database( 'primary', true );

		$params = [
			'databaseName'    => $databaseName,
			'primaryDatabase' => $primaryDatabase
		];
		$this->load->library('account', $params);

		if ($TransactionId) {
            $payment = $this->account->getAccountPaymentByTransaction($_SESSION['accountUrlPrefix'], $TransactionId);
			if ($payment) {
				if ( $payment->starting_plan != $payment->ending_plan ) {
					$_SESSION['billing']['OldPlanType'] = $payment->starting_plan;
				}
				$_SESSION['billing']['TransactionId']               = $payment->TransactionId;
				$_SESSION['billing']['HostedTransactionIdentifier'] = $payment->HostedTransactionIdentifier;
				$_SESSION['billing']['GrossPaid']                   = $payment->GrossAmt;
				$_SESSION['billing']['NetPaid']                     = $payment->NetAmt;
				$_SESSION['billing']['UserCount']                   = $payment->user_count;
				$_SESSION['billing']['UpgradeReason']               = $payment->change_reason;
				$_SESSION['billing']['PlanType']                    = 'pro_monthly';
			} else {
				$this->view_data['just_paid'] = false;
			}
		} else {
			unset ($_SESSION['billing']);
			//$_SESSION['billing']['OldPlanType'] = 'hustle_monthly';
			//$_SESSION['billing']['TransactionId'] = 12;
			//$_SESSION['billing']['HostedTransactionIdentifier'] = 'blah-blah-blah';
			//$_SESSION['billing']['GrossPaid'] = 39.90;
			//$_SESSION['billing']['NetPaid'] = 36.55;
			//$_SESSION['billing']['UserCount'] = 2;
			//$_SESSION['billing']['UpgradeReason'] = 'Number of users increased.';
			//$_SESSION['billing']['PlanType'] = 'pro_monthly';

			//TODO: make sure these are in the translations and menu list
			$this->view_data['just_paid'] = (isset($_SESSION['billing']['PlanType'])) ? true : false;
		}
		$this->view_data['breadcrumb']    = $this->lang->line( 'application_account_payments' );
		$this->view_data['breadcrumb_id'] = "accountpayments";


		$this->view_data['settings']    = Setting::first();
		$this->view_data['form_action'] = 'settings/account_payments';
		$this->view_data['title'] = 'Payment Completed';
		$this->view_data['paymentHistory'] = $this->account->getAccountPaymentHistory($_SESSION['accountUrlPrefix']);
		$this->content_view             = 'settings/account_payments';
	}

	function bank_transfer()
	{
		$this->view_data['breadcrumb']    = $this->lang->line( 'application_bank_transfer' );
		$this->view_data['breadcrumb_id'] = "banktransfer";

		if ( $_POST ) {
			unset( $_POST['send'] );
			unset( $_POST['note-codable'] );
			unset( $_POST['files'] );
			if ( isset( $_POST['bank_transfer'] ) ) {
				if ( $_POST['bank_transfer'] != "1" ) {
					$_POST['bank_transfer'] = "0";
				}
			} else {
				$_POST['bank_transfer'] = "0";
			}
			$settings = Setting::first();
			$settings->update_attributes( $_POST );
			if ( $settings ) {
				$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_save_settings_success' ) );
				redirect( 'settings/bank_transfer' );
			} else {
				$this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_save_settings_error' ) );
				redirect( 'settings/bank_transfer' );
			}
		} else {

			$this->view_data['settings']    = Setting::first();
			$this->view_data['form_action'] = 'settings/bank_transfer';
			$this->content_view             = 'settings/bank_transfer';
		}
	}

	function cronjob()
	{
		$this->view_data['breadcrumb']    = $this->lang->line( 'application_cronjob' );
		$this->view_data['breadcrumb_id'] = "cronjob";
		if ( $_POST ) {

			unset( $_POST['send'] );
			if ( $_POST['cronjob'] != "1" ) {
				$_POST['cronjob'] = "0";
			}
			if ( $_POST['autobackup'] != "1" ) {
				$_POST['autobackup'] = "0";
			}
			$settings = Setting::first();
			$settings->update_attributes( $_POST );
			if ( $settings ) {
				$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_save_settings_success' ) );
				redirect( 'settings/cronjob' );
			} else {
				$this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_save_settings_error' ) );
				redirect( 'settings/cronjob' );
			}
		} else {

			$this->view_data['settings']    = Setting::first();
			$this->view_data['form_action'] = 'settings/cronjob';
			$this->content_view             = 'settings/cronjob';
		}
	}

	function ticket()
	{
		$this->view_data['breadcrumb']    = $this->lang->line( 'application_ticket' );
		$this->view_data['breadcrumb_id'] = "ticket";
		$this->view_data['imap_loaded']   = false;
		if ( extension_loaded( 'imap' ) ) {
			$this->view_data['imap_loaded'] = true;
		}
		if ( $_POST ) {

			unset( $_POST['send'] );
			if ( ! isset( $_POST['ticket_config_active'] ) ) {
				$_POST['ticket_config_active'] = "0";
			}
			if ( ! isset( $_POST['ticket_config_delete'] ) ) {
				$_POST['ticket_config_delete'] = "0";
			}
			if ( ! isset( $_POST['ticket_config_ssl'] ) ) {
				$_POST['ticket_config_ssl'] = "0";
			}
			if ( ! isset( $_POST['ticket_config_imap'] ) ) {
				$_POST['ticket_config_imap'] = "0";
			}
			$settings = Setting::first();
			$settings->update_attributes( $_POST );
			if ( $settings ) {
				$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_save_settings_success' ) );
				redirect( 'settings/ticket' );
			} else {
				$this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_save_settings_error' ) );
				redirect( 'settings/ticket' );
			}
		} else {

			$this->view_data['settings']    = Setting::first();
			$this->view_data['types']       = Type::find( 'all', array(
				'conditions' => array(
					'inactive = ?',
					'0'
				)
			) );
			$this->view_data['queues']      = Queue::find( 'all', array(
				'conditions' => array(
					'inactive = ?',
					'0'
				)
			) );
			$this->view_data['owners']      = User::find( 'all', array(
				'conditions' => array(
					'status = ?',
					'active'
				)
			) );
			$this->view_data['form_action'] = 'settings/ticket';
			$this->content_view             = 'settings/ticket';
		}
	}

	function ticket_type( $id = false, $condition = false )
	{
		if ( $condition == "delete" ) {
			$_POST["inactive"] = "1";
			$type              = Type::find_by_id( $id );
			$type->update_attributes( $_POST );
		} else {

			if ( $_POST ) {

				unset( $_POST['send'] );

				if ( $id ) {
					$type = Type::find_by_id( $id );
					$type->update_attributes( $_POST );

				} else {
					$type = Type::create( $_POST );
				}
				if ( $type ) {
					$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_save_settings_success' ) );
					redirect( 'settings/ticket' );
				} else {
					$this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_save_settings_error' ) );
					redirect( 'settings/ticket' );
				}
			} else {
				if ( $id ) {
					$this->view_data['type'] = Type::find_by_id( $id );
				}

				$this->view_data['title']       = $this->lang->line( 'application_type' );
				$this->view_data['form_action'] = 'settings/ticket_type/' . $id;
				$this->content_view             = 'settings/_ticket_type';
			}
		}
		$this->theme_view = 'modal_nojs';
	}

	function ticket_queue( $id = false, $condition = false )
	{
		if ( $condition == "delete" ) {
			$_POST["inactive"] = "1";
			$type              = Queue::find_by_id( $id );
			$type->update_attributes( $_POST );
		} else {

			if ( $_POST ) {

				unset( $_POST['send'] );
				if ( $id ) {
					$queue = Queue::find_by_id( $id );
					$queue->update_attributes( $_POST );
				} else {
					$queue = Queue::create( $_POST );
				}
				if ( $queue ) {
					$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_save_settings_success' ) );
					redirect( 'settings/ticket' );
				} else {
					$this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_save_settings_error' ) );
					redirect( 'settings/ticket' );
				}
			} else {
				if ( $id ) {
					$this->view_data['queue'] = Queue::find_by_id( $id );
				}
				$this->theme_view               = 'modal_nojs';
				$this->view_data['title']       = $this->lang->line( 'application_queue' );
				$this->view_data['form_action'] = 'settings/ticket_queue/' . $id;
				$this->content_view             = 'settings/_ticket_queue';
			}
		}
	}

	function testpostmaster()
	{

		$emailconfig       = Setting::first();
		$config['login']   = $emailconfig->ticket_config_login;
		$config['pass']    = $emailconfig->ticket_config_pass;
		$config['host']    = $emailconfig->ticket_config_host;
		$config['port']    = $emailconfig->ticket_config_port;
		$config['mailbox'] = $emailconfig->ticket_config_mailbox;

		if ( $emailconfig->ticket_config_imap == "1" ) {
			$flags = "/imap";
		} else {
			$flags = "/pop3";
		}
		if ( $emailconfig->ticket_config_ssl == "1" ) {
			$flags .= "/ssl";
		}

		$config['service_flags'] = $flags . $emailconfig->ticket_config_flags;

		$this->load->library( 'peeker_connect' );
		$this->peeker_connect->initialize( $config );

		if ( $this->peeker_connect->is_connected() ) {
			$this->view_data['msgresult'] = "success";
			$this->view_data['result']    = "Connection to email mailbox successful!";
		} else {
			$this->view_data['msgresult'] = "error";
			$this->view_data['result']    = "Connection to email mailbox not successful!";
		}
		$this->peeker_connect->message_waiting();

		$this->peeker_connect->close();
		$this->view_data['trace'] = $this->peeker_connect->trace();
		$this->content_view       = 'settings/_testpostmaster';
		$this->theme_view         = 'modal_nojs';
		$this->view_data['title'] = $this->lang->line( 'application_postmaster_test' );
	}

	function customize()
	{
		$this->view_data['breadcrumb']    = $this->lang->line( 'application_customize' );
		$this->view_data['breadcrumb_id'] = "customize";

		$this->load->helper( 'file' );
		$this->view_data['settings'] = Setting::first();
		if ( $_POST ) {
			$data = $_POST['css-area'];
			//$settings = Setting::first();
			//$settings->update_attributes($_POST);


			if ( write_file( './assets/' . $this->view_data['settings']->template . '/css/user.css', $data ) ) {
				$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_save_customize_success' ) );
				redirect( 'settings/customize' );
			} else {
				$this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_save_customize_error' ) );
				redirect( 'settings/customize' );
			}
		} else {
			$this->view_data['writable'] = false;
			if ( is_writable( './assets/' . $this->view_data['settings']->template . '/css/user.css' ) ) {
				$this->view_data['writable'] = true;
			}
			$this->view_data['css']         = read_file( './assets/' . $this->view_data['settings']->template . '/css/user.css' );
			$this->view_data['form_action'] = 'settings/customize';
			$this->content_view             = 'settings/customize';
		}
	}

	function registration()
	{
		if ( $_POST ) {
			unset( $_POST['send'] );

			if ( ! isset( $_POST['registration'] ) ) {
				$_POST['registration'] = 0;
			}
			if ( ! empty( $_POST["access"] ) ) {
				$_POST["default_client_modules"] = implode( ",", $_POST["access"] );
			} else {
				$_POST["default_client_modules"] = "";
			}
			unset( $_POST["access"] );
			$settings = Setting::first();
			$settings->update_attributes( $_POST );


			if ( $settings ) {
				$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_save_settings_success' ) );
				redirect( 'settings/registration' );
			}
		}
		$this->view_data['breadcrumb']    = $this->lang->line( 'application_registration' );
		$this->view_data['breadcrumb_id'] = "registration";

		$this->view_data['client_modules'] = Module::find( 'all', array(
			'order'      => 'sort asc',
			'conditions' => array( 'type = ?', 'client' )
		) );
		$this->view_data['settings']       = Setting::first();
		$this->view_data['form_action']    = 'settings/registration';
		$this->content_view                = 'settings/registration';
	}

	function users()
	{
		$this->view_data['breadcrumb']    = $this->lang->line( 'application_users' );
		$this->view_data['breadcrumb_id'] = "users";

		$options                  = array( 'conditions' => array( 'status != ?', 'deleted' ) );
		$users                    = User::all( $options );
		$this->view_data['users'] = $users;
		$this->content_view       = 'settings/user';
	}

	function user_delete( $user = false )
	{

		if ( $this->user->id != $user ) {
			$user         = User::find_by_id( $user );
			$user->status = 'deleted';
			$user->save();
			$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_delete_user_success' ) );
		} else {
			$this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_delete_user_error' ) );
		}
		redirect( 'settings/users' );
	}
	function user_delete_json( $user = false )
	{
		if ( $this->user->id != $user ) {
			$user         = User::find_by_id( $user );
			$user->status = 'deleted';
			$user->save();
			$deleted = true;
		} else {
			$deleted = false;
		}

		echo json_encode( [
			                  'status' => $deleted
		                  ] );
		die();
	}

	function user_create()
	{
		$this->load->helper( 'notification' );

		if ( $_POST ) {
			$emails = $_POST['emails'];

			$config['upload_path']   = './files/media/';
			$config['encrypt_name']  = true;
			$config['allowed_types'] = 'csv';//'gif|jpg|jpeg|png|csv|xls|xlsx';

			$this->load->library( 'upload', $config );

			if ( $this->upload->do_upload() ) {
				$csv  = $this->upload->data();
				$csv  = file_get_contents( $csv['full_path'] );
				$rows = explode( ' ', $csv );

				foreach ( $rows as $row ) {
					$columns = explode( ',', $row );
					foreach ( $columns as $column ) {
						$emails[] = $column;
					}
				}
			}

			$emails = array_filter( $emails, function ( $email ) {
				return ! empty( trim( $email ) );
			} );

			$access = [ 1, 2 ];
			if ( ! empty( $_POST["access"] ) ) {
				$access = $_POST["access"];
			}
			$access = implode( ",", $access );

			$id = UserInvitation::generate_guid();
			$link = base_url('/team/register/' . $id);
			$text = '<p>You are invited to join team.</p><br/>';
			$text .= '<a href="'.$link.'">Click here to accept invitation</a><br/>';

			foreach ( $emails as $email ) {
				$created = UserInvitation::create( [
					                                   'guid'  => $id,
					                                   'email' => $email,
					                                   'meta'  => json_encode( [
						                                                           'access' => $access,
						                                                           'status' => $_POST['status'],
						                                                           'queue'  => $_POST['queue'],
						                                                           'admin'  => $_POST['admin']
					                                                           ] )
				                                   ] );
				if ( $created ) {
					send_notification( $email, $this->lang->line( 'application_notification_user_invitation' ), $text );
				}
			}

			$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_invitation_sent' ) );

			$url = 'settings/users';
			if ( isset( $_GET['redirect'] ) ) {
				$url = urldecode( $_GET['redirect'] );
			}
			redirect( $url );
		} else if ( $_POST ) {

			$config['upload_path']   = './files/media/';
			$config['encrypt_name']  = true;
			$config['allowed_types'] = 'gif|jpg|jpeg|png';
			$config['max_width']     = '180';
			$config['max_height']    = '180';

			$this->load->library( 'upload', $config );

			if ( $this->upload->do_upload() ) {
                $this->load->library('platformaws', ['aws_access_key' => $this->config->item('aws_access_key'), 'aws_secret_key' => $this->config->item('aws_secret_key')]);
                $environment = ENVIRONMENT;
                $bucket = "spera-" . $environment;
				$data = array( 'upload_data' => $this->upload->data() );
				$_POST['userpic'] = $data['upload_data']['file_name'];
                $s3UploadPathAndFile = $_SESSION['accountUrlPrefix'] .
                    explode('.',$config['upload_path'])[1] .
                    $_POST['userpic'];
                $result = $this->platformaws->putObjectFile(
                    $bucket,
                    $s3UploadPathAndFile,
                    $config['upload_path'] . $_POST['userpic']
                );
                unlink($config['upload_path'] . $_POST['userpic']);
            }

			unset( $_POST['file-name'] );
			unset( $_POST['send'] );
			unset( $_POST['confirm_password'] );
			if ( ! empty( $_POST["access"] ) ) {
				$_POST["access"] = implode( ",", $_POST["access"] );
			}
			$_POST       = array_map( 'htmlspecialchars', $_POST );
			$user_exists = User::find_by_username( $_POST['username'] );
			if ( empty( $user_exists ) ) {
				$user = User::create( $_POST );
				if ( ! $user ) {
					$this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_create_user_error' ) );
				} else {
					$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_create_user_success' ) );
				}
			} else {
				$this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_create_user_exists' ) );
			}
			$url = 'settings/users';
			if ( isset( $_GET['redirect'] ) ) {
				$url = urldecode( $_GET['redirect'] );
			}
			redirect( $url );
		} else {
			$this->theme_view               = 'modal';
			$this->view_data['title']       = $this->lang->line( 'application_create_user' );
			$this->view_data['kill_username'] = true;
			$this->view_data['modules']     = Module::find( 'all', array(
				'order'      => 'sort asc',
				'conditions' => array( 'type != ?', 'client' )
			) );
			$this->view_data['queues']      = Queue::find( 'all', array(
				'conditions' => array(
					'inactive=?',
					'0'
				)
			) );
			$this->view_data['form_action'] = 'settings/user_create/';
			if ( isset( $_GET['redirect'] ) ) {
				$this->view_data['form_action'] = 'settings/user_create/?redirect=' . urlencode( $_GET['redirect'] );
			}
			$this->content_view = 'settings/_userform';
			$this->content_view = 'settings/_new_userform';
		}

	}

	function user_update( $user = false )
	{
		$user = User::find( $user );

		if ( $_POST ) {

			$config['upload_path']   = './files/media/';
			$config['encrypt_name']  = true;
			$config['allowed_types'] = 'gif|jpg|jpeg|png';
			$config['max_width']     = '180';
			$config['max_height']    = '180';

			$this->load->library( 'upload', $config );

			if ( $this->upload->do_upload() ) {
				$data = array( 'upload_data' => $this->upload->data() );
                $_POST['userpic'] = $data['upload_data']['file_name'];
                $this->load->library('platformaws', ['aws_access_key' => $this->config->item('aws_access_key'), 'aws_secret_key' => $this->config->item('aws_secret_key')]);
                $environment = ENVIRONMENT;
                $bucket = "spera-" . $environment;
                $s3UploadPathAndFile = $_SESSION['accountUrlPrefix'] .
                    explode('.',$config['upload_path'])[1] .
                    $_POST['userpic'];
                $result = $this->platformaws->putObjectFile(
                    $bucket,
                    $s3UploadPathAndFile,
                    $config['upload_path'] . $_POST['userpic']
                );
                unlink($config['upload_path'] . $_POST['userpic']);
			}

			unset( $_POST['file-name'] );
			unset( $_POST['send'] );
			unset( $_POST['confirm_password'] );
			if ( ! empty( $_POST["access"] ) ) {
				$_POST["access"] = implode( ",", $_POST["access"] );
			}
			$_POST = array_map( 'htmlspecialchars', $_POST );
			if ( empty( $_POST['password'] ) ) {
				unset( $_POST['password'] );
			}
			if ( $_POST['admin'] == "0" && $_POST['username'] == "Admin" ) {
				$_POST['admin'] = "1";
			}
			$user->update_attributes( $_POST );
			$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_save_user_success' ) );
			//redirect( 'settings/users' );
			redirect('/team');
		} else {
			$this->view_data['user']    = $user;
			$this->theme_view           = 'modal';
			$this->view_data['modules'] = Module::find( 'all', array(
				'order'      => 'sort asc',
				'conditions' => array( 'type != ?', 'client' )
			) );
			$this->view_data['queues']  = Queue::all();

			$this->view_data['title']       = $this->lang->line( 'application_edit_user' );
			$this->view_data['form_action'] = 'settings/user_update/' . $user->id;
			$this->content_view             = 'settings/_userform';
		}

	}

	function updates()
	{
		$this->view_data['breadcrumb']    = $this->lang->line( 'application_updates' );
		$this->view_data['breadcrumb_id'] = "updates";
		$this->view_data['settings']      = Setting::first();
		$this->load->helper( 'file' );
		$this->load->helper( 'curl' );

		$filename = './application/controllers/projects.php';
		if ( is_writable( $filename ) ) {
			$this->view_data['writable'] = "TRUE";
		} else {
			$this->view_data['writable'] = "FALSE";
		}

		$fileversion = read_file( './application/version.txt' );

		if ( $fileversion != $this->view_data['settings']->version ) {
			$this->view_data['version_mismatch'] = "TRUE";
		} else {
			$this->view_data['version_mismatch'] = "FALSE";
		}


		$downloaded_updates                    = get_filenames( './files/updates/' );
		$this->view_data['downloaded_updates'] = array();
		if ( ! empty( $downloaded_updates ) ) {
			foreach ( $downloaded_updates as $value ) {
				$this->view_data['downloaded_updates'][ $value ] = array(
					"filename" => $value,
					"md5"      => md5_file( "./files/updates/" . $value )
				);
			}
		}


		$object                        = remote_get_contents( 'http://fc2.luxsys-apps.com/updates/xml.php?code=' . $this->view_data['settings']->pc );
		$object                        = json_decode( $object );
		$this->view_data['curl_error'] = false;

		if ( isset( $object->error ) ) {
			if ( $object->error == false ) {
				$this->view_data['lists'] = $object->updatelist;
				foreach ( $this->view_data['lists'] as $key => $file ) {
					if ( isset( $file->md5 ) && array_key_exists( $file->file, $this->view_data['downloaded_updates'] ) && $this->view_data['downloaded_updates'][ $file->file ]["md5"] != $file->md5 ) {
						unset( $this->view_data['downloaded_updates'][ $file->file ] );
						@unlink( "./files/updates/" . $file->file );
					}
				}
			} else {
				$this->view_data['lists'] = array();
				$this->session->set_flashdata( 'message', 'error: ' . $object->error );
			}

		} else {
			$this->view_data['curl_error'] = true;
			$this->view_data['lists']      = array();
		}

		$this->content_view = 'settings/updates';
	}

	function checkForUpdates()
	{
		if ( $this->user->admin == 1 ) {
			$settings = Setting::first();
			$this->load->helper( 'curl' );
			$this->theme_view  = 'blank';
			$object            = remote_get_contents( 'http://fc2.luxsys-apps.com/updates/xml.php?code=' . $settings->pc, 3 );
			$object            = json_decode( $object );
			$object->newUpdate = false;

			if ( isset( $object->error ) ) {
				if ( empty( $object->error ) && $object->lastupdate > $settings->version ) {
					$object->newUpdate = true;
				}
			}
			echo json_encode( $object );
		}


	}

	function backup()
	{
		$this->view_data['breadcrumb']    = $this->lang->line( 'application_backup' );
		$this->view_data['breadcrumb_id'] = "backup";

		$this->view_data['settings'] = Setting::first();
		$this->load->helper( 'file' );
		$this->view_data['backups'] = get_filenames( './files/backup/' );
		if ( ! isset( $this->view_data['backups'] ) ) {
			$this->session->set_flashdata( 'message', 'error: Could not check backup folder' );
		}

		$this->content_view = 'settings/backup';
	}

	function logs( $val = false )
	{
		$this->view_data['breadcrumb']    = $this->lang->line( 'application_logs' );
		$this->view_data['breadcrumb_id'] = "logs";

		$this->load->helper( 'file' );
		if ( $val == "clear" ) {
			delete_files( './application/logs/' );
			$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_log_cleared' ) );
			redirect( 'settings/logs' );

		} else {
			$lognames = get_filenames( './application/logs/' );
			if ( ! $lognames ) {
				$lognames = array();
			}
			$lognames                = array_diff( $lognames, array( "index.html" ) );
			$this->view_data['logs'] = "";
			$i                       = 0;
			krsort( $lognames );
			foreach ( $lognames as $value ) {
				if ( $i < 6 ) {
					$this->view_data['logs'] .= read_file( './application/logs/' . $value );
					$i                       += 1;
				}
			}

			$this->view_data['logs'] = explode( "\n", $this->view_data['logs'] );
			$this->view_data['logs'] = array_diff( $this->view_data['logs'], array(
				"<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>",
				""
			) );
			$this->view_data['logs'] = preg_grep( "/(?i)(?:(?<=^|\s)(?=\S)|(?<=\S|^)(?=\s))Division by zero(?:(?<=\S)(?=\s|$)|(?<=\s)(?=\S|$))/", $this->view_data['logs'], PREG_GREP_INVERT );
			$this->view_data['logs'] = array_map( function ( $line ) {
				return ( strpos( $line, "[cronjob]" ) == true ) ? '<div style="color:#337ab7">' . $line . "</div>" : $line;
			}, $this->view_data['logs'] );

			//$this->view_data['logs'] = preg_grep("/(?i)(?:(?<=^|\s)(?=\S)|(?<=\S|^)(?=\s))Trying to get property of non-object(?:(?<=\S)(?=\s|$)|(?<=\s)(?=\S|$))/", $this->view_data['logs'], PREG_GREP_INVERT);

			rsort( $this->view_data['logs'] );
			$this->view_data['settings']    = Setting::first();
			$this->view_data['form_action'] = 'settings/logs';
			$this->content_view             = 'settings/logs';
		}
	}

    function links( $val = false )
    {
        $this->view_data['breadcrumb']    = $this->lang->line( 'application_links' );
        $this->view_data['breadcrumb_id'] = "links";
        $this->view_data['form_action'] = 'settings/links';

        $this->load->helper( 'slack' );
        // Setup the Slack handler

        $link = SlackLink::getSlackLatestLink($this->user->id);
        $str = $link ? json_encode($link->to_array()) : false;

        $slack = initialize_slack_interface($str);

        if( !empty($link) )
            slack_update_user_info($slack, $link->id);

        $this->view_data['slack'] = $slack;
        // If an action was passed, execute it before rendering the page layout
        $this->view_data['result_message'] = "";
        if ( isset( $_REQUEST['action'] ) ) {
            $action = $_REQUEST['action'];
            $result = '';
            switch ( $action ) {

                // Handles the OAuth callback by exchanging the access code to
                // a valid token and saving it in a file
                case 'oauth':
                    if( isset($_GET['code'])) {
                        $code = $_GET['code'];
                        $auth_result = slack_oauth($slack, $code, $this->user->id);
                        $result = $auth_result['msg'];
                    }
                    break;

                // Sends a notification to Slack
                case 'send_notification':
                    $msg = isset( $_REQUEST['text'] ) ? $_REQUEST['text'] : 'Hello!';
                    $result = slack_send_notification($slack, $msg);
                    break;

                default:
                    break;

            }
            $this->view_data['result_message'] = $result;
        }

        $this->user = User::find($this->user->id);
        $this->view_data['state'] = trim(htmlspecialchars($_SESSION['accountUrlPrefix']));
        $this->view_data['slack_links'] = $this->user->slack_links;

        $this->content_view             = 'settings/links';
    }

    function unlink_slack( $team_id, $access_token )
    {
        if ( $team_id ) {
            $this->load->helper( 'slack' );

            slack_unlink_oauth($this->user->id, $access_token, $team_id);
            $this->session->set_flashdata( 'message', 'success: ' . $this->lang->line( 'messages_unlink_slack_team_success' ) );
        } else {
            $this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_unlink_slack_team_error' ) );
        }
        redirect( 'settings/links' );
    }

	function themeoptions( $val = false )
	{
		$this->view_data['breadcrumb']    = $this->lang->line( 'application_theme_options' );
		$this->view_data['breadcrumb_id'] = "themeoptions";
		$this->view_data['settings']      = Setting::first();

        $this->load->library('platformaws', ['aws_access_key' => $this->config->item('aws_access_key'), 'aws_secret_key' => $this->config->item('aws_secret_key')]);

        $environment = ENVIRONMENT;
        $bucket = "spera-" . $environment;

		if ( $_POST ) {
			if ( is_uploaded_file( $_FILES['userfile']['tmp_name'] ) ) {
				$config['upload_path']   = './assets/blueline/images/backgrounds/';
				$config['encrypt_name']  = false;
				$config['overwrite']     = true;
				$config['allowed_types'] = 'gif|jpg|jpeg|png';

				$this->load->library( 'upload', $config );

				if ( $this->upload->do_upload( "userfile" ) ) {
					$data                      = array( 'upload_data' => $this->upload->data() );
					$_POST['login_background'] = $data['upload_data']['file_name'];
					$s3UploadPathAndFile = $_SESSION['accountUrlPrefix'] .
                        explode('.',$config['upload_path'])[1] .
                        $_POST['login_background'];
                    $result = $this->platformaws->putObjectFile(
                        $bucket,
                        $s3UploadPathAndFile,
                        $config['upload_path'] . $_POST['login_background']
                    );
                    unlink($config['upload_path'] . $_POST['login_background']);
				}
			}
			if ( is_uploaded_file( $_FILES['userfile2']['tmp_name'] ) ) {

				$config['upload_path']   = './files/media/';
				$config['encrypt_name']  = false;
				$config['overwrite']     = true;
				$config['allowed_types'] = 'gif|jpg|jpeg|png|svg';

				$this->load->library( 'upload', $config );

				if ( $this->upload->do_upload( "userfile2" ) ) {
					$data                = array( 'upload_data' => $this->upload->data() );
					$_POST['login_logo'] = explode('./',$config['upload_path'])[1] . $data['upload_data']['file_name'];
                    $this->load->library('platformaws', ['aws_access_key' => $this->config->item('aws_access_key'), 'aws_secret_key' => $this->config->item('aws_secret_key')]);
                    $environment = ENVIRONMENT;
                    $bucket = "spera-" . $environment;
                    $s3UploadPathAndFile = $_SESSION['accountUrlPrefix'] .
                        '/' . $_POST['login_logo'];
                    $result = $this->platformaws->putObjectFile(
                        $bucket,
                        $s3UploadPathAndFile,
                        './' . $_POST['login_logo']
                    );
                    unlink('./' . $_POST['login_logo']);
                }
			}
			if ( ! isset( $_POST['custom_colors'] ) ) {
				$_POST['custom_colors'] = 0;
			}
			unset( $_POST['file-name'] );
			unset( $_POST['userfile2'] );
			unset( $_POST['send'] );
			$this->view_data['settings']->update_attributes( $_POST );
			$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_save_settings_success' ) );
			redirect( 'settings/themeoptions' );
		}

		$this->load->helper( 'file' );

        $this->view_data['backgrounds'] = $this->platformaws->getFileList($bucket,$_SESSION['accountUrlPrefix'] . '/assets/blueline/images/backgrounds/');

		$this->view_data['form_action'] = 'settings/themeoptions';
		$this->content_view             = 'settings/themeoptions';

	}

	function update_download( $update = false )
	{

		if ( $update ) {
			$update = $update . ".zip";
			$ch     = curl_init();
			curl_setopt( $ch, CURLOPT_URL, 'http://fc2.luxsys-apps.com/updates/files/' . $update );

			$fp = fopen( './files/updates/' . $update, 'w+' );
			curl_setopt( $ch, CURLOPT_FILE, $fp );
			curl_exec( $ch );
			curl_close( $ch );
			fclose( $fp );

			/* Make auto backup after update download */
			$this->load->helper( 'file' );
			$this->load->dbutil();
			$settings = Setting::first();
			$version  = str_replace( ".", "-", $settings->version );
			$prefs    = array(
				'format'   => 'zip',
				'filename' => 'Database-full-backup_' . $version . '_' . date( 'Y-m-d_H-i' )
			);
			$backup   =& $this->dbutil->backup( $prefs );
			@write_file( './files/backup/Database-full-backup_' . $version . '_' . date( 'Y-m-d_H-i' ) . '.zip', $backup );
		}
		redirect( 'settings/updates' );

	}

	function update_install( $file = false, $version = false, $newsPage = false )
	{
		$this->load->helper( 'unzip' );
		$this->load->helper( 'file' );
		$file = $file . ".zip";
		if ( ! unzip( "files/updates/" . $file, "", true, true ) ) {
			$this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_install_update_error' ) );
		} else {
			$attr            = array();
			$attr['version'] = $version;
			$migration       = str_replace( '.', '-', $version );
			if ( file_exists( "application/migrations/" . $migration . ".php" ) ) {
				$this->load->dbforge();
				include( "application/migrations/" . $migration . ".php" );
			}
			$settings    = Setting::first();
			$fileversion = read_file( './application/version.txt' );

			if ( $fileversion != $version ) {
				$this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_install_update_error' ) );
			} else {
				$settings->update_attributes( $attr );
				$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_install_update_success' ) );
			}


		}
		if ( $newsPage ) {
			redirect( 'settings/updatenews' );
		} else {
			redirect( 'settings/updates' );
		}

	}

	function updatenews()
	{
		$this->view_data['settings'] = Setting::first();
		$this->content_view          = 'settings/updatenews';
	}

	function update_man()
	{
		$this->load->helper( 'file' );
		$settings         = Setting::first();
		$_POST['version'] = read_file( 'application/version.txt' );
		if ( $_POST['version'] > $settings->version ) {
			$update = str_replace( '.', '-', $_POST['version'] );
			if ( file_exists( "application/migrations/" . $update . ".php" ) ) {
				$this->load->dbforge();
				include( "application/migrations/" . $update . ".php" );
			}
			$settings->update_attributes( $_POST );
			$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_install_update_success' ) );
		}
		redirect( 'settings/updates' );
	}

	function mysql_backup()
	{
		$this->load->helper( 'file' );
		$this->load->dbutil();
		$settings = Setting::first();
		$version  = str_replace( ".", "-", $settings->version );
		$prefs    = array(
			'format'   => 'zip',
			'filename' => 'Database-full-backup_' . $version . '_' . date( 'Y-m-d_H-i' )
		);

		$backup =& $this->dbutil->backup( $prefs );

		if ( ! write_file( './files/backup/Database-full-backup_' . $version . '_' . date( 'Y-m-d_H-i' ) . '.zip', $backup ) ) {
			$this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_create_backup_error' ) );
		} else {
			$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_create_backup_success' ) );
		}

		redirect( 'settings/backup' );
	}

	function mysql_download( $filename )
	{
		$this->load->helper( 'file' );
		$this->load->helper( 'download' );
		$filename = $filename . ".zip";
		$file     = './files/backup/' . $filename;
		$mime     = get_mime_by_extension( $file );

		if ( file_exists( $file ) ) {
			header( 'Content-Description: File Transfer' );
			header( 'Content-Type: ' . $mime );
			header( 'Content-Disposition: attachment; filename=' . basename( $filename ) );
			header( 'Content-Transfer-Encoding: binary' );
			header( 'Expires: 0' );
			header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
			header( 'Pragma: public' );
			header( 'Content-Length: ' . filesize( $file ) );
			readfile( $file );
			flush();
			exit;
		}

		redirect( 'settings/backup' );
	}

	function mysql_restore()
	{
		if ( $_POST ) {
			$this->load->helper( 'file' );
			$this->load->helper( 'unzip' );
			$this->load->database();
			$settings = Setting::first();

			$config['upload_path']   = './files/temp/';
			$config['allowed_types'] = 'zip|gzip';
			$config['max_size']      = '9000';

			$this->load->library( 'upload', $config );

			if ( ! $this->upload->do_upload() ) {
				$error = $this->upload->display_errors( '', ' ' );
				$this->session->set_flashdata( 'message', 'error:' . $error );
				redirect( 'settings/updates' );
			} else {
				$data   = array( 'upload_data' => $this->upload->data() );
				$backup = "files/temp/" . $data['upload_data']['file_name'];

			}


			if ( ! unzip( $backup, "files/temp/", true, true ) ) {
				$this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_restore_backup_error' ) );
			} else {

				$version = explode( "_", $backup );
				$version = str_replace( "-", ".", $version[1] );


				$this->load->dbforge();
				$backup       = str_replace( '.zip', '', $backup );
				$backup       = str_replace( '.gzip', '', $backup );
				$file_content = file_get_contents( $backup . ".sql" );
				$this->db->query( 'USE `' . $this->db->database . '`;' );

				if ( $version < $settings->version ) {
					$pattern = "INSERT INTO";
					$pattern = "/^.*$pattern.*\$/m";
					// search, and store all matching occurences in $matches
					if ( preg_match_all( $pattern, $file_content, $matches ) ) {
						$file_content = implode( "\n", $matches[0] );
						$file_content = str_replace( "INSERT INTO", "INSERT IGNORE INTO", $file_content );
					}

				}
				foreach ( explode( ";\n", $file_content ) as $sql ) {
					$sql = trim( $sql );
					if ( $sql ) {
						$this->db->query( $sql );
					}
				}
				$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_restore_backup_success' ) );

			}
			unlink( $backup . ".sql" );
			@unlink( $backup . ".zip" );
			@unlink( $backup . ".gzip" );
			redirect( 'settings/updates' );
		} else {
			$this->theme_view               = 'modal';
			$this->view_data['title']       = $this->lang->line( 'application_upload_backup' );
			$this->view_data['form_action'] = 'settings/mysql_restore';
			$this->content_view             = 'settings/_backup';
		}
	}


	function smtp_settings()
	{
		$this->config->load( 'email' );
		if ( isset( $_POST["testemail"] ) ) {
			//send test email
			$this->load->helper( 'notification' );
			if ( send_notification( $_POST["testemail"], "[Email Settings] Test Email", 'This is a test email.' ) ) {
				$this->session->set_flashdata( 'message', 'success: Test email has been sent. Check your inbox!' );
			} else {
				$this->session->set_flashdata( 'message', 'error: Email not sent. Check your email settings!' );
			}
			redirect( 'settings/smtp_settings' );
		}
		if ( isset( $_POST["protocol"] ) ) {
			$this->load->helper( 'file' );
			$crypto = $_POST["smtp_crypto"];
			$data   = '<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");
	$config["useragent"]        = "PHPMailer";      
	$config["protocol"]         = "' . $_POST["protocol"] . '";
	$config["mailpath"]         = "/usr/sbin/sendmail";
	$config["smtp_host"]        = "' . $_POST["smtp_host"] . '";
	$config["smtp_user"]        = "' . $_POST["smtp_user"] . '";
	$config["smtp_pass"]        = "' . addslashes( $_POST["smtp_pass"] ) . '";
	$config["smtp_port"]        = "' . $_POST["smtp_port"] . '";
	$config["smtp_timeout"]     = "' . $_POST["smtp_timeout"] . '";      
	$config["smtp_crypto"]      = "' . $crypto . '";    
	$config["smtp_debug"]       = "' . $_POST["smtp_debug"] . '";      
	$config["wordwrap"]         = true;
	$config["wrapchars"]        = 76;
	$config["mailtype"]         = "html";          
	$config["charset"]          = "utf-8";
	$config["validate"]         = true;
	$config["priority"]         = 3;                
	$config["crlf"]             = "\r\n";                     
	$config["newline"]          = "\r\n";                    
	$config["bcc_batch_mode"]   = false;
	$config["bcc_batch_size"]   = 200;
				';

			if ( ! write_file( './application/config/email.php', $data ) ) {
				$this->session->set_flashdata( 'message', 'error: Unable to write file. Make sure that /application/config/smtp.php as writing permissions!' );
			} else {
				$this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_save_settings_success' ) );
			}

			redirect( 'settings/smtp_settings', 'refresh' );
		} else {
			$this->view_data['breadcrumb']    = $this->lang->line( 'application_smtp_settings' );
			$this->view_data['breadcrumb_id'] = "smtpsettings";
			$this->view_data['settings']      = Setting::first();


			$this->view_data['form_action'] = 'settings/smtp_settings';
			$this->content_view             = 'settings/smtp_settings';
		}

	}

}