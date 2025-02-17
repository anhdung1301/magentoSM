<?php


namespace SM\SumUp\Controller\Adminhtml\Post;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use SM\SumUp\Model\Post;
use SM\SumUp\Model\PostFactory;
use RuntimeException;


class InlineEdit extends Action
{
    /**
     * JSON Factory
     *
     * @var JsonFactory
     */
    public $jsonFactory;

    /**
     * Post Factory
     *
     * @var PostFactory
     */
    public $postFactory;

    /**
     * InlineEdit constructor.
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param PostFactory $postFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        PostFactory $postFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->postFactory = $postFactory;

        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error      = false;
        $messages   = [];
        $postItems  = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && !empty($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error'    => true,
            ]);
        }

        $key    = array_keys($postItems);
        $postId = !empty($key) ? (int) $key[0] : '';
        /** @var Post $post */
        $post = $this->postFactory->create()->load($postId);
        try {
            $postData = $postItems[$postId];
            $post->addData($postData);
            $post->save();
        } catch (LocalizedException $e) {
            $messages[] = $this->getErrorWithPostId($post, $e->getMessage());
            $error      = true;
        } catch (RuntimeException $e) {
            $messages[] = $this->getErrorWithPostId($post, $e->getMessage());
            $error      = true;
        } catch (Exception $e) {
            $messages[] = $this->getErrorWithPostId(
                $post,
                __('Something went wrong while saving the Post.')
            );
            $error      = true;
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error'    => $error
        ]);
    }

    /**
     * Add Post id to error message
     *
     * @param Post $post
     * @param string $errorText
     *
     * @return string
     */
    public function getErrorWithPostId(Post $post, $errorText)
    {
        return '[Post ID: ' . $post->getId() . '] ' . $errorText;
    }
}
