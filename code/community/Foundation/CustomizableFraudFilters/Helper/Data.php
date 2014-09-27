<?php
class Foundation_CustomizableFraudFilters_Helper_Data extends Mage_Core_Helper_Abstract
{
  public function applyFraudFlag($order){
                $state = 'holded';
                $status = 'manual_review';
                $comment = 'Flagged for manual review.';
                $isCustomerNotified = false;
                $order->setState($state, $status, $comment, $isCustomerNotified);
                $order->save(); 
  }
}
	 