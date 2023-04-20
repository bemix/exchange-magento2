<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Allsecureexchange\Allsecureexchange\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Framework\DataObject;

class AddAdditionalData extends AbstractDataAssignObserver
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $dataObject = $this->readDataArgument($observer);

        $additionalData = $dataObject->getData(PaymentInterface::KEY_ADDITIONAL_DATA);

        if (!is_array($additionalData)) {
            return;
        }

        $additionalData = new DataObject($additionalData);
        
        $transaction_token = $additionalData->getData('allsecurepay_transaction_token');

        $paymentModel = $this->readPaymentModelArgument($observer);
    
        $paymentModel->setAdditionalInformation(
            'allsecurepay_transaction_token',
            $transaction_token
        );
    }
}
