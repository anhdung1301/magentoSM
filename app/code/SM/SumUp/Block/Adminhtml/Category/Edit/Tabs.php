<?php

namespace SM\SumUp\Block\Adminhtml\Category\Edit;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use SM\SumUp\Model\Category;

/**
 * Class Tabs
 * @package SM\SumUp\Block\Adminhtml\Category\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::widget/tabshoriz.phtml';

    /**
     * @var Registry
     */
    public $coreRegistry;

    /**
     * Tabs constructor.
     *
     * @param Registry $coreRegistry
     * @param Context $context
     * @param EncoderInterface $jsonEncoder
     * @param Session $authSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        EncoderInterface $jsonEncoder,
        Session $authSession,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * Initialize Tabs
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('category_info_tabs');
        $this->setDestElementId('category_tab_content');
        $this->setTitle(__('Category Data'));
    }

    /**
     * Retrieve Blog Category object
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->coreRegistry->registry('category');
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function _prepareLayout()
    {
        $this->addTab('category', [
            'label'   => __('Category information'),
            'content' => $this->getLayout()
                ->createBlock(
                    Tab\Category::class,
                    'sm_blog_category_edit_tab_category'
                )
                ->toHtml()
        ]);

        $this->_eventManager->dispatch('adminhtml_mageplaza_blog_category_tabs', ['tabs' => $this]);

        return parent::_prepareLayout();
    }
}
