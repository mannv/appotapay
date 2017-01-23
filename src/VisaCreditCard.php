<?php

/**
 * Created by PhpStorm.
 * User: mannv
 * Date: 1/20/2017
 * Time: 1:58 PM
 * @see https://appotapay.com/Docs/docsVisaCreditCard
 */
namespace Kayac\AppotaPay;
class VisaCreditCard extends PaymentBase
{
    /*
    * function get payment bank url
    */
    public function getPaymentBankUrl($developer_trans_id, $amount, $state = '', $target = '', $success_url = '', $error_url = '', $bank_id = 0, $client_ip)
    {
        // build api url
        $api_url = $this->API_URL . $this->VERSION . '/services/pay_visa?api_key=' . $this->API_KEY . '&lang=' . $this->LANG;
        if(config('appotapay.sandbox') == true) {
            $api_url = $this->API_URL . $this->VERSION . '/sandbox/services/pay_visa?api_key=' . $this->API_KEY . '&lang=' . $this->LANG;
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
        $check_hash = md5($amount . $country_code . $currency . $developer_trans_id . $sandbox  . $state . $status . $target . $transaction_id . $transaction_type . $type . $this->SECRET_KEY);
        if ($check_hash !== $hash) {
            // return check hash fail
            return array(
                'error_code' => 100,
                'amount' => 0
            );
        }

        // check transaction status
        if ($status == 1) {
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
}