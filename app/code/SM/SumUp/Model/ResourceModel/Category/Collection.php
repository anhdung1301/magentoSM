<?php
namespace SM\SumUp\Model\ResourceModel\Category;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'category_id';
    protected $_eventPrefix = 'sm_sumup_category_collection';
    protected $_eventObject = 'category_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SM\SumUp\Model\Category', 'SM\SumUp\Model\ResourceModel\Category');
    }

}
