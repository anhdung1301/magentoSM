<?php
namespace SM\SumUp\Controller\Adminhtml\Author;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * @var PageFactory
     */
    public $_resultPageFactory;

    /**
     * Index constructor.
     *
     * @param PageFactory $resultPageFactory
     * @param Action\Context $context
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Action\Context $context
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Authors'));
        return $resultPage;
    }
}
