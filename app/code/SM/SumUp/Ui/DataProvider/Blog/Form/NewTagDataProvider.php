<?php

namespace SM\SumUp\Ui\DataProvider\Blog\Form;

use Magento\Framework\UrlInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use SM\SumUp\Model\ResourceModel\Tag\CollectionFactory;

/**
 * DataProvider for new category form
 */
class NewTagDataProvider extends AbstractDataProvider
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param UrlInterface $urlBuilder
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        UrlInterface $urlBuilder,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection = $collectionFactory->create();
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
//        $this->data = array_replace_recursive(
//            $this->data,
//            [
//                'config' => [
//                    'data' => [
//                        'end'                    => 1,
//                    ]
//                ]
//            ]
//        );

        return $this->data;
    }
}
