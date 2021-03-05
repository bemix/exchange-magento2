<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Allsecureexchange\Allsecureexchange\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 *
 * allsecureecommerce Card Type Dropdown source
 */
class CardType implements ArrayInterface
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
                'value' => 'MAESTRO',
                'label' => __('MAESTRO')
            ],
			            [
                'value' => 'MASTERCARD',
                'label' => __('MASTERCARD')
            ],
			[
                'value' => 'DINACARD',
                'label' => __('DINACARD')
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
            ]
                        
        ];
    }
}
