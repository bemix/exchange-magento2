<?php

namespace Allsecureexchange\Allsecureexchange\Model\System\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Payment\Model\Method\AbstractMethod;

/**
 * Class TransactionType
 */
class TransactionType implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'debit',
                'label' => __('Debit')
            ],
            [
                'value' => 'authorize',
                'label' => __('Preauthorize')
            ]
        ];
    }
}
