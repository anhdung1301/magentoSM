<?php


namespace SM\Brand\Ui\Component\Listing\Columns;

use Magento\Catalog\Helper\Image;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use SM\Brand\Model\Brand as BrandModel;

class Thumbnail extends Column
{
    const NAME = 'thumbnail';

    const ALT_FIELD = 'name';

    /**
     * @param Filesystem $filesystem
     */
    protected $filesystem;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /** @var brandModel */
    protected $brandModel;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Image $imageHelper
     * @param UrlInterface $urlBuilder
     * @param Filesystem $filesystem
     * @param StoreManagerInterface $storeManager
     * @param BrandModel $brandModel
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Image $imageHelper,
        UrlInterface $urlBuilder,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        BrandModel $brandModel,
        array $components = [],
        array $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
        $this->filesystem = $filesystem;
        $this->_storeManager = $storeManager;
        $this->brandModel = $brandModel;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return void
     */
    public function prepareDataSource(array $dataSource)
    {

        /** @var Read $mediaDirectory */
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $mediaFolder = 'sumup/brand/';

        $path = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if (!isset($item['thumbnail'])) continue;
                if ($item['thumbnail']) {
                    $thumbnailUrl = $path . $item['thumbnail'];
                    $item[$fieldName . '_src'] = $thumbnailUrl;
                    $item[$fieldName . '_alt'] = $item['name'];
                    $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                        'brand/brand/edit',
                        ['brand_id' => $item['brand_id'], 'store' => $this->context->getRequestParam('store')]
                    );
                    $item[$fieldName . '_orig_src'] = $thumbnailUrl;
                }
            }
        }
        return $dataSource;
    }

//    /**
//     * @param array $row
//     *
//     * @return null|string
//     */
//    protected function getAlt($row)
//    {
//        $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
//        return isset($row[$altField]) ? $row[$altField] : null;
//    }
}
