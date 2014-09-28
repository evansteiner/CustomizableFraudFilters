<?php
class Foundation_CustomizableFraudFilters_Helper_Data extends Mage_Core_Helper_Abstract
{

  public function checkCity($order) {
    Mage::log(print_r($order));
    $billingAddress = $order->getBillingAddress();
    $shippingAddress = $order->getShippingAddress();
    $billingCity = $billingAddress["city"];
    $shippingCity = $shippingAddress["city"];
    if ($billingCity != $shippingCity) {
      $flagReason = "Shipping and billing city do not match.";
      Mage::helper('customizablefraudfilters')->applyFraudFlag($order, $flagReason);
    }
  }

  public function checkZipCode($order) {
    $billingAddress = $order->getBillingAddress();
    $shippingAddress = $order->getShippingAddress();
    $billingZip = $billingAddress["postcode"];
    $shippingZip = $shippingAddress["postcode"];
    if ($billingZip != $shippingZip) {
      $flagReason = "Shipping and billing zip code do not match.";
      Mage::helper('customizablefraudfilters')->applyFraudFlag($order, $flagReason);
    }
  }

  public function checkGrandTotalMax($order, $grandTotalMax) {
    $grandTotal = $order["grand_total"];
    if ($grandTotal > $grandTotalMax) {
      $flagReason = "Grand total of this order ($".number_format($grandTotal,2).") exceeds the maximum grand total limit ($".$grandTotalMax.").";
      Mage::helper('customizablefraudfilters')->applyFraudFlag($order, $flagReason);
    }
  }

  public function checkGrandTotalMin($order, $grandTotalMin) {
    $grandTotal = $order["grand_total"];
    if ($grandTotal < $grandTotalMin) {
      $flagReason = "Grand total of this order ($".number_format($grandTotal,2).") is less then the minimum grand total limit ($".$grandTotalMin.").";
      Mage::helper('customizablefraudfilters')->applyFraudFlag($order, $flagReason);
    }
  }


  public function applyFraudFlag($order, $flagReason){
    $state = "holded";
    $status = "manual_review";
    $notice = "Flagged for manual review: ";
    $comment = $notice.$flagReason;
    $isCustomerNotified = false;
    $order->setState($state, $status, $comment, $isCustomerNotified);
    $order->save(); 
  }
}
	 