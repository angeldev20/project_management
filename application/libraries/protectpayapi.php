<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 10/6/17
 * Time: 2:15 PM
 */
class protectpayapi
{

    /* change this to the production url for going live after testing https://api.propay.com */
    //test 'https://xmltestapi.propay.com';
    private $_apiBaseUrl;

    /* credentials that would normally be set from database values or a config value */
    private $_billerId;
    private $_authToken;

    /* for creating hosted transactions */
    private $_createHostedTransactionData;
    private $_createdHostedTransactionInfo;

    /* for getting a hosted transaction */
    private $_getHostedTransactionData;
    private $_getHostedTransactionInfo;

    /* for creating payer ID */
    private $_createPayerIdData;
    private $_createPayerIdInfo;

    /**
     * @param array $payerIdData
     * @return $this\
     */
    public function setCreatePayerIdData($payerIdData) {
        $this->_createPayerIdData = $payerIdData;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatePayerIdInfo() {
        return $this->_createPayerIdInfo;
    }

    /**
     * Creates a payer id
     * @return $this
     */
    public function createPayerId()
    {
        $data_string = json_encode($this->_createPayerIdData);

        $ch = curl_init($this->_apiBaseUrl . '/Payers');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->_getAuth());
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        ));

        $this->_createPayerIdInfo = curl_exec($ch);
        return $this;
    }

    /**
     * Used to set the base url for either testing or production
     * @param string $apiBaseUrl
     * @return $this
     */
    public function setApiBaseUrl($apiBaseUrl) {
        $this->_apiBaseUrl = $apiBaseUrl;
        return $this;
    }

    /**
     * @param string $billerId
     * @return $this
     */
    public function setBillerId($billerId) {
        $this->_billerId = $billerId;
        return $this;
    }

    /**
     * @param string $authToken
     * @return $this
     */
    public function setAuthToken($authToken) {
        $this->_authToken = $authToken;
        return $this;
    }

    /**
     * @return string
     */
    private function _getAuth() {
        return $this->_billerId . ':' . $this->_authToken;
    }

    /**
     * @return mixed
     */
    public function getCreatedHostedTransactionInfo() {
        return $this->_createdHostedTransactionInfo;
    }

    /**
     * @param string $getHostedTransactionData
     * This is the hosted transaction id such as "3c2d361a-23a7-4ca1-9c4d-4c18e1af7ad1"
     * @return $this
     */
    public function setGetHostedTransactionData($getHostedTransactionData) {
        $this->_getHostedTransactionData = $getHostedTransactionData;
        return $this;
    }

    /**
     * @param array $hostedTransactionData
     * @return $this
     */
    public function setHostedTransactionData($hostedTransactionData) {
        $this->_createHostedTransactionData = $hostedTransactionData;
        return $this;
    }

    /**
     * Created the hosted transaction
     * @return $this
     * _createdHostedTransactionInfo contains a json string like this
     * {
    "HostedTransactionIdentifier":"f1549c53-e499-476d-84cc-93f99586505d",
    "Result":
    {
    "ResultValue":"SUCCESS",
    "ResultCode":"00",
    "ResultMessage":""
    }
    }
     */
    public function createHostedTransaction() {
        $data_string = json_encode($this->_createHostedTransactionData);

        $ch = curl_init($this->_apiBaseUrl . '/hostedtransactions');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->_getAuth());
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        ));

        $this->_createdHostedTransactionInfo = curl_exec($ch);
        return $this;
    }

    /**
     * Creates a merchant profile
     * string $merchantProfileData
     * @return string|bool - json
     */
    public function createMerchantProfile($merchantProfileData) {
        $data_string = json_encode($merchantProfileData);

        $ch = curl_init($this->_apiBaseUrl . '/MerchantProfiles');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->_getAuth());
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        ));

        return curl_exec($ch);
    }


    /**
     * @return $this
     */
    public function getHostedTransaction() {
        $ch = curl_init($this->_apiBaseUrl . '/HostedTransactionResults/' . $this->_getHostedTransactionData);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->_getAuth());
        $this->_getHostedTransactionInfo = curl_exec($ch);
        return $this;
    }

    /**
     * Returns the hosted transaction information in json or false if the id does not exist.
     * @return mixed
     */
    public function getHostedTransactionInfo() {
        return $this->_getHostedTransactionInfo;
    }


}