<?php

namespace SM\Brand\Controller\Adminhtml\Brand;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\ObjectManagerInterface;
use RuntimeException;

class Save extends Action
{
    /**
     * @var Filesystem
     */
    protected $_fileSystem;

    /**
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param Filesystem $filesystem
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        Js $jsHelper
    ) {
        $this->_fileSystem = $filesystem;
        $this->jsHelper = $jsHelper;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        ini_set('display_errors', 1);
        $data = $this->getRequest()->getPostValue();
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('SM\Brand\Model\Brand');

            $id = $this->getRequest()->getParam('brand_id');
            $brand_image = $brand_thumbnail = "";
            if ($id) {
                $model->load($id);
                $brand_image = $model->getImage();
                $brand_thumbnail = $model->getThumbnail();
            }

            /** @var Read $mediaDirectory */
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
            $mediaFolder = 'sumup/brand/';
            $path = $mediaDirectory->getAbsolutePath($mediaFolder);

            // Delete, Upload Image
            $imagePath = $mediaDirectory->getAbsolutePath($model->getImage());
            if (isset($data['image']['delete']) && file_exists($imagePath)) {
                unlink($imagePath);
                $data['image'] = '';
                if ($brand_image && $brand_thumbnail && $brand_image == $brand_thumbnail) {
                    $data['thumbnail'] = '';
                }
            }
            if (isset($data['image']) && is_array($data['image'])) {
                unset($data['image']);
            }
            if ($image = $this->uploadImage('image')) {
                $data['image'] = $image;
            }

            // Delete, Upload Thumbnail
            $thumbnailPath = $mediaDirectory->getAbsolutePath($model->getThumbnail());
            if (isset($data['thumbnail']['delete']) && file_exists($thumbnailPath)) {
                unlink($thumbnailPath);
                $data['thumbnail'] = '';
                if ($brand_image && $brand_thumbnail && $brand_image == $brand_thumbnail) {
                    $data['image'] = '';
                }
            }
            if (isset($data['thumbnail']) && is_array($data['thumbnail'])) {
                unset($data['thumbnail']);
            }
            if ($thumbnail = $this->uploadImage('thumbnail')) {
                $data['thumbnail'] = $thumbnail;
            }

            if ($data['url_key'] == '') {
                $data['url_key'] = $data['name'];
            }
            $url_key = $this->_objectManager->create('Magento\Catalog\Model\Product\Url')->formatUrlKey($data['url_key']);
            $data['url_key'] = $url_key;

            $links = $this->getRequest()->getPost('links');
            $links = is_array($links) ? $links : [];
            if (!empty($links) && isset($links['related'])) {
                $products = $this->jsHelper->decodeGridSerializedInput($links['related']);
                $data['products'] = $products;
            }

            $model->setData($data);
            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved this brand.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['brand_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the brand.'));
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['brand_id' => $this->getRequest()->getParam('brand_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    public function uploadImage($fieldId = 'image')
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if (isset($_FILES[$fieldId]) && $_FILES[$fieldId]['name'] != '') {
            $uploader = $this->_objectManager->create(
                'Magento\Framework\File\Uploader',
                ['fileId' => $fieldId]
            );
            $path = $this->_fileSystem->getDirectoryRead(
                DirectoryList::MEDIA
            )->getAbsolutePath(
                'catalog/category/'
            );

            /** @var Read $mediaDirectory */
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
            $mediaFolder = 'sumup/brand/';
            try {
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);
                $result = $uploader->save(
                    $mediaDirectory->getAbsolutePath($mediaFolder)
                );
                return $mediaFolder . $result['name'];
            } catch (Exception $e) {
                $this->_logger->critical($e);
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['brand_id' => $this->getRequest()->getParam('brand_id')]);
            }
        }
        return;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SM_Brand::brand_save');
    }
}
