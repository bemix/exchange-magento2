<?php
declare(strict_types=1);

namespace Allsecureexchange\Allsecureexchange\Block\Checkout;

use Magento\Framework\View\Element\Template;

class PaymentJsLoader extends Template
{
    /**
     * @var \Allsecureexchange\Allsecureexchange\Helper\Data
     */
    private $allsecureexchangeHelper;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param \Allsecureexchange\Allsecureexchange\Helper\Data $allsecureexchangeHelper,
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Allsecureexchange\Allsecureexchange\Helper\Data $allsecureexchangeHelper,
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
