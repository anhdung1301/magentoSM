<?php

namespace SM\SumUp\Controller\Index;

class save extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $_postFactory;
    protected $_mediaDirectory;
    protected $_fileUploaderFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \SM\SumUp\Model\PostFactory $postFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory

    )
    {
        $this->_pageFactory = $pageFactory;
        $this->_postFactory = $postFactory;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;

        return parent::__construct($context);
    }

    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            $input = $this->getRequest()->getPost();
            $post = $this->_postFactory->create();

            try {
                $target = $this->_mediaDirectory->getAbsolutePath('images/');
                /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
                $uploader = $this->_fileUploaderFactory->create(['fileId' => 'image']);
                $uploader->setAllowedExtensions(['jpg', 'pdf', 'doc', 'png', 'zip', 'doc']);
                $uploader->setAllowRenameFiles(true);
                $uploader->save($target);
                $post->addData([
                    'name' => $input['name'],
                    'short_description' => $input['short_description'],
                    'description' => $input['description'],
                    'content' => $input['content'],
                    'image' => $_FILES['image']['name'],
                    'gallery' => $input['gallery']
                ]);

                $post->save();
                $this->messageManager->addSuccess(__('successfully uploaded'));
                return $this->_redirect('sumup/index/index');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

    }
}
