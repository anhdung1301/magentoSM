<?php


namespace SM\SumUp\Block\Post;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Messages;
use SM\SumUp\Helper\Data;
use SM\SumUp\Model\Post;

/**
 * Class View
 * @package SM\SumUp\Block\Post
 * @method Post getPost()
 * @method void setPost($post)
 */
class View extends \SM\SumUp\Block\Listpost
{
    /**
     * config logo blog path
     */
    const LOGO = 'mageplaza/blog/logo/';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();

        $post      = $this->postFactory->create();
        $id        = $this->getRequest()->getParam('id');
        $historyId = $this->getRequest()->getParam('historyId');

        if ($historyId) {
            $history = $this->helperData->getFactoryByType(Data::TYPE_HISTORY)->create()->load($historyId);
            $post    = $this->helperData->getFactoryByType(Data::TYPE_POST)->create()->load($history->getPostId());
            $data    = $history->getData();
            $post->addData($data);
        } elseif ($id) {
            $post->load($id);
        }
        $this->setPost($post);
    }

    /**
     * @return bool
     */
    public function getRelatedMode()
    {
        return (int) $this->helperData->getConfigGeneral('related_mode') === 1 ? true : false;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getDecrypt($value)
    {
        return $this->enc->decrypt($value);
    }

    /**
     * @return mixed
     */
    protected function getBlogObject()
    {
        return $this->getPost();
    }

    /**
     * check customer is logged in or not
     */
    public function isLoggedIn()
    {
        return $this->helperData->isLogin();
    }
    /**
     * @param $tag
     *
     * @return string
     */
    public function getTagUrl($tag)
    {
        return $this->helperData->getBlogUrl($tag, Data::TYPE_TAG);
    }

    /**
     * @param $category
     *
     * @return string
     */
    public function getCategoryUrl($category)
    {
        return $this->helperData->getBlogUrl($category, Data::TYPE_CATEGORY);
    }

    /**
     * get tag list
     *
     * @param Post $post
     *
     * @return string
     */
    public function getTagList($post)
    {
        $tagCollection = $post->getSelectedTagsCollection();
        $result        = '';
        if (!empty($tagCollection)) {
            $listTags = [];
            foreach ($tagCollection as $tag) {
                $listTags[] =  $tag->getName();
            }
            $result = implode(', ', $listTags);
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->customerUrl->getLoginUrl();
    }

    /**
     * @return string
     */
    public function getRegisterUrl()
    {
        return $this->customerUrl->getRegisterUrl();
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            if ($catId = $this->getRequest()->getParam('cat')) {
                $category = $this->categoryFactory->create()
                    ->load($catId);
                if ($category->getId()) {
                    $breadcrumbs->addCrumb($category->getUrlKey(), [
                        'label' => $category->getName(),
                        'title' => $category->getName(),
                        'link'  => $this->helperData->getBlogUrl($category, Data::TYPE_CATEGORY)
                    ]);
                }
            }

            $post = $this->getPost();
            $breadcrumbs->addCrumb($post->getUrlKey(), [
                'label' => $post->getName(),
                'title' => $post->getName()
            ]);
        }
    }

    /**
     * @param bool $meta
     *
     * @return array|string
     */
    public function getBlogTitle($meta = false)
    {
        $blogTitle = parent::getBlogTitle($meta);
        $post      = $this->getBlogObject();
        if (!$post) {
            return $blogTitle;
        }

        if ($meta) {
            if ($post->getMetaTitle()) {
                $blogTitle[] = $post->getMetaTitle();
            } else {
                $blogTitle[] = ucfirst($post->getName());
            }

            return $blogTitle;
        }

        return ucfirst($post->getName());
    }

    /**
     * @param $priority
     * @param $message
     *
     * @return string
     */
    public function getMessagesHtml($priority, $message)
    {
        /** @var $messagesBlock Messages */
        $messagesBlock = $this->_layout->createBlock(Messages::class);
        $messagesBlock->{$priority}(__($message));

        return $messagesBlock->toHtml();
    }
}
