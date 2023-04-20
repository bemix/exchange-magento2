<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Allsecureexchange\Allsecureexchange\Block;

class Head extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Allsecureexchange\Allsecureexchange\Helper\Data $helper
     */
    protected $helper;
    
    /**
     * @var \Allsecureexchange\Allsecureexchange\Model\Pay $payment
     */
    protected $payment;

    /**
     * Constructor
     *
     * @param \Allsecureexchange\Allsecureexchange\Helper\Data $helper
     * @param \Allsecureexchange\Allsecureexchange\Model\Pay $payment
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param array $data
     */
    public function __construct(
        \Allsecureexchange\Allsecureexchange\Helper\Data $helper,
        \Allsecureexchange\Allsecureexchange\Model\Pay $payment,
        \Magento\Catalog\Block\Product\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->payment = $payment;
    }
    
    /**
     * Get Helper Class object
     *
     * return Helper object
     */
    public function getHelper()
    {
        return $this->helper;
    }
    
    /**
     * Get Payment Class object
     *
     * return payment object
     */
    public function getPayment()
    {
        return $this->payment;
    }
}
