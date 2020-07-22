<?php

namespace SM\SumUp\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutFactory;
use SM\SumUp\Block\Adminhtml\Category\Tree;
use SM\SumUp\Controller\Adminhtml\Category;
use SM\SumUp\Model\CategoryFactory;

/**
 * Class SuggestCategories
 * @package SM\SumUp\Controller\Adminhtml\Category
 */
class SuggestCategories extends Category
{
    /**
     * Json result factory
     *
     * @var JsonFactory
     */
    public $resultJsonFactory;

    /**
     * Layout factory
     *
     * @var LayoutFactory
     */
    public $layoutFactory;

    /**
     * SuggestCategories constructor.
     *
     * @param JsonFactory $resultJsonFactory
     * @param LayoutFactory $layoutFactory
     * @param CategoryFactory $categoryFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        CategoryFactory $categoryFactory,
        JsonFactory $resultJsonFactory,
        LayoutFactory $layoutFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory     = $layoutFactory;

        parent::__construct($context, $coreRegistry, $categoryFactory);
    }

    /**
     * Blog Category list suggestion based on already entered symbols
     *
     * @return Json
     */
    public function execute()
    {
        /** @var Tree $treeBlock */
        $treeBlock = $this->layoutFactory->create()->createBlock('SM\SumUp\Block\Adminhtml\Category\Tree');
        $data      = $treeBlock->getSuggestedCategoriesJson($this->getRequest()->getParam('label_part'));

        /** @var Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setJsonData($data);

        return $resultJson;
    }
}
