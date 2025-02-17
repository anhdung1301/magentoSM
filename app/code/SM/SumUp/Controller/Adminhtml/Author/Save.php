<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Blog
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace SM\SumUp\Controller\Adminhtml\Author;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use SM\SumUp\Controller\Adminhtml\Author;
use SM\SumUp\Helper\Image;
use SM\SumUp\Model\AuthorFactory;
use RuntimeException;


class Save extends Author
{
    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param AuthorFactory $authorFactory
     * @param Image $imageHelper
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AuthorFactory $authorFactory,
        Image $imageHelper
    ) {
        $this->imageHelper = $imageHelper;

        parent::__construct($context, $registry, $authorFactory);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();


        if ($data = $this->getRequest()->getPost('author')) {
            if(isset($data['url_key']) && $data['url_key'] ==''){
                $data['url_key'] =preg_replace('/\s+/', '', $data['name']);
            }
            $author = $this->initAuthor();
            $this->prepareData($author, $data);
            $this->_eventManager->dispatch(
                'sumup_blog_author_prepare_save',
                ['author' => $author, 'request' => $this->getRequest()]
            );

            try {
                $author->save();

                $this->messageManager->addSuccessMessage(__('The Author has been saved.'));
                $this->_getSession()->setData('sm_blog_author_data', false);

                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('sumup/*/edit', ['id' => $author->getId(), '_current' => true]);
                } else {
                    $resultRedirect->setPath('sumup/*/');
                }

                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Author.'));
            }

            $this->_getSession()->setData('sm_blog_author_data', $data);

            $resultRedirect->setPath('sumup/*/edit', ['id' => $author->getId(), '_current' => true]);

            return $resultRedirect;
        }
        $resultRedirect->setPath('sumup/*/');

        return $resultRedirect;
    }

    /**
     * @param $author
     * @param $data
     *
     * @return $this
     */
    public function prepareData($author, $data)
    {
        // upload image
        if (!$this->getRequest()->getParam('image')) {
            try {
                $this->imageHelper->uploadImage($data, 'image', Image::TEMPLATE_MEDIA_TYPE_AUTH, $author->getImage());
            } catch (Exception $exception) {
                $data['image'] = isset($data['image']['value']) ? $data['image']['value'] : '';
            }
        }
        if ($this->getRequest()->getParam('image')['delete']) {
            $data['image'] = '';
        }
        // set data
        if (!empty($data)) {
            $author->addData($data);
        }

        return $this;
    }
}
