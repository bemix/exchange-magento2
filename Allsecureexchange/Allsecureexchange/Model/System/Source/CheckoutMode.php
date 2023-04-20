<?php

namespace Allsecureexchange\Allsecureexchange\Model\System\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Payment\Model\Method\AbstractMethod;

/**
 * Class CheckoutMode
 */
class CheckoutMode implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'paymentjs',
                'label' => __('Payment.js Javascript Integration')
            ],
            [
                'value' => 'redirect',
                'label' => __('Full-Page Redirect')
            ]
        ];
    }
}
