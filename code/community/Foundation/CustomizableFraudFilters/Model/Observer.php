<?php
class Foundation_CustomizableFraudFilters_Model_Observer {
  public function applyFilters(Varien_Event_Observer $observer) {
    $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
    $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
    //begin filters
    $a = Mage::getStoreConfig('customizablefraudfilters/filter_options/zip_code_flag');
    Mage::log("zip code is set to: ".$a);
    Mage::helper('customizablefraudfilters')->checkZipCode($order);

  }
}