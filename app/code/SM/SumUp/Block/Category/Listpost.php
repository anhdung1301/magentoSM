<?php


namespace SM\SumUp\Block\Category;

use SM\SumUp\Helper\Data;
use SM\SumUp\Model\ResourceModel\Post\Collection;

/**
 * Class Listpost
 * @package SM\SumUp\Block\Category
 */
class Listpost extends \SM\SumUp\Block\Listpost
{
    /**
     * @var string
     */
    protected $_category;

    /**
     * Override this function to apply collection for each type
     *
     * @return Collection
     */
    protected function getCollection()
    {
        if ($category = $this->getBlogObject()) {
            return $this->helperData->getPostCollection(Data::TYPE_CATEGORY, $category->getId());
        }

        return null;
    }

    /**
     * @return mixed
     */
    protected function getBlogObject()
    {
        if (!$this->_category) {
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $category = $this->helperData->getObjectByParam($id, null, Data::TYPE_CATEGORY);
                if ($category && $category->getId()) {
                    $this->_category = $category;
                }
            }
        }

        return $this->_category;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $category = $this->getBlogObject();
            if ($category) {
                $breadcrumbs->addCrumb($category->getUrlKey(), [
                    'label' => __('Category'),
                    'title' => __('Category')
                ]);
            }
        }
    }

    /**
     * @param bool $meta
     *
     * @return array
     */
    public function getBlogTitle($meta = false)
    {
        $blogTitle = parent::getBlogTitle($meta);
        $category  = $this->getBlogObject();
        if (!$category) {
            return $blogTitle;
        }

        if ($meta) {
            if ($category->getMetaTitle()) {
                array_push($blogTitle, $category->getMetaTitle());
            } else {
                array_push($blogTitle, ucfirst($category->getName()));
            }

            return $blogTitle;
        }

        return ucfirst($category->getName());
    }
}
