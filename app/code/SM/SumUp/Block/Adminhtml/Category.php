<?php


namespace SM\SumUp\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Tag
 * @package Mageplaza\Blog\Block\Adminhtml
 */
class Category extends Container
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller     = 'adminhtml_category';
        $this->_blockGroup     = 'SM_SumUp';
        $this->_headerText     = __('Category');
        $this->_addButtonLabel = __('Create New Category');

        parent::_construct();
    }
}
