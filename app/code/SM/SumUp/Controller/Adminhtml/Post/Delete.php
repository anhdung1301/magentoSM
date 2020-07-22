<?php

namespace SM\SumUp\Controller\Adminhtml\Post;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use SM\SumUp\Controller\Adminhtml\Post;

/**
 * Class Delete
 * @package SM\SumUp\Controller\Adminhtml\Post
 */
class Delete extends Post
{
    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $this->postFactory->create()
                    ->load($id)
                    ->delete();

                $this->messageManager->addSuccessMessage(__('The Post has been deleted.'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setPath('sumup/*/edit', ['id' => $id]);

                return $resultRedirect;
            }
        } else {
            $this->messageManager->addErrorMessage(__('Post to delete was not found.'));
        }

        $resultRedirect->setPath('sumup/*/');

        return $resultRedirect;
    }
}
