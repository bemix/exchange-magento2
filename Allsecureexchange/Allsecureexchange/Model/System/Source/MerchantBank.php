<?php

namespace Allsecureexchange\Allsecureexchange\Model\System\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Payment\Model\Method\AbstractMethod;

/**
 * Class MerchantBank
 */
class MerchantBank implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'none',
                'label' => __('Select')
            ],
            [
                'value' => 'hbm',
                'label' => __('Hipotekarna Banka')
            ],
            [
                'value' => 'aik',
                'label' => __('AIK Banka')
            ],
            [
                'value' => 'bib',
                'label' => __('Banca Intesa')
            ],
            [
                'value' => 'nlb-mne',
                'label' => __('NLB Banka Montenegro')
            ],
            [
                'value' => 'ckb',
                'label' => __('CKB Banka')
            ],
            [
                'value' => 'rfb-bih',
                'label' => __('Raiffeisen Bank')
            ],
        ];
    }
}
