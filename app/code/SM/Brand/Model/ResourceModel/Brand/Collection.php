<?php
namespace SM\Brand\Model\ResourceModel\Brand;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'brand_id';
    protected $_eventPrefix = 'sm_Brand_collection';
    protected $_eventObject = 'brand_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SM\Brand\Model\Brand', 'SM\Brand\Model\ResourceModel\Brand');
    }

}
