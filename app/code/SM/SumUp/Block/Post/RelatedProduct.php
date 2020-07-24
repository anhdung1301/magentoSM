<?php


namespace SM\SumUp\Block\Post;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Helper\Output;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Url\Helper\Data;
use SM\SumUp\Helper\Data as HelperData;

/**
 * Class RelatedProduct
 * @package SM\SumUp\Block\Post
 */
class RelatedProduct extends ListProduct
{
    /**
     * Default related product page title
     */
    const TITLE = 'Related Products';
    /**
     * Default limit related products
     */
    const LIMIT = '12';

    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * @var Output
     */
    protected $outputHelper;

    /**
     * RelatedProduct constructor.
     *
     * @param Context $context
     * @param PostHelper $postDataHelper
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CollectionFactory $productCollectionFactory
     * @param HelperData $helperData
     * @param Output $output
     * @param Data $urlHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        CollectionFactory $productCollectionFactory,
        HelperData $helperData,
        Output $output,
        Data $urlHelper,
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->helper                    = $helperData;
        $this->outputHelper              = $output;

        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
    }

    /**
     * @return Output
     */
    public function getHelper()
    {
        return $this->outputHelper;
    }

    /**
     * @return mixed
     */
    public function relatedMode()
    {
        return $this->helper->getModuleConfig('product_post/post_detail/related_mode');
    }

    /**
     * @return mixed
     */
    public function hasProduct()
    {
        $collection = $this->_getProductCollection();

        return $collection->getSize();
    }

    /**
     * @return mixed
     * get ProductCollection in same brand ( filter by Atrribute Option_Id )
     */
    public function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $postId     = $this->getRequest()->getParam('id');
            $collection = $this->_productCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addStoreFilter();

            $collection->getSelect()
                ->join(
                    ['product_post' => $collection->getTable('sm_blog_post_product')],
                    'e.entity_id = product_post.entity_id'
                )
                ->where('product_post.post_id = ' . $postId)
                ->order('product_post.position ASC')
                ->limit((int) self::LIMIT);

            $this->_productCollection = $collection;
        }

        return $this->_productCollection;
    }

    /**
     * @inheritdoc
     */
    public function getMode()
    {
        return 'grid';
    }

    /**
     * @inheritdoc
     */
    public function getToolbarHtml()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getAdditionalHtml()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    protected function _beforeToHtml()
    {
        return $this;
    }
}
