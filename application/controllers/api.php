<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 10/16/17
 * Time: 11:18 AM
 */
class api extends My_Controller
{

    /** @var  account */
    public $account;

    /** @var  rest */
    public $rest;

    /** @var  projecttasks */
    public $projecttasks;

    /** @var  crypto */
    public $crypto;

    /** @var propay_api */
    public $propay_api;

    /** @var protectpayapi */
    public $protectpayapi;

    /** @var fullpayment */
    public $fullpayment;

    /** @var  MY_Email */
	public $email;

	/** @var CI_Lang */
	public $lang;

	/** @var  CI_Parser */
	public $parser;

	/** @var  CI_Config */
	public $config;

	/** @var platformaws */
	public $platformaws;


	/**
     * requires param email
     */
    public function accountlogin()
    {

        //if ($_SESSION['accountUrlPrefix'] != 'spera') {
        //    echo json_encode([
        //        'success' => false,
        //        'message' => "You are not authorized to use this.",
        //        'data' => []
        //    ]);
        //} else {
        /** @var CI_DB_mysql_driver $primaryDatabase */
        $primaryDatabase = $this->load->database('primary', TRUE);

        $params = [
            'primaryDatabase' => $primaryDatabase,
        ];

        $this->load->library('account', $params);

        $accountList = $this->account->getAccountListByEmail($_REQUEST['email']);
        $accountList = json_decode(json_encode($accountList), true);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => (count($accountList) > 0) ? true : false,
            'message' => (count($accountList) > 0) ? count($accountList) . ' Accounts Found for this email.' : 'no accounts found',
            'data' => $accountList
        ]);
        //}
    }

    public function rest() {

        $request = $_REQUEST;
        $accountUrlPrefix = $_SESSION['accountUrlPrefix'];

        $databasePrefix = $accountUrlPrefix;

        if(in_array(substr($databasePrefix,0,1),explode(',','0,1,2,3,4,5,6,7,8,9')))
            $databasePrefix = 'z' . $databasePrefix;

        /** @var CI_DB_mysql_driver $primaryDatabase */
        $primaryDatabase = $this->load->database('primary', TRUE);

        $databaseName = $databasePrefix . '_' . ENVIRONMENT;

        $_SESSION['accountDatabasePrefix'] = $databasePrefix;

        /** @var CI_DB_mysql_driver $accountDatabase */
        $accountDatabase = $this->load->database($databaseName, TRUE);

        $subObjectList = [];
        $method = (!isset($request['method'])) ? 'active' : $_REQUEST['method'];
	    switch ($method) {
		    case 'edit_primary_bank_account':
		    case 'edit_secondary_bank_account':
			    $params = [
				    'databaseName' => $databaseName,
				    'primaryDatabase' => $primaryDatabase,
				    'accountDatabase' => $accountDatabase,
				    'accountUrlPrefix' => $_SESSION['accountUrlPrefix'],
				    'username' => $this->user->username,
			    ];

			    $this->load->library( 'account', $params );
			    $this->load->library( 'crypto', $params );
			    $this->load->library( 'propay_api', $params );

			    $params['account'] = $this->account;
			    $params['crypto'] = $this->crypto;
			    $params['propay_api'] = $this->propay_api;

			    $this->load->library('fullpayment', $params);

			    $merchantData = $this->fullpayment->isMerchantData()->getMerchantData();

			    $this->fullpayment->getMerchantInfo();
			    $subObjectList['fullpayment'] = clone $this->fullpayment;

			    break;
		    case 'transfer_to_crypto':
            case 'user_balances':
	        case 'user_transactions':
	        $params = [
                    'databaseName' => $databaseName,
                    'primaryDatabase' => $primaryDatabase,
                    'accountDatabase' => $accountDatabase,
                    'accountUrlPrefix' => $_SESSION['accountUrlPrefix'],
                    'username' => (isset($this->user->username)) ? $this->user->username : '{not logged in}'
                ];
                $this->load->library( 'crypto', $params );
                $subObjectList['crypto'] = clone $this->crypto;
                break;
	        case 'account_balance':
	        case 'details':
		        $params = [
			        'databaseName' => $databaseName,
			        'primaryDatabase' => $primaryDatabase,
			        'accountDatabase' => $accountDatabase,
			        'accountUrlPrefix' => $_SESSION['accountUrlPrefix'],
			        'username' => (isset($this->user->username)) ? $this->user->username : '{not logged in}'
		        ];
		        $this->load->library('account', $params);
		        $this->load->library('propay_api', $params);
		        $subObjectList['account'] = clone $this->account;
		        $subObjectList['propay_api'] = clone $this->propay_api;
	        	break;
	        case 'hid':
		    case 'gethid':
		        $this->load->library('protectpayapi');
		        $params = [
			        'databaseName' => $databaseName,
			        'primaryDatabase' => $primaryDatabase,
			        'accountDatabase' => $accountDatabase,
			        'accountUrlPrefix' => $_SESSION['accountUrlPrefix'],
			        'username' => (isset($this->user->username)) ? $this->user->username : '{not logged in}'
		        ];
		        $this->load->library('account', $params);
		        $this->load->library('propay_api', $params);
		        $this->load->library('protectpayapi', $params);
		        $subObjectList['protectpayapi'] = clone $this->protectpayapi;
		        $subObjectList['account'] = clone $this->account;
		        $subObjectList['propay_api'] = clone $this->propay_api;
	        	break;
		    case 'board':
		        $params = [
			        'databaseName' => $databaseName,
			        'primaryDatabase' => $primaryDatabase,
			        'accountDatabase' => $accountDatabase,
			        'accountUrlPrefix' => $_SESSION['accountUrlPrefix'],
			        'username' => (isset($this->user->username)) ? $this->user->username : '{not logged in}',
			        'load' => $this->load
		        ];
		        $this->load->library('account', $params);
		        $this->load->library('propay_api', $params);
			    $this->load->library('protectpayapi', $params);
		        $this->load->library('parser');
		        $this->load->library( 'platformaws', [
			        'aws_access_key' => $this->config->item( 'aws_access_key' ),
			        'aws_secret_key' => $this->config->item( 'aws_secret_key' )
		        ] );
		        $subObjectList['account'] = clone $this->account;
		        $subObjectList['propay_api'] = clone $this->propay_api;
			    $subObjectList['protectpayapi'] = clone $this->protectpayapi;
		        $subObjectList['parser'] = clone $this->parser;
		        $subObjectList['lang'] = clone $this->lang;
		        $subObjectList['config'] = clone $this->config;
		        $subObjectList['platformaws'] = clone $this->platformaws;
			    $subObjectList['email'] = clone $this->email;
		        break;
		    case 'board_propay':
			    $params = [
				    'databaseName' => $databaseName,
				    'primaryDatabase' => $primaryDatabase,
				    'accountDatabase' => $accountDatabase,
				    'accountUrlPrefix' => $_SESSION['accountUrlPrefix'],
				    'username' => (isset($this->user->username)) ? $this->user->username : '{not logged in}',
				    'load' => $this->load
			    ];
			    $this->load->library('account', $params);
			    $this->load->library('propay_api', $params);
			    $this->load->library('protectpayapi', $params);
			    $this->load->library('parser');
			    $subObjectList['account'] = clone $this->account;
			    $subObjectList['propay_api'] = clone $this->propay_api;
			    $subObjectList['protectpayapi'] = clone $this->protectpayapi;
			    $subObjectList['parser'] = clone $this->parser;
			    $subObjectList['lang'] = clone $this->lang;
			    $subObjectList['email'] = clone $this->email;
            default:
                $subObjectList[] = (object) [];
        }


        $params = [
            'primaryDatabase' => $primaryDatabase,
            'accountDatabase' => $accountDatabase,
            'requestMethod' => $_SERVER['REQUEST_METHOD'],
            'pathInfo' => $_SERVER['REDIRECT_URL'],
            'inputData' => file_get_contents('php://input'),
            'user' => $this->user,
            'sub_object_list' => $subObjectList,
            'accountUrlPrefix' => $_SESSION['accountUrlPrefix']
        ];

        $this->load->library('rest', $params);
        $this->rest->process($_REQUEST);
    }

    public function accountlist()
    {

        if ($_SESSION['accountUrlPrefix'] != 'spera' && $_SESSION['accountUrlPrefix'] != 'damon' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1' && $_SESSION['accountUrlPrefix'] != 'test') { //?????????????
            echo "Sorry you are not autorized to use this api";
        } else {

            /** @var CI_DB_mysql_driver $primaryDatabase */
            $primaryDatabase = $this->load->database('primary', TRUE);

            $params = [
                'primaryDatabase' => $primaryDatabase,
            ];

            $this->load->library('account', $params);
            $accountList = $this->account->getAccountListWithCounts();
            $exclusionList = $this->account->getReportingExclusionList();

            $newAccountList = [];
            $counter = 0;
            foreach($accountList as $account) {
            	if($counter==0) {
		            $newAccountList[] = $account;
	            } else {
		            if(!in_array($account->accountUrlPrefix, $exclusionList)) {
			            $newAccountList[] = $account;
		            }
	            }
	            $counter++;
            }
            foreach($newAccountList as $index => $account) {
	            if ( isset( $account->last_login ) ) {
		            $dt = new DateTime();
		            $dt->setTimestamp( $account->last_login );
		            $account->last_login   = $dt->format( "Y-m-d H:i:s" );
		            $newAccountList[ $index ] = $account;
	            }
            }
            $this->exportCSVToSheet($newAccountList);

//            $csv = $this->account->formatTsv($accountList);
//
//            header("Content-type: text/tab-separated-values");
//            header("Content-Disposition: attachment; filename=accountList.tsv");
//            header("Pragma: no-cache");
//            header("Expires: 0");
//
//            echo $csv;
            die();
        }
    }

    public function projecttasklist()
    {
        if ($_SESSION['accountUrlPrefix'] != 'spera' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
            echo "Sorry you are not autorized to use this api";
        } else {
            $parsedAccountUrlPrefix = $_SESSION['accountUrlPrefix'];

            $databaseName = $parsedAccountUrlPrefix . '_' . ENVIRONMENT;

            // @var CI_DB_mysql_driver $primaryDatabase /
            $primaryDatabase = $this->load->database($databaseName, TRUE);

            $params = [
                'databaseName' => $databaseName,
                'primaryDatabase' => $primaryDatabase        ];

            $this->load->library('projecttasks', $params);

            $projectTasks = $this->projecttasks->getProjectTasks();

            $tsv = $this->projecttasks->formatTsv($projectTasks);

            header("Content-type: text/tab-separated-values");
            header("Content-Disposition: attachment; filename=projectTaskList.tsv");
            header("Pragma: no-cache");
            header("Expires: 0");

            echo $tsv;
        }
    }

    private function exportCSVToSheet($accountList){

        $client = new \Google_Client();
        $client->setApplicationName('SperaSheet');
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');

        //$jsonAuth = getenv('JSON_AUTH');
        $jsonAuth = $this->get_JSON_Auth();
        $client->setAuthConfig(json_decode($jsonAuth, true));

        /* With the Google_Client we can get a Google_Service_Sheets service object to interact with sheets */
        $sheets = new \Google_Service_Sheets($client);

        $spreadsheetId = $this->get_spread_id();

        $values = [];
        $num = count($accountList);
        if($num > 0) {
            $values[] = (array) $accountList[0];

            for($i=1; $i<$num; $i++) {
                $rowValue = [];
                foreach ($accountList[0] as $name) {
                    $item = (array)$accountList[$i];

                    if( $item[$name] == null)
                        $rowValue[] = 'NULL';
                    else
                        $rowValue[] = $item[$name];
                }
                $values[] = $rowValue;
            }
        }

        $clearRange = 'A1:Z';
        $requestBody = new Google_Service_Sheets_ClearValuesRequest();
        $sheets->spreadsheets_values->clear($spreadsheetId, $clearRange, $requestBody);

        $valueRange = new \Google_Service_Sheets_ValueRange([
            'majorDimension' => 'ROWS',
            'values' => $values
        ]);
        $updateRange = 'A1';
        $param = ['valueInputOption' => 'USER_ENTERED'];
        $result = $sheets->spreadsheets_values->update($spreadsheetId, $updateRange, $valueRange, $param);
        // echo '<pre>', $result->getUpdatedRange(), '</pre>';
        echo '<pre>Googe Sheet is successfully updated.</pre>';
    }

    /**
     * Returns the JSON Auth string for Google sheet.
     *
     * @return string   The string
     */
    private function get_JSON_Auth() {
        if (defined('ENVIRONMENT'))
        {
            switch (ENVIRONMENT)
            {
                case 'release':
                case 'production':
                    // First, check in a constant, next check environment variable
                    if ( defined( 'SHEET_JSON_AUTH' ) ) return SHEET_JSON_AUTH;
                    if ( getenv( 'SHEET_JSON_AUTH' ) ) return getenv( 'SHEET_JSON_AUTH' );

                    break;
                case 'testing':
                case 'development':
                    // First, check in a constant, next check environment variable
                    if ( defined( 'SHEET_JSON_AUTH' ) ) return SHEET_JSON_AUTH;
                    if ( getenv( 'SHEET_JSON_AUTH' ) ) return getenv( 'SHEET_JSON_AUTH' );

                    break;

                default:
                    exit('The application environment is not set correctly.');
            }
        }

        // Not configured, return empty string
        return '';
    }

    /**
     * Returns the Spread Sheet id for Google sheet.
     *
     * @return string   The string
     */
    private function get_spread_id() {
        if (defined('ENVIRONMENT'))
        {
            switch (ENVIRONMENT)
            {
                case 'release':
                case 'production':
                    // First, check in a constant, next check environment variable
                    if ( defined( 'SPREADSHEET_ID' ) ) return SPREADSHEET_ID;
                    if ( getenv( 'SPREADSHEET_ID' ) ) return getenv( 'SPREADSHEET_ID' );

                    break;
                case 'testing':
                case 'development':
                    // First, check in a constant, next check environment variable
                    if ( defined( 'SPREADSHEET_ID' ) ) return SPREADSHEET_ID;
                    if ( getenv( 'SPREADSHEET_ID' ) ) return getenv( 'SPREADSHEET_ID' );

                    break;

                default:
                    exit('The application environment is not set correctly.');
            }
        }

        // Not configured, return empty string
        return '';
    }
}