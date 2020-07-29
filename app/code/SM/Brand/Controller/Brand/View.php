<?php

namespace SM\Brand\Controller\Brand;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManager;
use SM\Brand\Helper\Data;
use SM\Brand\Model\Brand;
use SM\Brand\Model\Layer\Resolver;

class View extends Action
{

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var ResponseInterface
     */
    protected $_response;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var Brand
     */
    protected $_brandModel;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;
    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;
    /**
     * @var Data
     */
    protected $_brandHelper;
    /**
     * Catalog Layer Resolver
     *
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @param Context $context [description]
     * @param StoreManager $storeManager [description]
     * @param PageFactory $resultPageFactory [description]
     * @param Brand $brandModel [description]
     * @param Registry $coreRegistry [description]
     * @param Resolver $layerResolver [description]
     * @param ForwardFactory $resultForwardFactory [description]
     * @param Data $brandHelper [description]
     */
    public function __construct(
        Context $context,
        StoreManager $storeManager,
        PageFactory $resultPageFactory,
        Brand $brandModel,
        Registry $coreRegistry,
        Resolver $layerResolver,
        ForwardFactory $resultForwardFactory,
        Data $brandHelper
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_brandModel = $brandModel;
        $this->layerResolver = $layerResolver;
        $this->_coreRegistry = $coreRegistry;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_brandHelper = $brandHelper;
    }

    /**
     * Default customer account page
     *
     * @return Page
     */
    public function execute()
    {

        $brand = $this->_initBrand();
        if ($brand) {
//            $this->layerResolver->create('brand');
            /** @var Page $resultPage */
            $page = $this->resultPageFactory->create();
            // apply custom layout (page) template once the blocks are generated
            if ($brand->getPageLayout()) {
                $page->getConfig()->setPageLayout($brand->getPageLayout());
            }
            $page->addHandle(['type' => 'MAGETOP_BRAND_' . $brand->getId()]);
            if (($layoutUpdate = $brand->getLayoutUpdateXml()) && trim($layoutUpdate) != '') {
                $page->addUpdate($layoutUpdate);
            }
            /*$collectionSize = $brand->getProductCollection()->getSize();
            if($collectionSize){
                $page->addHandle(['type' => 'magetopbrand_brand_layered']);
            }*/
            $page->getConfig()->addBodyClass('page-products')
                ->addBodyClass('brand-' . $brand->getUrlKey());
            return $page;
        } elseif (!$this->getResponse()->isRedirect()) {

            return $this->resultForwardFactory->create()->forward('noroute');
        }
    }

    public function _initBrand()
    {
        $brandId = (int)$this->getRequest()->getParam('brand_id', false);

        if (!$brandId) {
            return false;
        }
        try {
            $brand = $this->_brandModel->load($brandId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
        $this->_coreRegistry->register('current_brand', $brand);
        return $brand;
    }
}
