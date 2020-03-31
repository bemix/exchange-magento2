<?php

namespace allsecureexchange\allsecureexchange\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

final class ConfigProvider implements ConfigProviderInterface
{
    const CREDITCARD_CODE = 'allsecureexchange_creditcard';

    /**
     * @var \allsecureexchange\allsecureexchange\Helper\Data
     */
    private $allsecureexchangeHelper;

    public function __construct(\allsecureexchange\allsecureexchange\Helper\Data $allsecureexchangeHelper)
    {
        $this->allsecureexchangeHelper = $allsecureexchangeHelper;
    }

    public function getConfig()
    {
        return [
            'payment' => [
                static::CREDITCARD_CODE => [
                    'seamless' => $this->allsecureexchangeHelper->getPaymentConfigDataFlag('seamless', static::CREDITCARD_CODE),
                    'integration_key' => $this->allsecureexchangeHelper->getPaymentConfigData('integration_key', static::CREDITCARD_CODE)
                ]
            ],
        ];
    }
}
