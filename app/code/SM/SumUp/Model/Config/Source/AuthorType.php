<?php


namespace SM\SumUp\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;


class AuthorType implements ArrayInterface
{

    const ADMIN    = '0';
    const CUSTOMER = '1';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->toArray() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::ADMIN    => __('Created by Admin'),
            self::CUSTOMER => __('Customer Register')
        ];
    }
}
