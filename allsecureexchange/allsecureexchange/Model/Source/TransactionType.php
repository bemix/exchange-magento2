<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace allsecureexchange\allsecureexchange\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 *
 * allsecureexchange Transaction Type Dropdown source
 */
class TransactionType implements ArrayInterface
{
    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'Debit',
                'label' => __('Debit')
            ],
            [
                'value' => 'Preauthorize',
                'label' => __('Preauthorize')
            ]
        ];
    }
}