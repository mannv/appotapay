<?php
/**
 * Created by PhpStorm.
 * User: mannv
 * Date: 1/20/2017
 * Time: 2:12 PM
 */
namespace Kayac\AppotaPay;
class PaymentBase
{
    protected $API_URL = 'https://api.appotapay.com/';
    protected $API_KEY;
    protected $SECRET_KEY;
    protected $LANG;
    protected $VERSION;
    protected $METHOD = 'POST';

    function __construct()
    {
        // set params
        $this->API_KEY = config('appotapay.api_key');
        $this->LANG = config('appotapay.lang');
        $this->SECRET_KEY = config('appotapay.secret_key');
        $this->VERSION = config('appotapay.version');
    }

    /*
     * function make request
     * url : string | url request
     * params : array | params request
     * method : string(POST,GET) | method request
     */
    protected function makeRequest($url, $params, $method = 'POST')
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); // Time out 60s
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); // connect time out 60s

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

    /*
    * function check transantion status
    * @param: transaction_id
    */
    protected function checkTransaction($transaction_id)
    {
        // build api url
        $api_url = $this->API_URL . $this->VERSION . '/services/check_transaction_status?api_key=' . $this->API_KEY . '&lang=' . $this->LANG;
        // build params
        $params = array(
            'developer_trans_id' => $transaction_id,
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
}