<?php


namespace SM\SumUp\Block\Adminhtml\Category;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use SM\SumUp\Helper\Data;
use SM\SumUp\Model\Category;

/**
 * Class Edit
 * @package SM\SumUp\Block\Adminhtml\Category
 */
class Edit extends Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * Edit constructor.
     *
     * @param Registry $coreRegistry
     * @param Data $helperData
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $coreRegistry,
        Data $helperData,
        Context $context,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->_helperData  = $helperData;

        parent::__construct($context, $data);
    }

    /**
     * prepare the form
     */
    protected function _construct()
    {
        $this->_objectId   = 'id';
        $this->_blockGroup = 'SM_SumUp';
        $this->_controller = 'adminhtml_category';

        parent::_construct();

        /** @var Category $category */
        $category = $this->coreRegistry->registry('category');

        if ($category->getId() && !$category->getDuplicate()) {
            $this->buttonList->add(
                'duplicate',
                [
                    'label'   => __('Duplicate'),
                    'class'   => 'duplicate',
                    'onclick' => sprintf("location.href = '%s';", $this->getDuplicateUrl()),
                ],
                -101
            );
        }

        $this->buttonList->remove('delete');
        $this->buttonList->remove('back');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('save');
    }

    /**
     * @return string
     */
    protected function getDuplicateUrl()
    {
        /** @var Category $category */
        $category = $this->coreRegistry->registry('category');

        return $this->getUrl(
            '*/*/duplicate',
            ['id' => $category->getId(), 'duplicate' => true, 'parent' => $category->getParentId()]
        );
    }

    /**
     * @return int
     */
    public function getMagentoVersion()
    {
        return (int) $this->_helperData->versionCompare('2.3.0') ? '4' : '';
    }
}
