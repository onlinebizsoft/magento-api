<?php
/**
 * Created by PhpStorm.
 * User: Leo
 * Date: 15/6/4
 * Time: 下午11:41
 */

class Lading_Api_Model_Checkout extends Lading_Api_Model_Abstract {

    /**
     * 获取账单
     * @return mixed
     */
    public function getQuote() {
        return Mage::getSingleton('checkout/session')->getQuote();
    }



    /**
     * 获取账单
     * @param $quote
     * @return array
     */
    public function getAddressByQuote($quote) {
        $address = array();
        $shipping_address = $quote->getShippingAddress();
        $billing_address = $quote->getBillingAddress();
        $address['shipping_address'] = array(
            'address_id' => $shipping_address->getCustomerAddressId(),
            'customer_id' =>$shipping_address->getCustomerId(),
            'address_type'=>$shipping_address->getAddressType(),
            'email'=>$shipping_address->getEmail(),
            'firstname'=>$shipping_address->getFirstname(),
            'lastname'=>$shipping_address->getLastname(),
            'company'=>$shipping_address->getCompany(),
            'street'=>$shipping_address->getStreet(),
            'city' =>$shipping_address->getCity(),
            'region' =>$shipping_address->getRegion(),
            'region_id'=>$shipping_address->getRegionId(),
            'postcode'=>$shipping_address->getPostcode(),
            'country_id'=>$shipping_address->getCountryId(),
            'telephone'=>$shipping_address->getTelephone(),
            'fax'=>$shipping_address->getFax(),
            'shipping_method'=>$shipping_address->getShippingMethod(),
            'shipping_description'=>$shipping_address->getShippingDescription(),
            'weight'=>$shipping_address->getWeight(),
            'subtotal'=>$shipping_address->getSubtotal(),
            'base_subtotal'=>$shipping_address->getBaseSubtotal(),
            'subtotal_with_discount' => $shipping_address->getSubtotalWithDiscount(),
            'base_subtotal_with_discount' => $shipping_address->getBaseSubtotalWithDiscount(),
            'tax_amount' => $shipping_address->getTaxAmount(),
            'base_tax_amount' => $shipping_address->getBaseTaxAmount(),
            'shipping_amount' =>$shipping_address->getShippingAmount(),
            'base_shipping_amount' =>$shipping_address->getBaseShippingAmount(),
            'shipping_tax_amount' =>$shipping_address->getShippingTaxAmount(),
            'base_shipping_tax_amount' =>$shipping_address->getBaseShippingTaxAmount(),
            'discount_amount' =>$shipping_address->getDiscountAmount(),
            'base_discount_amount' =>$shipping_address->getBaseDiscountAmount(),
            'grand_total' => $shipping_address->getGrandTotal(),
            'base_grand_total' =>$shipping_address->getBaseGrandTotal()
        );
        $address['billing_address'] = array(
            'address_id' => $billing_address->getCustomerAddressId(),
            'customer_id' =>$billing_address->getCustomerId(),
            'address_type'=>$billing_address->getAddressType(),
            'email'=>$billing_address->getEmail(),
            'firstname'=>$billing_address->getFirstname(),
            'lastname'=>$billing_address->getLastname(),
            'company'=>$billing_address->getCompany(),
            'street'=>$billing_address->getStreet(),
            'city' =>$billing_address->getCity(),
            'region' =>$billing_address->getRegion(),
            'region_id'=>$billing_address->getRegionId(),
            'postcode'=>$billing_address->getPostcode(),
            'country_id'=>$billing_address->getCountryId(),
            'telephone'=>$billing_address->getTelephone(),
            'fax'=>$billing_address->getFax()
        );
        return $address;
    }



    /**
     * get active payment method
     * @param $quote
     * @return array
     */
    public function getActivePaymentMethods($quote){
        if ($quote->getPayment()->getMethod()){
            $quote_payment_code = $quote->getPayment()->getMethodInstance()->getCode();
        }
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();
        $methods = array();
        foreach ($payments as $paymentCode=>$paymentModel) {
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            if($paymentCode != 'free'){
                $methods[$paymentCode] = array(
                    'title'   => $paymentTitle,
                    'code' => $paymentCode
                );
                if($paymentCode == $quote_payment_code){
                    $methods[$paymentCode]['is_selected'] = true;
                }
            }
        }
        return $methods;
    }


    /**
     * get shipping method detail by quote
     * @param $quote
     * @return array
     */
    public function getShippingMethodByQuote($quote){
        $quoteShippingAddress = $quote->getShippingAddress();
        $shippingMethod = $quoteShippingAddress->getShippingMethod();
        if (is_null($quoteShippingAddress->getId())) {
            $return_result['msg'] = 'shipping_address_is_not_set';
            $return_result['code'] = 1;
            return $return_result;
        }
        $rates = $quoteShippingAddress->getShippingRatesCollection();
        $result_rates = array();
        foreach($rates as $rate){
            if($rate->getCode() == $shippingMethod){
                $result_rates['carrier'] = $rate->getCarrier();
                $result_rates['carrier_title'] = $rate->getCarrierTitle();
                $result_rates['code'] = $rate->getCode();
                $result_rates['method'] = $rate->getMethod();
                $result_rates['method_title'] = $rate->getMethodTitle();
                $result_rates['price'] = $rate->getPrice();
                $result_rates['method_description'] = $rate->getMethodDescription();
            }
        }
        return $result_rates;
    }



    /**
     * get shipping method list by quote
     * @param $quote
     * @return array
     */
    public function getShippingMethodListByQuote($quote){
        $shippingMethodLists = array();
        $quoteShippingAddress = $quote->getShippingAddress();
        $shippingMethod = $quoteShippingAddress->getShippingMethod();
        $rates = $quoteShippingAddress->getShippingRatesCollection();
        foreach($rates as $rate){
            $temp_rates = array();
            $temp_rates['carrier'] = $rate->getCarrier();
            $temp_rates['carrier_title'] = $rate->getCarrierTitle();
            $temp_rates['code'] = $rate->getCode();
            $temp_rates['method'] = $rate->getMethod();
            $temp_rates['method_title'] = $rate->getMethodTitle();
            $temp_rates['price'] = $rate->getPrice();
            $temp_rates['method_description'] = $rate->getMethodDescription();
            if($rate->getCode() == $shippingMethod){
                $temp_rates['is_selected'] = true;
            }
            array_push($shippingMethodLists,$temp_rates);
        }
        $after_group = array();
        foreach ( $shippingMethodLists as $value ) {
            $after_group[$value['carrier_title']][] = $value;
        }
        return $after_group;
    }



}