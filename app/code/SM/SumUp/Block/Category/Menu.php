<?php

namespace SM\SumUp\Block\Category;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use SM\SumUp\Api\Data\CategoryInterface;
use SM\SumUp\Helper\Data as HelperData;
use SM\SumUp\Model\Category;
use SM\SumUp\Model\CategoryFactory;
use SM\SumUp\Model\ResourceModel\Category\Collection;
use SM\SumUp\Model\ResourceModel\Category\CollectionFactory;

/**
 * Class Widget
 * @package SM\SumUp\Block\Category
 */
class Menu extends Template
{
    /**
     * @var CollectionFactory
     */
    protected $categoryCollection;

    /**
     * @var CategoryFactory
     */
    protected $category;

    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * Menu constructor.
     *
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param CategoryFactory $categoryFactory
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        CategoryFactory $categoryFactory,
        HelperData $helperData,
        array $data = []
    ) {
        $this->categoryCollection = $collectionFactory;
        $this->category           = $categoryFactory;
        $this->helper             = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * @param $id
     *
     * @return CategoryInterface[]
     */
    public function getChildCategory($id)
    {
        return $this->categoryCollection->create()->addAttributeToFilter('parent_id', $id)
            ->addAttributeToFilter('enabled', '1')->getItems();
    }

    /**
     * @return Collection
     */
    public function getCollections()
    {
        return $this->categoryCollection->create()
            ->addAttributeToFilter('level', '1')->addAttributeToFilter('enabled', '1');

    }

    public function getUrlBlog(){
        return $this->getUrl('sumup/');
    }
    /**
     * @param Category $parentCategory
     *
     * @return string
     */
    public function getMenuHtml($parentCategory)
    {
        $categoryUrl = $this->helper->getBlogUrl('category/' . $parentCategory->getUrlKey());
//        $categoryUrl = $this->getUrlBlog().'category/'.$parentCategory->getUrlKey();
        $html        = '<li class="level' . $parentCategory->getLevel()
            . ' category-item ui-menu-item" role="presentation">'
            . '<a href="' . $categoryUrl . '" class="ui-corner-all" tabindex="-1" role="menuitem">'
            . '<span>' . $parentCategory->getName() . '</span></a>';

        $childCategorys = $this->getChildCategory($parentCategory->getId());

        if (count($childCategorys) > 0) {
            $html .= '<ul class="level' . $parentCategory->getLevel() . ' submenu ui-menu ui-widget'
                . ' ui-widget-content ui-corner-all"'
                . ' role="menu" aria-expanded="false" style="display: none; top: 47px; left: -0.15625px;"'
                . ' aria-hidden="true">';

            /** @var Category $childCategory */
            foreach ($childCategorys as $childCategory) {
                $html .= $this->getMenuHtml($childCategory);
            }
            $html .= '</ul>';
        }
        $html .= '</li>';

        return $html;
    }


    /**
     * @return string
     */
    public function getBlogHomePageTitle()
    {
        return  __('Blog');
    }

    /**
     * @return string
     */

}
