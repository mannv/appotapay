<?php
/**
 * Created by PhpStorm.
 * User: mannv
 * Date: 1/20/2017
 * Time: 9:17 AM
 */
namespace Kayac\AppotaPay;
class iBanking
{
    private $API_URL = 'https://api.appotapay.com/';
    private $API_KEY;
    private $SECRET_KEY;
    private $LANG;
    private $VERSION;
    private $METHOD = 'POST';

    public function __construct()
    {
        // set params
        $this->API_KEY = config('appotapay.api_key');
        $this->LANG = config('appotapay.lang');
        $this->SECRET_KEY = config('appotapay.secret_key');
        $this->VERSION = config('appotapay.version');
    }

    /*
    * function get payment bank url
    */
    public function getPaymentBankUrl($developer_trans_id, $amount, $state = '', $target = '', $success_url = '', $error_url = '', $bank_id = 0, $client_ip)
    {
        // build api url
        $api_url = $this->API_URL . $this->VERSION . '/services/ibanking?api_key=' . $this->API_KEY . '&lang=' . $this->LANG;
        if(config('appotapay.sandbox') == true) {
            $api_url = $this->API_URL . $this->VERSION . '/sandbox/services/ibanking?api_key=' . $this->API_KEY . '&lang=' . $this->LANG;
        }
        // build params
        $params = array(
            'developer_trans_id' => $developer_trans_id, // Require param
            'amount' => $amount, // Require param
            'state' => $state, // Optional param
            'target' => $target, // Optional param
            'success_url' => $success_url, // Optional param
            'error_url' => $error_url, // Optional param
            'bank_id' => $bank_id, // Optional param
            'client_ip' => $client_ip // Require param
        );

        // request get payment url
        $result = $this->makeRequest($api_url, $params, $this->METHOD);
        // decode result
        $result = json_decode($result);

        // check result
        if (isset($result->error_code) && $result->error_code === 0) { // charging success
            $transaction_id = $result->data->transaction_id;
            $bank_options = $result->data->bank_options;
            return $bank_options[0]->url;
        }
    }

    /*
    * function verify hash IPN for bank transaction
    * @param: pass your var $_POST that your server received from AppotaPay's server
    */
    public function verifyBankTransactionIpnHash($params)
    {
        // get params
        $status = $params['status'];
        $amount = $params['amount'];
        $type = $params['type'];
        $country_code = $params['country_code'];
        $currency = $params['currency'];
        $sandbox = $params['sandbox'];
        $state = $params['state'];
        $target = $params['target'];
        $transaction_id = $params['transaction_id'];
        $developer_trans_id = $params['developer_trans_id'];
        $transaction_type = $params['transaction_type'];
        $hash = $params['hash'];

        // check hash
        $check_hash = md5($amount . $country_code . $currency . $sandbox . $developer_trans_id . $state . $status . $target . $transaction_id . $transaction_type . $type . $this->SECRET_KEY);
        if ($check_hash !== $hash) {
            // return check hash fail
        }

        // check transaction status
        if ($status === 1) {
            // return transaction success
            return array(
                'error_code' => 0,
                'amount' => $amount
            );
        } else {
            return array(
                'error_code' => 1,
                'amount' => 0
            );
        }
    }

    /*
    * function check transantion status
    * @param: developer_trans_id
    */
    public function checkTransaction($developer_trans_id)
    {
        // build api url
        $api_url = $this->API_URL . $this->VERSION . '/services/check_transaction_status?api_key=' . $this->API_KEY . '&lang=' . $this->LANG;
        // build params
        $params = array(
            'developer_trans_id' => $developer_trans_id,
            'transaction_type' => 'BANK'
        );

        // request check transaction
        $result = $this->makeRequest($api_url, $params, $this->METHOD);
        // decode result
        $result = json_decode($result);

        // check result
        if (isset($result->error_code) && $result->error_code === 0) { // transaction success
            $transaction_id = $result->data->transaction_id; // appota transaction id
            $developer_trans_id = $result->data->developer_trans_id; // developer transaction id
            $amount = $result->data->amount;

        } else { // trasaction fail
            return array(
                'error_code' => $result->error_code,
                'message' => $result->message
            );
        }
    }

    /*
     * function make request
     * url : string | url request
     * params : array | params request
     * method : string(POST,GET) | method request
     */
    private function makeRequest($url, $params, $method = 'POST')
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); // Time out 60s
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); // connect time out 5s

        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_error($ch)) {
            return false;
        }

        if ($status != 200) {
            curl_close($ch);
            return false;
        }
        // close curl
        curl_close($ch);

        return $result;
    }

}