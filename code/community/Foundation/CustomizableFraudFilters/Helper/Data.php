<?php
class Foundation_CustomizableFraudFilters_Helper_Data extends Mage_Core_Helper_Abstract
{

  public function checkState($order) {
    $billingAddress = $order->getBillingAddress();
    $shippingAddress = $order->getShippingAddress();
    $billingState = $billingAddress["region"];
    $shippingState = $shippingAddress["region"];
    if ($billingState != $shippingState) {
      $flagReason = "Shipping and billing state does not match.";
      Mage::helper('customizablefraudfilters')->applyFraudFlag($order, $flagReason);
    }
  }  

  public function checkCity($order) {
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


  public function checkOrderContainsProducts($order) {
    $flaggedItems = "";
    $filterProducts = Mage::getStoreConfig('customizablefraudfilters/filters/order_contains_products_flag');
    $filterProducts = explode(",", $filterProducts);
    foreach ($filterProducts as $filterProduct) {
      trim($filterProduct);
    }
    $items = $order->getAllItems();
    $itemcount = count($items);
    foreach ($items as $item){
      $itemId = $item->getProductId();
      if (in_array($itemId, $filterProducts)){
        $flaggedItems = $flaggedItems."Product ID: ".$itemId." - ".$item->getName()."<br/>";
      }
    }
    if($flaggedItems != ""){
      $flagReason = "The order contained the following items which have been flagged for manual review: <br/><br/>".$flaggedItems;
      Mage::helper('customizablefraudfilters')->applyFraudFlag($order, $flagReason);
      }
  }


  public function checkShippingCountry($order) {
    $filterCountries = Mage::getStoreConfig('customizablefraudfilters/filters/shipping_country_flag');
    $filterCountries = explode(",", $filterCountries);
    foreach ($filterCountries as $filterCountry) {
      trim($filterCountry);
    }
    $shippingAddress = $order->getShippingAddress();
    $shippingCountry = $shippingAddress["country_id"];
    if(in_array($shippingCountry, $filterCountries)) {
      $flagReason = "The shipping country for this order (".$shippingCountry.") is on the filter list.";
      Mage::helper('customizablefraudfilters')->applyFraudFlag($order, $flagReason);
    }
  }

  public function applyFraudFlag($order, $flagReason){

    $countries = Mage::getStoreConfig('customizablefraudfilters/filters/shipping_country_flag');
    Mage::log("countries: ".$countries);

    $state = "holded";
    $status = "manual_review";
    $notice = "Flagged for manual review: ";
    $comment = $notice.$flagReason;
    $isCustomerNotified = false;
    $order->setState($state, $status, $comment, $isCustomerNotified);
    $order->save(); 

    if(Mage::getStoreConfig('customizablefraudfilters/alerts/alert_email') != null){
      $alertEmailAddresses = Mage::getStoreConfig('customizablefraudfilters/alerts/alert_email');
      $alertEmailAddresses = explode(",", $alertEmailAddresses);
      foreach ($alertEmailAddresses as $alertEmailAddress) {
        $alertEmailAddress = trim($alertEmailAddress);
        Mage::helper('customizablefraudfilters')->sendAlertEmail($order, $alertEmailAddress, $flagReason);
      }
    }
  }


  public function sendAlertEmail($order, $alertEmailAddress, $flagReason) {
    $emailTemplate = Mage::getModel("core/email_template")->loadDefault("fraud_filter_alert");
    $emailTemplate->setSenderName("Fraud Alert");
    $emailTemplate->setSenderEmail("no-reply@fraud-alert.com");
    if(Mage::getStoreConfig('customizablefraudfilters/alerts/alert_email_subject') != "") {
      $subject = Mage::getStoreConfig('customizablefraudfilters/alerts/alert_email_subject');
    }
    else {
      $subject = "Potential Fraud Alert";
    }
    $emailTemplate->setTemplateSubject($subject.": Order #".$order["increment_id"]);
    $emailTemplate->setType("html");

    $emailTemplateVariables = array();
    $emailTemplateVariables['orderNumber'] = $order["increment_id"];
    $emailTemplateVariables['storeName'] = Mage::app()->getStore()->getFrontendName();
    $emailTemplateVariables['flagReason'] = $flagReason;

    $emailTemplate->send($alertEmailAddress, null, $emailTemplateVariables);   
  }

}
	 