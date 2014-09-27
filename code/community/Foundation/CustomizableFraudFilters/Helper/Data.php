<?php
class Foundation_CustomizableFraudFilters_Helper_Data extends Mage_Core_Helper_Abstract
{
  public function checkZipCode($order) {
    $billingAddress = $order->getBillingAddress();
    $shippingAddress = $order->getShippingAddress();
    $billingZip = $billingAddress["postcode"];
    $shippingZip = $shippingAddress["postcode"];
    Mage::log($billingZip);
    Mage::log($shippingZip); 
    if ($billingZip != $shippingZip) {
    	Mage::log("transition fired");
      Mage::helper('customizablefraudfilters')->applyFraudFlag($order);
    }
  }


  public function applyFraudFlag($order){
    $state = 'holded';
    $status = 'manual_review';
    $comment = 'Flagged for manual review.';
    $isCustomerNotified = false;
    $order->setState($state, $status, $comment, $isCustomerNotified);
    $order->save(); 
  }
}
	 