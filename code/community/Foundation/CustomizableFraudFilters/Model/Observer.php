<?php
class Foundation_CustomizableFraudFilters_Model_Observer {
  public function applyFilters(Varien_Event_Observer $observer) {
    $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
    $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

    //set flags
    $cityFlag = Mage::getStoreConfig('customizablefraudfilters/filters/city_flag');
    Mage::log("&cityFlag: ".$cityFlag);  

    $zipCodeFlag = Mage::getStoreConfig('customizablefraudfilters/filters/zip_code_flag');
    Mage::log("&zipCodeFlag: ".$zipCodeFlag);

    $grandTotalMaxFlag = Mage::getStoreConfig('customizablefraudfilters/filters/grand_total_max_flag');
    Mage::log("&grandTotalMaxFlag: ".$grandTotalMaxFlag);

    $grandTotalMinFlag = Mage::getStoreConfig('customizablefraudfilters/filters/grand_total_min_flag');
    Mage::log("&grandTotalMinFlag: ".$grandTotalMinFlag);    




    //begin filters
    if ($cityFlag == 1) {
      Mage::helper('customizablefraudfilters')->checkCity($order);
    }
    if ($zipCodeFlag == 1) {
      Mage::helper('customizablefraudfilters')->checkZipCode($order);
    }
       if ($grandTotalMaxFlag != null && $grandTotalMaxFlag > 0) {
      $grandTotalMax = Mage::getStoreConfig('customizablefraudfilters/filters/grand_total_max_flag');
      Mage::helper('customizablefraudfilters')->checkGrandTotalMax($order, $grandTotalMax);
    } 
    if ($grandTotalMinFlag != null && $grandTotalMinFlag > 0) {
      $grandTotalMin = Mage::getStoreConfig('customizablefraudfilters/filters/grand_total_min_flag');
      Mage::helper('customizablefraudfilters')->checkGrandTotalMin($order, $grandTotalMin);
    } 
  }
}