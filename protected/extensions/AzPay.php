<?php

/**
 * AzPay Class
 *
 * Autor:       Aztive ePayments
 * Version:     1.5 beta
 * Date         14/11/2013
 * Description
 *      This is a helper class to integrate Aztive AzPay online payments platform.
 *      The goal is to minimize the effor a merchant makes to integrate the payment platform
 *      by giving a fully functional implementation of it.
 */
class AzPay {

    private static $onepay = 'https://azpay.aztive.com';    
    private static $urlD = '/Direct/pay.php';
    private static $urlM = '/Method/pay.php';
    private static $push = '/pushSMS/push.php';
    /* private static $urlT   = '/Tokens/pay.php';

      private static $urlTs = '/Tokens/add_user.php';
      private static $urlTu = '/Tokens/delete_user.php';
      private static $urlTl = '/Tokens/list_users.php';
      private static $urlTa = '/Tokens/add_tokens.php'; */
    private $customerid = NULL;
    private $terminal = NULL;
    private $secret = NULL;
    private $lastOrderId = NULL;
    private $httpAuthUser = NULL;
    private $httpAuthPwd = NULL;
    
    const CUSTOMERID = '396349057';
    
    /*PARA PRUEBAS*/
    const TERMINAL = '999';    
    const SECRET = 'qwerty1234567890uiop';
//    
    /*PARA PRODUCCION*/
//    const TERMINAL = '001';    
//    const SECRET = 'CA4AE93932ADF12EF0D2';

    /**
     * OnePay class constructor. Creates an object with
     * basic commerce information.
     *
     * @param $customer string Customer id of the commerce asigned by OnePay
     *
     * @param $terminal string The terminal number from which the transactions will be performed
     *
     * @param $secret string The secret code asigned by Aztive to sign the transactions
     */
    public function __construct() {
        $this->customerid = self::CUSTOMERID;
        $this->terminal = self::TERMINAL;
        $this->secret = self::SECRET;
    }

    /**
     * Generates the url to call the chosen payment method.
     * 
     * @param string $amount Amount to charge without decimal point.
     *
     * @param string $id Payment method ID to use in case of not using Direct pay.
     *
     * @param string $orderid Unique payment orderid generated by the commerce. If set to '' the function will generate it automatically.
     * 
     * @param string $recurrency An array containing recurrency parameters.
     *      <ul>
     *          <li> "recurring": Recurrency parameter. 'I' for initial recurrency (any method) or order_id of the initial recurrency (for credit card).</li>
     *          <li>PayPal specific:</li>
     *          <ul>
     *              <li> "rDescription": Billing Agreement description.</li>
     *              <li> "rAmount": Recurring payments amount.</li>
     *              <li> "rCurrency": (optional) Currency associated to the amount to pay [default = EUR].</li>
     *              <li> "rPeriod": Recurring payments measure unit [Day | Week | SemiMonth | Month | Year].</li>
     *              <li> "rFrequency": Amount of billing periods that make a billing cycle.</li>
     *              <li> "rCycles": Total number of payments to make. If set to 0 it will continue until the billing agreement is cancelled.</li>
     *              <li> Total recurring payments: one every rFrequency rPeriod until rCycles </li>
     *          </ul>
     *      </ul>
     *
     * @param string $cData Customer custom data. The value of this param will be returned within the notification upon transaction completion.
     * 
     * @param array $APoptional An array containing the optional parameters for certain payment methods.
     * 		Use the parameters' name as key for the array.
     * 		Refer to the API doc to know when to provide these optional parameters.
     * 					<li> "currency": Currency in which to charge the amount. </li>
     * 					<li> "lang": Payment panel language. </li>
     * 					<li> "name": Commerce name. </li>
     * 					<li> "product_name": Product description </li>
     *
     * @param array $Toptional An array conatining aditional parameters for Tokens payment system
     *                  <li>"username": Username of the customer in Tokens system</li>
     *                  <li>"ratio": Conversion ratio from the specified amount to Tokens (Eg: 1 Currency = ratio Tokens)</li>
     * 
     * @return string URL to call to direct payment method.
     */
    public function AztivePay($amount, $id = '', $orderid = '', $recurrency = NULL, $APoptional = NULL, $cData = null, $Toptional = NULL) {
        // make sure the amount does not contain a decimal point
        $amount = $this->normalizeAmount($amount);

        // Let OnePay class generate an orderId
        if ($orderid == '')
            $orderid = $this->genUniqueOrderId();

        // Setup common params
        $params = array('orderid' => $orderid,
            'amount' => $amount,
            'customerid' => $this->customerid,
            'terminal' => $this->terminal,            
            );

        // add id
        if ($id != '')
            $params['id'] = $id;

        // add recurrency parameters
        if ($recurrency != NULL) {
            $params['recurring'] = $recurrency['recurring'];
            if (isset($recurrency['rDescription']))
                $params['rDescription'] = $recurrency['rDescription'];
            if (isset($recurrency['rPeriod']))
                $params['rPeriod'] = $recurrency['rPeriod'];
            if (isset($recurrency['rFrequency']))
                $params['rFrequency'] = $recurrency['rFrequency'];
            if (isset($recurrency['rCycles']))
                $params['rCycles'] = $recurrency['rCycles'];
            if (isset($recurrency['rAmount'])) {
                $params['rAmount'] = $recurrency['rAmount'];
                $params['rCurrency'] = isset($recurrency['rCurrency']) ? $recurrency['rCurrency'] : 'EUR';
            }
        }

        // add tokens parameters
        if ($Toptional != NULL) {
            if (isset($Toptional['username']))
                $params['username'] = $Toptional['username'];
            if (isset($Toptional['ratio']))
                $params['ratio'] = $Toptional['ratio'];
        }

        // add merchant custom parameter
        if (!is_null($cData))
            $params['cData'] = $cData;

        // compute the signature
        $params['signature'] = $this->signature($params);

        // add the optional parameters
        if ($APoptional != NULL) {
            if (isset($APoptional['currency']))
                $params['currency'] = $APoptional['currency'];
            if (isset($APoptional['lang']))
                $params['lang'] = $APoptional['lang'];
            if (isset($APoptional['name']))
                $params['name'] = $APoptional['name'];
            if (isset($APoptional['product_name']))
                $params['product_name'] = $APoptional['product_name'];
        }

        if ($id == '')
            return $this->genURL(AzPay::$urlD, $params);
        return $this->genURL(AzPay::$urlM, $params);
    }

