<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 10/9/17
 * Time: 12:05 PM
 */
class Predispatch {

    public function checkSessionStart() {
        if (session_status() == PHP_SESSION_NONE) {
	        session_start();
	        $currentCookieParams = session_get_cookie_params();
	        $sidvalue = session_id();
	        setcookie(
		        'PHPSESSID',//name
		        $sidvalue,//value
		        0,//expires at end of session
		        $currentCookieParams['path'],//path
		        $currentCookieParams['domain'],//domain
		        true //secure
	        );
        }
    }

    /**
     * get the accountUrlPrefix if there is one
     * @return string
     */
    public function getAccountUrlPrefix() {
        $parsedAccountUrlPrefix = (count(explode('.', $_SERVER['HTTP_HOST'])) > 2) ? explode('.', $_SERVER['HTTP_HOST'])[0] : '';
        switch ($parsedAccountUrlPrefix) {
            case 'dev':
            case 'release':
            case 'testing':
            case 'www':
            case 'app':
            case 'platform':
                $parsedAccountUrlPrefix = "";
                break;
            default:
        }
        return strtolower($parsedAccountUrlPrefix);
    }

}
