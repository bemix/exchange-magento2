<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Allsecureexchange\Allsecureexchange\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Framework\View\Asset\Repository;
use Magento\Payment\Model\Config as PaymentConfig;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string[]
     */
    protected $methodCodes = [
        'allsecureexchange'
    ];

    /**
     * @var \Magento\Payment\Model\Method\AbstractMethod[]
     */
    protected $methods = [];

    /**
     * @var Escaper
     */
    protected $escaper;
    
    /**
     * @var \Magento\Framework\View\Asset\Repository $assetRepo
     */
    protected $assetRepo;
    
    /**
     * @var \Magento\Payment\Model\Config
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
        Repository $assetRepo,
        PaymentConfig $paymentConfig
    ) {
        $this->paymentConfig = $paymentConfig;
        $this->escaper = $escaper;
        $this->assetRepo = $assetRepo;
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
                $config['payment'][$code]['image_path'] = $this->assetRepo->getUrl('Allsecureexchange_Allsecureexchange::images/');
                $config['payment'][$code]['months'] = $this->getExpiryMonths();
                $config['payment'][$code]['years'] = $this->getExpiryYears();
                $config['payment'][$code]['checkout_mode'] = $this->methods[$code]->getConfigValue('checkout_mode');
                $config['payment'][$code]['integration_key'] = $this->methods[$code]->getConfigValue('integration_key');
                $config['payment'][$code]['card_supported'] = strtolower($this->methods[$code]->getConfigValue('card_supported'));
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
    
    /**
     * Get Expiry Month Values
     *
     * @return array
     */
    protected function getExpiryMonths()
    {
        $months[0] = __('Month');
        $months = array_merge($months, $this->paymentConfig->getMonths());
        return $months;;
    }
    
    /**
     * Get Expiry Year Values
     *
     * @return array
     */
    protected function getExpiryYears()
    {
        $years = $this->paymentConfig->getYears();
        $years = [0 => __('Year')] + $years;
        return $years;
    }
}
