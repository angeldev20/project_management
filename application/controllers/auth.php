<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'third_party/google/google-login-api.php');

class Auth extends MY_Controller
{
    /** @var  account */
    public $account;

    /**
     * Decrypts the string using the server defined SALT string
     * @param string $stringToDecrypt
     * @return string
     */
    public function decryptString($stringToDecrypt) {
        $decryptedString = false;
        $c = base64_decode($stringToDecrypt);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, SALT, $options=OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, SALT, $as_binary=true);
        if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
        {
            $decryptedString = $original_plaintext;
        }
        return $decryptedString;
    }

    public function ssologin(){
        $core_settings = Setting::first();

        $accountUrlPrefix = '';
        $accountList = [];
        $email = '';
        $domainParts = explode('.', $_SERVER['HTTP_HOST']);
        $domain = $domainParts[count($domainParts) - 2] . '.' . $domainParts[count($domainParts) - 1];
        $selectAccount = false;

        $this->view_data['error'] = "false";

        if (isset($_REQUEST['code'])) {

            $gapi = new GoogleLoginApi();
        
            // Get the access token 
            $data = $gapi->GetAccessToken(DEV_CLIENT_ID, DEV_CLIENT_REDIRECT_URL, DEV_CLIENT_SECRET, $_GET['code']);
            
            // Get user information
            $user_info = $gapi->GetUserProfileInfo($data['access_token']);

            if (isset($user_info['id'])) {
                $google_account_id = $user_info['id'];
                $email = $user_info['emails'][0]['value'];
                $familyName = $user_info['name']['familyName'];
                $givenName = $user_info['name']['givenName'];

                $primaryDatabase = $this->load->database('primary', TRUE);

                $params = [
                    'primaryDatabase' => $primaryDatabase,
                ];

                $this->load->library('account', $params);

                $accountList = $this->account->getAccountListByEmail($email);
                $this->session->unset_userdata( ['login_sso_email'=>''] );
                if($accountList === false){
                    $sign_up_data = array(
                        'email'=>$email,
                        'password'=>md5(uniqid(rand(), true)),
                        'firstname'=>$givenName,
                        'lastname'=>$familyName,
                        'planType'=>'pro_monthly'
                    );
                    $this->session->set_userdata('register_email_confirmation', $sign_up_data);
                    redirect(base_url() . 'signup/confirm?account=' . md5(json_encode($sign_up_data)) , 'refresh');
                }else{
                    
                    $selectAccount = true;
                    // redirect(base_url() . 'login?sso=allow');
                }
            }
            else{ //failed Google 
                $this->view_data['error'] = "true";
                $this->view_data['message'] = 'error:' . ' gmail signup is failed.';
            }
        }


        $this->theme_view = 'app_login';
        $this->content_view = 'auth/sso_app_login';
        
        $this->view_data['email'] = $email;
        $this->view_data['accountUrlPrefix'] = $accountUrlPrefix;
        $this->view_data['accountList'] = $accountList;
        $this->view_data['selectAccount'] = $selectAccount;
        $this->view_data['domain'] = $domain;
        $this->view_data['sso'] = "true";
    }

    function slogin(){


        $domainParts = explode('.', $_SERVER['HTTP_HOST']);
        $domain = $domainParts[count($domainParts) - 2] . '.' . $domainParts[count($domainParts) - 1];
        $accountUrlPrefix = '';
        $accountList = [];
        if($_POST)
        {
            if (isset($_POST['accountUrlPrefix'])) {
                $email = $_POST['sso_email'];
                $hashed = md5(DEV_CLIENT_SECRET.$email);//A hash that you'll pass as well
                $accountUrlPrefix = trim(htmlspecialchars($_POST['accountUrlPrefix']));
                redirect('https://' . $accountUrlPrefix .  '.' . $domain.'/login?hashed='.$hashed.'&m='.$email , 'refresh');
            }
        }
        if($accountUrlPrefix == 'default' || $accountUrlPrefix == '') { //$email || $selectAccount ||
            $this->theme_view = 'app_login';
            $this->content_view = 'auth/app_login';
        } else {
            $this->theme_view = 'login';
            $this->content_view = 'auth/login';
        }
        $this->view_data['email'] = $email;
        $this->view_data['accountUrlPrefix'] = $accountUrlPrefix;
        $this->view_data['accountList'] = $accountList;
        $this->view_data['selectAccount'] = false;
        $this->view_data['domain'] = $domain;
    }
    function sso_signin(){

        $accountUrlPrefix = '';
        if(isset($_SESSION['accountUrlPrefix']) &&  trim(htmlspecialchars($_SESSION['accountUrlPrefix'])) != '')
            $accountUrlPrefix = trim(htmlspecialchars($_SESSION['accountUrlPrefix']));
        $this->view_data['error'] = "false";

        $accountList = [];
        $email = '';
        $domainParts = explode('.', $_SERVER['HTTP_HOST']);
        $domain = $domainParts[count($domainParts) - 2] . '.' . $domainParts[count($domainParts) - 1];
        $selectAccount = false;
        $user = '';
        if (isset($_REQUEST['code'])) {

            $gapi = new GoogleLoginApi();
        
            // Get the access token 
            $data = $gapi->GetAccessToken(DEV_SUB_CLIENT_ID, DEV_SUB_CLIENT_REDIRECT_URL, DEV_SUB_CLIENT_SECRET, $_GET['code']);
            $prefix = $_GET['state'];
            // Get user information
            $user_info = $gapi->GetUserProfileInfo($data['access_token']);

            if (isset($user_info['id'])) {
                $google_account_id = $user_info['id'];
                $email = $user_info['emails'][0]['value'];
                $familyName = $user_info['name']['familyName'];
                $givenName = $user_info['name']['givenName'];
                // $user = User::validate_gmail($email);

                // $user = User::validate_gmail("lty818km@gmail.com");
                // if ($user) {
                //     if ($this->input->cookie('fc2_link') != "") {
                //         redirect($this->input->cookie('fc2_link'));
                //     } else {
                //         $last_page = $this->session->userdata( 'last_page' );
                //         if($last_page) {
                //             $this->session->unset_userdata(['last_page'=>'']);
                //             redirect($last_page , 'refresh');
                //         } else {
                //             redirect('');
                //         }
                //     }
                // }
                if(!empty($email)){
                    $hashed = md5(DEV_CLIENT_SECRET.$email);//A hash that you'll pass as well
                    $accountUrlPrefix = trim($prefix);
                    redirect('https://' . $accountUrlPrefix .  '.' . $domain.'/login?hashed='.$hashed.'&m='.$email , 'refresh');
                }
            }
            else{ //failed Google 
                
            }
        }
        $this->view_data['error'] = "true";
        $this->view_data['message'] = 'error:' . $this->lang->line('messages_login_incorrect');

        $this->theme_view = 'login';
        $this->content_view = 'auth/login';
        $this->view_data['email'] = $accountUrlPrefix;
        $this->view_data['accountUrlPrefix'] = $accountUrlPrefix;
        $this->view_data['accountList'] = $accountList;
        $this->view_data['selectAccount'] = $selectAccount;
        $this->view_data['domain'] = $domain;
    }
	
    function login()
    {
        if(isset($_REQUEST['login'])) {
            $decryptedLogin = json_decode($this->decryptString($_REQUEST['login']));
            $user = User::validate_login($decryptedLogin->username, $decryptedLogin->password);
            if ($user) {
            	$_SESSION['cro'] = true;
                if ($this->input->cookie('fc2_link') != "") {
                    redirect($this->input->cookie('fc2_link'));
                } else {
                    redirect('');
                }
            } else {
                $this->view_data['accountUrlPrefix'] = $_SESSION['accountUrlPrefix'];
                $this->view_data['error'] = "true";
                $this->view_data['username'] = $this->security->xss_clean($decryptedLogin->username);
                $this->view_data['message'] = 'error:' . $this->lang->line('messages_login_incorrect');
            }
        }

        $domainParts = explode('.', $_SERVER['HTTP_HOST']);
        $domain = $domainParts[count($domainParts) - 2] . '.' . $domainParts[count($domainParts) - 1];
        $accountUrlPrefix = '';
        $accountList = [];
        $selectAccount = false;
        if(isset($_SESSION['accountUrlPrefix']) &&  trim(htmlspecialchars($_SESSION['accountUrlPrefix'])) != '')
            $accountUrlPrefix = trim(htmlspecialchars($_SESSION['accountUrlPrefix']));
        $this->view_data['error'] = "false";

        $email = '';
        if($_POST)
        {
            if (isset($_POST['accountUrlPrefix'])) {
                $accountUrlPrefix = trim(htmlspecialchars($_POST['accountUrlPrefix']));
                redirect('https://' . $accountUrlPrefix .  '.' . $domain , 'refresh');
            }
            if (isset($_POST['email'])) $email = trim(htmlspecialchars($_POST['email']));
            if (($email && !isset($_POST['username'])) || ($accountUrlPrefix && !isset($_POST['username']))) {
                $this->session->unset_userdata(['last_page'=>'']);
                /** @var CI_DB_mysql_driver $primaryDatabase */
                $primaryDatabase = $this->load->database('primary', TRUE);

                $params = [
                    'primaryDatabase' => $primaryDatabase,
                ];

                $this->load->library('account', $params);

                $accountList = $this->account->getAccountListByEmail($email);

                if (!$accountList) {
                    $this->view_data['error'] = "true";
                    //TODO: need translations on this line
                    $this->view_data['message'] = 'error:' . ' email address was not found in our system.';
                    $email = '';
                } else {
                    $selectAccount = true;
                }
            } else {
                $_POST['username'] = $this->security->xss_clean($_POST['username']);
                $user = User::validate_login($_POST['username'], $_POST['password']);
                if ($user) {
                    if ($this->input->cookie('fc2_link') != "") {
                        redirect($this->input->cookie('fc2_link'));
                    } else {
                        $last_page = $this->session->userdata( 'last_page' );
                        if($last_page) {
                            $this->session->unset_userdata(['last_page'=>'']);
                            redirect($last_page , 'refresh');
                        } else {
                            redirect('');
                        }
                    }
                } else {
                    $this->view_data['accountUrlPrefix'] = $accountUrlPrefix;
                    $this->view_data['error'] = "true";
                    $this->view_data['username'] = $this->security->xss_clean($_POST['username']);
                    $this->view_data['message'] = 'error:' . $this->lang->line('messages_login_incorrect');
                }
            }
        }
        if($_GET){
            if(!empty($_GET['hashed']) && !empty($_GET['m'])){
                $hashed = md5(DEV_CLIENT_SECRET.$_GET['m']);
                $hash = $_GET['hashed'];
                if($hashed == $hash){
                    $user = User::validate_gmail($_GET['m']);
                    if ($user) {
                        if ($this->input->cookie('fc2_link') != "") {
                            redirect($this->input->cookie('fc2_link'));
                        } else {
                            $last_page = $this->session->userdata( 'last_page' );
                            if($last_page) {
                                $this->session->unset_userdata(['last_page'=>'']);
                                redirect($last_page , 'refresh');
                            } else {
                                redirect('');
                            }
                        }
                    }
                }
            }
        }
        if($accountUrlPrefix == 'default' || $accountUrlPrefix == '') { //$email || $selectAccount ||
            $this->theme_view = 'app_login';
            $this->content_view = 'auth/app_login';
        } else {
            $this->theme_view = 'login';
            $this->content_view = 'auth/login';
        }
        $this->view_data['email'] = $email;
        $this->view_data['accountUrlPrefix'] = $accountUrlPrefix;
        $this->view_data['accountList'] = $accountList;
        $this->view_data['selectAccount'] = $selectAccount;
        $this->view_data['domain'] = $domain;
    }
	function logout()
	{
	    	if($this->user){ 
			$update = User::find($this->user->id); 
				$update->last_active = 0;
				$update->save();
			}elseif($this->client){
			$update = Client::find($this->client->id);
				$update->last_active = 0;
				$update->save();
			}
				
		User::logout();
        unset($_SESSION['accountUrlPrefix']);
		redirect('login');
	}
	function language($lang = false){
		$folder = 'application/language/';
		$languagefiles = scandir($folder);
		if(in_array($lang, $languagefiles)){
		$cookie = array(
                   'name'   => 'fc2language',
                   'value'  => $lang,
                   'expire' => '31536000',
               );
 
		$this->input->set_cookie($cookie);
		}
		redirect(''); 
	}
}
