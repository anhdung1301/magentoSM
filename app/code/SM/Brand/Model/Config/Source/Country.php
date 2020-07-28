<?php


namespace SM\Brand\Model\Config\Source;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Option\ArrayInterface;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;

class Country implements ArrayInterface
{

    public $_countryFactory;

    public function __construct(
        CollectionFactory $countryFactory
    ) {
        $this->_countryFactory = $countryFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->getCountryname() as $value => $country) {
            $options[] = [
                'value' => $value,
                'label' => $country->getName()
            ];
        }
        return $options;
    }

    public function getCountryname()
    {
        return $this->_countryFactory->create()->loadByStore();
    }
}
