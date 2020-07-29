<?php

namespace SM\Brand\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManager;
use SM\Brand\Helper\Data;

class Index extends Action
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
     * @var Data
     */
    protected $_brandHelper;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @param Context $context
     * @param StoreManager $storeManager
     * @param PageFactory $resultPageFactory
     * @param Data $brandHelper
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        StoreManager $storeManager,
        PageFactory $resultPageFactory,
        Data $brandHelper,
        ForwardFactory $resultForwardFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_brandHelper = $brandHelper;
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return Page
     */
    public function execute()
    {

        /** @var Page $resultPage */
        $page = $this->resultPageFactory->create();
        return $page;
    }
}
