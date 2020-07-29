<?php

namespace SM\Brand\Block\Category;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
//use SM\Brand\Api\Data\CategoryInterface;
use SM\Brand\Helper\Data as HelperData;
use SM\Brand\Model\BrandFactory;
use SM\Brand\Model\ResourceModel\Brand\Collection;
use SM\Brand\Model\ResourceModel\Brand\CollectionFactory;

/**
 * Class Widget
 * @package SM\Brand\Block\Category
 */
class Menu extends Template
{
    /**
     * @var CollectionFactory
     */
    protected $brandCollection;

    /**
     * @var BrandFactory
     */
    protected $brand;

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
        BrandFactory $brandFactory,
        HelperData $helperData,
        array $data = []
    ) {
        $this->brandCollection = $collectionFactory;
        $this->brand = $brandFactory;
        $this->helper = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * @return Collection
     */
    public function getCollections()
    {
    return $this->brandCollection->
    create()->addFieldToFilter('enabled', '1')
        ->addFieldToFilter('is_feature', '1')
        ->setOrder('name', 'ASC');

    }

    public function getUrlBlog()
    {
        $brandRoute = $this->helper->getConfig('general_settings/route');

        return $this->getUrl($brandRoute);
    }

    /**
     * @return string
     */
    public function getBlogHomePageTitle()
    {
        return __('Brand');
    }

    /**
     * @return string
     */
}
