<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 1/2/18
 * Time: 10:25 AM
 */?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class tInvoices extends MY_Controller
{

    /* current team member (user) */
    public $team;

    /** @var  account */
    public $account;

    /** @var  propay_api */
    public $propay_api;

    function __construct()
    {
        parent::__construct();

        if (!$this->user) redirect('login');
        $firstUser = User::first();
        //if(isset($this->user->username) && $firstUser->username != $this->user->username) {
            $this->team = clone $this->user;
        //}

        $access = FALSE;
        if ($this->team) {
            foreach ($this->view_data['menu'] as $key => $value) {
                if ($value->link == "tinvoices") {
                    $access = TRUE;
                }
            }

            if (!$access) {
                redirect('login');
            }
        }
        $this->view_data['submenu'] = array(
            $this->lang->line('application_all_invoices') => 'tinvoices',
        );
    }

    function data()
    {
        $data   = [];
        $status = isset( $_GET['status'] ) ? $_GET['status'] : false;

        if ( $this->user->admin == 0 ) {
            $comp_array             = array();
            $thisUserHasNoCompanies = (array) $this->user->companies;
            if ( ! empty( $thisUserHasNoCompanies ) ) {
                foreach ( $this->user->companies as $value ) {
                    array_push( $comp_array, $value->id );
                }
                // $options = array(
                //     'conditions' => array(
                //         'estimate != ? AND company_id in (?) AND id  IN (SELECT invoice_id FROM invoice_has_users WHERE user_id = ?)',
                //         1,
                //         $comp_array, $this->user->id
                //     )
                // );
                $options = array(
                    'conditions' => array(
                        'estimate != ? AND id  IN (SELECT invoice_id FROM invoice_has_users WHERE user_id = ?)',
                        1,
                        $this->user->id
                    )
                );
                if ( $status && $status != 'All' ) {
                    $options = array(
                        'conditions' => array(
                            'status = ? AND estimate != ? AND id  IN (SELECT invoice_id FROM invoice_has_users WHERE user_id = ?)',
                            $status,
                            1,
                            $this->user->id
                        )
                    );
                }
                $data['invoices'] = Invoice::find( 'all', $options );
            } else {
                $data['invoices'] = (object) array();
            }
        } else {
            $options = array(
                'conditions' => array(
                    'estimate != ? AND id IN (SELECT invoice_id FROM invoice_has_users)',
                    1
                )
            );
            if ( $status && $status != 'All' ) {
                $options = array(
                    'conditions' => array(
                        'status = ? AND estimate != ? AND id IN (SELECT invoice_id FROM invoice_has_users)',
                        $status,
                        1
                    )
                );
            }
            $data['invoices'] = Invoice::find( 'all', $options );
        }

        $days_in_this_month = days_in_month( date( 'm' ), date( 'Y' ) );
        $lastday_in_month   = strtotime( date( 'Y' ) . "-" . date( 'm' ) . "-" . $days_in_this_month );
        $firstday_in_month  = strtotime( date( 'Y' ) . "-" . date( 'm' ) . "-01" );

        $data['invoices_paid_this_month'] = Invoice::count( array( 'conditions' => 'UNIX_TIMESTAMP(`paid_date`) <= ' . $lastday_in_month . ' and UNIX_TIMESTAMP(`paid_date`) >= ' . $firstday_in_month . ' AND estimate != 1 AND status = "paid" AND id NOT IN (SELECT invoice_id FROM invoice_has_users)' ) );
        $data['invoices_due_this_month']  = Invoice::count( array( 'conditions' => 'UNIX_TIMESTAMP(`due_date`) <= ' . $lastday_in_month . ' and UNIX_TIMESTAMP(`due_date`) >= ' . $firstday_in_month . ' AND estimate != 1 AND status != "paid" AND status != "canceled" AND id NOT IN (SELECT invoice_id FROM invoice_has_users)' ) );

        //statistic
        $now                                    = time();
        $beginning_of_week                      = strtotime( 'last Monday', $now ); // BEGINNING of the week
        $end_of_week                            = strtotime( 'next Sunday', $now ) + 86400; // END of the last day of the week
        $data['invoices_due_this_month_graph']  = Invoice::find_by_sql( 'select count(id) AS "amount", DATE_FORMAT(`due_date`, "%w") AS "date_day", DATE_FORMAT(`due_date`, "%Y-%m-%d") AS "date_formatted" from invoices where id NOT IN (SELECT invoice_id FROM invoice_has_users)    
            AND UNIX_TIMESTAMP(`due_date`) >= "' . $beginning_of_week . '" AND UNIX_TIMESTAMP(`due_date`) <= "' . $end_of_week . '" AND estimate != 1 GROUP BY due_date' );
        $data['invoices_paid_this_month_graph'] = Invoice::find_by_sql( "SELECT 
            COUNT(id) AS 'amount',
            DATE_FORMAT(`paid_date`, '%w') AS 'date_day',
            DATE_FORMAT(`paid_date`, '%Y-%m-%d') AS 'date_formatted'
        FROM
            invoices
        WHERE id NOT IN (SELECT invoice_id FROM invoice_has_users)    
            AND 
            UNIX_TIMESTAMP(`paid_date`) >= '$beginning_of_week'
                AND UNIX_TIMESTAMP(`paid_date`) <= '$end_of_week'
                AND estimate != 1
        GROUP BY paid_date" );

        foreach ( $data as $dk => $dv ) {
            if ( is_array( $dv ) ) {
                $data[ $dk ] = [];

                foreach ( $dv as $dvv ) {
                    $attr = $dvv->attributes();

                    if ( isset($attr['company_id']) ) {
                        $company = Company::find( 'all', array( 'conditions' => array( 'id' => $attr['company_id'] ) ) );
                        if ( isset( $company[0] ) ) {
                            $company         = $company[0]->attributes();
                            $attr['company'] = $company;
                            $client          = Client::find( 'all', array( 'conditions' => array( 'id' => $company['client_id'] ) ) );

                            if ( isset( $client[0] ) ) {
                                $client         = $client[0]->attributes();

                                if ( ! empty( $client['userpic'] ) ) {
                                    $client['userpic'] = get_user_pic( $client['userpic'] );
                                }

                                $attr['client'] = $client;
                            }
                        }
                    }

                    $data[ $dk ][] = $attr;
                }
            }
        }

        $data['admin'] = $this->team->admin;
        echo json_encode( $data );
        die();
    }

    function timesheets($invoice_id){

        
        $timesheets = ProjectHasTimeSheet::find('all', array('order'=>'start asc', 'conditions' => array('user_id = ?  AND (invoice_id = 0 or invoice_id = ?) and start is not null and start != ? and time > 0', $this->user->id, $invoice_id,'')));
        $timesheets = array_map(function($item) {

            $attributes = $item->attributes();
            
            $core_settings = Setting::first();
            $weekdays = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Saturday');

            $week = date('w',$item->start);
            $attributes['starts'] = $weekdays[$week].', '.date($core_settings->date_format, $item->start);
            $attributes['project_name'] = $item->project->name;
            $attributes['tracked_hours'] = number_format($item->time / 86400,2);
            return $attributes;
        }, $timesheets);

        $timesheet_array = array();
        foreach ($timesheets as $timesheet) {
            $added = false;
            for ($i=0; $i < count($timesheet_array); $i++) { 
                if($timesheet_array[$i]['date'] == $timesheet['starts']){
                    $timesheet_array[$i]['timesheet'][] = $timesheet;
                    $added = true;
                }
            }
            if(!$added){
                $timesheet_array[] = array('date'=>$timesheet['starts'], 'timesheet'=>array($timesheet) );
            }
        }
        echo json_encode( [
                              'status' => true,
                              'data'   => $timesheet_array
                          ] );
        die();
    }
    function edit( $id = false )
    {
        $this->setTitle( "Money" );
        $this->content_view = 'invoices/team_views/all';
    }
    function details($id = false) {
        $invoice = Invoice::find($id);
        $items = $invoice->invoice_has_items;

        $items = array_map(function($item) {
            return $item->attributes();
        }, $items);

        echo json_encode( [
                              'status' => true,
                              'data'   => [
                                  'invoice' => $invoice->attributes(),
                                  'items'   => $items
                              ]
                          ] );
        die();
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
        redirect( 'tinvoices' );

    }

    function filter( $condition = false )
    {
        $days_in_this_month                          = days_in_month( date( 'm' ), date( 'Y' ) );
        $lastday_in_month                            = date( 'Y' ) . "-" . date( 'm' ) . "-" . $days_in_this_month;
        $firstday_in_month                           = date( 'Y' ) . "-" . date( 'm' ) . "-01";
        $this->view_data['invoices_paid_this_month'] = Invoice::count( array( 'conditions' => 'paid_date <= ' . $lastday_in_month . ' and paid_date >= ' . $firstday_in_month . ' AND estimate != 1' ) );
        $this->view_data['invoices_due_this_month']  = Invoice::count( array( 'conditions' => 'due_date <= ' . $lastday_in_month . ' and due_date >= ' . $firstday_in_month . ' AND estimate != 1' ) );

        //statistic
        $now                                               = time();
        $beginning_of_week                                 = strtotime( 'last Monday', $now ); // BEGINNING of the week
        $end_of_week                                       = strtotime( 'next Sunday', $now ) + 86400; // END of the last day of the week

        $this->view_data['invoices_due_this_month_graph']  = Invoice::find_by_sql(
            'SELECT count(i.id) AS "amount", 
                        DATE_FORMAT(i.`due_date`, "%w") AS "date_day", 
                        DATE_FORMAT(i.`due_date`, "%Y-%m-%d") AS "date_formatted" 
                        FROM invoices AS i 
                 JOIN invoice_has_users AS ihu ON ihu.user_id = " . $this->user->id . " AND ihu.invoice_id = i.id    
                 WHERE UNIX_TIMESTAMP(i.`due_date`) >= "' . $beginning_of_week . '" 
                 AND UNIX_TIMESTAMP(i.`due_date`) <= "' . $end_of_week . '" 
                 AND i.estimate != 1 
                 AND ihu.user_id = ' . $this->user->id . '
                 GROUP BY i.due_date'
        );
        $this->view_data['invoices_paid_this_month_graph'] = Invoice::find_by_sql( "SELECT 
    COUNT(i.id) AS 'amount',
        DATE_FORMAT(i.`paid_date`, '%w') AS 'date_day',
        DATE_FORMAT(i.`paid_date`, '%Y-%m-%d') AS 'date_formatted'
    FROM
        invoices AS i
    JOIN invoice_has_users AS ihu ON ihu.user_id = " . $this->user->id . " AND ihu.invoice_id = i.id    
    WHERE
        UNIX_TIMESTAMP(i.`paid_date`) >= '$beginning_of_week'
        AND UNIX_TIMESTAMP(i.`paid_date`) <= '$end_of_week'
        AND i.estimate != 1
        AND ihu.user_id = " . $this->user->id . "
    GROUP BY i.paid_date" );


        switch ( $condition ) {
            case 'open':
                $option = 'status = "Open" and estimate != 1';
                break;
            case 'sent':
                $option = 'status = "Sent" and estimate != 1';
                break;
            case 'paid':
                $option = 'status = "Paid" and estimate != 1';
                break;
            case 'PartiallyPaid':
                $option = 'status = "PartiallyPaid" and estimate != 1';
                break;
            case 'canceled':
                $option = 'status = "Canceled" and estimate != 1';
                break;
            case 'overdue':
                $option = '(status = "Open" OR status = "Sent" OR status = "PartiallyPaid") and estimate != 1 and due_date < "' . date( 'Y' ) . "-" . date( 'm' ) . '-' . date( 'd' ) . '" ';
                break;
            default:
                $option = 'estimate != 1';
                break;
        }

        if ( $this->user->admin == 0 ) {
            $comp_array             = array();
            $thisUserHasNoCompanies = (array) $this->user->companies;
            if ( ! empty( $thisUserHasNoCompanies ) ) {
                foreach ( $this->user->companies as $value ) {
                    array_push( $comp_array, $value->id );
                }
                $options                     = array(
                    'conditions' => array(
                        $option . ' AND company_id in (?)',
                        $comp_array
                    )
                );
                $this->view_data['invoices'] = Invoice::find( 'all', $options );
            } else {
                $this->view_data['invoices'] = (object) array();
            }
        } else {
            $options                     = array( 'conditions' => array( $option ) );
            $this->view_data['invoices'] = Invoice::find( 'all', $options );
        }


        $this->content_view = 'invoices/team_views/all';
    }

    function create()
    {
        if ( $_POST ) {
            unset( $_POST['send'] );
            unset( $_POST['_wysihtml5_mode'] );
            unset( $_POST['files'] );

            $item_attr = [];

            $item_attr['type'] = $_POST['type'];
            unset( $_POST['type']);
            $item_attr['name'] = $_POST['name'];
            unset( $_POST['name']);
            $item_attr['value'] = $_POST['value'];
            unset( $_POST['value']);
            $item_attr['amount'] = $_POST['amount'];
            unset( $_POST['amount']);
            $item_attr['description'] = $_POST['description'];
            unset( $_POST['description']);

            $core_settings = Setting::first();


            $client = Client::find_by_sql("select * from clients where `email`='" . $core_settings->email . "'");
            $company = Company::find_by_sql("select * from companies where `name`='" . $core_settings->company . "'");

            if(!$company) {
                $company_attr['name'] = $core_settings->company;
                $company_attr['website'] = $core_settings->domain;
                $company_attr['phone'] = $core_settings->invoice_tel;
                $company_attr['mobile'] = '';
                $company_attr['address'] = $core_settings->invoice_address;
                $company_attr['zipcode'] = ''; //$core_settings->invoice_zip;
                $company_attr['city'] = $core_settings->invoice_city;
                $company_attr['country'] = '';
                $company_attr['province'] = '';
                $company_attr['vat'] = "0";
                $company_attr['reference'] = $core_settings->company_reference;
                $company = Company::create($company_attr);
                $core_settings->company_reference = $core_settings->company_reference+1;
                $core_settings->save();
                $_POST['company_id'] = $company->id;
            } else {
                $company = $company[0];
                $_POST['company_id'] = $company->id;
            }

	        $companyHasAdmin = CompanyHasAdmin::find_by_sql("select * from company_has_admins where `company_id`=2
                 AND `user_id`=" . $this->team->id . ";");

	        if (empty($companyHasAdmin)) {
		        $attributes = array( 'company_id' => $company->id, 'user_id' => $this->team->id );
		        $companyHasAdmin = CompanyHasAdmin::create( $attributes );
	        }


	        $companyHasAdmin = CompanyHasAdmin::find_by_sql("select * from company_has_admins where `company_id`=" . $company->id . "
                 AND `user_id`=" . $this->team->id . ";");

            if(!$client) {
                $lastclient = Client::last();
                $client_attr = [];
                $client_attr['email'] = $core_settings->email;
                $invoiceContactParts = explode (' ', $core_settings->invoice_contact);
                $client_attr['firstname'] = (isset($invoiceContactParts[0])) ? $invoiceContactParts[0] : '';
                $client_attr['lastname'] = (isset($invoiceContactParts[1])) ? $invoiceContactParts[1] : '';
                $client_attr['phone'] = $company->phone;
                $client_attr['mobile'] = '';
                $client_attr['address'] = $company->address;
                $client_attr['zipcode'] = $company->zipcode;
                $client_attr['city'] = $company->city;
                $client_attr['access'] = $core_settings->default_client_modules;
                $client_attr['company_id'] = $company->id;
                $client = Client::create($client_attr);
	            $company->client_id = $client->id;
	            $company->save();
            } else {
                $client = $client[0];
            }

            $project = Project::find_by_sql("select * from projects where `name`='subcontractors'");
            if(!$project) {
                $time = time();
                $endTime = $time + (3.15569*10^8);
                $project = Project::create([
                    'datetime' => time(),
                    'reference' => $core_settings->project_reference,
                    'name' => 'subcontractors',
                    'description' => 'subcontractors',
                    'start' => date ("Y-m-d", $time),
                    'end' => date ("Y-m-d", $endTime),
                    'progress' => 0,
                    'sticky' => 0,
                    'category' => 'Service',
                    'company_id' => $company->id

                ]);
                $new_project_reference = $core_settings->project_reference + 1;
                $core_settings->update_attributes(array('project_reference' => $new_project_reference));
                $_POST['project_id'] = $project->id;
            } else {
                $project = $project[0];
                $_POST['project_id'] = $project->id;
            }

            $projectHasUsers = ProjectHasWorker::find_by_sql("select * from project_has_workers where `project_id`=" . $project->id . " AND user_id=" . $this->user->id);
            if (!$projectHasUsers) {
                $projectHasUsers = ProjectHasWorker::create([
                    'project_id' => $project->id,
                    'user_id' => $this->user->id
                ]);
            } else {
                $projectHasUsers = $projectHasUsers[0];
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

            $invoice     = Invoice::create( $_POST );
            $invoiceUser = InvoiceHasUser::create(['invoice_id' => $invoice->id, 'user_id' => $this->user->id ]);
            $new_invoice_reference = $_POST['reference'] + 1;

            $invoice_reference = Setting::first();
            $invoice_reference->update_attributes( array( 'invoice_reference' => $new_invoice_reference ) );
            if ( ! $invoice ) {
                $this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_create_invoice_error' ) );
            } else {
                $this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_create_invoice_success' ) );
                $item_attr['invoice_id'] = $invoice->id;
                $item_attr['item_id'] = 0;
                $item_attr['task_id'] = 0;
                InvoiceHasItem::create($item_attr);
                redirect('tinvoices/view/' . $invoice->id);
            }
            redirect( 'tinvoices' );
        } else {
            $this->view_data['invoices'] = Invoice::find('all',array('conditions' => array('estimate != ? AND issue_date<=?',1,date('Y-m-d', time()))));
            $this->view_data['next_reference'] = Invoice::last();
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
            $this->theme_view               = 'modal';
            $this->view_data['title']       = $this->lang->line( 'application_create_invoice' );
            $this->view_data['form_action'] = 'tinvoices/create';
            $this->content_view             = 'invoices/team_views/_invoice';
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

    function index(){
        $this->setTitle("Money");
        $this->content_view = 'invoices/team_views/all';
    }

    function create_()
    {
        $this->setTitle( "Money" );
        $this->content_view = 'invoices/all';
    }

    function store()
    {
        $core_settings = Setting::first();
        if ( $_POST ) {
            $allowed = ['status', 'currency'];

            $data    = array_filter( $_POST, function ( $field ) use ( &$allowed ) {
                return in_array( $field, $allowed );
            }, ARRAY_FILTER_USE_KEY );

            

            $data['reference'] = $core_settings->invoice_reference;
            

            $core_settings->update_attributes( array( 'invoice_reference' => $core_settings->invoice_reference + 1 ) );
            
            $data['issue_date'] = date("Y-m-d");
            $data['due_date'] = date("Y-m-d", strtotime("+30 days"));
            $data['company_id'] = 0;

            $invoice     = Invoice::create( $data );
            $invoiceUser = InvoiceHasUser::create(['invoice_id' => $invoice->id, 'user_id' => $this->user->id ]);

            if ( ! $invoice ) {
                echo json_encode( [
                                  'status' => false,
                              ] );
            } else {
                $this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_create_invoice_success' ) );
                $item_attr['invoice_id'] = $invoice->id;
                $item_attr['item_id'] = 0;
                $item_attr['task_id'] = 0;
                InvoiceHasItem::create($item_attr);
                echo json_encode( [
                                  'status' => true,
                                  'data'   => $invoice->attributes()
                              ] );
            }

            
        }else{
            $data = array();
            $data['status'] = 'Draft';
            $data['currency'] = '$';

            $allowed = ['status', 'currency'];

            $data    = array_filter( $data, function ( $field ) use ( &$allowed ) {
                return in_array( $field, $allowed );
            }, ARRAY_FILTER_USE_KEY );

            $data['issue_date'] = date("Y-m-d");
            $data['due_date'] = date("Y-m-d", strtotime("+30 days"));
            $data['company_id'] = 0;

            

            $data['reference'] = $core_settings->invoice_reference;
            
            $invoice     = Invoice::create( $data );
            $invoiceUser = InvoiceHasUser::create(['invoice_id' => $invoice->id, 'user_id' => $this->user->id ]);

            $core_settings->update_attributes( array( 'invoice_reference' => $core_settings->invoice_reference + 1 ) );

            redirect( 'invoices/edit/'.$invoice->id );      
        }
        die();
    }

    function index1()
    {
        $parsedAccountUrlPrefix = $_SESSION['accountUrlPrefix'];

        $databaseName = $parsedAccountUrlPrefix . '_' . ENVIRONMENT;

        /** @var CI_DB_mysql_driver $primaryDatabase */
        $primaryDatabase = $this->load->database('primary', TRUE);

        $params = [
            'databaseName' => $databaseName,
            'primaryDatabase' => $primaryDatabase        ];

        $this->load->library('propay_api', $params);

        $this->view_data['isSignedUp'] = $this->propay_api->isSignedUp($parsedAccountUrlPrefix, $this->user->username);

        if ( $this->user->admin == 0 ) {
            $comp_array             = array();
            $thisUserHasNoCompanies = (array) $this->user->companies;
            if ( ! empty( $thisUserHasNoCompanies ) ) {
	            foreach ( $this->user->companies as $value ) {
		            array_push( $comp_array, $value->id );
	            }
                $options = "estimate != 1 AND company_id IN (" . implode(",",$comp_array) . ")";
                $this->view_data['invoices'] = Invoice::getTeamMemberInvoices($this->team->id, $options);
            } else {
                $this->view_data['invoices'] = (object) array();
            }
        } else {
            $options                     = array( 'conditions' => array( 'estimate != ?', 1 ) );
            $this->view_data['invoices'] = Invoice::find( 'all', $options );
        }

        $days_in_this_month = days_in_month( date( 'm' ), date( 'Y' ) );
        $lastday_in_month   = strtotime( date( 'Y' ) . "-" . date( 'm' ) . "-" . $days_in_this_month );
        $firstday_in_month  = strtotime( date( 'Y' ) . "-" . date( 'm' ) . "-01" );

        $this->view_data['invoices_paid_this_month'] = Invoice::count( array( 'conditions' => 'UNIX_TIMESTAMP(`paid_date`) <= ' . $lastday_in_month . ' and UNIX_TIMESTAMP(`paid_date`) >= ' . $firstday_in_month . ' AND estimate != 1 AND status = "paid" AND id IN (SELECT invoice_id FROM invoice_has_users WHERE user_id=' . $this->user->id . ')' ) );
        $this->view_data['invoices_due_this_month']  = Invoice::count( array( 'conditions' => 'UNIX_TIMESTAMP(`due_date`) <= ' . $lastday_in_month . ' and UNIX_TIMESTAMP(`due_date`) >= ' . $firstday_in_month . ' AND estimate != 1 AND status != "paid" AND status != "canceled" AND id IN (SELECT invoice_id FROM invoice_has_users WHERE user_id=' . $this->user->id . ')' ) );

        //statistic
        $now                                               = time();
        $beginning_of_week                                 = strtotime( 'last Monday', $now ); // BEGINNING of the week
        $end_of_week                                       = strtotime( 'next Sunday', $now ) + 86400; // END of the last day of the week
        $this->view_data['invoices_due_this_month_graph']  = Invoice::find_by_sql(
            'SELECT count(i.id) AS "amount", 
                        DATE_FORMAT(i.`due_date`, "%w") AS "date_day", 
                        DATE_FORMAT(i.`due_date`, "%Y-%m-%d") AS "date_formatted" 
                        FROM invoices AS i 
                 JOIN invoice_has_users AS ihu ON ihu.user_id = " . $this->user->id . " AND ihu.invoice_id = i.id    
                 WHERE UNIX_TIMESTAMP(i.`due_date`) >= "' . $beginning_of_week . '" 
                 AND UNIX_TIMESTAMP(i.`due_date`) <= "' . $end_of_week . '" 
                 AND i.estimate != 1 
                 AND ihu.user_id = ' . $this->user->id . '
                 GROUP BY i.due_date'
        );
        $this->view_data['invoices_paid_this_month_graph'] = Invoice::find_by_sql( "SELECT 
    COUNT(i.id) AS 'amount',
        DATE_FORMAT(i.`paid_date`, '%w') AS 'date_day',
        DATE_FORMAT(i.`paid_date`, '%Y-%m-%d') AS 'date_formatted'
    FROM
        invoices AS i
    JOIN invoice_has_users AS ihu ON ihu.user_id = " . $this->user->id . " AND ihu.invoice_id = i.id    
    WHERE
        UNIX_TIMESTAMP(i.`paid_date`) >= '$beginning_of_week'
        AND UNIX_TIMESTAMP(i.`paid_date`) <= '$end_of_week'
        AND i.estimate != 1
        AND ihu.user_id = " . $this->user->id . "
    GROUP BY i.paid_date" );


        $this->content_view = 'invoices/team_views/all';
        $this->view_data['user'] = $this->user;
    }

    function view($id = FALSE)
    {
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

        $this->view_data['isSignedUp'] = $this->propay_api->isSignedUp($parsedAccountUrlPrefix, $this->team->username);

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

        $invoiceUser = InvoiceHasUser::find('all',array('conditions' => array('user_id=? AND invoice_id=?',$this->user->id,$id)));
        if($this->user->admin != 1) {
            if (!count($invoiceUser) > 0 || count($invoiceUser) > 0 && $invoiceUser[0]->user_id != $this->user->id) {
                echo "<pre>";
                var_export($id);
                die();
                //redirect('tinvoices');
            }
        }
        $this->content_view = 'invoices/team_views/view';
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

        $this->view_data['form_action'] = 'tinvoices/propay';
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
                    redirect('tinvoices');
                }
            }
            $this->view_data['isSignedUp']  = $isSignedUp;
            $this->view_data['settings']    = Setting::first();
            $this->content_view             = 'invoices/_propay_signup';
            $this->view_data['form_action'] = 'tinvoices/propay_signup';
            $this->view_data['title']       = $this->lang->line( 'application_payment_id' );
            $this->theme_view       = 'modal';
        }
    }

    function download($id = FALSE){
        $this->load->helper(array('dompdf', 'file'));
        $this->load->library('parser');
        $data["invoice"] = Invoice::find($id);
        $data['items'] = InvoiceHasItem::find('all',array('conditions' => array('invoice_id=?',$id)));
        $invoiceUser = InvoiceHasUser::find('all',array('conditions' => array('user_id=? AND invoice_id=?',$this->team->id,$id)));
        if (!count($invoiceUser) > 0 || count($invoiceUser) > 0 && $invoiceUser[0]->user_id != $this->team->id)
            redirect('tinvoices');
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
                    redirect( 'tinvoices/view/' . $_POST['invoice_id'] );

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
            redirect( 'tinvoices/view/' . $_POST['invoice_id'] );

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
            $this->view_data['form_action'] = 'tinvoices/item';
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
            redirect( 'tinvoices/view/' . $_POST['invoice_id'] );

        } else {
            $this->view_data['invoice_has_items'] = InvoiceHasItem::find( $id );
            $this->theme_view                     = 'modal';
            $this->view_data['title']             = $this->lang->line( 'application_edit_item' );
            $this->view_data['form_action']       = 'tinvoices/item_update';
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
        redirect( 'tinvoices/view/' . $invoice_id );
    }

    function preview( $id = false, $attachment = false )
    {
        $this->load->helper( array( 'dompdf', 'file' ) );
        $this->load->library( 'parser' );
        $data["invoice"]       = Invoice::find( $id );
        $data['items']         = InvoiceHasItem::find( 'all', array( 'conditions' => array( 'invoice_id=?', $id ) ) );

	    $data["core_settings"] = Setting::first();
	    $data["core_settings"]->email = $this->user->email;
	    $data["core_settings"]->company = $this->user->firstname . ' ' . $this->user->lastname;
	    $data["core_settings"]->invoice_address = '';
	    $data["core_settings"]->invoice_city = '';
	    $data["core_settings"]->invoice_contact = $this->user->firstname . ' ' . $this->user->lastname;
	    $data["core_settings"]->invoice_tel = '';

	    $user = null;
	    $invoiceUser = InvoiceHasUser::find('all',array('conditions' => array('invoice_id=?',$id)));
	    if (count($invoiceUser) > 0) {
		    $user = User::find( $invoiceUser[0]->user_id );
	    }
	    $company = ($user != null) ? $user->firstname . ' ' . $user->lastname : $data["core_settings"]->company;

        $due_date   = date( $data["core_settings"]->date_format, human_to_unix( $data["invoice"]->due_date . ' 00:00:00' ) );
        $parse_data = array(
            'due_date'    => $due_date,
            'invoice_id'  => $data["core_settings"]->invoice_prefix . $data["invoice"]->reference,
            'client_link' => $data["core_settings"]->domain . '/pinvoices/view/' . $id . '?accessCode=' . md5( $_SESSION['accountUrlPrefix'] . $id),
            'company'     => $company,
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
	    $data["core_settings"]->email = $this->user->email;
	    $data["core_settings"]->company = $this->user->firstname . ' ' . $this->user->lastname;
	    $data["core_settings"]->invoice_address = '';
	    $data["core_settings"]->invoice_city = '';
	    $data["core_settings"]->invoice_contact = $this->user->firstname . ' ' . $this->user->lastname;
	    $data["core_settings"]->invoice_tel = '';

	    $user = null;
	    $invoiceUser = InvoiceHasUser::find('all',array('conditions' => array('invoice_id=?',$id)));
	    if (count($invoiceUser) > 0) {
		    $user = User::find( $invoiceUser[0]->user_id );
	    }
	    $company = ($user != null) ? $user->firstname . ' ' . $user->lastname : $data["core_settings"]->company;

        $due_date           = date( $data["core_settings"]->date_format, human_to_unix( $data["invoice"]->due_date . ' 00:00:00' ) );
        $parse_data         = array(
            'due_date'    => $due_date,
            'invoice_id'  => $data["core_settings"]->invoice_prefix . $data["invoice"]->reference,
            'client_link' => $data["core_settings"]->domain . '/pinvoices/view/' . $id . '?accessCode=' . md5( $_SESSION['accountUrlPrefix'] . $id),
            'company'     => $company,
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

	    $user = $this->user;
	    $invoiceUser = InvoiceHasUser::find('all',array('conditions' => array('invoice_id=?',$id)));
	    if (count($invoiceUser) > 0) {
		    $user = User::find( $invoiceUser[0]->user_id );
	    }

        $data["invoice"]       = Invoice::find( $id );
        $data['items']         = InvoiceHasItem::find( 'all', array( 'conditions' => array( 'invoice_id=?', $id ) ) );
        $data["core_settings"] = Setting::first();
        $old_core_settings = [];
	    $old_core_settings['email'] =  $data["core_settings"]->email;
	    $old_core_settings['company'] =  $data["core_settings"]->company;
	    $old_core_settings['invoice_address'] =  $data["core_settings"]->invoice_address;
	    $old_core_settings['invoice_city'] =  $data["core_settings"]->invoice_city;
	    $old_core_settings['invoice_contact'] =  $data["core_settings"]->invoice_contact;
	    $old_core_settings['invoice_tel'] =  $data["core_settings"]->invoice_tel;

	    $data["core_settings"]->email = $user->email;
	    $data["core_settings"]->company = $user->firstname . ' ' . $user->lastname;
	    $data["core_settings"]->invoice_address = '';
	    $data["core_settings"]->invoice_city = '';
	    $data["core_settings"]->invoice_contact = $user->firstname . ' ' . $user->lastname;
	    $data["core_settings"]->invoice_tel = '';

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
        $subject = "New Invoice from Team Member: " . $user->username;
	    $from_email = EMAIL_FROM; //$data["core_settings"]->email
        $this->email->from( $from_email, $data["core_settings"]->company );
        //if ( ! is_object( $data["invoice"]->company->client ) && $data["invoice"]->company->client->email == "" ) {
        //    $this->session->set_flashdata( 'message', 'error:This client company has no primary contact! Just add a primary contact.' );
        //    redirect( 'tinvoices/view/' . $id );
        //}
        $adminUser = User::first();
        $this->email->to( $adminUser->email );
        $this->email->reply_to( $data["core_settings"]->email, $data["core_settings"]->company );
        $this->email->subject( $subject );
        $this->email->attach( "files/temp/" . $filename . ".pdf" );
	    $this->email->set_smtp_conn_options(
		    [
			    'ssl' => [
				    'verify_peer' => false,
				    'verify_peer_name' => false,
				    'allow_self_signed' => true
			    ]
		    ]
	    );

        $email_invoice = read_file( './application/views/' . $data["core_settings"]->template . '/templates/email_invoice.html' );
        $message       = $this->parser->parse_string( $email_invoice, $parse_data );
        $this->email->message( $message );
        if ( $this->email->send() ) {
            $this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_send_invoice_success' ) );
            if ( $data["invoice"]->status == "Open" ) {
                $data["invoice"]->update_attributes( array( 'status' => 'Sent', 'sent_date' => date( "Y-m-d" ) ) );
            }
            log_message( 'error', 'Invoice #' . $data["core_settings"]->invoice_prefix . $data["invoice"]->reference . ' has been send to ' . $data["core_settings"]->email );
        } else {
            $this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_send_invoice_error' ) );
            log_message( 'error', 'ERROR: Invoice #' . $data["core_settings"]->invoice_prefix . $data["invoice"]->reference . ' has not been send to ' . $data["core_settings"]->email . '. Please check your servers email settings.' );
        }
        unlink( "files/temp/" . $filename . ".pdf" );
        redirect( 'tinvoices/view/' . $id );
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
                redirect( 'tinvoices/view/' . $id );
            } else {
                redirect( 'tinvoices' );
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
            $this->view_data['form_action'] = 'tinvoices/update';
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
            redirect( 'tinvoices/view/' . $_POST['invoice_id'] );
        } else {
            $this->view_data['invoice']           = Invoice::find_by_id( $id );
            $this->view_data['payment_reference'] = InvoiceHasPayment::count( array( 'conditions' => 'invoice_id = ' . $id ) ) + 1;
            $this->view_data['sumRest']           = sprintf( "%01.2f", round( $this->view_data['invoice']->sum - $this->view_data['invoice']->paid, 2 ) );


            $this->theme_view               = 'modal';
            $this->view_data['title']       = $this->lang->line( 'application_add_payment' );
            $this->view_data['form_action'] = 'tinvoices/payment';
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
            redirect( 'tinvoices/view/' . $_POST['invoice_id'] );

        } else {
            $this->view_data['payment']     = InvoiceHasPayment::find_by_id( $id );
            $this->view_data['invoice']     = Invoice::find_by_id( $this->view_data['payment']->invoice_id );
            $this->theme_view               = 'modal';
            $this->view_data['title']       = $this->lang->line( 'application_add_payment' );
            $this->view_data['form_action'] = 'tinvoices/payment_update';
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

    function delete( $id = false )
    {
        $invoice = Invoice::find( $id );
        $invoice->delete();
        $this->content_view = 'invoices/all';
        if ( ! $invoice ) {
            $this->session->set_flashdata( 'message', 'error:' . $this->lang->line( 'messages_delete_invoice_error' ) );
        } else {
            $this->session->set_flashdata( 'message', 'success:' . $this->lang->line( 'messages_delete_invoice_success' ) );
        }
        redirect( 'tinvoices' );
    }

}