<?php

namespace SM\SumUp\Block\Adminhtml\Tag\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Button "Create Tag" in "New Category" slide-out panel of a product page
 * Class CreateTag
 * @package SM\SumUp\Block\Adminhtml\Tag\Edit\Button
 */
class CreateTag implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label'          => __('Create Tag'),
            'class'          => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order'     => 10
        ];
    }
}
