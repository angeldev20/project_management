<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 2/28/18
 * Time: 4:22 PM
 */
include_once('crypto_constants.php');

//use Traversable;

class BigDecimal {
    /** used for new instances, when scale isn't provided */
    const DEFAULT_SCALE = 20;
    /** @var string String representation of decimal value */
    protected $value;
    /** @var int scale */
    protected $scale;
    /**
     * Create a new instance with decimal value and scale
     *
     * @param int|string|float|BigDecimal Decimal value or BigDecimal instance
     * @param int $scale Decimal scale (number of decimal places), uses scale from given BigDecimal instance, or uses default scale if null
     */
    public function __construct($value=0, $scale=null)
    {
        if ($value instanceof BigDecimal) {
            $this->setScale($scale!==null ? $scale : $value->scale);
            $this->setValue($value->value);
        }
        else {
            $this->setScale($scale!==null ? $scale : self::DEFAULT_SCALE);
            $this->setValue($value);
        }
    }
    /**
     * Converts all elements in array to BigDecimal instance
     *
     * @param array $items Input array, each element must be a valid argument for BigDecimal constructor
     * @param bool  $allowNullValues Allow null values in input array
     * @return array Resulting BigDecimal array
     * @throws \InvalidArgumentException If any element is null or invalid type for BigInstance constructor
     */
    public static function toArray(array $items, $allowNullValues=false)
    {
        $results = array();
        try {
            foreach ($items as $key => $val) {
                if ($allowNullValues && $val===null) {
                    $results[] = null;
                } elseif ($val === null) {
                    throw new \InvalidArgumentException('Null value found in provided array');
                } elseif (!$val instanceof BigDecimal) {
                    $results[] = new BigDecimal($val);
                } else {
                    $results[] = $val;
                }
            }
        }
        catch(\Exception $e) {
            throw new \InvalidArgumentException('Element at index '.$key.' could not be converted to BigDecimal!', 42, $e);
        }
        return $results;
    }
    /**
     * Converts all elements in array to \Application\Object\BigDecimal instance
     *
     * @param array $items Source array, each element must be a valid argument for BigDecimal constructor
     * @param int $scale Set non-BigDecimal values to this scale or use default if null
     * @param bool $allowNullValues Allow elements with null value in array
     * @return array BigDecimal Resulting BigDecimal array
     * @throws \InvalidArgumentException If any element is null or invalid type for BigInstance constructor
     */
    public static function toArrayWithScale(array $items, $scale, $allowNullValues=false)
    {
        $results = array();
        try {
            foreach ($items as $key => $val) {
                if ($allowNullValues && $val===null) {
                    $results[] = null;
                } elseif ($val === null) {
                    throw new \InvalidArgumentException('Null value found in provided array');
                } else {
                    $results[] = new BigDecimal($val, $scale);
                }
            }
        }
        catch(\Exception $e) {
            throw new \InvalidArgumentException('Element at index '.$key.' could not be converted to BigDecimal!', 42, $e);
        }
        return $results;
    }
    /**
     * Sets scale (number of decimal places)
     *
     * @param int $scale
     */
    protected function setScale($scale)
    {
        if (!is_int($scale)) {
            throw new \InvalidArgumentException('BigDecimal scale was not int, type was: "' . Utils::getType($scale).'"');
        }
        elseif ($scale<0) {
            throw new \InvalidArgumentException('BigDecimal scale cannot be negative value: "'.Utils::getType($scale).'"');
        }
        $this->scale = $scale;
    }
    /**
     * @param string|int|float $value
     */
    protected function setValue($value)
    {
        if ($value === null) {
            throw new \InvalidArgumentException('BigDecimal value cannot be null');
        }
        elseif (is_string($value)) {
            if (!filter_var($value, FILTER_VALIDATE_INT)
                && !preg_match('/^[-+]?\\d+([.]\\d+)?$/u', $value)) {
                throw new \InvalidArgumentException('Invalid characters in BigDecimal constructor string: '.$value);
            }
            $this->value = bcadd($value, 0, $this->scale);
        }
        elseif (is_int($value)) {
            $this->value = bcadd($value, 0, $this->scale);
        }
        elseif (is_float($value)) {
            $this->value = bcadd($value, 0, $this->scale);
        }
        else {
            throw new \InvalidArgumentException('Invalid type for BigDecimal value, type was: "'.Utils::getType($value).'"');
        }
    }
    /**
     * Returns string representation of decimal value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * Returns decimal scale
     *
     * @return int
     */
    public function getScale()
    {
        return $this->scale;
    }
    /**
     * Magic getter for accessing private value and scale properties
     */
    public function __get($name)
    {
        if($name==='value') {
            return $this->value;
        }
        else if($name==='scale') {
            return $this->scale;
        }
        else {
            throw new \InvalidArgumentException('Cannot get undefined property "'.$name.'"');
        }
    }
    /**
     * Returns string representation of decimal value
     *
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }
    /**
     * Returns string representation of decimal value with given scale
     *
     * @param int $scale Desired scale (number of decimal places)
     * @return string
     */
    public function toStringWith($scale)
    {
        return $this->format($scale);
    }
    /**
     * Returns string representation of decimal value with given scale
     *
     * @param int $scale Desired scale (number of decimal places)
     * @return string
     */
    public function format($scale)
    {
        return number_format($this->value, $scale);
    }
// ----------------------------------------------------------------------------
    protected function _add(BigDecimal $other)
    {
        return new BigDecimal(bcadd($this->value, $other->value, $this->scale), $this->scale);
    }
    /**
     * Adds another BigDecimal value to this instance
     *
     * @param $other BigDecimal Value to be added
     * @return BigDecimal Resulting BigDecimal instance
     */
    public function add($other)
    {
        return $this->_add(new BigDecimal($other, $this->scale));
    }
    protected function _sub(BigDecimal $other)
    {
        return new BigDecimal(bcsub($this->value, $other->value, $this->scale), $this->scale);
    }
    /**
     * Subtract another BigDecimal value from this instance
     *
     * @param $other BigDecimal Value to be subtracted
     * @return BigDecimal Resulting BigDecimal instance
     */
    public function sub($other)
    {
        return $this->_sub(new BigDecimal($other, $this->scale));
    }
// ----------------------------------------------------------------------------
    protected function _comp(BigDecimal $other)
    {
        return bccomp($this->value, $other->value, $this->scale);
    }
    /**
     * Compares with another BigDecimal
     *
     * @param $other BigDecimal Value to compare with
     * @return int Returns 0 if the two values are equal, 1 if other value is
     * larger, -1 otherwise.
     */
    public function comp($other)
    {
        return $this->_comp(new BigDecimal($other, $this->scale));
    }
    protected function _gt(BigDecimal $other)
    {
        return $this->comp($other) > 0;
    }
    /**
     * Check if this value is greater than another BigDecimal
     *
     * @param $other BigDecimal Value to compare with
     * @return bool Returns true if greater than other, false otherwise
     */
    public function gt($other)
    {
        return $this->_gt(new BigDecimal($other, $this->scale));
    }
    protected function _gte(BigDecimal $other) {
        return $this->comp($other) >= 0;
    }
    /**
     * Check if this value is greater or equal than another BigDecimal
     *
     * @param $other BigDecimal Value to compare with
     * @return bool Returns true if greater or equal than other, false otherwise
     */
    public function gte($other)
    {
        return $this->_gte(new BigDecimal($other, $this->scale));
    }
    protected function _lt(BigDecimal $other)
    {
        return $this->comp($other) < 0;
    }
    /**
     * Check if this instance is less than another BigDecimal
     *
     * @param $other BigDecimal Value to compare with
     * @return bool Returns true if less than other, false otherwise
     */
    public function lt($other)
    {
        return $this->_lt(new BigDecimal($other, $this->scale));
    }
    protected function _lte(BigDecimal $other)
    {
        return $this->comp($other) <= 0;
    }
    /**
     * Check if this instance is less or equal than another BigDecimal
     *
     * @param $other BigDecimal Value to compare with
     * @return bool Returns true if less or equal than other, false otherwise
     */
    public function lte($other)
    {
        return $this->_lte(new BigDecimal($other, $this->scale));
    }
// ----------------------------------------------------------------------------
    protected function _mul(BigDecimal $other)
    {
        return new BigDecimal(bcmul($this->value, $other->value, $this->scale), $this->scale);
    }
    /**
     * Multiply with another BigDecimal
     *
     * @param $other BigDecimal Value to multiply with
     * @return BigDecimal Resulting BigDecimal instance
     */
    public function mul($other)
    {
        return $this->_mul(new BigDecimal($other, $this->scale));
    }
    protected function _div(BigDecimal $other)
    {
        return new BigDecimal(bcdiv($this->value, $other->value, $this->scale), $this->scale);
    }
    /**
     * Divide with another BigDecimal
     *
     * @param $other BigDecimal Value to divide with
     * @return BigDecimal Resulting BigDecimal instance
     */
    public function div($other)
    {
        return $this->_div(new BigDecimal($other, $this->scale));
    }
    protected function _mod(BigDecimal $other)
    {
        return new BigDecimal(bcmod($this->value, $other->value, $this->scale), $this->scale);
    }
    /**
     * Get modulus with another BigDecimal
     *
     * @param $other BigDecimal Value to multiply with
     * @return BigDecimal Resulting BigDecimal instance
     */
    public function mod($other)
    {
        return $this->_mod(new BigDecimal($other, $this->scale));
    }
// ----------------------------------------------------------------------------
    protected function _pow(BigDecimal $other)
    {
        return new BigDecimal(bcpow($this->value, $other->value, $this->scale));
    }
    /**
     * Raise this value to another BigDecimal value
     *
     * @param $other BigDecimal Value to multiply with
     * @return BigDecimal Resulting BigDecimal instance
     */
    public function pow($other)
    {
        return $this->_pow(self::from($other));
    }
    /**
     * Square root
     *
     * @return BigDecimal Resulting BigDecimal instance
     */
    public function sqrt()
    {
        return new BigDecimal(bcsqrt($this->value, $this->scale), $this->scale);
    }
}

