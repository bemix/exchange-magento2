<?php

namespace Allsecureexchange\Allsecureexchange\Model\System\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Payment\Model\Method\AbstractMethod;

/**
 * Class BannerType
 */
class BannerType implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'none',
                'label' => __('No Banner')
            ],
            [
                'value' => 'light',
                'label' => __('Light Background')
            ],
            [
                'value' => 'dark',
                'label' => __('Dark Background')
            ]
        ];
    }
}
