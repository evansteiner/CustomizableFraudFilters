<?php
class Foundation_CustomizableFraudFilters_Model_Observer {
  public function applyFilters(Varien_Event_Observer $observer) {
    $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
    $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
    Mage::helper('customizablefraudfilters')->checkZipCode($order);
    //Mage::helper('customizablefraudfilters')->applyFraudFlag($order);
  }
}