    /*     * ******************************************************************************************** */
    /** MISCELLANEOUS ***************************************************************************** */
    /*     * ******************************************************************************************** */

    /**
     * Sets HTTP authentication for all AzPay to merchant responses.
     *
     * @param string $user HTTP authentication username
     * @param string $pwd HTTP authentication password
     *
     * @deprecated
     */
    public function setHttpAuthentication($user, $pwd) {
        $this->httpAuthUser = $user;
        $this->httpAuthPwd = $pwd;
    }

    /**
     * Returns whether the signature in the response corresponds
     * with the signature computed locally.
     * 
     * @param array $response The whole response data. Tipically $_GET.
     * 
     * @return boolean True if the signatures are equal. False otherwise.
     */
    public function validateResponseData($response) {
        $params = array(
            'order' => (isset($response['onepay_customer_order']) ? $response['onepay_customer_order'] : ''),
            'code' => (isset($response['onepay_customer_code']) ? $response['onepay_customer_code'] : ''),
            'terminal' => (isset($response['onepay_customer_terminal']) ? $response['onepay_customer_terminal'] : ''),
            'auth_code' => (isset($response['onepay_authorization_code']) ? $response['onepay_authorization_code'] : ''),
            'response' => (isset($response['onepay_response']) ? $response['onepay_response'] : ''),
            'card_no' => (isset($response['onepay_card_number']) ? $response['onepay_card_number'] : ''),
            'cData' => (isset($response['onepay_cData']) ? $response['onepay_cData'] : '')
        );

        // local signature
        $local = $this->signature($params);

        return ($local === $response['onepay_signature']);
    }

    /**
     * Method to get the last used order id
     * 
     * @return string Last used order id or NULL if no transaction has been made.
     */
    public function getLastOrderId() {
        return $this->lastOrderId;
    }

    /*     * ******************************************************************************************** */
    /** Push SMS ********************************************************************************** */
    /*     * ******************************************************************************************** */

    /**
     * Performs a request to send a sms with the provided message to the provided number.
     *
     * @param $msg string Message to send.
     * @param string|int $destination Message destination (number with country prefix).
     * @param string $sender Sender of the message.
     *      <ul>
     *          <li>If it is not set, or set to "", then the default value stored in Aztive is used.</li>
     *      </ul>
     * @param bool $trim Whether to limit the message to fit 1 sms or not.
     *      <ul>
     *          <li>"true": The message will be trimmed to fit 1 sms length, that is 160 characters. </li>
     *          <li>"false": The message will be sent as it is. </li>
     *      </ul>
     * @return array
     */
    public function pushSMS($msg, $destination, $sender = "", $trim = true) {
        $url = AzPay::$onepay . AzPay::$push;
        $params = array(
            "customerid" => $this->customerid,
            "terminal" => $this->terminal,
            "text" => $msg,
            "destino" => $destination,
            "sender" => $sender,
            "concat" => $trim ? 0 : 1,
        );

        $params['signature'] = $this->signature($params);

        $query = $url . '?' . http_build_query($params);
        $ch = curl_init($query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $ret = curl_exec($ch);

        curl_close($ch);

        return json_decode($ret, true);
    }

    /**
     * Request to send mutliple sms as specified in $request
     *
     * @param array $request
     *      array format:
     *          [0:n] => array(
     *              [0] => message
     *              [1] => destination tel numbers (international format, eg: 34699999999) separated by coma
     *          )
     * @param string $sender Sender of the messages.
     *      <ul>
     *          <li>If it is not set, or set to "", then the default value stored in Aztive is used.</li>
     *      </ul>
     * @param bool $trim Whether to limit the message to fit 1 sms or not.
     *      <ul>
     *          <li>"true": The message will be trimmed to fit 1 sms length, that is 160 characters. </li>
     *          <li>"false": The message will be sent as it is. </li>
     *      </ul>
     *
     * @return array
     */
    public function pushSMS_multi($request, $sender = "", $trim = true) {
        $parsed = array();
        $parsed[0] = count($request);

        // join both arrays
        $parsed = array_merge($parsed, $request);

        // append sender and trim info
        $parsed['sender'] = $sender;
        $parsed['concat'] = $trim ? 0 : 1;

        $url = AzPay::$onepay . AzPay::$push;
        $params = array(
            "customerid" => $this->customerid,
            "terminal" => $this->terminal,
            "params" => json_encode($parsed),
        );
        $params['signature'] = $this->signature($params);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST, count($params));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $ret = curl_exec($ch);

        curl_close($ch);

        return json_decode($ret, true);
    }

