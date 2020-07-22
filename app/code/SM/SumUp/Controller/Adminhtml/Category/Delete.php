<?php


namespace SM\SumUp\Controller\Adminhtml\Category;

use Exception;
use Magento\Framework\Controller\Result\Redirect;
use SM\SumUp\Controller\Adminhtml\Category;

/**
 * Class Delete
 * @package SM\SumUp\Controller\Adminhtml\Category
 */
class Delete extends Category
{
    /**
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $this->categoryFactory->create()
                    ->load($id)
                    ->delete();

                $this->messageManager->addSuccessMessage(__('The Blog Category has been deleted.'));

                $resultRedirect->setPath('sumup/*/');

                return $resultRedirect;
            } catch (Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                $resultRedirect->setPath('sumup/*/edit', ['id' => $id]);

                return $resultRedirect;
            }
        }

        // display error message
        $this->messageManager->addErrorMessage(__('Blog Category to delete was not found.'));
        // go to grid
        $resultRedirect->setPath('sumup/*/');

        return $resultRedirect;
    }
}
