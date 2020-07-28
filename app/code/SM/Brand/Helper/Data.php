<?php

namespace SM\Brand\Helper;

use Magento\Catalog\Model\Template\Filter\Factory;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{

    /** @var StoreManagerInterface */
    protected $_storeManager;

    /**
     * Brand config node per website
     *
     * @var array
     */
    protected $_config = [];

    /**
     * Template filter factory
     *
     * @var Factory
     */
    protected $_templateFilterFactory;

    /**
     * @var FilterProvider
     */
    protected $_filterProvider;

    protected $_request;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        FilterProvider $filterProvider
    )
    {
        parent::__construct($context);
        $this->_filterProvider = $filterProvider;
        $this->_storeManager = $storeManager;
        $this->_request = $context->getRequest();
    }


    /**
     * @param $str
     * @return string
     * @throws \Exception
     */
    public function filter($str)
    {
        $html = $this->_filterProvider->getPageFilter()->filter($str);
        return $html;
    }

    public function getSearchFormUrl()
    {
        $url = $this->_storeManager->getStore()->getBaseUrl();
        $url_prefix = $this->getConfig('general_settings/url_prefix');
        $url_suffix = $this->getConfig('general_settings/url_suffix');
        $urlPrefix = '';
        if ($url_prefix) {
            $urlPrefix = $url_prefix . '/';
        }
        return $url . $urlPrefix . 'search';
    }

    /**
     * Return brand config value by key and store
     *
     * @param string $key
     * @param Store|int|string $store
     * @return string|null
     */
    public function getConfig($key, $store = null)
    {
        $store = $this->_storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();

        $result = $this->scopeConfig->getValue(
            'brand/' . $key,
            ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }

    public function getSearchKey()
    {
        return $this->_request->getParam('s');
    }

}
