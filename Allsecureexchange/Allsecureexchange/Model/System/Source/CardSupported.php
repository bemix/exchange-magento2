<?php

namespace Allsecureexchange\Allsecureexchange\Model\System\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Payment\Model\Method\AbstractMethod;

/**
 * Class CardSupported
 */
class CardSupported implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'VISA',
                'label' => __('VISA')
            ],
            [
                'value' => 'MASTERCARD',
                'label' => __('MASTERCARD')
            ],
            [
                'value' => 'MAESTRO',
                'label' => __('MAESTRO')
            ],
            [
                'value' => 'AMEX',
                'label' => __('AMEX')
            ],
            [
                'value' => 'DINERS',
                'label' => __('DINERS')
            ],
            [
                'value' => 'JCB',
                'label' => __('JCB')
            ],
            [
                'value' => 'DINACARD',
                'label' => __('DINA')
            ],
            [
                'value' => 'DISCOVER',
                'label' => __('DISCOVER')
            ],
        ];
    }
}
