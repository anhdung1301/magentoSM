<?php

namespace SM\SumUp\Model;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime;
use SM\SumUp\Helper\Data;
use SM\SumUp\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use SM\SumUp\Model\ResourceModel\Post as PostResource;
use SM\SumUp\Model\ResourceModel\Post\Collection;
use SM\SumUp\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use SM\SumUp\Model\ResourceModel\Tag\CollectionFactory;

/**
 * @method Post setName($name)
 * @method Post setShortDescription($shortDescription)
 * @method Post setPostContent($postContent)
 * @method Post setImage($image)
 * @method Post setViews($views)
 * @method Post setEnabled($enabled)
 * @method Post setUrlKey($urlKey)
 * @method Post setInRss($inRss)
 * @method Post setAllowComment($allowComment)
 * @method Post setMetaTitle($metaTitle)
 * @method Post setMetaDescription($metaDescription)
 * @method Post setMetaKeywords($metaKeywords)
 * @method Post setMetaRobots($metaRobots)
 * @method mixed getName()
 * @method mixed getPostContent()
 * @method mixed getImage()
 * @method mixed getViews()
 * @method mixed getEnabled()
 * @method mixed getUrlKey()
 * @method mixed getInRss()
 * @method mixed getAllowComment()
 * @method mixed getMetaTitle()
 * @method mixed getMetaDescription()
 * @method mixed getMetaKeywords()
 * @method mixed getMetaRobots()
 * @method Post setCreatedAt(string $createdAt)
 * @method string getCreatedAt()
 * @method Post setUpdatedAt(string $updatedAt)
 * @method string getUpdatedAt()
 * @method Post setTagsData(array $data)
 * @method Post setProductsData(array $data)
 * @method array getTagsData()
 * @method array getProductsData()
 * @method Post setIsChangedTagList(bool $flag)
 * @method Post setIsChangedProductList(bool $flag)
 * @method Post setIsChangedCategoryList(bool $flag)
 * @method bool getIsChangedTagList()
 * @method bool getIsChangedCategoryList()
 * @method Post setAffectedTagIds(array $ids)
 * @method Post setAffectedEntityIds(array $ids)
 * @method Post setAffectedCategoryIds(array $ids)
 * @method bool getAffectedTagIds()
 * @method bool getAffectedCategoryIds()
 * @method array getCategoriesIds()
 * @method Post setCategoriesIds(array $categoryIds)
 * @method array getTagsIds()
 * @method Post setTagsIds(array $tagIds)
 */
