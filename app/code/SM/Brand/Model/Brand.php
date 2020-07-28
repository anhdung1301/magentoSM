<?php


namespace SM\Brand\Model;

use Magento\Catalog\Helper\Category;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use SM\Brand\Helper\Data;
use SM\Brand\Model\ResourceModel\Brand\Collection;

/**
 * Brand Model
 */
class Brand extends AbstractModel
{
    /**
     * Brand's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Product collection factory
     *
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /** @var StoreManagerInterface */
    protected $_storeManager;

    /**
     * URL Model instance
     *
     * @var UrlInterface
     */
    protected $_url;

    /**
     * @var Category
     */
    protected $_brandHelper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ResourceModel\Brand|null $resource
     * @param Collection|null $resourceCollection
     * @param CollectionFactory $productCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $url
     * @param Data $brandHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ResourceModel\Brand $resource = null,
        Collection $resourceCollection = null,
        CollectionFactory $productCollectionFactory,
        StoreManagerInterface $storeManager,
        UrlInterface $url,
        Data $brandHelper,
        array $data = []
    )
    {
        $this->_storeManager = $storeManager;
        $this->_url = $url;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_brandHelper = $brandHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize customer model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('SM\Brand\Model\ResourceModel\Brand');
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    /**
     * Get category products collection
     *
     * @return AbstractDb
     */
    public function getProductCollection()
    {
        $collection = $this->_productCollectionFactory->create()->addAttributeToSelect('*')->addAttributeToFilter('product_brand', array('eq' => $this->getId()));
        return $collection;
    }

    public function getUrl()
    {
        $url = $this->_storeManager->getStore()->getBaseUrl();
        $route = $this->_brandHelper->getConfig('general_settings/route');
        $url_prefix = $this->_brandHelper->getConfig('general_settings/url_prefix');
        $urlPrefix = '';
        if ($url_prefix) {
            $urlPrefix = $url_prefix . '/';
        }
        $url_suffix = $this->_brandHelper->getConfig('general_settings/url_suffix');
        return $url . $urlPrefix . $this->getUrlKey() . $url_suffix;
    }

    /**
     * Retrive image URL
     *
     * @return string
     */
    public function getImageUrl()
    {
        $url = false;
        $image = $this->getImage();
        if ($image) {
            $url = $this->_storeManager->getStore()->getBaseUrl(
                    UrlInterface::URL_TYPE_MEDIA
                ) . $image;
        }
        return $url;
    }

    public function loadByBrandName($brand_name = "")
    {
        if ($brand_name) {
            $brand_id = $this->_getResource()->getBrandIdByName($brand_name);
            if ($brand_id) {
                $this->load((int)$brand_id);
            }
        }
        return $this;
    }

    public function saveProduct($product_id = "0")
    {
        if ($product_id) {
            $this->_getResource()->saveProduct($this, $product_id);
        }
        return $this;
    }

    public function deleteBrandsByProduct($product_id = "0")
    {
        if ($product_id) {
            $this->_getResource()->deleteBrandsByProduct($product_id);
        }
        return $this;
    }

    /**
     * Retrive thumbnail URL
     *
     * @return string
     */
    public function getThumbnailUrl()
    {
        $url = false;
        $thumbnail = $this->getThumbnail();
        if ($thumbnail) {
            $url = $this->_storeManager->getStore()->getBaseUrl(
                    UrlInterface::URL_TYPE_MEDIA
                ) . $thumbnail;
        }
        return $url;
    }
}
