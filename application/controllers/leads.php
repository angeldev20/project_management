<?php if ( ! defined( 'BASEPATH' ) ) {
	exit( 'No direct script access allowed' );
}

class Leads extends MY_Controller
{
	function index()
	{
		$this->setTitle( 'Money' );
		$this->content_view = 'invoices/all';
	}

	function data()
	{
		$quotations = array_map( function ( $quotation ) {
			$data          = $quotation->attributes();
			$data['leads'] = Quoterequest::count( array(
				                                      'conditions' => array(
					                                      'custom_quotation_id = ?',
					                                      $data['id']
				                                      )
			                                      ) );

			return $data;
		}, Customquote::all() );

		echo json_encode( [
			                  'status' => true,
			                  'leads'  => $quotations
		                  ] );
		die();
	}

	function edit( $id = false )
	{
		$this->setTitle( 'Money' );
		$this->content_view = 'invoices/all';
	}

	function create()
	{
		$this->setTitle( 'Money' );
		$this->content_view = 'invoices/all';
	}

	function edit_or_create( $id = false )
	{
		if ( $_POST ) {
			$_POST["user_id"] = $this->user->id;
			if ( $id ) {
				$quote = Customquote::find( $id );
				$quote->update_attributes( $_POST );
			} else {
				$quote = Customquote::create( $_POST );
			}

			echo json_encode( [
				                  'status' => true,
				                  'quote'  => $quote->attributes()
			                  ] );
		}
		die();
	}

	function details( $id = false )
	{
		$quote = Customquote::find( $id );

		echo json_encode( [
			                  'status' => true,
			                  'quote'  => $quote->attributes()
		                  ] );
		die();
	}

	function delete( $id = false )
	{
		$quotation = Customquote::find( $id );
		$quotation->delete();

		echo json_encode( [
			                  'status' => true
		                  ] );
		die();
	}
}