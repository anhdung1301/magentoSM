<?php

namespace SM\Brand\Block\Adminhtml\Brand\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;
use SM\Brand\Helper\Data;
use SM\Brand\Model\Brand;
use SM\Brand\Model\Config\Source\Country;


class Main extends Generic implements TabInterface
{
    protected $_country;

    /**
     * @var Store
     */
    protected $_systemStore;

    /**
     * @var Config
     */
    protected $_wysiwygConfig;

    /**
     * @var Data
     */
    protected $_viewHelper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Store $systemStore
     * @param Config $wysiwygConfig
     * @param Data $viewHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        Config $wysiwygConfig,
        Data $viewHelper,
        Country $country,
        array $data = []
    )
    {
        $this->_viewHelper = $viewHelper;
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_country = $country;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare label for tab
     *
     * @return Phrase
     */
    public function getTabLabel()
    {
        return __('Brand Information');
    }

    /**
     * Prepare title for tab
     *
     * @return Phrase
     */
    public function getTabTitle()
    {
        return __('Brand Information');
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
        /** @var $model Brand */
        $model = $this->_coreRegistry->registry('SM_brand');

        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);
        /**
         * Checking if user have permission to save information
         */
        if ($this->_isAllowedAction('SM_Brand::brand_edit')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        /** @var Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('brand_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Brand Information')]);

        if ($model->getId()) {
            $fieldset->addField('brand_id', 'hidden', ['name' => 'brand_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Brand Name'),
                'title' => __('Brand Name'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'url_key',
            'text',
            [
                'name' => 'url_key',
                'label' => __('URL Key'),
                'title' => __('URL Key'),
                'note' => __('Empty to auto create url key'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'image',
            'image',
            [
                'name' => 'image',
                'label' => __('Image'),
                'title' => __('Image'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'thumbnail',
            'image',
            [
                'name' => 'thumbnail',
                'label' => __('Thumbnail'),
                'title' => __('Thumbnail'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'description',
            'editor',
            [
                'name' => 'description',
                'style' => 'height:200px;',
                'label' => __('Description'),
                'title' => __('Description'),
                'disabled' => $isElementDisabled,
                'config' => $wysiwygConfig
            ]
        );


        $fieldset->addField(
            'enabled',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Page Status'),
                'name' => 'enabled',
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );


        $fieldset->addField(
            'is_feature',
            'select',
            [
                'label' => __('is_feature'),
                'title' => __('is_feature'),
                'name' => 'is_feature',
                'options' => ['1' => __('Yes'), '0' => __('No')],
                'value' => $this->getData('is_feature')
            ]
        );
        $fieldset->addField('country', 'select', [
            'name'   => 'country',
            'label'  => __('Country'),
            'title'  => __('Country'),
            'values' => $this->_country->toOptionArray()
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
