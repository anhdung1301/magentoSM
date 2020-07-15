<?php
namespace SM\SumUp\Model\ResourceModel\Tag;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'tag_id';
    protected $_eventPrefix = 'sm_sumup_tag_collection';
    protected $_eventObject = 'tag_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SM\SumUp\Model\Tag', 'SM\SumUp\Model\ResourceModel\Tag');
    }

}
