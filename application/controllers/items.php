<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Items extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$access = FALSE;
		if($this->client){
			redirect('cprojects');
		}elseif($this->user){
			foreach ($this->view_data['menu'] as $key => $value) {
				if($value->link == "items"){ $access = TRUE;}
			}
			if(!$access){redirect('login');}
		}else{
			redirect('login');
		}
		$this->view_data['submenu'] = array(
				 		$this->lang->line('application_all_items') => 'items'
				 		);

	}
	function index()
	{
		$this->view_data['items'] = Item::find('all',array('conditions' => array('inactive=?','0')));

		$this->setTitle('Money');

		$this->content_view = 'invoices/items';
		$this->content_view = 'invoices/all';
	}

	function data()
	{
		$data = Item::find( 'all', array('conditions' => array('inactive = 0')) );
		$data = array_map(function($item) {
			return $item->attributes();
		}, $data);

		echo json_encode( [
			                  'items' => $data
		                  ] );
		die();
	}

	function types($action = false, $id = false)
	{
		switch($action) {
			case "add":
				$type = Type::create($_POST);
				echo json_encode( [
					                  'status' => true,
					                  'type'   => $type->attributes()
				                  ] );
				break;
			default:
				// $types = array_map( function ( $type ) {
				// 	return $type->attributes();
				// }, Type::all() );
				if($id){
					$item = Item::find_by_id($id);
					echo json_encode([
				                 'status' => true,
				                 'data' => $item->attributes()
			                 ] );
					die();
				}else{
					$types = array_map( function ( $type ) {
						return $type->attributes();
					}, Item::find( 'all', array('conditions' => array("inactive = 0 AND name !='' AND name is not null")) ) );
					$types[] = array("id"=>"timetracking", "name"=>"Add From Tracked Time");
					echo json_encode( [
					                  'status' => true,
					                  'data'   => $types
				                  ] );
					break;
				}
				
				
		}
		die();
	}

	function create_item() {
		if ( $_POST ) {
			$type = $_POST['type'];
			// if(intval($type)) {
			// 	$type_obj = Type::find($type);
			// 	if($type_obj)
			// 		$type = $type_obj->name;
			// }
			if(!empty($type) ){
				if(isset($_POST['itemId']) && $_POST['itemId'] > 0){
					$item = Item::find_by_id($_POST['itemId']);
				}else{
					$item = Item::create( [
					                      'name'        => !empty($_POST['name']) ? $_POST['name'] : '',
					                      'value'       => $_POST['value'],
					                      'description' => $_POST['description'],
					                      'type'        => $type
				                      ] );
				}
					
					if(isset($_POST['invoice_id']) && isset($_POST['qty']) && $_POST['qty'] > 0) {
						$invoice_item = InvoiceHasItem::create( [
							                                        'invoice_id'  => $_POST['invoice_id'],
							                                        'item_id'     => $item->id,
							                                        'amount'      => $_POST['qty'],
							                                        'value'       => $_POST['value'],
							                                        'description' => $_POST['description'],
							                                        'type'        => $type
						                                        ] );
					}

					echo json_encode( [
						                  'status' => true,
						                  'data'   => $item->attributes()
					                  ] );			
			}else{
				echo json_encode( [
					                  'status' => false,
				                  ] );	
			}
			
		}
		die();
	}
	function create_items(){
		if($_POST){
			unset($_POST['send']);
			$_POST['inactive'] = 0;
			$description = $_POST['description'];
			$_POST = array_map('htmlspecialchars', $_POST);
			$_POST['description'] = $description;
			$item = Item::create($_POST);
       		if(!$item){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_create_item_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_create_item_success'));}
			redirect('items');

		}else
		{
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_create_item');
			$this->view_data['form_action'] = 'items/create_items';
			$this->content_view = 'invoices/_items';
		}
	}
	function update_items($id = FALSE){
		if($_POST){
			unset($_POST['send']);
			$description = $_POST['description'];
			$_POST = array_map('htmlspecialchars', $_POST);
			$_POST['description'] = $description;
			$id = $_POST['id'];
			$item = Item::find($id);
			$item = $item->update_attributes($_POST);
       		if(!$item){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_item_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_item_success'));}
			redirect('items');

		}else
		{
			$this->view_data['items'] = Item::find($id);
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit_item');
			$this->view_data['form_action'] = 'items/update_items';
			$this->content_view = 'invoices/_items';
		}
	}
	function edit($id = false)
	{
		if ( $_POST ) {
			$item = Item::find( $id );
			$item->update_attributes( $_POST );

			echo json_encode( [
				                  'status' => true
			                  ] );
		}
		die();
	}

	function get( $id = false )
	{
		$item = Item::find( $id );

		echo json_encode( [
			                  'status' => true,
			                  'item'   => $item->attributes()
		                  ] );
		die();
	}

	function delete_item($id = false) {
		$item = Item::find($id);
		$item->inactive = 1;
		$item->save();

		$invoice_items = InvoiceHasItem::find('all', array('conditions' => array('item_id = ?', $id)));
		foreach($invoice_items as $item) {
			$item->delete();
		}

		echo json_encode([
							'status' => true
		                 ]);
		die();
	}
	function delete_items($id = FALSE){
		$item = Item::find($id);
		$item->inactive = 1;
		$item->save();
		if(!$item){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_delete_item_error'));}
       	else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_delete_item_success'));}
		redirect('items');
	}

	function copy( $id = false )
	{
		if($_POST){
			unset($_POST['send']);
			$_POST['inactive'] = 0;
			$description = $_POST['description'];
			$_POST = array_map('htmlspecialchars', $_POST);
			$_POST['description'] = $description;
			
			$item = Item::create( [
				                      'name'        => !empty($_POST['name']) ? $_POST['name'] : '',
				                      'value'       => $_POST['value'],
				                      'description' => $_POST['description'],
				                      'type'        => $type
			                      ] );

			//$item = Item::create($_POST);
       		if(!$item){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_create_item_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_create_item_success'));}
			redirect('items');

		}else
		{
			$this->theme_view = 'modal';
			$this->view_data['items'] = Item::find( $id );
			$this->view_data['title'] = $this->lang->line('application_copy_item');
			$this->view_data['form_action'] = 'items/copy';
			$this->content_view = 'invoices/_items';
		}
	}

}