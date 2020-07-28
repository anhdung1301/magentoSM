<?php

namespace SM\Brand\Controller\Adminhtml\Brand;

class Index extends \SM\Brand\Controller\Adminhtml\Brand
{
    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SM_SumUp::brand');
    }

    /**
     * Brand list action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {

        $resultPage = $this->resultPageFactory->create();

        /**
         * Set active menu item
         */
        $resultPage->setActiveMenu("SM_SumUp::brand");
        $resultPage->getConfig()->getTitle()->prepend(__('Brands'));

        /**
         * Add breadcrumb item
         */
        $resultPage->addBreadcrumb(__('Brands'),__('Brands'));
        $resultPage->addBreadcrumb(__('Manage Brands'),__('Manage Brands'));

        return $resultPage;
    }

}
