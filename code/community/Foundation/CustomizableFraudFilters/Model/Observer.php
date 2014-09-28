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

    $grandTotalFlag = Mage::getStoreConfig('customizablefraudfilters/filters/grand_total_flag');
    Mage::log("&grandTotalFlag: ".$grandTotalFlag);




    //begin filters
    if ($cityFlag == 1) {
      Mage::helper('customizablefraudfilters')->checkCity($order);
    }
    if ($zipCodeFlag == 1) {
      Mage::helper('customizablefraudfilters')->checkZipCode($order);
    }
    if ($grandTotalFlag != null && $grandTotalFlag > 0) {
      $grandTotalLimit = Mage::getStoreConfig('customizablefraudfilters/filters/grand_total_flag');
      Mage::helper('customizablefraudfilters')->checkGrandTotal($order, $grandTotalLimit);
    }    
  }
}