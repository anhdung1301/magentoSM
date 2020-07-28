<?php

namespace SM\Brand\Model;

class Brandlist extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected  $_brand;

    /**
     *
     * @param \SM\Brand\Model\Brand $brand
     */
    public function __construct(
        \SM\Brand\Model\Brand $brand
    ) {
        $this->_brand = $brand;
    }


    /**
     * Get Gift Card available templates
     *
     * @return array
     */
    public function getAvailableTemplate()
    {
        $brands = $this->_brand->getCollection()
            ->addFieldToFilter('enabled', '1');
        $listBrand = array();
        foreach ($brands as $brand) {
            $listBrand[] = array('label' => $brand->getName(),
                'value' => $brand->getId());
        }
        return $listBrand;
    }

    /**
     * Get model option as array
     *
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        $options = array();
        $options = $this->getAvailableTemplate();

        if ($withEmpty) {
            array_unshift($options, array(
                'value' => '',
                'label' => '-- Please Select --',
            ));
        }
        return $options;
    }
}
