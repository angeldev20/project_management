<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Clients extends MY_Controller {

    /** @var  CI_Upload */
    public $upload;

    /** @var  platformaws */
    public $platformaws;

	function __construct()
	{
		parent::__construct();
		$access = FALSE;
		if($this->client){	
			redirect('cprojects');
		}elseif($this->user){
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
	function data() {
		$clients = Client::find('all',array('conditions' => array('inactive=?','0')));

		echo json_encode( [
			                  'status' => true,
			                  'data' => array_map( function ( $client ) {
				                  $attributes            = $client->attributes();
				                  $attributes['userpic'] = get_user_pic( $client->userpic );

				                  return $attributes;
			                  }, $clients )
		                  ] );
		die();
	}
	function index1()
	{
		if($this->user->admin == 0){ 
			$thisUserHasNoCompanies = (array) $this->user->companies;
					if(!empty($thisUserHasNoCompanies)){
				$comp_array = array();
				foreach ($this->user->companies as $value) {
					array_push($comp_array, $value->id);
				}
				$options = array('conditions' => array('inactive = ? AND id in (?)',0,$comp_array));
				$this->view_data['companies'] = Company::find('all',$options);
			}
			else{ 
				$this->view_data['companies'] = (object) array();
			}
		}else{
			$options = array('conditions' => array('inactive=?','0'));
			$this->view_data['companies'] = Company::find('all',$options);
		}
		
		$this->content_view = 'clients/all';
	}
	function index() {
		$this->setTitle('People');
		$this->content_view = 'people/index';
	}
	function create($company_id = FALSE)
	{	
		if($_POST){
			$config['upload_path'] = './files/media/';
					$config['encrypt_name'] = TRUE;
					$config['allowed_types'] = 'gif|jpg|png';
					$config['max_width'] = '180';
					$config['max_height'] = '180';

					$this->load->library('upload', $config);

					if ( $this->upload->do_upload())
						{
							$data = array('upload_data' => $this->upload->data());
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
						}else{
							$error = $this->upload->display_errors('', ' ');
							if($error != "You did not select a file to upload. "){
								$this->session->set_flashdata('message', 'error:'.$error);
								redirect('clients');
							}
						}

			unset($_POST['send']);
			unset($_POST['userfile']);
			unset($_POST['file-name']);
			if(isset($_POST["access"])){ $_POST["access"] = implode(",", $_POST["access"]); }else{unset($_POST["access"]);}
			$_POST = array_map('htmlspecialchars', $_POST);
			$_POST["company_id"] = $company_id;
			$client = Client::create($_POST);
			$client->password = $client->set_password($_POST['password']);
			$client->save();
       		if(!$client){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_client_add_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_client_add_success'));
       		$company = Company::find($company_id);
       			if(!isset($company->client->id)){
       				$client = Client::last();
       				$company->update_attributes(array('client_id' => $client->id));
       			}
       				
       		}
			redirect('clients/view/'.$company_id);
		}else
		{
			$this->view_data['clients'] = Client::find('all',array('conditions' => array('inactive=?','0')));
			$this->view_data['modules'] = Module::find('all', array('order' => 'sort asc', 'conditions' => array('type = ?', 'client')));
			$this->view_data['next_reference'] = Client::last();
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_add_new_contact');
			$this->view_data['form_action'] = 'clients/create/'.$company_id;
			$this->content_view = 'clients/_clients';
		}	
	}	
	function create_json()
	{
		if($_POST){
			$client = Client::find_by_email($_POST['email']);
			if(isset($client)){
				if(isset($_POST['invite'])){
					$this->credentials($client->id,true,false,false);
				}else{
				}
				echo json_encode([
				                 'status' => false,
				                 'client' => $client->attributes()
			                 ] );
				die();

			}else{
				$company_data = array();
				if(!isset($_POST['company'])){
					$name = $_POST['firstname'].' '.$_POST['lastname'];
				}else{
					$name = $_POST['company'];
				}

				$options = array('conditions' => array('name = ? ',$name));
				$company = Company::find($options);
				
				$company_reference = Setting::first();

				if($company == null){
					$last_company = Company::find(array('order' => 'reference desc'));
					$company_data['name'] = $name;
					$company_data['reference'] = $company_reference->company_reference;
					$company_data['zipcode'] = '11111';
					$company = Company::create($company_data);
					$company_id = Company::last()->id;
					$attributes = array('company_id' => $company_id, 'user_id' => $this->user->id);
					CompanyHasAdmin::create($attributes);

					$company_reference->update_attributes(array('company_reference' => $company_reference->company_reference + 1));
				}else{
					$company_id = $company->id;
				}

				

				$_POST['company_id'] = $company_id;

				$user_data = array();
				$user_data['company_id'] = $company_id;
				$user_data['firstname'] = $_POST['firstname'];
				$user_data['lastname'] = $_POST['lastname'];
				$user_data['email'] = $_POST['email'];
				$user_data['zipcode'] = '11111';
				$perm = $_POST['perm_message'];

				$modules = Module::find('all', array('order' => 'sort asc', 'conditions' => array("type = ? AND  ? like CONCAT('%', name, '%') ", 'client', $perm)));

				$access = '';

				foreach ($modules as $module) {
					$access .= $module->id.',';
				}

				$access = rtrim($access,',');
				$user_data['access'] = $access;

				$client = Client::create($user_data);

				if($company->client_id == null){
					$company->client_id = $client->id;
					$company->save();
				}
				
				if(isset($_POST['invite'])){
					$this->credentials($client->id,true,false,false);
				}else{
					
				}


				echo json_encode([
			                'status' => true,
			                'client' => $client->attributes(),
		                 ] );
				die();	
			}
			
		}
	}
	function update($id = FALSE, $getview = FALSE)
	{	
		if($_POST){
			$id = $_POST['id'];
			$client = Client::find($id);
					$config['upload_path'] = './files/media/';
					$config['encrypt_name'] = TRUE;
					$config['allowed_types'] = 'gif|jpg|png';
					$config['max_width'] = '180';
					$config['max_height'] = '180';

					$this->load->library('upload', $config);

					if(isset($_POST['userfile'])){
						if ( $this->upload->do_upload())
						{
							$data = array('upload_data' => $this->upload->data());
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
						}else{
							$error = $this->upload->display_errors('', ' ');
							if($error != "You did not select a file to upload. "){
								$this->session->set_flashdata('message', 'error:'.$error);
								redirect('clients');
							}
						}
					}
					

			unset($_POST['send']);
			unset($_POST['userfile']);
			unset($_POST['file-name']);
			if(empty($_POST["password"])){unset($_POST['password']);}else{$_POST['password'] = $client->set_password($_POST['password']);}
			if(!empty($_POST["access"])){
				$_POST["access"] = implode(",", $_POST["access"]);
			}else{
				$_POST["access"] = "";	
			}
			
			
			if(isset($_POST['view'])){
				$view = $_POST['view'];
				unset($_POST['view']);
			}
			$_POST = array_map('htmlspecialchars', $_POST);
			
			$client->update_attributes($_POST);
       		if(!$client){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_client_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_client_success'));}
			//redirect('clients/view/'.$client->company->id);
			redirect('/clients');
		}else
		{
			$this->view_data['client'] = Client::find($id);
			$this->view_data['modules'] = Module::find('all', array('order' => 'sort asc', 'conditions' => array('type = ?', 'client')));
			if($getview == "view"){$this->view_data['view'] = "true";}
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit_client');
			$this->view_data['form_action'] = 'clients/update';
			$this->content_view = 'clients/_clients';
		}	
	}
	function notes($id = FALSE)
	{	
		if($_POST){
			unset($_POST['send']);
			$_POST = array_map('htmlspecialchars', $_POST);
			$project = Company::find($id);
			$project->update_attributes($_POST);
		}
		$this->theme_view = 'ajax';
	}	
	function company($condition = FALSE, $id = FALSE)
	{	
		switch ($condition) {
			case 'create':
				if($_POST){
					unset($_POST['send']);
					$_POST = array_map('htmlspecialchars', $_POST);
					$company = Company::create($_POST);
					$companyid = Company::last();
					$attributes = array('company_id' => $companyid->id, 'user_id' => $this->user->id);
					$adminExists = CompanyHasAdmin::exists($attributes);
					if(!$adminExists){
						$addUserAsClientAdmin = CompanyHasAdmin::create($attributes);
					}
					$new_company_reference = $_POST['reference']+1;
					$company_reference = Setting::first();
					$company_reference->update_attributes(array('company_reference' => $new_company_reference));
		       		if(!$company){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_company_add_error'));}
		       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_company_add_success'));}
					redirect('clients/view/'.$companyid->id);
				}else
				{
					$this->view_data['clients'] = Company::find('all',array('conditions' => array('inactive=?','0')));
					$this->view_data['next_reference'] = Company::last();
					$this->theme_view = 'modal';
					$this->view_data['title'] = $this->lang->line('application_add_new_company');
					$this->view_data['form_action'] = 'clients/company/create';
					$this->content_view = 'clients/_company';
				}	
					break;
			case 'update':
				if($_POST){
					unset($_POST['send']);
					$id = $_POST['id'];
					if(isset($_POST['view'])){
						$view = $_POST['view'];
						unset($_POST['view']);
					}
					$_POST = array_map('htmlspecialchars', $_POST);
					$company = Company::find($id);
					$company->update_attributes($_POST);
		       		if(!$company){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_company_error'));}
		       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_company_success'));}
					redirect('clients/view/'.$id);
					
				}else
				{
					$this->view_data['company'] = Company::find($id);
					$this->theme_view = 'modal';
					$this->view_data['title'] = $this->lang->line('application_edit_company');
					$this->view_data['form_action'] = 'clients/company/update';
					$this->content_view = 'clients/_company';
				}	
				break;
			case 'delete':
				$company = Company::find($id);
				$company->inactive = '1';
				$company->save();
				foreach ($company->clients as $value) {
				$client = Client::find($value->id);
				$client->inactive = '1';
				$client->save();
				}
				$this->content_view = 'clients/all';
				if(!$company){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_delete_company_error'));}
		       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_delete_company_success'));}
					redirect('clients');
				break;

		}
		
	}	
	function assign($id = FALSE)
	{	
		$this->load->helper('notification');
		if($_POST){
			unset($_POST['send']);
			$id = addslashes($_POST['id']);
			$company = Company::find_by_id($id);

			$users_query = $company->company_has_admins;
			$still_assigned_users = array();
			//remove unselected users
			foreach ($users_query as $value) {
				if(!in_array($value->user_id, $_POST["user_id"])){
					$delete = CompanyHasAdmin::find_by_id($value->id);
					$delete->delete();
				}else{
					array_push($still_assigned_users, $value->user_id);
				}
			}
			//add selected users
			foreach ($_POST["user_id"] as $value) {
				if(!in_array($value, $still_assigned_users)){
					$attributes = array('company_id' => $id, 'user_id' => $value);
					$create = CompanyHasAdmin::create($attributes);
				}
			}
			

       		if(!isset($delete) && !isset($create)){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_client_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_client_success'));}
			redirect('clients/view/'.$id);
		}else
		{
			$this->view_data['users'] = User::find('all',array('conditions' => array('status=?','active')));
			$this->view_data['company'] = Company::find_by_id($id);
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_assign_to_agents');
			$this->view_data['form_action'] = 'clients/assign';
			$this->content_view = 'clients/_assign';
		}	
	}	
	function removeassigned($id = FALSE, $companyid = FALSE)
	{	
		$delete = CompanyHasAdmin::find(array('conditions' => array('user_id = ? AND company_id = ?', $id, $companyid)));
		$delete->delete();
		$this->theme_view = 'ajax';
	}
	function delete($id = FALSE)
	{	
		$client = Client::find($id);
		$client->inactive = '1';
		$client->save();
		$this->content_view = 'clients/all';
		if(!$client){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_delete_client_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_delete_client_success'));}
			redirect('clients');
	}
	function delete_json($id = false) {
		$client = Client::find($id);
		$client->inactive = '1';
		$client->save();

		if (count($client->company->clients) == 0) {
			$company = Company::find($client->company_id);
			$company->inactive = '1';
			$company->save();
		}

		echo json_encode( [
			                  'status' => true
		                  ] );
		die();
	}
	function view($id = FALSE)
	{
		$this->view_data['submenu'] = array(
						$this->lang->line('application_back') => 'clients',
				 		
				 		);	
		$this->view_data['company'] = Company::find($id);
		if($this->user->admin != 1){
				$comp_array = array();
			foreach ($this->user->companies as $value) {
				array_push($comp_array, $value->id);
			}
			if(!in_array($this->view_data['company']->id, $comp_array)){redirect('clients');}
		}
		$this->view_data['invoices'] = Invoice::find('all', array('conditions' => array('estimate != ? AND company_id = ? AND estimate_status != ?', 1, $id, 'Declined')));
		$this->content_view = 'clients/view';
	}

	function credentials($id = FALSE, $email = FALSE, $newPass = FALSE, $noview = TRUE)
	{
		if($email){
			$this->load->helper('file');
			$client = Client::find($id);
			$newPassword = $this->random_password();
			$client->set_password($newPassword);
			$client->save();
			$setting = Setting::first();
			$from_email = EMAIL_FROM; //$setting->email
			$this->email->from($from_email, $setting->company);
			$this->email->to($client->email); 
			$this->email->subject($setting->credentials_mail_subject);
			$this->load->library('parser');
			$parse_data = array(
            					'client_contact' => $client->firstname.' '.$client->lastname,
            					'client_company' => $client->company->name,
            					'client_link' => $setting->domain,
            					'company' => $setting->company,
            					'username' => $client->email,
            					'password' => $newPassword,
            					'logo' => '<img style="max-height: 50px; max-width: 200px; width: auto;" src="https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION["accountUrlPrefix"] . '/' . $setting->logo.'" alt="'.$setting->company.'"/>',
            					'invoice_logo' => '<img style="max-height: 50px; max-width: 200px; width: auto;" src="https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION["accountUrlPrefix"] . '/' . $setting->invoice_logo.'" alt="'.$setting->company.'"/>'
            					);
			
			$message = read_file('./application/views/'.$setting->template.'/templates/email_credentials.html');
  			$message = $this->parser->parse_string($message, $parse_data);
			$this->email->message($message);
			if($this->email->send()){$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_send_login_details_success'));}
       		else{$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_send_login_details_error'));}
       		if($noview){
       			redirect('clients/view/'.$client->company_id);	
       		}else{
       			return;
       		}
			

		} else {
			$this->view_data['client'] = Client::find($id);
			$this->theme_view = 'modal';
			$this->view_data['new_password'] = random_password();
			$this->view_data['title'] = $this->lang->line('application_login_details');
			$this->view_data['form_action'] = 'clients/credentials';
			$this->content_view = 'clients/_credentials';
		}
	}

	

	function random_password( $length = 8 ) {
	    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	    $password = substr( str_shuffle( $chars ), 0, $length );
	    return $password;
	}

	function hash_passwords(){
		$clients = Client::all();
		foreach ($clients as $client) {
			$pass = $client->password_old; 
			$client->password = $client->set_password($pass);
			$client->save();
			
			
		}
		redirect('clients');
	}

	
}