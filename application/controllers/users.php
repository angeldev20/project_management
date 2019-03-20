<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 11/10/17
 * Time: 11:35 AM
 */
class Users extends MY_Controller {

    /** @var  CI_Email */
    public $email;

    public function __construct()
    {
        parent::__construct();
        $access = FALSE;
        if($this->client){
            redirect('cprojects');
        }elseif($this->user){
            //TODO: we need some security review here, to make sure we have this
            // checking for the right security i.e. super admin, to have this feature
            // also the team view should only display the mail icon for the same reason
            $this->view_data['project_access'] = FALSE;
            $this->view_data['invoice_access'] = FALSE;
            foreach ($this->view_data['menu'] as $key => $value) {
                if($value->link == "clients"){ $access = TRUE;}
                if($value->link == "invoices"){ $this->view_data['invoice_access'] = TRUE;}
                if($value->link == "projects"){ $this->view_data['project_access'] = TRUE;}
            }
            if(!$access){redirect('login');}
        }else{
            redirect('login');
        }

    }

    function credentials($id = FALSE, $email = FALSE, $newPass = FALSE)
    {
        if($email){
            $this->load->helper('file');
            $user = User::find($id);
            $user->password = $user->set_password($newPass);
            $user->save();
            $setting = Setting::first();
            $from_email = EMAIL_FROM; //$setting->email
            $this->email->from($from_email, $setting->company);
            $this->email->to($user->email);
            $this->email->subject($setting->credentials_mail_subject);
            $this->load->library('parser');
            $parse_data = array(
                'client_contact' => $user->firstname . ' ' . $user->lastname,
                'client_company' => $setting->company,
                'client_link' => $setting->domain,
                'company' => $setting->company,
                'username' => $user->email,
                'password' => $newPass,
                'logo' => '<img style="max-height: 50px; max-width: 200px; width: auto;" src="https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION["accountUrlPrefix"] . '/' . $setting->logo.'" alt="'.$setting->company.'"/>',
                'invoice_logo' => '<img style="max-height: 50px; max-width: 200px; width: auto;" src="https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION["accountUrlPrefix"] . '/' . $setting->invoice_logo.'" alt="'.$setting->company.'"/>'
            );

            $message = read_file('./application/views/'.$setting->template.'/templates/email_credentials.html');
            $message = $this->parser->parse_string($message, $parse_data);
            $this->email->message($message);
            if($this->email->send()){$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_send_login_details_success'));}
            else{$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_send_login_details_error'));}
            redirect('settings/users');

        } else {
            $this->view_data['user'] = User::find($id);
            $this->theme_view = 'modal';
            function random_password( $length = 8 ) {
                $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                $password = substr( str_shuffle( $chars ), 0, $length );
                return $password;
            }
            $this->view_data['new_password'] = random_password();
            $this->view_data['title'] = $this->lang->line('application_login_details');
            $this->view_data['form_action'] = 'users/credentials';
            $this->content_view = 'settings/_credentials';
        }
    }

	function data() {
		$users = User::all();

		echo json_encode( [
			                  'status' => true,
			                  'data'   => array_map( function ( $user ) {
				                  return $user->attributes();
			                  }, $users )
		                  ] );
		die();
	}

	function get( $id = false )
	{
		$user = User::find( $id );

		echo json_encode( [
			                  'status' => true,
			                  'user'   => $user->attributes()
		                  ] );
		die();
	}

}
