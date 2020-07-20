<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/22/19
 * Time: 5:24 PM
 */

namespace Sm\SumUp\Controller\Adminhtml;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;


abstract class AbstractSave extends Action
{
    /**
     * @var null
     */
    protected $modelFactory = null;
    /**
     * @var null
     */
    protected $idFieldName = null;

    /**
     * Save constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context
    )
    {
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        $idFieldName = $this->getIdFieldName();
        $data = $this->getRequest()->getPostValue();
        $id = (int)$this->getRequest()->getParam($idFieldName);

   if(isset($data['url_key']) && $data['url_key'] ==''){
       $data['url_key'] = $data['name'];
   }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $model = $this->getModel();

            if ($id) {
                $model->load($id);
            } elseif (!$id) {
                unset($data[$idFieldName]);
            }

            $model->setData($data)->save();

            $this->messageManager->addSuccessMessage($this->getMessageSuccess());
            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', [$idFieldName => $model->getId(), 'duplicate' => '0']);
            }

            return $resultRedirect->setPath('*/*/index');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }

    /**
     * @return string
     */
    public function getIdFieldName()
    {
        return $this->idFieldName;
    }

    /**
     * return mixin
     */
    public function getModel()
    {
        return $this->modelFactory->create();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getMessageSuccess()
    {
        return __('save object success');
    }
}
