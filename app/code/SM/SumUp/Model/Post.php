<?php

namespace SM\SumUp\Model;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Post extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'sm_blog_post';

    protected $_cacheTag = 'sm_blog_post';

    protected $_eventPrefix = 'sm_blog_post';


    protected function _construct()
    {
        $this->_init('SM\SumUp\Model\ResourceModel\Post');
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
