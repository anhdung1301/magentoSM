<?php

namespace SM\SumUp\Block;

use Exception;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Url;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use SM\SumUp\Block\Adminhtml\Post\Edit\Tab\Renderer\Category as CategoryOptions;
use SM\SumUp\Block\Adminhtml\Post\Edit\Tab\Renderer\Tag as TagOptions;
use SM\SumUp\Helper\Data as HelperData;
use SM\SumUp\Helper\Image;
use SM\SumUp\Model\CategoryFactory;
use SM\SumUp\Model\Config\Source\AuthorStatus;
use SM\SumUp\Model\PostFactory;

/**
 * Class Frontend
 *
 * @package SM\SumUp\Block
 */
class Frontend extends Template
{
    /**
     * @var FilterProvider
     */
    public $filterProvider;

    /**
     * @var HelperData
     */
    public $helperData;

    /**
     * @var StoreManagerInterface
     */
    public $store;

    /**
     * @var CustomerRepositoryInterface
     */
    public $customerRepository;


    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var DateTime
     */
    public $dateTime;

    /**
     * @var PostFactory
     */
    protected $postFactory;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var Url
     */
    protected $customerUrl;

    /**
     * @var CategoryOptions
     */
    protected $categoryOptions;



    /**
     * @var TagOptions
     */
    protected $tagOptions;



    /**
     * @var AuthorStatus
     */
    protected $authorStatusType;

    /**
     * @var ThemeProviderInterface
     */
    protected $themeProvider;

    /**
     * @var EncryptorInterface
     */
    public $enc;

    /**
     * Frontend constructor.
     *
     * @param Context $context
     * @param FilterProvider $filterProvider
     * @param CustomerRepositoryInterface $customerRepository
     * @param Registry $coreRegistry
     * @param HelperData $helperData
     * @param Url $customerUrl
     * @param CategoryFactory $categoryFactory
     * @param PostFactory $postFactory
     * @param DateTime $dateTime
     * @param CategoryOptions $category
     * @param TagOptions $tag
     * @param ThemeProviderInterface $themeProvider
     * @param EncryptorInterface $enc
     * @param AuthorStatus $authorStatus
     * @param array $data
     */
    public function __construct(
        Context $context,
        FilterProvider $filterProvider,
        CustomerRepositoryInterface $customerRepository,
        Registry $coreRegistry,
        HelperData $helperData,
        Url $customerUrl,
        CategoryFactory $categoryFactory,
        PostFactory $postFactory,
        DateTime $dateTime,
        CategoryOptions $category,
        TagOptions $tag,
        ThemeProviderInterface $themeProvider,
        EncryptorInterface $enc,
        AuthorStatus $authorStatus,
        array $data = []
    )
    {
        $this->filterProvider = $filterProvider;
        $this->customerRepository = $customerRepository;
        $this->helperData = $helperData;
        $this->coreRegistry = $coreRegistry;
        $this->dateTime = $dateTime;
        $this->categoryFactory = $categoryFactory;
        $this->postFactory = $postFactory;
        $this->customerUrl = $customerUrl;
        $this->categoryOptions = $category;
        $this->tagOptions = $tag;
        $this->authorStatusType = $authorStatus;
        $this->themeProvider = $themeProvider;
        $this->store = $context->getStoreManager();
        $this->enc = $enc;

        parent::__construct($context, $data);
    }

    /**
     * @return HelperData
     */
    public function getBlogHelper()
    {
        return $this->helperData;
    }

    /**
     * @return bool
     */
    public function isBlogEnabled()
    {
        return $this->helperData->isEnabled();
    }

    /**
     * @param $content
     *
     * @return string
     * @throws Exception
     */
    public function getPageFilter($content)
    {
        return $this->filterProvider->getPageFilter()->filter((string)$content);
    }

    /**
     * @param $image
     * @param string $type
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getImageUrl($image, $type = Image::TEMPLATE_MEDIA_TYPE_POST)
    {
        $imageHelper = $this->helperData->getImageHelper();
        $imageFile = $imageHelper->getMediaPath($image, $type);

        return $this->helperData->getImageHelper()->getMediaUrl($imageFile);
    }

    /**
     * @param $urlKey
     * @param null $type
     *
     * @return string
     */
    public function getRssUrl($urlKey, $type = null)
    {
        if (is_object($urlKey)) {
            $urlKey = $urlKey->getUrlKey();
        }

        $urlKey = ($type ? $type . '/' : '') . $urlKey;
        $url = $this->helperData->getUrl($this->helperData->getRoute() . '/' . $urlKey);

        return rtrim($url, '/') . '.xml';
    }

    /**
     * @param $post
     *
     * @return Phrase|string
     * @throws Exception
     */
    public function getPostInfo($post)
    {

        $html = __(
            '<i class="mp-blog-icon mp-blog-calendar-times"></i> %1',
            $this->getDateFormat($post->getPublishDate())
        );

        if ($categoryPost = $this->getPostCategoryHtml($post)) {
            $html .= __('| Posted in %1', $categoryPost);
        }

        $author = $this->helperData->getAuthorByPost($post);
        if ($author && $author->getName() && $this->helperData->showAuthorInfo()) {
            $aTag = '<a class="mp-info" href="' . $author->getUrl() . '">'
                . $this->escapeHtml($author->getName()) . '</a>';
            $html .= __('| <i class="mp-blog-icon mp-blog-user"></i> %1', $aTag);
        }



        if ($post->getViewTraffic()) {
            $html .= __(
                '| <i class="mp-blog-icon mp-blog-traffic" aria-hidden="true"></i> %1',
                $post->getViewTraffic()
            );
        }
        return $html;
    }


    /**
     * get list category html of post
     *
     * @param $post
     *
     * @return null|string
     */
    public function getPostCategoryHtml($post)
    {
        if (!$post->getCategoryIds()) {
            return null;
        }

        $categories = $this->helperData->getCategoryCollection($post->getCategoryIds());
        $categoryHtml = [];
        foreach ($categories as $_cat) {
            $categoryHtml[] = '<a class="mp-info" href="' . $this->helperData->getBlogUrl(
                    $_cat,
                    HelperData::TYPE_CATEGORY
                ) . '">' . $_cat->getName() . '</a>';
        }

        return implode(', ', $categoryHtml);
    }

    /**
     * @param $date
     * @param bool $monthly
     *
     * @return false|string
     * @throws Exception
     */
    public function getDateFormat($date, $monthly = false)
    {
        return $this->helperData->getDateFormat($date, $monthly);
    }

    /**
     * @param $image
     * @param null $size
     * @param string $type
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function resizeImage($image, $size = null, $type = Image::TEMPLATE_MEDIA_TYPE_POST)
    {
        if (!$image) {
            return $this->getDefaultImageUrl();
        }

        return $this->helperData->getImageHelper()->resizeImage($image, $size, $type);
    }

    /**
     * get default image url
     */
    public function getDefaultImageUrl()
    {
        return $this->getViewFileUrl('SM_SumUp::media/images/mageplaza-logo-default.png');
    }

    /**
     * @return string
     */
    public function getDefaultAuthorImage()
    {
        return $this->getViewFileUrl('SM_SumUp::media/images/no-artist-image.jpg');
    }
}
