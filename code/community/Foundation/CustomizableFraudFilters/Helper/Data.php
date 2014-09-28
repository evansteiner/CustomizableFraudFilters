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


  public function sendAlertEmail($alertEmailAddress){
    Mage::log("step 4");
    $emailTemplate = Mage::getModel("core/email_template")->loadDefault("fraud_filter_alert");
    $emailTemplate->setSenderName("Fraud Alert");
    $emailTemplate->setSenderEmail("no-reply@fraud-alert.com");
    $emailTemplate->setTemplateSubject("Potential Fraud Alert");

    $emailTemplateVariables = array();
    $emailTemplateVariables['orderNumber'] = "12345";
    $emailTemplateVariables['storeName'] = "My Test Store";
    $emailTemplateVariables['flagReason'] = "This is just a test";

    Mage::log("step 5");

    $emailTemplate->send($alertEmailAddress, null, $emailTemplateVariables);   

    Mage::log("step 6");
  }


  public function applyFraudFlag($order, $flagReason){
    $state = "holded";
    $status = "manual_review";
    $notice = "Flagged for manual review: ";
    $comment = $notice.$flagReason;
    $isCustomerNotified = false;
    $order->setState($state, $status, $comment, $isCustomerNotified);
    $order->save(); 

    if(Mage::getStoreConfig('customizablefraudfilters/alerts/alert_email') != null){
      Mage::log("step 1");
      $alertEmailAddresses = Mage::getStoreConfig('customizablefraudfilters/alerts/alert_email');
      Mage::log("step 2: ".$alertEmailAddresses);
      $alertEmailAddresses = explode(",", $alertEmailAddresses);
      foreach ($alertEmailAddresses as $alertEmailAddress) {
        Mage::log("step 3: ".$alertEmailAddress);
        Mage::helper('customizablefraudfilters')->sendAlertEmail($alertEmailAddress);
      }
    }
  }
}
	 