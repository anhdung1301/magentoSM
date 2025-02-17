<?php


namespace SM\SumUp\Block\Adminhtml\Post\Edit\Tab;

use DateTimeZone;
use Exception;
use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Model\Auth\Session;
use Magento\Cms\Model\Page\Source\PageLayout as BasePageLayout;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Config\Model\Config\Source\Design\Robots;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\System\Store;
use SM\SumUp\Block\Adminhtml\Post\Edit\Tab\Renderer\Category;
use SM\SumUp\Block\Adminhtml\Post\Edit\Tab\Renderer\Tag;
use SM\SumUp\Helper\Image;
use SM\SumUp\Model\Config\Source\Author;
use SM\SumUp\Model\Config\Source\AuthorStatus;


class Post extends Generic implements TabInterface
{
    /**
     * Wysiwyg config
     *
     * @var Config
     */
    public $wysiwygConfig;

    /**
     * Country options
     *
     * @var Yesno
     */
    public $booleanOptions;

    /**
     * @var Robots
     */
    public $metaRobotsOptions;

    /**
     * @var Store
     */
    public $systemStore;

    /**
     * @var Session
     */
    protected $authSession;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var DateTime
     */
    protected $_date;

    /**
     * @var BasePageLayout
     */
    protected $_layoutOptions;

    /**
     * @var Author
     */
    protected $_author;

    /**
     * @var AuthorStatus
     */
    protected $_status;

    /**
     * Post constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Session $authSession
     * @param DateTime $dateTime
     * @param BasePageLayout $layoutOption
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param Yesno $booleanOptions
     * @param Robots $metaRobotsOptions
     * @param Store $systemStore
     * @param Image $imageHelper
     * @param Author $author
     * @param AuthorStatus $status
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Session $authSession,
        DateTime $dateTime,
        BasePageLayout $layoutOption,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Yesno $booleanOptions,
        Robots $metaRobotsOptions,
        Store $systemStore,
        Image $imageHelper,
        Author $author,
        AuthorStatus $status,
        array $data = []
    ) {
        $this->wysiwygConfig     = $wysiwygConfig;
        $this->booleanOptions    = $booleanOptions;
        $this->metaRobotsOptions = $metaRobotsOptions;
        $this->systemStore       = $systemStore;
        $this->authSession       = $authSession;
        $this->_date             = $dateTime;
        $this->_layoutOptions    = $layoutOption;
        $this->imageHelper       = $imageHelper;
        $this->_author           = $author;
        $this->_status           = $status;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function _prepareForm()
    {

        /** @var \SM\SumUp\Model\Post $post */
        $post = $this->_coreRegistry->registry('sm_blog_post');

        /** @var Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('post_');
        $form->setFieldNameSuffix('post');
        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Post Information'),
            'class'  => 'fieldset-wide'
        ]);

        if ($this->_request->getParam('duplicate')) {
            $fieldset->addField('duplicate', 'hidden', [
                'name'  => 'duplicate',
                'value' => 1
            ]);
        }
        $fieldset->addField('name', 'text', [
            'name'     => 'name',
            'label'    => __('Name'),
            'title'    => __('Name'),
            'required' => true
        ]);

        $fieldset->addField('author_id', 'select', [
            'name'     => 'author_id',
            'label'    => __('Author'),
            'title'    => __('Author'),
            'required' => true,
            'values'   => $this->_author->toOptionArray()
        ]);
        $fieldset->addField('enabled', 'select', [
            'name'   => 'enabled',
            'label'  => __('Status'),
            'title'  => __('Status'),
            'values' => $this->_status->toOptionArray()
        ]);
        if (!$post->hasData('enabled')) {
            $post->setEnabled(1);
        }

        $fieldset->addField('short_description', 'textarea', [
            'name'  => 'short_description',
            'label' => __('Short Description'),
            'title' => __('Short Description'),
            'required' => true
        ]);
        $fieldset->addField('description', 'editor', [
            'name'   => 'description',
            'label'  => __('Description'),
            'title'  => __('Description'),
            'config' => $this->wysiwygConfig->getConfig([
                'add_variables'  => false,
                'add_widgets'    => true,
                'add_directives' => true
            ]),
            'required' => true
        ]);

        $fieldset->addField('image', \SM\SumUp\Block\Adminhtml\Renderer\Image::class, [
            'name'  => 'image',
            'label' => __('Image'),
            'title' => __('Image'),
            'path'  => $this->imageHelper->getBaseMediaPath(Image::TEMPLATE_MEDIA_TYPE_POST),
            'note'  => __('The appropriate size is 265px * 250px.'),
            'required' => true

        ]);
        $fieldset->addField('categories_ids', Category::class, [
            'name'  => 'categories_ids',
            'label' => __('Categories'),
            'title' => __('Categories'),
        ]);
        if (!$post->getCategoriesIds()) {
            $post->setCategoriesIds($post->getCategoryIds());
        }


        $fieldset->addField('tags_ids', Tag::class, [
            'name'  => 'tags_ids',
            'label' => __('Tags'),
            'title' => __('Tags'),
        ]);

        if (!$post->getTagsIds()) {
            $post->setTagsIds($post->getTagIds());
        }
        $fieldset->addField(
            'publish_date',
            'date',
            [
                'name'        => 'publish_date',
                'label'       => __('Publish Date'),
                'title'       => __('Publish Date'),
                'date_format' => 'yyyy-MM-dd',
                'timezone'    => false,
                'time_format' => 'hh:mm:ss'
            ]
        );
        $fieldset->addField(
            'publish_date_to',
            'date',
            [
                'name'        => 'publish_date_to',
                'label'       => __('Close Date'),
                'title'       => __('Close Date'),
                'date_format' => 'yyyy-MM-dd',
                'timezone'    => false,
                'time_format' => 'hh:mm:ss'
            ]
        );
        $fieldset->addField('url_key', 'text', [
            'name'  => 'url_key',
            'label' => __('URL Key'),
            'title' => __('URL Key')
        ]);

        /** Get the public_date from database */
        if ($post->getData('publish_date')) {
            $publicDateTime = new \DateTime($post->getData('publish_date'), new DateTimeZone('UTC'));
            $publicDateTime->setTimezone(new DateTimeZone($this->_localeDate->getConfigTimezone()));
            $publicDateTime = $publicDateTime->format('m/d/Y H:i:s');
            $post->setData('publish_date', $publicDateTime);
        }

        $form->addValues($post->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Post');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
