<?php
class Foundation_CustomizableFraudFilters_Model_Observer {
  public function applyFilters(Varien_Event_Observer $observer) {
    $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
    $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

    //set flags
    $stateFlag = Mage::getStoreConfig('customizablefraudfilters/filters/state_match_flag');
    Mage::log("&stateFlag: ".$stateFlag);

    $cityFlag = Mage::getStoreConfig('customizablefraudfilters/filters/city_match_flag');
    Mage::log("&cityFlag: ".$cityFlag);  

    $zipCodeFlag = Mage::getStoreConfig('customizablefraudfilters/filters/zip_code_match_flag');
    Mage::log("&zipCodeFlag: ".$zipCodeFlag);

    $countryFlag = Mage::getStoreConfig('customizablefraudfilters/filters/country_match_flag');
    Mage::log("&countryFlag: ".$countryFlag);

    $guestFlag = Mage::getStoreConfig('customizablefraudfilters/filters/guest_flag');
    Mage::log("&guestFlag: ".$guestFlag);

    $grandTotalMaxFlag = Mage::getStoreConfig('customizablefraudfilters/filters/grand_total_max_flag');
    Mage::log("&grandTotalMaxFlag: ".$grandTotalMaxFlag);

    $grandTotalMinFlag = Mage::getStoreConfig('customizablefraudfilters/filters/grand_total_min_flag');
    Mage::log("&grandTotalMinFlag: ".$grandTotalMinFlag);    

    $orderContainsProductsFlag = Mage::getStoreConfig('customizablefraudfilters/filters/order_contains_products_flag');
    Mage::log("&orderContainsProductsFlag: ".$orderContainsProductsFlag);  

    $shippingCountryFlag = Mage::getStoreConfig('customizablefraudfilters/filters/shipping_country_flag');
    Mage::log("&shippingCountryFlag: ".$shippingCountryFlag); 

    $billingCountryFlag = Mage::getStoreConfig('customizablefraudfilters/filters/billing_country_flag');
    Mage::log("&billingCountryFlag: ".$billingCountryFlag);

    $restrictedEmailFlag = Mage::getStoreConfig('customizablefraudfilters/filters/restricted_email_flag');
    Mage::log("&restrictedEmailFlag: ".$restrictedEmailFlag);

    //begin filters
    if ($stateFlag == 1) {
      Mage::helper('customizablefraudfilters')->checkState($order);
    }
    if ($cityFlag == 1) {
      Mage::helper('customizablefraudfilters')->checkCity($order);
    }
    if ($zipCodeFlag == 1) {
      Mage::helper('customizablefraudfilters')->checkZipCode($order);
    }
    if ($countryFlag == 1) {
      Mage::helper('customizablefraudfilters')->checkCountry($order);
    }
    if ($guestFlag == 1) {
      Mage::helper('customizablefraudfilters')->checkGuest($order);
    }
    if ($grandTotalMaxFlag != null && $grandTotalMaxFlag > 0) {
      $grandTotalMax = Mage::getStoreConfig('customizablefraudfilters/filters/grand_total_max_flag');
      Mage::helper('customizablefraudfilters')->checkGrandTotalMax($order, $grandTotalMax);
    } 
    if ($grandTotalMinFlag != null && $grandTotalMinFlag > 0) {
      $grandTotalMin = Mage::getStoreConfig('customizablefraudfilters/filters/grand_total_min_flag');
      Mage::helper('customizablefraudfilters')->checkGrandTotalMin($order, $grandTotalMin);
    } 

    if ($orderContainsProductsFlag != null) {
      Mage::helper('customizablefraudfilters')->checkOrderContainsProducts($order);
    }     

    if ($shippingCountryFlag != null) {
      Mage::helper('customizablefraudfilters')->checkShippingCountry($order);
    }   
    if ($billingCountryFlag != null) {
      Mage::helper('customizablefraudfilters')->checkBillingCountry($order);
    }   
    if ($restrictedEmailFlag != null) {
      Mage::helper('customizablefraudfilters')->checkRestrictedEmails($order);
    } 
  }
}