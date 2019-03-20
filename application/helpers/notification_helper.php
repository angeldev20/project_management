<?php if ( ! defined( 'BASEPATH' ) ) {
	exit( 'No direct script access allowed' );
}
/**
 * Notification Helper
 */
function send_notification( $email, $subject, $text, $attachment = false )
{
	$instance =& get_instance();
	$instance->email->clear();
	$instance->load->helper( 'file' );
	$instance->load->library( 'parser' );

	$data["core_settings"] = Setting::first();
	$from_email            = $data["core_settings"]->email;
	$from_email            = EMAIL_FROM;

	$instance->email->from( $from_email, $data["core_settings"]->company );
	$instance->email->to( $email );
	$instance->email->subject( $subject );

	if ( $attachment ) {
		if ( is_array( $attachment ) ) {
			foreach ( $attachment as $value ) {
				$instance->email->attach( 'files/media/' . $value );
			}

		} else {
			$instance->email->attach( 'files/media/' . $attachment );
		}
	}
	//Set parse values
	$parse_data  = array(
		'company'        => $data["core_settings"]->company,
		'link'           => base_url(),
		'logo'           => '<img style="max-height: 50px; max-width: 200px; width: auto;" src="https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION["accountUrlPrefix"] . '/' . $data["core_settings"]->logo . '" alt="' . $data["core_settings"]->company . '"/>',
		'invoice_logo'   => '<img style="max-height: 50px; max-width: 200px; width: auto;" src="https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION["accountUrlPrefix"] . '/' . $data["core_settings"]->invoice_logo . '" alt="' . $data["core_settings"]->company . '"/>',
		'message'        => $text,
		'client_contact' => '',
		'client_company' => ''
	);
	$find_client = Client::find_by_email( $email );
	if ( isset( $find_client->firstname ) ) {
		$parse_data["client_contact"] = $find_client->firstname . " " . $find_client->lastname;
		$parse_data["client_company"] = $find_client->company->name;
	}

	$email_message = read_file( './application/views/' . $data["core_settings"]->template . '/templates/email_notification.html' );
	$message       = $instance->parser->parse_string( $email_message, $parse_data );

	$instance->email->message( $message );
	$send = $instance->email->send();

	return $send;
}

/**
 * Notification Helper
 */
function send_user_notification( $user, $email, $subject, $text, $attachment = false, $callbackUrl = false )
{
    $instance =& get_instance();
    $instance->email->clear();
    $instance->load->helper( 'file' );
    $instance->load->library( 'parser' );

    $data["core_settings"] = Setting::first();
    $from_email            = $data["core_settings"]->email;
    $from_email            = EMAIL_FROM;

    $instance->email->from( $from_email, $data["core_settings"]->company );
    $instance->email->to( $email );
    $instance->email->subject( $subject );

    if ( $attachment ) {
        if ( is_array( $attachment ) ) {
            foreach ( $attachment as $value ) {
                $instance->email->attach( 'files/media/' . $value );
            }

        } else {
            $instance->email->attach( 'files/media/' . $attachment );
        }
    }
    //Set parse values
    $parse_data  = array(
        'first_last_name'        => $user->firstname . ' ' . $user->lastname,
        'company'        => $data["core_settings"]->company,
        'link'           => ($callbackUrl == false) ? base_url() : $callbackUrl,
        'logo'           => '<img style="max-height: 50px; max-width: 200px; width: auto;" src="https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION["accountUrlPrefix"] . '/' . $data["core_settings"]->logo . '" alt="' . $data["core_settings"]->company . '"/>',
        'invoice_logo'   => '<img style="max-height: 50px; max-width: 200px; width: auto;" src="https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION["accountUrlPrefix"] . '/' . $data["core_settings"]->invoice_logo . '" alt="' . $data["core_settings"]->company . '"/>',
        'message'        => $text,
        'client_contact' => '',
        'client_company' => ''
    );
    $find_client = Client::find_by_email( $email );
    if ( isset( $find_client->firstname ) ) {
        $parse_data["client_contact"] = $find_client->firstname . " " . $find_client->lastname;
        $parse_data["client_company"] = $find_client->company->name;
    }

    $email_message = read_file( './application/views/' . $data["core_settings"]->template . '/templates/email_user_notification.html' );
    $message       = $instance->parser->parse_string( $email_message, $parse_data );

    $instance->email->message( $message );
    $send = $instance->email->send();

    return $send;
}

