<?php

namespace SM\Brand\Controller\Adminhtml\Brand;

use Magento\Backend\App\Action\Context;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\View\Result\LayoutFactory;

class Products extends Product
{
    /**
     * @var LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @param Context $context
     * @param Builder $productBuilder
     * @param LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        Context $context,
        Builder $productBuilder,
        LayoutFactory $resultLayoutFactory
    )
    {
        parent::__construct($context, $productBuilder);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * @return Layout
     */
    public function execute()
    {
        $id = $this->getRequest()->getparam('brand_id');
        $brand = $this->_objectManager->create('SM\Brand\Model\Brand');
        $brand->load($id);
        $registry = $this->_objectManager->get('Magento\Framework\Registry');
        $registry->register("current_brand", $brand);

        $this->productBuilder->build($this->getRequest());
        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('brand.edit.tab.products')->setProductsRelated($this->getRequest()->getPost('products', null));
        return $resultLayout;
    }
}
