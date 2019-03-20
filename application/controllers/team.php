<?php

class team extends My_Controller
{
	function index()
	{
		$this->setTitle( 'People' );
		$this->content_view = 'people/index';
	}

	function data()
	{
		

		$users = array_map( function ( $user ) {
			$attributes = $user->attributes();

			$attributes['userpic'] = get_user_pic( $attributes['userpic'] );
			
			$dt = new DateTime();	
            $dt->setTimestamp( $attributes['last_login'] );
            $attributes['last_login']   = $dt->format( "Y-m-d H:i:s" );

			return $attributes;
		}, User::find( 'all', array( 'conditions' => array( 'status != ?', 'deleted' ), 'order'=> 'favor desc', ) ) );

		echo json_encode( [
			                  'status' => true,
			                  'users'  => $users
		                  ] );
		die();
	}

	function edit($id, $what, $value){
		switch($what) {
			case "status":
				$user = User::find($id);
				if($user->status == 'active'){
					$user->status = 'inactive';
				}elseif($user->status == 'inactive'){
					$user->status = 'active';	
				}
				$user->save();
				echo json_encode( [
                  	'status' => true,
                  	'data'=>$user->attributes(),
              	] );
              	die();
				break;
			case "admin":
				$user = User::find($id);
				$user->admin = ($user->admin + 1) % 2;
				$user->save();
				echo json_encode( [
                  	'status' => true,
                  	'data'=>$user->attributes(),
              	] );
              	die();
				break;
			case "favor":
				$user = User::find($id);
				$user->favor = ($user->favor + 1) % 2;
				$user->save();
				echo json_encode( [
                  	'status' => true,
                  	'data'=>$user->attributes(),
              	] );
              	die();
				break;
		}
	}

	function inviteAgain($id)
	{
		$user = User::find($id);

		$this->load->helper( 'notification' );

		if ( isset($user->email) ) {
			$email = trim( $user->email);

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

			$access = [ 1, 2 ];
			if ( ! empty( $_POST["access"] ) ) {
				$access = $_POST["access"];
			}
			$access = implode( ",", $access );

			$id   = UserInvitation::generate_guid();
			$link = base_url( '/team/register/' . $id );
			$text = '<p>You are invited to join team.</p><br/>';
			$text .= '<a href="' . $link . '">Click here to accept invitation</a><br/>';

			$created = UserInvitation::create( [
				                                   'guid'  => $id,
				                                   'email' => $email,
				                                   'meta'  => json_encode( [
					                                                           'access' => $user->access,
					                                                           'status' => 'active',
					                                                           'queue'  => $user->queue,
					                                                           'admin'  => $user->admin
				                                                           ] )
			                                   ] );
			if ( $created ) {
				send_notification( $email, $this->lang->line( 'application_notification_user_invitation' ), $text );
			}

			echo json_encode( [
				                  'status' => true
			                  ] );
		}
		die();
	}

	function invite()
	{
		$this->load->helper( 'notification' );

		if ( $_POST ) {
			$email = trim( $_POST['email'] );

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

			$access = [ 1, 2 ];
			if ( ! empty( $_POST["access"] ) ) {
				$access = $_POST["access"];
			}
			$access = implode( ",", $access );

			$id   = UserInvitation::generate_guid();
			$link = base_url( '/team/register/' . $id );
			$text = '<p>You are invited to join team.</p><br/>';
			$text .= '<a href="' . $link . '">Click here to accept invitation</a><br/>';

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

			echo json_encode( [
				                  'status' => true
			                  ] );
		}
		die();
	}

	public function register( $id )
	{
		$this->view_data['error'] = false;
		$exists                   = UserInvitation::guid_exists( $id );

		if ( ! $exists ) {
			redirect( 'login' );
		}

		$invitation = UserInvitation::find( "all", array(
			"conditions" => array(
				"guid = ?",
				$id
			)
		) );
		$invitation = $invitation[0];

		if ( $_POST ) {
			$config['upload_path']   = './files/media/';
			$config['encrypt_name']  = true;
			$config['allowed_types'] = 'gif|jpg|jpeg|png';
			$config['max_width']     = '180';
			$config['max_height']    = '180';

			$this->load->library( 'upload', $config );

			if ( $this->upload->do_upload() ) {
				$this->load->library( 'platformaws', [
					'aws_access_key' => $this->config->item( 'aws_access_key' ),
					'aws_secret_key' => $this->config->item( 'aws_secret_key' )
				] );
				$environment         = ENVIRONMENT;
				$bucket              = "spera-" . $environment;
				$data                = array( 'upload_data' => $this->upload->data() );
				$_POST['userpic']    = $data['upload_data']['file_name'];
				$s3UploadPathAndFile = $_SESSION['accountUrlPrefix'] .
				                       explode( '.', $config['upload_path'] )[1] .
				                       $_POST['userpic'];
				$result              = $this->platformaws->putObjectFile(
					$bucket,
					$s3UploadPathAndFile,
					$config['upload_path'] . $_POST['userpic']
				);
				unlink( $config['upload_path'] . $_POST['userpic'] );
			}

			unset( $_POST['file-name'] );
			unset( $_POST['send'] );
			unset( $_POST['confirm_password'] );

			$data           = array_map( 'htmlspecialchars', $_POST );
			$data['status'] = 1;
			$data['admin']  = 0;
			$data['title']  = ucwords( $data['username'] );
			$user_exists    = User::find_by_username( $_POST['username'] );

			$invitation_meta = json_decode( $invitation->meta, true );
			if ( $invitation_meta ) {
				if ( ! empty( $invitation_meta['access'] ) ) {
					$data['access'] = $invitation_meta['access'];
				}
				if ( ! empty( $invitation_meta['status'] ) ) {
					$data['status'] = $invitation_meta['status'];
				}
				if ( ! empty( $invitation_meta['admin'] ) ) {
					$data['admin'] = $invitation_meta['admin'];
				}
				if ( ! empty( $invitation_meta['queue'] ) ) {
					$data['queue'] = $invitation_meta['queue'];
				}
			}

			if ( empty( $user_exists ) ) {
				$user = User::create( $data );
				if ( ! $user ) {
					$this->view_data['error'] = $this->lang->line( 'messages_create_user_error' );
				} else {
					$invitation->update_attributes( [
						                                'has_registered' => 1
					                                ] );

					User::validate_login( $_POST['username'], $_POST['password'] );
					redirect( base_url() );
				}
			} else {
				$this->view_data['error'] = $this->lang->line( 'messages_create_user_exists' );
			}
		}

		$this->theme_view               = 'login';
		$this->content_view             = 'auth/team_register';
		$this->view_data['form_action'] = 'team/register/' . $id;
		$this->view_data['email']       = $invitation->email;
	}

	function queues()
	{
		$queues = array_map( function ( $queue ) {
			return $queue->attributes();
		}, Queue::all() );

		echo json_encode( [
			                  'status' => true,
			                  'queues' => $queues
		                  ] );
		die();
	}
}