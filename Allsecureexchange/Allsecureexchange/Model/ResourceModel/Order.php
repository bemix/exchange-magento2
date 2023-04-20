<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Allsecureexchange\Allsecureexchange\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Order extends AbstractDb
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('allsecureexchange_order', 'id');
    }
}