function send_ticket_notification( $email, $subject, $text, $ticket_id, $attachment = false )
{
	$instance =& get_instance();
	$instance->email->clear();
	$instance->load->helper( 'file' );
	$instance->load->library( 'parser' );
	$data["core_settings"] = Setting::first();

	$ticket      = Ticket::find_by_id( $ticket_id );
	$ticket_link = base_url() . 'tickets/view/' . $ticket->id;
	$from_email  = EMAIL_FROM;//data["core_settings"]->email

	$instance->email->reply_to( $data["core_settings"]->ticket_config_email );
	$instance->email->from( $from_email, $data["core_settings"]->company );

	$instance->email->to( $email );
	$instance->email->subject( $subject );
	if ( $attachment ) {
		if ( is_array( $attachment ) ) {
			foreach ( $attachment as $value ) {
				$instance->email->attach( './files/media/' . $value );
			}

		} else {
			$instance->email->attach( './files/media/' . $attachment );
		}
	}
	//Set parse values
	$parse_data = array(
		'company'             => $data["core_settings"]->company,
		'link'                => base_url(),
		'ticket_link'         => $ticket_link,
		'ticket_number'       => $ticket->reference,
		'ticket_created_date' => date( $data["core_settings"]->date_format . '  ' . $data["core_settings"]->date_time_format, $ticket->created ),
		'ticket_status'       => $instance->lang->line( 'application_ticket_status_' . $ticket->status ),
		'logo'                => '<img style="max-height: 50px; max-width: 200px; width: auto;" src="https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION["accountUrlPrefix"] . '/' . $data["core_settings"]->logo . '" alt="' . $data["core_settings"]->company . '"/>',
		'invoice_logo'        => '<img style="max-height: 50px; max-width: 200px; width: auto;" src="https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION["accountUrlPrefix"] . '/' . $data["core_settings"]->invoice_logo . '" alt="' . $data["core_settings"]->company . '"/>',
		'message'             => $text
	);
	if ( isset( $ticket->client->firstname ) ) {
		$parse_data["client_contact"] = $ticket->client->firstname . " " . $ticket->client->lastname;
		$parse_data["client_company"] = $ticket->company->name;
	}
	$email_invoice = read_file( './application/views/' . $data["core_settings"]->template . '/templates/email_ticket_notification.html' );
	$message       = $instance->parser->parse_string( $email_invoice, $parse_data );
	$instance->email->message( $message );
	$instance->email->send();

}

function receipt_notification( $clientId, $subject = false, $paymentId = false )
{
	$instance =& get_instance();
	$instance->email->clear();
	$instance->load->helper( 'file' );
	$instance->load->library( 'parser' );
	$settings    = Setting::first();
	$payment     = InvoiceHasPayment::find_by_id( $paymentId );
	$unixDate    = human_to_unix( $payment->date . ' 00:00' );
	$paymentDate = date( $settings->date_format, $unixDate );
	$client      = Client::find_by_id( $clientId );
	$from_email  = EMAIL_FROM;//$settings->email


	$instance->email->from( $from_email, $settings->company );
	$instance->email->to( $client->email );
	$instance->email->subject( $instance->lang->line( 'application_receipt' ) . " #" . $payment->reference );
	//Set parse values
	$parse_data    = array(
		'company'           => $settings->company,
		'link'              => base_url(),
		'logo'              => '<img style="max-height: 50px; max-width: 200px; width: auto;" src="https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION["accountUrlPrefix"] . '/' . $settings->logo . '" alt="' . $settings->company . '"/>',
		'invoice_logo'      => '<img style="max-height: 50px; max-width: 200px; width: auto;" src="https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION["accountUrlPrefix"] . '/' . $settings->invoice_logo . '" alt="' . $settings->company . '"/>',
		'payment_date'      => $paymentDate,
		'invoice_id'        => $settings->invoice_prefix . $payment->invoice->reference,
		'payment_method'    => $instance->lang->line( 'application_' . $payment->type ),
		'payment_reference' => $payment->reference,
		'payment_amount'    => display_money( $payment->amount, $payment->invoice->currency ),
		'client_firstname'  => $client->firstname,
		'client_lastname'   => $client->lastname,
		'client_company'    => $client->company->name,

	);
	$email_invoice = read_file( './application/views/' . $settings->template . '/templates/email_receipt.html' );
	$message       = $instance->parser->parse_string( $email_invoice, $parse_data );
	$instance->email->message( $message );
	$instance->email->send();
}
