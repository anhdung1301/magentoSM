<?php

namespace SM\Brand\Block;

use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Cms\Model\Page;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use SM\Brand\Helper\Data;
use SM\Brand\Model\Brand;

class Brandpage extends Template
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Data
     */
    protected $_brandHelper;

    /**
     * @var Brand
     */
    protected $_brand;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Data $brandHelper
     * @param Brand $brand
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Data $brandHelper,
        Brand $brand,
        array $data = []
    )
    {
        $this->_brand = $brand;
        $this->_coreRegistry = $registry;
        $this->_brandHelper = $brandHelper;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        parent::_construct();

        $itemsperpage = (int)$this->getConfig('brand_list_page/item_per_page', 12);
        $brand = $this->_brand;
        $brandCollection = $brand->getCollection()
            ->addFieldToFilter('enabled', 1)
            ->setOrder('name', 'ASC');
        $this->setCollection($brandCollection);

        $template = '';
        $layout = $this->getConfig('brand_list_page/layout');
        if ($layout == 'grid') {
            $template = 'brandlistpage_grid.phtml';
        } else {
            $template = 'brandlistpage_list.phtml';
        }
        if (!$this->hasData('template')) {
            $this->setTemplate($template);
        }
    }

    public function getConfig($key, $default = '')
    {
        $result = $this->_brandHelper->getConfig($key);
        if (!$result) {

            return $default;
        }
        return $result;
    }

    /**
     * Set brand collection
     * @param Brand
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this->_collection;
    }

    /**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $page_title = "All Brand";

        $this->_addBreadcrumbs();
        $this->pageConfig->addBodyClass('magetop-brandlist');
        if ($page_title) {
            $this->pageConfig->getTitle()->set($page_title);
        }
        return parent::_prepareLayout();
    }

    /**
     * Prepare breadcrumbs
     *
     * @param Page $brand
     * @return void
     * @throws LocalizedException
     */
    protected function _addBreadcrumbs()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $brandRoute = $this->_brandHelper->getConfig('<general_settings/>route');

        $page_title = $this->_brandHelper->getConfig('brand_list_page/page_title');

        if ($breadcrumbsBlock) {

            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $baseUrl
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'shopbybrand',
                [
                    'label' => 'All Brand',
                    'title' => "All Brand",
                    'link' => $brandRoute
                ]
            );
        }
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $collection = $this->getCollection();
        $toolbar = $this->getToolbarBlock();

        // set collection to toolbar and apply sort
        if ($toolbar) {
            $itemsperpage = (int)$this->getConfig('brand_list_page/item_per_page', 12);
            $toolbar->setData('_current_limit', $itemsperpage)->setCollection($collection);
            $this->setChild('toolbar', $toolbar);
        }
        return parent::_beforeToHtml();
    }

    /**
     * Retrive brand collection
     * @param Brand
     */
    public function getCollection()
    {
        $this->_collection->getSelect()->reset(Select::ORDER);
        $this->_collection->setOrder('name', 'ASC');
        return $this->_collection;
    }

    /**
     * Retrieve Toolbar block
     *
     * @return Toolbar
     */
    public function getToolbarBlock()
    {
        $block = $this->getLayout()->getBlock('SMbrand_toolbar');
        if ($block) {
            $block->setDefaultOrder("name");
            $block->removeOrderFromAvailableOrders("price");
            return $block;
        }
    }
}
