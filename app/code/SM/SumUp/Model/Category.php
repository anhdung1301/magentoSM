<?php

namespace SM\SumUp\Model;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Category extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'sm_blog_category';

    protected $_cacheTag = 'sm_blog_category';

    protected $_eventPrefix = 'sm_blog_category';


    protected function _construct()
    {
        $this->_init('SM\SumUp\Model\ResourceModel\Category');
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
