<?php
namespace SM\SumUp\Model\ResourceModel\Author;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'user_id';
    protected $_eventPrefix = 'sm_sumup_author_collection';
    protected $_eventObject = 'author_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SM\SumUp\Model\Author', 'SM\SumUp\Model\ResourceModel\Author');
    }

}
