<?php

namespace SM\SumUp\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class AuthorStatus
 * @package Mageplaza\Faqs\Model\Config\Source
 */
class AuthorStatus implements ArrayInterface
{

    const PENDING     = '0';
    const APPROVED    = '1';
    const DISAPPROVED = '2';

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
            self::PENDING     => __('Pending'),
            self::APPROVED    => __('Approved'),
            self::DISAPPROVED => __('Disapproved')
        ];
    }
}
