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
        $allsecurepay_pay_installment = $additionalData->getData('allsecurepay_pay_installment');
        $allsecurepay_installment_number = $additionalData->getData('allsecurepay_installment_number');

        $paymentModel = $this->readPaymentModelArgument($observer);
    
        $paymentModel->setAdditionalInformation(
            'allsecurepay_transaction_token',
            $transaction_token
        );
        
        $paymentModel->setAdditionalInformation(
            'allsecurepay_pay_installment',
            $allsecurepay_pay_installment
        );
        
        $paymentModel->setAdditionalInformation(
            'allsecurepay_installment_number',
            $allsecurepay_installment_number
        );
    }
}
