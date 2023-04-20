<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Allsecureexchange\Allsecureexchange\Model;

use Magento\Framework\Model\AbstractModel;

class Order extends AbstractModel
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(\Allsecureexchange\Allsecureexchange\Model\ResourceModel\Order::class);
    }
}
