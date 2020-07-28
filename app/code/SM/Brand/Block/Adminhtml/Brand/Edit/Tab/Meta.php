<?php

namespace SM\Brand\Block\Adminhtml\Brand\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\Form;
use Magento\Framework\Phrase;

/**
 * Customer account form block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Meta extends Generic implements TabInterface
{
    /**
     * Prepare label for tab
     *
     * @return Phrase
     */
    public function getTabLabel()
    {
        return __('Meta Data');
    }

    /**
     * Prepare title for tab
     *
     * @return Phrase
     */
    public function getTabTitle()
    {
        return __('Meta Data');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('SM_Brand::brand_edit')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        /** @var Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('brand_');

        $model = $this->_coreRegistry->registry('SM_brand');

        $fieldset = $form->addFieldset(
            'meta_fieldset',
            ['legend' => __('Meta Data'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'page_title',
            'text',
            [
                'name' => 'page_title',
                'label' => __('Page Title'),
                'title' => __('Page Title'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'meta_keywords',
            'textarea',
            [
                'name' => 'meta_keywords',
                'label' => __('Keywords'),
                'title' => __('Meta Keywords'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'meta_description',
            'textarea',
            [
                'name' => 'meta_description',
                'label' => __('Description'),
                'title' => __('Meta Description'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField('country', 'select', [
            'name'     => 'author_id',
            'label'    => __('Author'),
            'title'    => __('Author'),
            'required' => true,
            'values'   => 'Magento\Directory\Model\Config\Source\Country',
            'component' => 'Magento_Ui/js/form/element/country'
        ]);
        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
