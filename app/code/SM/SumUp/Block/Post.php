<?php

namespace SM\SumUp\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use SM\SumUp\Model\ResourceModel\Post\CollectionFactory;
use SM\SumUp\Model\ResourceModel\Tag\CollectionFactory as TagFactory;

class Post extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    protected $_tagFactory;
    /**
     * Post constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        CollectionFactory $collectionFactory,
        TagFactory $tagFactory
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_storeManager = $storeManager;
        $this->_tagFactory = $tagFactory;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function getPostCollection()
    {
        $post = $this->_collectionFactory->create()
            ->addFieldToFilter('enabled', 1);
        return $post;
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function geturlBase()
    {
        return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }
    public function getTagCollection(){
         $tag = $this->_tagFactory->create()
            ->addFieldToFilter('enabled', 1);
         return $tag;

    }
}
