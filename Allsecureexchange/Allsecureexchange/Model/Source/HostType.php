<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Allsecureexchange\Allsecureexchange\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 *
 * Allsecureexchange Transaction Type Dropdown source
 */
class HostType implements ArrayInterface
{
    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'https://asxgw.com/',
                'label' => __('Live')
            ],
            [
                'value' => 'https://asxgw.paymentsandbox.cloud/',
                'label' => __('Test')
            ]
        ];
    }
}