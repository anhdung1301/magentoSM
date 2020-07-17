<?php

namespace SM\SumUp\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use SM\SumUp\Model\AuthorFactory;

/**
 * Class Author
 * @package Mageplaza\Blog\Controller\Adminhtml
 */
abstract class Author extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'SM_SumUp::author';

    /**
     * @var Registry
     */
    public $coreRegistry;

    /**
     * @var AuthorFactory
     */
    public $authorFactory;

    /**
     * Author constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param AuthorFactory $authorFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        AuthorFactory $authorFactory
    ) {
        $this->authorFactory = $authorFactory;
        $this->coreRegistry  = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * @param bool $register
     *
     * @return bool|\Mageplaza\Blog\Model\Author
     */
    public function initAuthor($register = false)
    {
        $authorId = (int) $this->getRequest()->getParam('id');

        /** @var \Mageplaza\Blog\Model\Author $author */
        $author = $this->authorFactory->create();
        if ($authorId) {
            $author->load($authorId);
            if (!$author->getId()) {
                $this->messageManager->addErrorMessage(__('This author no longer exists.'));

                return false;
            }
        }

        if ($register) {
            $this->coreRegistry->register('mageplaza_blog_author', $author);
        }

        return $author;
    }
}
