<?php

namespace SM\SumUp\Controller\Adminhtml\Post;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use SM\SumUp\Controller\Adminhtml\Post;
use SM\SumUp\Helper\Data;
use SM\SumUp\Helper\Image;
use SM\SumUp\Model\Post as PostModel;
use SM\SumUp\Model\PostFactory;
use RuntimeException;

/**
 * Class Save
 * @package SM\SumUp\Controller\Adminhtml\Post
 */
class Save extends Post
{
    /**
     * JS helper
     *
     * @var Js
     */
    public $jsHelper;

    /**
     * @var DateTime
     */
    public $date;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var Data
     */
    protected $_helperData;



    /**
     * Save constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param PostFactory $postFactory
     * @param Js $jsHelper
     * @param Image $imageHelper
     * @param Data $helperData
     * @param DateTime $date
     */
    public function __construct(
        Context $context,
        Registry $registry,
        PostFactory $postFactory,
        Js $jsHelper,
        Image $imageHelper,
        Data $helperData,
        DateTime $date
    ) {
        $this->jsHelper     = $jsHelper;
        $this->_helperData  = $helperData;
        $this->imageHelper  = $imageHelper;
        $this->date         = $date;

        parent::__construct($postFactory, $registry, $context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        ini_set('display_errors', 1);

        $resultRedirect = $this->resultRedirectFactory->create();
        $action         = $this->getRequest()->getParam('action');

        if ($data = $this->getRequest()->getPost('post'))
        {

            $id =$this->getRequest()->getParam('id');

            /** @var PostModel $post */
            $post = $this->initPost(false, true);
            $checkName = $post->getDataByName($data['name']);
            if(!empty($checkName) && empty($id) ){
                $this->_getSession()->setData('sumup_post_data', $data);
                $this->messageManager->addError('Blog name exists');
            }else {
                $this->prepareData($post, $data);
                $this->_eventManager->dispatch(
                    'sumup_post_prepare_save',
                    ['post' => $post, 'request' => $this->getRequest()]
                );


                try {
                    if (empty($action) || $action === 'add') {
                        $post->save();
                        $this->messageManager->addSuccessMessage(__('The post has been saved.'));
                    }
                    $this->_getSession()->setData('sumup_post_data', false);

                    if ($this->getRequest()->getParam('back')) {
                        $resultRedirect->setPath('sumup/*/edit', ['id' => $post->getId(), '_current' => true]);
                    } else {
                        $resultRedirect->setPath('sumup/*/');
                    }

                    return $resultRedirect;
                } catch (RuntimeException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (Exception $e) {
                    $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Post.'));
                }

            }
            $this->_getSession()->setData('sumup_post_data', $data);

            $resultRedirect->setPath('sumup/*/edit', ['id' => $post->getId(), '_current' => true]);

            return $resultRedirect;
        }

        $resultRedirect->setPath('sumup/*/');

        return $resultRedirect;
    }



    /**
     * @param $post
     * @param array $data
     *
     * @return $this
     */
    protected function prepareData($post, $data = [])
    {
        if (!$this->getRequest()->getParam('image')) {
            try {
                $this->imageHelper->uploadImage($data, 'image', Image::TEMPLATE_MEDIA_TYPE_POST, $post->getImage());
            } catch (Exception $exception) {
                $data['image'] = isset($data['image']['value']) ? $data['image']['value'] : '';
            }
        } else {
            $data['image'] = '';
        }
        /** Set specify field data */
        $data['publish_date']   = !isset($data['publish_date'])
            ? $data['publish_date'] : $this->date->date();
        $data['modifier_id']    = $this->_auth->getUser()->getId();
        $data['categories_ids'] = (isset($data['categories_ids']) && $data['categories_ids']) ? explode(
            ',',
            $data['categories_ids']
        ) : [];
        $data['tags_ids']       = (isset($data['tags_ids']) && $data['tags_ids'])
            ? explode(',', $data['tags_ids']) : [];
        if ($post->getCreatedAt() == null) {
            $data['created_at'] = $this->date->date();
        }
        $data['updated_at'] = $this->date->date();
        $post->addData($data);
        if ($tags = $this->getRequest()->getPost('tags', false)) {
            $post->setTagsData(
                $this->jsHelper->decodeGridSerializedInput($tags)
            );
        }
        $products = $this->getRequest()->getPost('products', false);
        if ($products || $products === '') {
            $post->setProductsData(
                $this->jsHelper->decodeGridSerializedInput($products)
            );
        } else {
            $productData = [];
            foreach ($post->getProductsPosition() as $key => $value) {
                $productData[$key] = ['position' => $value];
            }
            $post->setProductsData($productData);
        }

        return $this;
    }
}
