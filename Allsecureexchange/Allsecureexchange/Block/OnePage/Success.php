<?php

namespace Allsecureexchange\Allsecureexchange\Block\OnePage;

use Magento\Framework\View\Element\Template;

class Success extends \Magento\Framework\View\Element\Template
{
	/**
     * @var \Allsecureexchange\Allsecureexchange\Helper\Data
     */
    private $allsecureexchangeHelper;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param \Allsecureexchange\Allsecureexchange\Helper\Data $allsecureexchangeHelper,
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Allsecureexchange\Allsecureexchange\Helper\Data $allsecureexchangeHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->allsecureexchangeHelper = $allsecureexchangeHelper;
    }
	
    public function getCustomSuccess()
    {
		\Allsecureexchange\Client\Client::setApiUrl($this->allsecureexchangeHelper->getGeneralConfigData('host'));
		$client = new \Allsecureexchange\Client\Client(
            $this->allsecureexchangeHelper->getGeneralConfigData('username'),
            $this->allsecureexchangeHelper->getGeneralConfigData('password'),
            $this->allsecureexchangeHelper->getPaymentConfigData('api_key', 'allsecureexchange_creditcard', null),
            $this->allsecureexchangeHelper->getPaymentConfigData('shared_secret', 'allsecureexchange_creditcard', null)
        );
		$statusRequestData = new \Allsecureexchange\Client\StatusApi\StatusRequestData();
		$statusRequestData->setMerchantTransactionId($_REQUEST['txid']);
		$statusResult = $client->sendStatusRequest($statusRequestData);  
		/* collect transaction data */
		$result = $statusResult -> getTransactionStatus();
		$transactionType = $statusResult -> getTransactionType();
		$amount = $statusResult -> getAmount();
		$currency = $statusResult -> getCurrency();
		$paymentMethod = $statusResult -> getpaymentMethod ();
		$cardData = $statusResult -> getreturnData();
		$cardHolder = $cardData -> getcardHolder();
		$binBrand = $cardData -> getbinBrand();
		$expiryMonth = $cardData -> getexpiryMonth();
		$expiryYear = $cardData -> getexpiryYear();
		$firstSixDigits = $cardData -> getfirstSixDigits();
		$lastFourDigits = $cardData -> getlastFourDigits();
		$extraData = $statusResult -> getextraData();
		if ( isset($extraData['authCode']) ) {
			$authCode = $extraData['authCode'];
		} else {
			$authCode = '';
		}
		$transactionUuid = $statusResult -> getTransactionUuid();
		$eci = $cardData -> getEci();
		$timestamp = date("Y-m-d H:i:s"); 
		
		/* Display Transaction Details */
        return '<h3>'. __("Transaction Details:").'</h3>
		<ul>
		<li>'. __("Approval Code: ") .' '. $authCode .'</li>
		<li>'. __("Response: ") .' '. $result .'</li>
		<li>'. __("Transaction UUID: ") .' '. $transactionUuid .'</li>
		<li>'. __("Payment Method: ") .' '. $paymentMethod .'</li>
		<li>'. __("Card Type: ") .' '. $binBrand . '****'. $lastFourDigits .'</li>
		<li>'. __("Payment Type: ") .' '. $transactionType .'</li>
		<li>'. __("3D Status (Eci): ") .' '. $eci .'</li>
		<li>'. __("Transaction Time: ") .' '. $timestamp .'</li>
		</ul>'; 
	}
}