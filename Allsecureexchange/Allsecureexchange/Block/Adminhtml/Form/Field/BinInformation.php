<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Allsecureexchange\Allsecureexchange\Block\Adminhtml\Form\Field;


class BinInformation extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * Prepare to render.
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn('bin', ['label' => __('BIN')]);
        $this->addColumn('installments', ['label' => __('Allowed Installments')]);
 
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
}