class Post extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'sm_blog_post';
    /**
     * Tag Collection
     *
     * @var ResourceModel\Tag\Collection
     */
    public $tagCollection;
    /**
     * Blog Category Collection
     *
     * @var ResourceModel\Category\Collection
     */
    public $categoryCollection;
    /**
     * Tag Collection Factory
     *
     * @var CollectionFactory
     */
    public $tagCollectionFactory;
    /**
     * Blog Category Collection Factory
     *
     * @var CategoryCollectionFactory
     */
    public $categoryCollectionFactory;
    /**
     * Post Collection Factory
     *
     * @var PostCollectionFactory
     */
    public $postCollectionFactory;
    /**
     * Related Post Collection
     *
     * @var Collection
     */
    public $relatedPostCollection;
    /**
     * Previous Post Collection
     *
     * @var Collection
     */
    public $prevPostCollection;
    /**
     * Next Post Collection
     *
     * @var Collection
     */
    public $nextPostCollection;
    /**
     * @var DateTime
     */
    public $dateTime;
    /**
     * @var Data
     */
    public $helperData;
    /**
     * @var ProductCollectionFactory
     */
    public $productCollectionFactory;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public $productCollection;
    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'sm_blog_post';
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sm_blog_post';

    /**
     * Post constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param DateTime $dateTime
     * @param Data $helperData
     * @param CollectionFactory $tagCollectionFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param PostCollectionFactory $postCollectionFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DateTime $dateTime,
        Data $helperData,
        CollectionFactory $tagCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        PostCollectionFactory $postCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->tagCollectionFactory = $tagCollectionFactory;

        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->helperData = $helperData;
        $this->dateTime = $dateTime;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @param bool $shorten
     *
     * @return mixed|string
     */
    public function getShortDescription($shorten = false)
    {
        $shortDescription = $this->getData('short_description');

        $maxLength = 200;
        if ($shorten && strlen($shortDescription) > $maxLength) {
            $shortDescription = substr($shortDescription, 0, $maxLength) . '...';
        }

        return $shortDescription;
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getUrl($store = null)
    {
        return $this->helperData->getBlogUrl($this, Data::TYPE_POST, $store);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * get entity default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];
        $values['enabled'] = '1';

        return $values;
    }

    /**
     * @return ResourceModel\Tag\Collection
     */
    public function getSelectedTagsCollection()
    {
        if ($this->tagCollection === null) {
            $collection = $this->tagCollectionFactory->create();
            $collection->getSelect()->join(
                $this->getResource()->getTable('sm_blog_post_tag'),
                'main_table.tag_id=' . $this->getResource()->getTable('sm_blog_post_tag') . '.tag_id AND '
                . $this->getResource()->getTable('sm_blog_post_tag') . '.post_id=' . $this->getId(),
                ['position']
            )->where("main_table.enabled='1'");
            $this->tagCollection = $collection;
        }

        return $this->tagCollection;
    }

    /**
     * @return ResourceModel\Category\Collection
     */
    public function getSelectedCategoriesCollection()
    {
        if ($this->categoryCollection === null) {
            $collection = $this->categoryCollectionFactory->create();
            $collection->join(
                $this->getResource()->getTable('sm_blog_post_category'),
                'main_table.category_id=' . $this->getResource()->getTable('sm_blog_post_category') .
                '.category_id AND ' . $this->getResource()->getTable('sm_blog_post_category') . '.post_id="'
                . $this->getId() . '"',
                ['position']
            );
            $this->categoryCollection = $collection;
        }

        return $this->categoryCollection;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getCategoryIds()
    {
        if (!$this->hasData('category_ids')) {
            $ids = $this->_getResource()->getCategoryIds($this);
            $this->setData('category_ids', $ids);
        }

        return (array)$this->_getData('category_ids');
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getTagIds()
    {
        if (!$this->hasData('tag_ids')) {
            $ids = $this->_getResource()->getTagIds($this);

            $this->setData('tag_ids', $ids);
        }

        return (array)$this->_getData('tag_ids');
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getSelectedProductsCollection()
    {
        if ($this->productCollection === null) {
            $collection = $this->productCollectionFactory->create();
            $collection->getSelect()->join(
                $this->getResource()->getTable('sm_blog_post_product'),
                'e.entity_id=' . $this->getResource()->getTable('sm_blog_post_product')
                . '.entity_id AND ' . $this->getResource()->getTable('sm_blog_post_product') . '.post_id='
                . $this->getId(),
                ['position']
            );
            $this->productCollection = $collection;
        }

        return $this->productCollection;
    }

    /**
     * @return array|mixed
     */
    public function getProductsPosition()
    {
        if (!$this->getId()) {
            return [];
        }
        $array = $this->getData('products_position');
        if ($array === null) {
            $array = $this->getResource()->getProductsPosition($this);
            $this->setData('products_position', $array);
        }

        return $array;
    }

    /**
     * get previous post
     * @return Collection
     */
    public function getPrevPost()
    {
        if ($this->prevPostCollection === null) {
            $collection = $this->postCollectionFactory->create();
            $collection->addFieldToFilter('post_id', ['lt' => $this->getId()])
                ->setOrder('post_id', 'DESC')->setPageSize(1)->setCurPage(1);
            $this->prevPostCollection = $collection;
        }

        return $this->prevPostCollection;
    }

    /**
     * get next post
     * @return Collection
     */
    public function getNextPost()
    {
        if ($this->nextPostCollection === null) {
            $collection = $this->postCollectionFactory->create();
            $collection->addFieldToFilter('post_id', ['gt' => $this->getId()])
                ->setOrder('post_id', 'ASC')->setPageSize(1)->setCurPage(1);
            $this->nextPostCollection = $collection;
        }

        return $this->nextPostCollection;
    }

    public function getDataByName($name)
    {
        $collection = $this->postCollectionFactory->create()
            ->addFieldToFilter('name', $name);
        return $collection->getData();
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(PostResource::class);
    }
}
