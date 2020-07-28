<?php

namespace SM\Brand\Block\Adminhtml\Brand\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Brand Information'));
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareLayout()
    {
        $this->addTab(
            'general',
            [
                'label' => __('Brand Information'),
                'content' => $this->getLayout()->createBlock('SM\Brand\Block\Adminhtml\Brand\Edit\Tab\Main')->toHtml()
            ]
        );

        $this->addTab(
            'products',
            [
                'label' => __('Products'),
                'url' => $this->getUrl('brand/*/products', ['_current' => true]),
                'class' => 'ajax'
            ]
        );

    }
}
