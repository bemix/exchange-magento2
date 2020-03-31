<?php
declare(strict_types=1);

namespace allsecureexchange\allsecureexchange\Block\Checkout;

use Magento\Framework\View\Element\Template;

class PaymentJsLoader extends Template
{
    /**
     * @var \allsecureexchange\allsecureexchange\Helper\Data
     */
    private $allsecureexchangeHelper;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param \allsecureexchange\allsecureexchange\Helper\Data $allsecureexchangeHelper,
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \allsecureexchange\allsecureexchange\Helper\Data $allsecureexchangeHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->allsecureexchangeHelper = $allsecureexchangeHelper;
    }

    public function getHost()
    {
        return $this->allsecureexchangeHelper->getGeneralConfigData('host');
    }
}
