<?php

namespace SM\SumUp\Controller\Index;

use Magento\Framework\View\Result\PageFactory;
use SM\SumUp\Helper\Data as HelperBlog;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var HelperBlog
     */
    protected $helperBlog;

    protected $_pageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $pageFactory,
        HelperBlog $helperBlog
    ) {
        $this->helperBlog = $helperBlog;

        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $this->helperBlog->flushCache();

        return $this->_pageFactory->create();
    }
}
