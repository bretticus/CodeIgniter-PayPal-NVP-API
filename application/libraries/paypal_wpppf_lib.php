<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Provide API calls for Website Payments Pro Payflow transactions.
 *
 * @author bmillett
 */

class Paypal_wpppf_lib extends Paypal_Api_Lib {
    const CARDTYPE_VISA = 'Visa';
    const CARDTYPE_MCARD = 'MasterCard';
    const CARDTYPE_DISC = 'Discover';
    const CARDTYPE_AMEX = 'Amex';
    const PAYTYPE_SALE = 'Sale';
    const PAYTYPE_AUTH = 'Authorization';

    /**
     * Send a DoDirectPayment request to payment API. Returns TRUE on success or
     * an aray of error messages on failure.
     *
     * @param string $first
     * @param string $last
     * @param string $address
     * @param string $address2
     * @param string $city
     * @param string $state
     * @param string $zip
     * @param string $amount
     * @param string $card_num
     * @param string $card_cvv2
     * @param string $card_type
     * @param string $payment_action
     * @param string $invoice_num
     * @return mixed 
     */
    public function do_direct_payment($first, $last, $address, $address2, $city, 
            $state, $zip, $amount, $card_num, $card_cvv2, $card_type='Visa', 
            $payment_action='Sale', $invoice_num=null) 
    {
        $this->add_nvp('PAYMENTACTION', $payment_action);
        $this->add_nvp('IPADDRESS', $this->CI->input->ip_address());
        $this->add_nvp('CREDITCARDTYPE', $card_type);
        $this->add_nvp('ACCT', $this->_format_acct($card_num));
        $this->add_nvp('CVV2', $this->_format_ccv2($card_cvv2));
        $this->add_nvp('FIRSTNAME', $first);
        $this->add_nvp('LASTNAME', $last);
        $this->add_nvp('STREET', $address);
        $this->add_nvp('STREET2', $address2);
        $this->add_nvp('CITY', $city);
        $this->add_nvp('STATE', $state);
        $this->add_nvp('ZIP', $zip);
        $this->add_nvp('AMT', $amount);
        if ( !is_null($invoice_num) )
            $this->add_nvp('INVNUM', $invoice_num);
        
        $success = $this->send_api_call('DoDirectPayment');
        
        if ( $success ) {
            return TRUE;
        } else {
            $errors = explode("\n", $this->return_nvp_errors());
        }
    }

    /**
     * Format credit card number
     *
     * @param string $acct
     * @return string 
     */
    private function _format_acct($acct) {
        return preg_replace('/[^0-9]/', '', $acct);
    }

    /**
     * Format credit card CVV2 code
     *
     * @param string $cvv2
     * @return string
     */
    private function _format_ccv2($cvv2) {
        return substr(preg_replace('/[^0-9]/', '', $cvv2), 0, 4);
    }

}

?>
