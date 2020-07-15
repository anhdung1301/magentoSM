<?php

namespace SM\SumUp\Model;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Tag extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'sm_blog_tag';

    protected $_cacheTag = 'sm_blog_tag';

    protected $_eventPrefix = 'sm_blog_tag';


    protected function _construct()
    {
        $this->_init('SM\SumUp\Model\ResourceModel\Tag');
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
