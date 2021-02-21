<?php

declare(strict_types=1);

namespace Allsecureexchange\Allsecureexchange\Controller\Payment;

use Magento\Backend\App\Action\Context;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Payment\Helper\Data;
use Magento\Sales\Model\Order;							
use Magento\Framework\Controller\Result\Redirect as RedirectResult;
use Magento\Framework\Controller\ResultFactory;

class Redirect extends Action implements CsrfAwareActionInterface
{
    const CHECKOUT_URL = 'checkout/cart';

    /**
     * @var Session
     */
    private $checkoutSession;
	
	/**
     * @var \Allsecureexchange\Allsecureexchange\Helper\Data
     */
    private $allsecureexchangeHelper;

    /**
     * Redirect constructor.
     * @param Context $context
     * @param Session $checkoutSession
     */
    public function __construct(Context $context, Session $checkoutSession,	\Allsecureexchange\Allsecureexchange\Helper\Data $allsecureexchangeHelper)
    {
        parent::__construct($context);
		$this->checkoutSession = $checkoutSession;
		$this->allsecureexchangeHelper = $allsecureexchangeHelper;
    }

    public function execute()
    {
        /**
         * @var $resultRedirect RedirectResult
         */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $methodName = $this->getRequest()->getParam('method');

        $this->checkoutSession->restoreQuote();
		//TODO: SELECT CORRECT PAYMENT SETTINGS
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
		$gatewayDecline = $statusResult->getFirstError();
		$gatewayErrorCode = $gatewayDecline->getCode();
		$gatewayErrorMessage = $gatewayDecline->getMessage();
        $this->messageManager->addNoticeMessage($gatewayErrorCode . ': ' . __($gatewayErrorCode));
        $resultRedirect->setPath(self::CHECKOUT_URL, ['_secure' => true]);

        return $resultRedirect;
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
