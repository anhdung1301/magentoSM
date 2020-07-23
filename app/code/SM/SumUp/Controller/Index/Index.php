<?php
namespace SM\SumUp\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory
        )
    {
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        ini_set('error_reporting', E_ERROR);
        register_shutdown_function("fatal_handler");
        function fatal_handler() {
            $error = error_get_last();
            echo("<pre>");
            print_r($error);
        }

        return $this->_pageFactory->create();
    }
}
