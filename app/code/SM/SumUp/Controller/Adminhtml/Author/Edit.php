<?php

namespace SM\SumUp\Controller\Adminhtml\Author;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use SM\SumUp\Controller\Adminhtml\Author;
use SM\SumUp\Model\AuthorFactory;

class Edit extends Author
{
    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param AuthorFactory $authorFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AuthorFactory $authorFactory,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context, $registry, $authorFactory);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $author = $this->initAuthor();
        if (!$author) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');

            return $resultRedirect;
        }
        /** Set entered data if was error when we do save */
        $data = $this->_session->getData('sumup_blog_author_data', true);
        if (!empty($data)) {
            $author->addData($data);
        }
        $this->coreRegistry->register('sumup_blog_author', $author);

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('SM_SumUp::author');
        $resultPage->getConfig()->getTitle()->set(__('Author Management'));

        $resultPage->getConfig()->getTitle()->prepend($author->getId() ? $author->getName() : __('New Author'));

        return $resultPage;
    }
}
