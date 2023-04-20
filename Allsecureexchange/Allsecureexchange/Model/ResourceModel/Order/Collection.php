<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Allsecureexchange\Allsecureexchange\Model\ResourceModel\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Allsecureexchange\Allsecureexchange\Model\Order::class,
            \Allsecureexchange\Allsecureexchange\Model\ResourceModel\Order::class
        );
    }
}
