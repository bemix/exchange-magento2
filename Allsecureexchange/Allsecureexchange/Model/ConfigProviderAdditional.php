<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Allsecureexchange\Allsecureexchange\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Payment\Model\Config as PaymentConfig;

class ConfigProviderAdditional implements ConfigProviderInterface
{
    /**
     * @var string[]
     */
    protected $methodCodes = [
        'allsecureexchange_sofort'
    ];

    /**
     * @var \Magento\Payment\Model\Method\AbstractMethod[]
     */
    protected $methods = [];

    /**
     * @var Escaper $escaper
     */
    protected $escaper;
    
    /**
     * @var \Magento\Payment\Model\Config $paymentConfig
     */
    protected $paymentConfig;

    /**
     * Constructor
     *
     * @param PaymentHelper $paymentHelper
     * @param Escaper $escaper
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        Escaper $escaper,
        PaymentConfig $paymentConfig
    ) {
        $this->paymentConfig = $paymentConfig;
        $this->escaper = $escaper;
        foreach ($this->methodCodes as $code) {
            $this->methods[$code] = $paymentHelper->getMethodInstance($code);
        }
    }

    /**
     * Get config
     *
     * @return string[] $config
     */
    public function getConfig()
    {
        $config = [];
        foreach ($this->methodCodes as $code) {
            if ($this->methods[$code]->isAvailable()) {
                $config['payment']['instructions'][$code] = $this->getInstructions($code);
                $config['payment'][$code]['redirectUrl'] = $this->methods[$code]->getCheckoutRedirectUrl();
            }
        }
        return $config;
    }

    /**
     * Get instructions text from config
     *
     * @param string $code
     *
     * @return string
     */
    protected function getInstructions($code)
    {
        return nl2br($this->escaper->escapeHtml($this->methods[$code]->getInstructions()));
    }
}
