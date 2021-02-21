<?php

namespace Allsecureexchange\Allsecureexchange\Controller\Payment;

use Allsecureexchange\Client\Transaction\Debit;
use Allsecureexchange\Client\Transaction\Preauthorize;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\UrlInterface;
use Magento\Payment\Helper\Data;
use Magento\Sales\Model\Order;

class Frontend extends Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $session;

    /**
     * @var \Magento\Checkout\Api\PaymentInformationManagementInterface
     */
    private $paymentInformation;

    /**
     * @var Data
     */
    private $paymentHelper;

    /**
     * @var \Allsecureexchange\Allsecureexchange\Helper\Data
     */
    private $allsecureexchangeHelper;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * Frontend constructor.
     * @param Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Api\PaymentInformationManagementInterface $paymentInformation,
        Data $paymentHelper,
        \Allsecureexchange\Allsecureexchange\Helper\Data $allsecureexchangeHelper,
        UrlInterface $urlBuilder,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->session = $checkoutSession;
        $this->paymentInformation = $paymentInformation;
        $this->paymentHelper = $paymentHelper;
        $this->urlBuilder = $urlBuilder;
        $this->allsecureexchangeHelper = $allsecureexchangeHelper;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $request = $this->getRequest()->getPost()->toArray();
        $response = $this->resultJsonFactory->create();

        $paymentMethod = 'allsecureexchange_creditcard';
	$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
	$store = $objectManager->get('Magento\Framework\Locale\Resolver');

        //TODO: SELECT CORRECT PAYMENT SETTINGS
        \Allsecureexchange\Client\Client::setApiUrl($this->allsecureexchangeHelper->getGeneralConfigData('host'));
        $client = new \Allsecureexchange\Client\Client(
            $this->allsecureexchangeHelper->getGeneralConfigData('username'),
            $this->allsecureexchangeHelper->getGeneralConfigData('password'),
            $this->allsecureexchangeHelper->getPaymentConfigData('api_key', $paymentMethod, null),
            $this->allsecureexchangeHelper->getPaymentConfigData('shared_secret', $paymentMethod, null), strtolower(substr($store->getLocale(), 0, 2 ))
        );

        $order = $this->session->getLastRealOrder();

	switch ($this->allsecureexchangeHelper->getPaymentConfigData('transaction_type', 'allsecureexchange_creditcard', null)) {
            case 'Preauthorize':
		$transaction = new Preauthorize();
		$transactionType = 'Preauthorize';
	        break;
		default:
	    case 'Debit':
	    	$transaction = new Debit();
		$transactionType = 'Debit';
		break;
        }
        if ($this->allsecureexchangeHelper->getPaymentConfigDataFlag('seamless', $paymentMethod)) {
            $token = (string) $request['token'];

            if (empty($token)) {
                die('empty token');
            }

            $transaction->setTransactionToken($token);
        }
        $transaction->addExtraData('3dsecure', 'OPTIONAL');
		
		$asx_transaction_order_id = $order->getIncrementId() .'-'. date('dmy').time();
        $transaction->setTransactionId($asx_transaction_order_id);
        $transaction->setAmount(\number_format($order->getGrandTotal(), 2, '.', ''));
        $transaction->setCurrency($order->getOrderCurrency()->getCode());

        $customer = new \Allsecureexchange\Client\Data\Customer();
        $customer->setFirstName($order->getCustomerFirstname());
        $customer->setLastName($order->getCustomerLastname());
        $customer->setEmail($order->getCustomerEmail());

	if(!empty($_SERVER['HTTP_CLIENT_IP'])){
		$customer->setIpAddress($_SERVER['HTTP_CLIENT_IP']);
	} elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$customer->setIpAddress($_SERVER['HTTP_X_FORWARDED_FOR']);
	} else{
		$customer->setIpAddress($_SERVER['REMOTE_ADDR']);
	}

        $billingAddress = $order->getBillingAddress();
        if ($billingAddress !== null) {
            $customer->setBillingAddress1($billingAddress->getStreet()[0]);
            $customer->setBillingPostcode($billingAddress->getPostcode());
            $customer->setBillingCity($billingAddress->getCity());
            $customer->setBillingCountry($billingAddress->getCountryId());
            $customer->setBillingPhone($billingAddress->getTelephone());
        }
        $shippingAddress = $order->getShippingAddress();
        if ($shippingAddress !== null) {
            $customer->setShippingCompany($shippingAddress->getCompany());
            $customer->setShippingFirstName($shippingAddress->getFirstname());
            $customer->setShippingLastName($shippingAddress->getLastname());
            $customer->setShippingAddress1($shippingAddress->getStreet()[0]);
            $customer->setShippingPostcode($shippingAddress->getPostcode());
            $customer->setShippingCity($shippingAddress->getCity());
            $customer->setShippingCountry($shippingAddress->getCountryId());
        }

        $transaction->setCustomer($customer);

        $baseUrl = $this->urlBuilder->getRouteUrl('allsecureexchange');

        $transaction->setSuccessUrl($this->urlBuilder->getUrl('checkout/onepage/success'));
        $transaction->setCancelUrl($baseUrl . 'payment/redirect?status=cancel' . '&txid='. $asx_transaction_order_id);
        $transaction->setErrorUrl($baseUrl . 'payment/redirect?status=error' . '&txid='. $asx_transaction_order_id );

        $transaction->setCallbackUrl($baseUrl . 'payment/callback');

        $this->prepare3dSecure2Data($transaction, $order);

	switch ($this->allsecureexchangeHelper->getPaymentConfigData('transaction_type', 'allsecureexchange_creditcard', null)) {
		case 'Preauthorize':
			$paymentResult = $client->preauthorize($transaction);
                	break;
			default:
		case 'Debit':
			$paymentResult = $client->debit($transaction);
			break;
        }
       if (!$paymentResult->isSuccess()) {
            $response->setData([
                'type' => 'error',
                'errors' => $paymentResult->getFirstError()->getMessage()
            ]);
            return $response;
        }

        if ($paymentResult->getReturnType() == \Allsecureexchange\Client\Transaction\Result::RETURN_TYPE_ERROR) {

            $response->setData([
                'type' => 'error',
                'errors' => $paymentResult->getFirstError()->getMessage()
            ]);
            return $response;

        } elseif ($paymentResult->getReturnType() == \Allsecureexchange\Client\Transaction\Result::RETURN_TYPE_REDIRECT) {

            $response->setData([
                'type' => 'redirect',
                'url' => $paymentResult->getRedirectUrl()
            ]);

            return $response;

        } elseif ($paymentResult->getReturnType() == \Allsecureexchange\Client\Transaction\Result::RETURN_TYPE_PENDING) {
            //payment is pending, wait for callback to complete

            //setCartToPending();

        } elseif ($paymentResult->getReturnType() == \Allsecureexchange\Client\Transaction\Result::RETURN_TYPE_FINISHED) {

            $response->setData([
                'type' => 'finished',
            ]);
        }

        return $response;
    }
    
    /**
     * @throws Exception
     * @return array
     */
    
    private function prepare3dSecure2Data($transaction, $order)
    {
        $transaction->addExtraData('3ds:channel', '02'); // Browser
        $transaction->addExtraData('3ds:authenticationIndicator ', '01'); // Payment transaction

        if ($order->getCustomerIsGuest()) {
            $transaction->addExtraData('3ds:cardholderAuthenticationMethod', '01');
            $transaction->addExtraData('3ds:cardholderAccountAgeIndicator', '01');
        } else {
            $transaction->addExtraData('3ds:cardholderAuthenticationMethod', '02');
            // $transaction->addExtraData('3ds:cardholderAccountDate', \date('Y-m-d', $order->getCustomer()->getCreatedAtTimestamp()));
        }

        // $transaction->addExtraData('3ds:shipIndicator', \date('Y-m-d', $order->getCustomer()->getCreatedAtTimestamp()));

        if ($order->getShippigAddressId() == $order->getBillingAddressId()) {
            $transaction->addExtraData('3ds:billingShippingAddressMatch ', 'Y');
        } else {
            $transaction->addExtraData('3ds:billingShippingAddressMatch ', 'N');
        }

    }
}
