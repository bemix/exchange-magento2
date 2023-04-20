<?php

namespace Allsecureexchange\Allsecureexchange\Model\System\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Payment\Model\Method\AbstractMethod;

/**
 * Class OperationMode
 */
class OperationMode implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'test',
                'label' => __('Test')
            ],
            [
                'value' => 'live',
                'label' => __('Live')
            ]
        ];
    }
}
