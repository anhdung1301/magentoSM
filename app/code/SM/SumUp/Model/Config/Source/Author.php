<?php


namespace SM\SumUp\Model\Config\Source;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Option\ArrayInterface;
use SM\SumUp\Model\AuthorFactory;


class Author implements ArrayInterface
{
    /**
     * @var AuthorFactory
     */
    public $_authorFactory;

    public function __construct(
        AuthorFactory $authorFactory
    ) {
        $this->_authorFactory = $authorFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->getAuthors() as $value => $author) {
            $options[] = [
                'value' => $value,
                'label' => $author->getName()
            ];
        }

        return $options;
    }

    /**
     * @return AbstractCollection
     */
    public function getAuthors()
    {
        return $this->_authorFactory->create()->getCollection()->addFieldToFilter('status', '1');
    }
}