class crypto {
    /** @var  string */
    private $_databaseName;

    /** @var  CI_DB_mysql_driver */
    private $_primaryDatabase;

    /** @var  CI_DB_mysql_driver */
    private $_accountDatabase;

    /** @var  string */
    private $_username;

    /** @var string */
    private $_encryptedString;

    /** @var string */
    private $_decryptedString;

    /** @var string */
    private $_accountUrlPrefix;

    public function __construct($init_array = NULL)
    {
        if (!is_null($init_array)) {
            if (isset($init_array['databaseName'])) $this->_databaseName = $init_array['databaseName'];
            if (isset($init_array['primaryDatabase'])) $this->_primaryDatabase = $init_array['primaryDatabase'];
            if (isset($init_array['accountDatabase'])) $this->_accountDatabase = $init_array['accountDatabase'];
            if (isset($init_array['accountUrlPrefix'])) $this->_accountUrlPrefix = $init_array['accountUrlPrefix'];
            if (isset($init_array['username'])) $this->_username = $init_array['username'];
        }
    }

    /**
     * Encrypts the string using the server defined SALT string
     * @param string $stringToEncrypt
     * @return $this
     */
    public function encryptString($stringToEncrypt) {
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($stringToEncrypt, $cipher, SALT, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, SALT, $as_binary=true);
        $this->_encryptedString = base64_encode( $iv.$hmac.$ciphertext_raw );
        return $this;
    }

