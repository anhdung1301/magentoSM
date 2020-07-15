<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SM\SumUp\Controller\Adminhtml\Tag;

use Magento\Framework\App\Action\HttpPostActionInterface;


class Delete extends \SM\SumUp\Controller\Adminhtml\Tag implements HttpPostActionInterface
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
        $id = $this->getRequest()->getParam('tag_id');

        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\NiceForNow\HairCare\Model\Condition::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the tag.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['tag_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Condition to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/index');
    }
}
