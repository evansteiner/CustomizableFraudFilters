<?php
class Foundation_CustomizableFraudFilters_Helper_Data extends Mage_Core_Helper_Abstract
{
  public function applyFraudFlag($order){
    // $order->setState("fraud_review");
    // $order->setStatus("New");
    // $order->save();
    
    //$order = Mage::getModel('sales/order')->loadByIncrementId($order_update->getIncrementId());
                $state = 'holded';
                $status = 'manual_review';
                $comment = 'Flagged for manual review.';
                $isCustomerNotified = false;
                $order->setState($state, $status, $comment, $isCustomerNotified);
                $order->save(); 
     
  }
}
	 