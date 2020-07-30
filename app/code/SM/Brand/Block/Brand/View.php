<?php

namespace SM\Brand\Block\Brand;

use Magento\Catalog\Helper\Category;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Cms\Model\Page;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use SM\Brand\Helper\Data;
use Magento\Directory\Model\CountryFactory;
class View extends Template
{
    /**
     * @var CountryFactory
     */
    protected $_countryFactory;
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * Catalog layer
     *
     * @var Layer
     */
    protected $_catalogLayer;

    /**
     * @var Category
     */
    protected $_brandHelper;


    /**
     * @param Context $context
     * @param Resolver $layerResolver
     * @param Registry $registry
     * @param \SM\Brand\Helper\Data $brandHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Resolver $layerResolver,
        Registry $registry,
        Data $brandHelper,
        CountryFactory $countryFactory,
        array $data = []
    )
    {
        $this->_brandHelper = $brandHelper;
        $this->_catalogLayer = $layerResolver->get();
        $this->_coreRegistry = $registry;
        $this->_countryFactory = $countryFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getProductListHtml()
    {
        return $this->getChildHtml('product_list');
    }

    /**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $brand = $this->getCurrentBrand();
        $page_title = $brand->getName();
        $meta_description = $brand->getMetaDescription();
        $meta_keywords = $brand->getMetaKeywords();
        $this->_addBreadcrumbs();
        if ($page_title) {
            $this->pageConfig->getTitle()->set($page_title);
        }
        if ($meta_keywords) {
            $this->pageConfig->setKeywords($meta_keywords);
        }
        if ($meta_description) {
            $this->pageConfig->setDescription($meta_description);
        }
        return parent::_prepareLayout();
    }

    public function getCurrentBrand()
    {
        $brand = $this->_coreRegistry->registry('current_brand');
        if ($brand) {
            $this->setData('current_brand', $brand);
        }
        return $brand;
    }
    public function getCountryname($countryCode){
        $country = $this->_countryFactory->create()->loadByCode($countryCode);
        return $country->getName();
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
        $brandRoute = $this->_brandHelper->getConfig('general_settings/route');
        $brandRoute = $brandRoute ? $brandRoute : "shopbybrand/index/index";
        $page_title = $this->_brandHelper->getConfig('brand_list_page/page_title');
        $brand = $this->getCurrentBrand();

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
                    'label' => $brand->getName(),
                    'title' => $brand->getName(),
                    'link' => $baseUrl . $brandRoute
                ]
            );

            $breadcrumbsBlock->addCrumb(
                'brand',
                [
                    'label' => $brand->getName(),
                    'title' => $brand->getName(),
                    'link' => ''
                ]
            );
        }
    }
}
