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
    public function addIdFilter($tagIds)
    {
        $condition = '';

        if (is_array($tagIds)) {
            if (!empty($tagIds)) {
                $condition = ['in' => $tagIds];
            }
        } elseif (is_numeric($tagIds)) {
            $condition = $tagIds;
        } elseif (is_string($tagIds)) {
            $ids = explode(',', $tagIds);
            if (empty($ids)) {
                $condition = $tagIds;
            } else {
                $condition = ['in' => $ids];
            }
        }

        if ($condition) {
            $this->addFieldToFilter('tag_id', $condition);
        }

        return $this;
    }
}
