<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SM\SumUp\Controller\Adminhtml\Author;

use Magento\Framework\App\Action\HttpPostActionInterface;


class Delete extends \SM\SumUp\Controller\Adminhtml\Author implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'SM_SumUp::save';
    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('user_id');

        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\NiceForNow\HairCare\Model\Condition::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the author.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['user_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a author to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/index');
    }
}
