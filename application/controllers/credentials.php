<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 10/12/17
 * Time: 10:12 AM
 */
class credentials extends My_Controller
{

    /** @var  propay_api */
    public $propay_api;

    public function store() {

        $parsedAccountUrlPrefix = $_SESSION['accountUrlPrefix'];

        $databaseName = $parsedAccountUrlPrefix . '_' . ENVIRONMENT;

        /** @var CI_DB_mysql_driver $primaryDatabase */
        $primaryDatabase = $this->load->database('primary', TRUE);

        $params = [
            'databaseName' => $databaseName,
            'primaryDatabase' => $primaryDatabase        ];

        $this->load->library('propay_api', $params);

        var_export($this->propay_api);
        die();
    }
}