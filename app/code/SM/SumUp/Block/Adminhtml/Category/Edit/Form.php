<?php


namespace SM\SumUp\Block\Adminhtml\Category\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Catalog\Block\Adminhtml\Category\AbstractCategory;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category\Tree;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\BlockInterface;
use SM\SumUp\Model\CategoryFactory as BlogCategoryFactory;
use SM\SumUp\Model\ResourceModel\Category\Tree as BlogResourceTree;

/**
 * Class Form
 * @package SM\SumUp\Block\Adminhtml\Category\Edit
 */
class Form extends AbstractCategory
{
    /**
     * @var array Additional buttons
     */
    public $additionalButtons = [];

    /**
     * @var string Block template
     */
    protected $_template = 'category/edit/form.phtml';

    /**
     * @var EncoderInterface
     */
    public $jsonEncoder;

    /**
     * Form constructor.
     *
     * @param Context $context
     * @param Tree $categoryTree
     * @param Registry $registry
     * @param CategoryFactory $categoryFactory
     * @param BlogResourceTree $blogCategoryTree
     * @param BlogCategoryFactory $blogCategoryFactory
     * @param EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        Context $context,
        Tree $categoryTree,
        Registry $registry,
        CategoryFactory $categoryFactory,
        BlogResourceTree $blogCategoryTree,
        BlogCategoryFactory $blogCategoryFactory,
        EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        parent::__construct($context, $categoryTree, $registry, $categoryFactory, $data);

        $this->jsonEncoder      = $jsonEncoder;
        $this->_categoryTree    = $blogCategoryTree;
        $this->_categoryFactory = $blogCategoryFactory;
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        $category   = $this->getCategory();
        $categoryId = (int) $category->getId();
        // 0 when we create Blog Category, otherwise some value for editing Blog Category

        $this->setChild(
            'tabs',
            $this->getLayout()->createBlock(Tabs::class, 'tabs')
        );

        // Save button
        $this->addButton('save', [
            'id'             => 'save',
            'label'          => __('Save Category'),
            'class'          => 'save primary save-category',
            'data_attribute' => [
                'mage-init' => [
                    'SM_SumUp/category/edit' => [
                        'url'  => $this->getSaveUrl(),
                        'ajax' => true
                    ]
                ]
            ]
        ]);

        // Delete button
        if ($categoryId && !in_array($categoryId, $this->getRootIds()) && !$this->getRequest()->getParam('duplicate')) {
            $this->addButton('delete', [
                'id'      => 'delete',
                'label'   => __('Delete Category'),
                'onclick' => "categoryDelete('" . $this->getUrl(
                        'sumup/*/delete',
                        ['_current' => true]
                    ) . "')",
                'class'   => 'delete'
            ]);
        }

        // Reset button
        $resetPath = $categoryId ? 'sumup/*/edit' : 'sumup/*/add';
        $this->addButton('reset', [
            'id'      => 'reset',
            'label'   => __('Reset'),
            'onclick' => "categoryReset('" . $this->getUrl($resetPath, ['_current' => true]) . "',false)",
            'class'   => 'reset'
        ]);

        return parent::_prepareLayout();
    }

    /**
     * Retrieve additional buttons html
     *
     * @return string
     */
    public function getAdditionalButtonsHtml()
    {
        $html = '';
        foreach ($this->additionalButtons as $childName) {
            $html .= $this->getChildHtml($childName);
        }

        return $html;
    }

    /**
     * @return mixed
     */
    public function isAjax()
    {
        return $this->getRequest()->isAjax();
    }

    /**
     * @param array $args
     *
     * @return string
     */
    public function getSaveUrl(array $args = [])
    {
        /** @var \SM\SumUp\Model\Category $category */
        $category = $this->_coreRegistry->registry('category');
        $params   = ['_current' => false, '_query' => false];
        if ($category->getDuplicate()) {
            $params['duplicate'] = true;
        }
        $params = array_merge($params, $args);

        return $this->getUrl('sumup/*/save', $params);
    }

    /**
     * @return string
     */
    public function getEditUrl()
    {
        return $this->getUrl('sumup/category/edit', ['_query' => false, 'id' => null, 'parent' => null]);
    }

    /**
     * @param $alias
     * @param $config
     *
     * @return $this
     * @throws LocalizedException
     */
    public function addAdditionalButton($alias, $config)
    {
        if (isset($config['name'])) {
            $config['element_name'] = $config['name'];
        }
        if ($this->hasToolbarBlock()) {
            $this->addButton($alias, $config);
        } else {
            $this->setChild(
                $alias . '_button',
                $this->getLayout()->createBlock(Button::class)->addData($config)
            );
            $this->additionalButtons[$alias] = $alias . '_button';
        }

        return $this;
    }

    /**
     * Remove additional button
     *
     * @param string $alias
     *
     * @return $this
     */
    public function removeAdditionalButton($alias)
    {
        if (isset($this->additionalButtons[$alias])) {
            $this->unsetChild($this->additionalButtons[$alias]);
            unset($this->additionalButtons[$alias]);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTabsHtml()
    {
        return $this->getChildHtml('tabs');
    }

    /**
     * @return Phrase|string
     */
    public function getHeader()
    {
        if ($this->getCategoryId()) {
            return $this->getCategoryName();
        }

        $parentId = (int) $this->getRequest()->getParam('parent');
        if ($parentId && $parentId !== Category::TREE_ROOT_ID) {
            return __('New Child 123 Category');
        } else {
            return __('New Root 12 Category');
        }
    }

    /**
     * @param array $args
     *
     * @return string
     */
    public function getDeleteUrl(array $args = [])
    {
        $params = ['_current' => true];
        $params = array_merge($params, $args);

        return $this->getUrl('sumup/*/delete', $params);
    }

    /**
     * Return URL for refresh input element 'path' in form
     *
     * @param array $args
     *
     * @return string
     */
    public function getRefreshPathUrl(array $args = [])
    {
        $params = ['_current' => true];
        $params = array_merge($params, $args);

        return $this->getUrl('sumup/*/refreshPath', $params);
    }

    /**
     * Get parent Blog Category id
     *
     * @return int
     */
    public function getParentCategoryId()
    {
        return (int) $this->templateContext->getRequest()->getParam('parent');
    }

    /**
     * Get Blog Category  id
     *
     * @return int
     */
    public function getCategoryId()
    {
        return (int) $this->templateContext->getRequest()->getParam('id');
    }

    /**
     * @param $buttonId
     * @param array $data
     *
     * @throws LocalizedException
     */
    public function addButton($buttonId, array $data)
    {
        $childBlockId = $buttonId . '_button';
        $button       = $this->getButtonChildBlock($childBlockId);
        $button->setData($data);
        $block = $this->getLayout()->getBlock('page.actions.toolbar');
        if ($block) {
            $block->setChild($childBlockId, $button);
        } else {
            $this->setChild($childBlockId, $button);
        }
    }

    /**
     * @return bool
     * @throws LocalizedException
     */
    public function hasToolbarBlock()
    {
        return $this->getLayout()->isBlock('page.actions.toolbar');
    }

    /**
     * @param $childId
     * @param null $blockClassName
     *
     * @return BlockInterface
     * @throws LocalizedException
     */
    public function getButtonChildBlock($childId, $blockClassName = null)
    {
        if ($blockClassName === null) {
            $blockClassName = Button::class;
        }

        return $this->getLayout()->createBlock($blockClassName, $this->getNameInLayout() . '-' . $childId);
    }

    /**
     * @return string
     */
    public function getPostsJson()
    {
        $posts = $this->getCategory()->getPostsPosition();
        if (!empty($posts)) {
            return $this->jsonEncoder->encode($posts);
        }

        return '{}';
    }
}
