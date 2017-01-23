<?php
/**
 * Created by PhpStorm.
 * User: mannv
 * Date: 1/20/2017
 * Time: 2:11 PM
 * @see https://appotapay.com/Docs/docsApiCard
 */
namespace Kayac\AppotaPay;
class CardCharging extends PaymentBase
{
    /*
    * function card charging
    */
    public function cardCharging($developer_trans_id, $code, $serial, $vendor, $state, $target)
    {
        // build api url
        $api_url = $this->API_URL . $this->VERSION . '/services/card_charging?api_key=' . $this->API_KEY . '&lang=' . $this->LANG;
        if(config('appotapay.sandbox') == true) {
            $api_url = $this->API_URL . $this->VERSION . '/sandbox/services/card_charging?api_key=' . $this->API_KEY . '&lang=' . $this->LANG;
        }
        // build params
        $params = array(
            'developer_trans_id' => $developer_trans_id,
            'card_code' => $code,
            'card_serial' => $serial,
            'vendor' => $vendor,
            'state' => $state, // Optional param
            'target' => $target // Optional param
        );

        // request charging
        $result = $this->makeRequest($api_url, $params, $this->METHOD);
        // decode result
        $result = json_decode($result);

        // check result
        if (isset($result->error_code) && $result->error_code === 0) { // charging success
            return array(
                'success' => true,
                'amount' => $result->data->amount,
                'transaction_id' => $result->data->transaction_id
            );
        } else {
            return array(
                'success' => false,
                'error_code' => $result->error_code,
                'message' => $result->message
            );
        }
    }

    /*
    * function verify hash IPN for card transaction
    * @param: pass your var $_POST that your server received from AppotaPay's server
    */
    public function verifyCardTransactionIpnHash($params)
    {
        // get params
        $status = $params['status'];
        $amount = $params['amount'];
        $card_code = $params['card_code'];
        $card_serial = $params['card_serial'];
        $vendor = $params['card_vendor'];
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
        $check_hash = md5($amount . $card_code . $card_serial . $vendor . $country_code . $currency . $developer_trans_id . $sandbox . $state . $status . $target . $transaction_id . $transaction_type . $this->SECRET_KEY);
        if ($check_hash !== $hash) {
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