    /**
     * Decrypts the string using the server defined SALT string
     * @param string $stringToDecrypt
     * @return $this
     */
    public function decryptString($stringToDecrypt) {
        $c = base64_decode($stringToDecrypt);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, SALT, $options=OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, SALT, $as_binary=true);
        if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
        {
            $this->_decryptedString = $original_plaintext;
        }
        return $this;
    }

    /** this was procedural code copied over into the class, needs work to be more object oriented */
    public function CallAPI($method, $url, $data = false)
    {
        $curl = curl_init();

        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        //curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        $milliseconds = round(microtime(true) * 1000);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'SC-ACCESS-KEY: ' . SC_ACCESS_KEY,
            'SC-NONCE: ' . $milliseconds,
            'Content-Type: application/json'
        ));

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }

    //Returns UserId from sessionId or -1 for failure
    public function LookupUserId($sessionId)
    {
        global $user_map;
        if (isset($user_map[$sessionId])) {
            return ($user_map[$sessionId]);
        }
        return (-1);
    }

    public function LookupUserName($sessionId)
    {
        global $name_map;
        //echo $sessionId;
        //echo LookupUserId($sessionId);
        //echo $name_map[101];
        return ($name_map[$this->LookupUserId($sessionId)]);
    }

    public function LookupUserNameByUserId($userId)
    {
        global $name_map;
        //echo $sessionId;
        //echo LookupUserId($sessionId);
        //echo $name_map[101];
        return ($name_map[$userId]);
    }

    public function LookupInvoice($invoiceId)
    {
        global $invoice_map;
        //  echo "Dumping invoice map";
        //  var_dump($invoice_map);
        return ($invoice_map[$invoiceId]);
    }

    public function DisplayCrypto($qty)
    {
        $bd = new BigDecimal($qty, 8);
        return ($bd->getValue());
    }

    public function DisplayFiat($qty, $currency_type = "USD")
    {
        $returnValue = '$0.00';
        if ($currency_type == "USD") {
            setlocale(LC_MONETARY, 'en_US');
            if(function_exists('money_format')) { // TODO: Windows doesn't supports money_format function
	            $returnValue = money_format( "%n", floatval( $qty ) );
            }
        }
        return $returnValue;
    }

    public function DisplayDashBoard($userId)
    {
        $balances = $this->balances(intval($userId));
        return [
	        'USD' => $this->DisplayCryptoBalance($balances, "USD"),
            'BTC' => $this->DisplayCryptoBalance($balances, "BTC"),
            'ETH' => $this->DisplayCryptoBalance($balances, "ETH"),
            'LTC' => $this->DisplayCryptoBalance($balances, "LTC")
        ];
    }


    public function DisplayCryptoBalance($balances, $asset)
    {
            $returnValue = [
                'asset' => $asset,
                'qty' => $this->DisplayCrypto($balances[$asset]['qty']),
                'value' => $this->DisplayFiat($balances[$asset]['value']),
                //TODO: had to exclude this for now, coversion rate did not exist in balances return, at least on test
                //'fiatValue' => $this->DisplayFiat($this->CalculateFiatFromCrypto($balances[$asset]['qty'], $balances[$asset]['conversionRate']))
            ];
            return $returnValue;
    }

    public function CalculateFiatFromCrypto($crypto_qty, $conversion)
    {
        $bd = new BigDecimal($crypto_qty, 8);
        $fiat = $bd->mul($conversion, 2);
        return ($fiat->getValue());
    }

    public function GetTransactions($userId, $asset)
    {
        $data = array("asset" => $asset);
        $result = $this->CallAPI("GET", CRYPTO_MODULE_BASE_URL . "/users/" . intval($userId) . "/transactions", $data);
        return ($result);
    }

    public function DisplayTransactions($userId, $asset)
    {
        $txs_json = $this->getTransactions($userId, $asset);

        $txs = json_decode($txs_json, true);


        //Safety check to make sure the txs are for the user we requested.
        if ($txs['userId'] == $userId) {
            for ($i = 0; $i < count($txs['transactions']); $i++) {
                $tx = $txs['transactions'][$i];
                $this->DisplayTransaction($tx);
            }

        }
        //var_dump($txs);
    }

    /** TODO: this needs to me moved to controller and view */
    public function DisplayTransaction($tx)
    {
        echo date('m/d/Y H:m:s', $tx['transactionDate'] / 1000) . " "; // . $tx['type'] . " ";

        if ($tx['type'] === 'FiatClientPayment') {
            echo "Payment of ";
            echo $this->DisplayFiat($tx['fiatCurrencyAmount'], $tx['fiatCurrency']);
        } elseif ($tx['type'] === 'FiatClientPaymentAndCryptoTransfer') {
            echo "Payment of ";
            echo $this->DisplayFiat($tx['fiatCurrencyAmount'], $tx['fiatCurrency']);
            echo " converted to ";
            echo $this->DisplayCrypto($tx['assetAmount']) . " " . $tx['asset'] . ' at ' . $tx['conversionRate'] . " " . $tx['asset'] . ' per ' . $tx['fiatCurrency'];

        } elseif ($tx['type'] === 'AssetTransfer') {
            echo "Conversion of ";
            echo $this->DisplayCrypto($tx['currencyAmount']) . ' ' . $tx['currency'];
            echo " converted to ";
            echo $this->DisplayCrypto($tx['assetAmount']) . " " . $tx['asset'] . ' at ' . $tx['conversionRate'] . " " . $tx['asset'] . ' per ' . $tx['currency'];

        } elseif ($tx['type'] === 'CryptoAssetWithdrawal') {
            echo "Withdrawal of ";
            echo $this->DisplayCrypto($tx['assetAmount']) . ' ' . $tx['asset'];
            echo " to ";
            echo $tx['depositAddress'];
            echo " in txid ";
            echo "<a href='" . $tx['transactionUrl'] . "'>";
            echo $tx['transactionId'];
            echo "</a>";
        }

        echo '<br />';
    }

    public function assetTransfers($userId, $currency, $currencyAmount, $asset)
    {
        $data = json_encode(array('currency' => $currency, 'currencyAmount' => $currencyAmount, 'asset' => $asset));
        $result = $this->CallAPI("POST", CRYPTO_MODULE_BASE_URL . "/users/" . intval($userId) . "/assetTransfers", $data);
        return ($result);
    }

	/**
	 * Evaluate crypt purchase response and return either purchase data or error codes
	 * false = spera crypto service is down
	 * net is down   'Error trying to buy crypto from fiat for cryptoExchangeAccountIdf5cd23a9-a383-558d-8ed2-ed99ab82719b with asset BTC'
	 * normal success '{"type":"FiatClientPaymentAndCryptoTransfer","transactionDate":1521045251733,"fiatCurrency":"USD","fiatCurrencyAmount":2.99,"clientId":"501","asset":"BTC","assetAmount":0.00023108,"conversionRate":0.00011554,"clientTransactionId":null,"currencyExchangeTransferFeeAmount":0.99,"currencySperaTransferFeeAmount":0}'
	 * too little money or bad other data 'Error trying to buy crypto from fiat for cryptoExchangeAccountIdf5cd23a9-a383-558d-8ed2-ed99ab82719b with asset BTC: Bad Request'
	 * @param mixed $data
	 *
	 * @return array
	 */
	public function getTransferResponseStatus($data) {
		$response = ['code' => '01', 'message' => 'Service is down'];
        switch ($data) {
	        case false:
	        	break;
	        default:
	        	if (strpos($data,'Bad Request') !== false) {
			        $response = ['code' => '02', 'message' => 'Transaction Fee Too Low, or other bad request data.'];
		        } else if(strpos($data,'with asset') !== false) {
			        $response = ['code' => '03', 'message' => 'Can\'t communicate with coinbase. Net may be down'];
		        } else {
			        $response = json_decode($data, true);
		        }
        }
        return $response;
	}

    public function cryptoAssetTransfers($userId, $clientId, $fiatCurrency, $fiatCurrencyAmount, $asset)
    {
        $data = json_encode(array('fiatCurrency' => $fiatCurrency, 'fiatCurrencyAmount' => $fiatCurrencyAmount, 'asset' => $asset));

        $result = $this->CallAPI("POST", CRYPTO_MODULE_BASE_URL . "/users/" . intval($userId) . "/fiatPayments/" . intval($clientId) . "/cryptoAssetTransfers", $data);
        return ($result);
    }

    //This function should connect to the database entry that gets updated
    //by the POST back from HPP and return the results.
    public function getCreditCardResponse($invoice)
    {
        $cc['result'] = 'success';
        $inv = $this->LookupInvoice($invoice);
        $cc['amount'] = $inv['amount'];
        $cc['auth'] = "ABCDEF";
        return ($cc);
    }

    ///users/{userId}/fiatPayments/{clientId}/cryptoAssetTransfers

    //TODO: need to be returning subfunction values
    public function balances($userId)
    {
        $new_result = [];
    	$result_json = $this->CallAPI("GET", CRYPTO_MODULE_BASE_URL . "/users/" . intval($userId) . "/balances");

        //Remap balances to an easier to use format
	    //TODO: take out this test code after all is mapped and working
	    $sampleErrorResponse =
		    [
			    'timestamp' => 1521654930881,
			    'status' => 500,
			    'error' => 'Internal Server Error',
			    'exception' => 'org.springframework.dao.InvalidDataAccessResourceUsageException',
			    'message' => 'could not extract ResultSet; SQL [n/a]; nested exception is org.hibernate.exception.SQLGrammarException: could not extract ResultSet',
			    'path' => '/users/0/balances',
		    ];

	    $sampleErrorResponse =
		    [
		        'timestamp' => 1521656302190,
		        'status' => 401,
		        'error' => 'Unauthorized',
		        'message' => 'Invalid authentication key',
		        'path' => '/users/0/balances',
		    ];

        $result = json_decode($result_json, true);

	    //if ($_SESSION['accountUrlPrefix'] == 'damon') {
		//    if (!isset($result['userBalances'])) {
		//	    echo "<pre>";
		//	    var_export( $result );
		//	    die();
		//    }
	    //}

        if (isset($result['userBalances'])) {
	        for ( $i = 0; $i < count( $result['userBalances'] ); $i ++ ) {
		        $new_result[ $result['userBalances'][ $i ]['asset'] ]['qty']   = $result['userBalances'][ $i ]['assetAmount'];
		        $new_result[ $result['userBalances'][ $i ]['asset'] ]['value'] = $result['userBalances'][ $i ]['currencyAmount'];
	        }
        }

        $this->ZeroAssetIfMissing($new_result, "USD");
        $this->ZeroAssetIfMissing($new_result, "BTC");
        $this->ZeroAssetIfMissing($new_result, "ETH");
        $this->ZeroAssetIfMissing($new_result, "LTC");


        return ($new_result);
    }

    public function ZeroAssetIfMissing(&$arr, $asset)
    {
        if (!isset($arr[$asset])) {
            $arr[$asset]['qty'] = 0;
            $arr[$asset]['value'] = 0;
        }
    }

    //Makes sure it is one of the assets
    public function GetAsset($asset)
    {
        $asset_list = array("USD", "BTC", "ETH", "LTC");

        if (in_array($asset, $asset_list))
            return ($asset);

        return ("");
    }

}