    /**
     * Requests to send multiple sms as specified in the provided CSV file.
     *
     * Accepted format is as follows:
     *      [sender, sender_name]
     *      [trim, {true|false}]
     *      msg, message1
     *      dst, tel1, .., teln
     *      msg, message2
     *      dst, tel1, .., teln
     *
     * @param $filepath string Path to CSV file to read and parse.
     * @return bool|array An associative array with the received response or false if the file does not exist or cannot be opened.
     */
    public function pushSMS_CSV($filepath) {
        $ret = false;
        if (file_exists($filepath)) {
            if (($handler = fopen($filepath, "r")) !== FALSE) {
                $sender = "";
                $trim = true;
                $parsed = array();
                $parsed[0] = 0;
                $i = 1;

                while (($data = fgetcsv($handler, ",")) !== FALSE) {
                    if ($data[0] == "sender") {
                        $sender = isset($data[1]) ? $data[1] : "";
                    } else if ($data[0] == "trim") {
                        $trim = isset($data[1]) ? ($data[1] == true) : false;
                    } else if ($data[0] == "msg") {
                        $msg = isset($data[1]) ? $data[1] : "";
                        if ($trim)
                            $msg = substr($msg, 0, 160);
                        $parsed[$i] = array(
                            $msg,
                            ""
                        );
                        $parsed[0]++;
                    }
                    else {
                        unset($data[0]);
                        $parsed[$i][1] = implode(',', $data);
                        $i++;
                    }
                }

                // add sender and trim
                $parsed['sender'] = $sender;
                $parsed['concat'] = $trim ? 0 : 1;

                $url = AzPay::$onepay . AzPay::$push;
                $params = array(
                    "customerid" => $this->customerid,
                    "terminal" => $this->terminal,
                    "params" => json_encode($parsed),
                );
                $params['signature'] = $this->signature($params);

                $ch = curl_init($url);

                curl_setopt($ch, CURLOPT_POST, count($params));
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);

                $ret = curl_exec($ch);

                curl_close($ch);
            }
            else
                return false;
        }
        else
            return false;

        return json_decode($ret, true);
    }

    /*     * ******************************************************************************************** */
    /** PRIVATE METHODS *************************************************************************** */
    /*     * ******************************************************************************************** */

    /**
     * Computes the signature with the given parameters.
     *
     * @param array $params Parameters to use to compute the signature. Note that the order in which the parameters are into the array is important.
     *
     * @return string The computed signature.
     */
    private function signature($params) {
        //error_log("[onepayclass] implode: " . implode('', $params) . $this->secret);
        return strtoupper(sha1(implode('', $params) . $this->secret));
    }

    /**
     * Computes a new unique order id.
     * 
     * @return string A new unique order id.
     */
    private function genUniqueOrderId() {
        $orderid = number_format(microtime(true), 4);
        $orderid = str_replace('.', '', $orderid);
        $orderid = str_replace(',', '', $orderid);
        $orderid = substr($orderid, 0, 12);            // Unix time in seconds and 2 second decimals
        return $orderid;
    }

    /**
     * Builts the url based on the parameters.
     * 
     * @param string
     * 
     * @param array
     * 
     * @return string The built url.
     */
    private function genURL($type, $params) {
        return (AzPay::$onepay . $type . '?' . http_build_query($params));
    }

    /**
     * Normalizes the provided amount depending on the format it is received.
     * 
     * @param mixed $amount
     * 
     * @return string Numeric string with the amount representation to send to onepay.
     */
    private function normalizeAmount($amount) {
        $amount = (float) $amount;
        $amount = $amount * 100;

        return (int) $amount;
    }

}