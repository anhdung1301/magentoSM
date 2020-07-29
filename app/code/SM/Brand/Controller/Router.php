<?php

namespace SM\Brand\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Url;
use Magento\Store\Model\StoreManagerInterface;
use SM\Brand\Helper\Data;
use SM\Brand\Model\Brand;

class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * Event manager
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * Response
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var bool
     */
    protected $dispatched;

    /**
     * Brand Factory
     *
     * @var Brand $brandCollection
     */
    protected $_brandCollection;

    /**
     * Brand Helper
     */
    protected $_brandHelper;

    /**
     * Store manager
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param ActionFactory $actionFactory
     * @param ResponseInterface $response
     * @param ManagerInterface $eventManager
     * @param Brand $brandCollection
     * @param Data $brandHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ActionFactory $actionFactory,
        ResponseInterface $response,
        ManagerInterface $eventManager,
        Brand $brandCollection,
        Data $brandHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
        $this->response = $response;
        $this->_brandHelper = $brandHelper;
        $this->_brandCollection = $brandCollection;
        $this->storeManager = $storeManager;
    }

    /**
     * @param RequestInterface $request
     * @return ActionInterface
     */
    public function match(RequestInterface $request)
    {
        $_brandHelper = $this->_brandHelper;
        if (!$this->dispatched) {
            $urlKey = trim($request->getPathInfo(), '/');
            $origUrlKey = $urlKey;
            /** @var Object $condition */
            $condition = new DataObject(['url_key' => $urlKey, 'continue' => true]);
            $this->eventManager->dispatch(
                'magetop_brand_controller_router_match_before',
                ['router' => $this, 'condition' => $condition]
            );
            $urlKey = $condition->getUrlKey();
            if ($condition->getRedirectUrl()) {
                $this->response->setRedirect($condition->getRedirectUrl());
                $request->setDispatched(true);
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Redirect',
                    ['request' => $request]
                );
            }
            if (!$condition->getContinue()) {
                return null;
            }
            $route = $_brandHelper->getConfig('general_settings/route');
            $urlKeyArr = explode(".", $urlKey);
            if (count($urlKeyArr) > 1) {
                $urlKey = $urlKeyArr[0];
            }
            $routeArr = explode(".", $route);
            if (count($routeArr) > 1) {
                $route = $routeArr[0];
            }
            if ($route != '' && $urlKey == $route) {
                $request->setModuleName('shopbybrand')
                    ->setControllerName('index')
                    ->setActionName('index');
                $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $urlKey);
                $this->dispatched = true;
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward',
                    ['request' => $request]
                );
            }
            $url_prefix = $_brandHelper->getConfig('general_settings/url_prefix');
            $url_suffix = $_brandHelper->getConfig('general_settings/url_suffix');
            $url_prefix = $url_prefix ? $url_prefix : $route;
            $identifiers = explode('/', $urlKey);
            // SEARCH PAGE
            if (count($identifiers) == 2 && $url_prefix == $identifiers[0] && $identifiers[1] == 'search') {
                $request->setModuleName('shopbybrand')
                    ->setControllerName('search')
                    ->setActionName('result');
                $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                $request->setDispatched(true);
                $this->dispatched = true;
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward',
                    ['request' => $request]
                );
            }
            if ((count($identifiers) == 2 && $identifiers[0] == $url_prefix) || (trim($url_prefix) == '' && count($identifiers) == 1)) {
                $brandUrl = '';
                if (trim($url_prefix) == '' && count($identifiers) == 1) {
                    $brandUrl = str_replace($url_suffix, '', $identifiers[0]);
                }
                if (count($identifiers) == 2) {
                    $brandUrl = str_replace($url_suffix, '', $identifiers[1]);
                }
                if ($brandUrl) {
                    $brand = $this->_brandCollection->getCollection()
                        ->addFieldToFilter('enabled', ['eq' => 1])
                        ->addFieldToFilter('url_key', ['eq' => $brandUrl])
                        ->getFirstItem();

                    if ($brand && $brand->getId()) {
                        $request->setModuleName('shopbybrand')
                            ->setControllerName('brand')
                            ->setActionName('view')
                            ->setParam('brand_id', $brand->getId());
                        $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                        $request->setDispatched(true);
                        $this->dispatched = true;
                        return $this->actionFactory->create(
                            'Magento\Framework\App\Action\Forward',
                            ['request' => $request]
                        );
                    }
                }
            }
            $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
            $request->setDispatched(true);
            $this->dispatched = true;
            return $this->actionFactory->create(
                'Magento\Framework\App\Action\Forward',
                ['request' => $request]
            );
        }
    }
}
