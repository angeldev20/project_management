<?php
if ( !defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
*/

$active_group = 'default';
$active_record = TRUE;
$db['default']['dbdriver'] = 'mysql';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;

$db['default']['database'] = 'platform_' . ENVIRONMENT;

switch (ENVIRONMENT) {
    case 'release':
    case 'testing':
    case 'development':
        $db['default']['hostname'] = '127.0.0.1';
        $db['default']['username'] = 'platform_' . ENVIRONMENT;
        $db['default']['password'] = DEVELOPMENT_DB_PW;
        break;
    case 'production':
        $db['default']['hostname'] = PRODUCTION_RDS_HOST;
        $db['default']['username'] = 'platform';
        $db['default']['password'] = PRODUCTION_DB_PW;
        break;

}

$db['primary'] = $db['default'];

if (
(isset($_SESSION['accountUrlPrefix']) && $_SESSION['accountUrlPrefix'] != '' && $_SESSION['accountUrlPrefix'] != NULL) ||
(isset($_SESSION['accountDatabasePrefix']) && $_SESSION['accountDatabasePrefix'] != '' && $_SESSION['accountDatabasePrefix'] != NULL)
) {
	if (isset($_SESSION['accountUrlPrefix'])) {
		$preformedAccountUrlPrefix =  $_SESSION['accountUrlPrefix'];
	}
	if (isset($_SESSION['accountDatabasePrefix']) && $_SESSION['accountDatabasePrefix'] != NULL) $_SESSION['accountUrlPrefix'] = $_SESSION['accountDatabasePrefix'];

	//if (!isset($_SESSION['accountUrlPrefix'])) $_SESSION['accountUrlPrefix'] = $_SESSION['accountDatabasePrefix'];
	$parsedAccountUrlPrefix = $_SESSION['accountUrlPrefix'];

	if (isset($preformedAccountUrlPrefix) && is_numeric( substr( $preformedAccountUrlPrefix, 0, 1 ) )) {
		$databaseName = 'z' . $preformedAccountUrlPrefix . '_' . ENVIRONMENT;
		$_SESSION['accountUrlPrefix'] = $preformedAccountUrlPrefix;
	} else {
	    $databaseName = $parsedAccountUrlPrefix . '_' . ENVIRONMENT;
	}

    
	$tempDatabase = mysqli_connect(
        $db['default']['hostname'],
        $db['default']['username'],
        $db['default']['password'],
        $db['default']['database'],
        3306);

	if (isset($preformedAccountUrlPrefix)) $parsedAccountUrlPrefix = $preformedAccountUrlPrefix;
    $sql = "SELECT * FROM accounts WHERE accountUrlPrefix='" . $parsedAccountUrlPrefix . "';";
    $result = $tempDatabase->query($sql);

    $db[$databaseName]['dbdriver'] = 'mysql';
    $db[$databaseName]['dbprefix'] = '';
    $db[$databaseName]['pconnect'] = TRUE;
    $db[$databaseName]['db_debug'] = TRUE;
    $db[$databaseName]['cache_on'] = FALSE;
    $db[$databaseName]['cachedir'] = '';
    $db[$databaseName]['char_set'] = 'utf8';
    $db[$databaseName]['dbcollat'] = 'utf8_general_ci';
    $db[$databaseName]['swap_pre'] = '';
    $db[$databaseName]['autoinit'] = TRUE;
    $db[$databaseName]['stricton'] = FALSE;
    if($result->num_rows > 0) {
        $accountObject = new account();
        $account = (object) $result->fetch_assoc();
        $accountDbPassword = $accountObject->decryptString($account->db_password)->getDecryptedString();
        $db[$databaseName]['hostname'] = $account->db_hostname;
        $db[$databaseName]['username'] = $account->db_username;
        $db[$databaseName]['password'] = $accountDbPassword;
        $db[$databaseName]['database'] = $account->db_database;
        $db['default']['hostname'] = $account->db_hostname;
        $db['default']['username'] = $account->db_username;
        $db['default']['password'] = $accountDbPassword;
        $db['default']['database'] = $account->db_database;

    }  else {
        $db[$databaseName]['database'] = $databaseName;
        switch (ENVIRONMENT) {

            case 'release':
            case 'testing':
            case 'development':
                $db[$databaseName]['username'] = 'platform_' . ENVIRONMENT;
                $db[$databaseName]['hostname'] = '127.0.0.1';
                $db[$databaseName]['password'] = DEVELOPMENT_DB_PW;
                break;
            case 'production':
                $db[$databaseName]['username'] = 'platform';
                $db[$databaseName]['hostname'] = PRODUCTION_RDS_HOST;
                $db[$databaseName]['password'] = PRODUCTION_DB_PW;
                break;
        }

    }
}
/* End of file database.php */
/* Location: ./application/config/database.php */
