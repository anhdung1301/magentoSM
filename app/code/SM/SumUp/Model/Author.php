<?php

namespace SM\SumUp\Model;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Author extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'sm_blog_author';

    protected $_cacheTag = 'sm_blog_author';

    protected $_eventPrefix = 'sm_blog_author';


    protected function _construct()
    {
        $this->_init('SM\SumUp\Model\ResourceModel\Author');
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
