<?php


namespace SM\SumUp\Block\Adminhtml\Post\Edit\Tab\Renderer;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\Multiselect;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use SM\SumUp\Model\ResourceModel\Tag\Collection;
use SM\SumUp\Model\ResourceModel\Tag\CollectionFactory as BlogTagCollectionFactory;

/**
 * Class Tag
 * @package SM\SumUp\Block\Adminhtml\Post\Edit\Tab\Renderer
 */
class Tag extends Multiselect
{
    /**
     * @var BlogTagCollectionFactory
     */
    public $collectionFactory;

    /**
     * Authorization
     *
     * @var AuthorizationInterface
     */
    public $authorization;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Tag constructor.
     *
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param BlogTagCollectionFactory $collectionFactory
     * @param AuthorizationInterface $authorization
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        BlogTagCollectionFactory $collectionFactory,
        AuthorizationInterface $authorization,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->authorization     = $authorization;
        $this->_urlBuilder       = $urlBuilder;

        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * @inheritdoc
     */
    public function getElementHtml()
    {
        $html = '<div class="admin__field-control admin__control-grouped">';
        $html .= '<div id="blog-tag-select" class="admin__field" data-bind="scope:\'blogTag\'" data-index="index">';
        $html .= '<!-- ko foreach: elems() -->';
        $html .= '<input name="post[tags_ids]" data-bind="value: value" style="display: none"/>';
        $html .= '<!-- ko template: elementTmpl --><!-- /ko -->';
        $html .= '<!-- /ko -->';

        $html .= '</div></div>';

        $html .= '<!-- ko scope: \'create_tag_modal\' --><!-- ko template: getTemplate() --><!-- /ko --><!-- /ko -->';

        $html .= $this->getAfterElementHtml();

        return $html;
    }

    /**
     * Get no display
     *
     * @return bool
     */
    public function getNoDisplay()
    {
        $isNotAllowed = !$this->authorization->isAllowed('SM_SumUp::tag');

        return $this->getData('no_display') || $isNotAllowed;
    }

    /**
     * @return mixed
     */
    public function getTagsCollection()
    {
        /* @var $collection Collection */
        $collection = $this->collectionFactory->create();
        $tagById    = [];
        foreach ($collection as $tag) {
            $tagById[$tag->getId()]['value']     = $tag->getId();
            $tagById[$tag->getId()]['is_active'] = 1;
            $tagById[$tag->getId()]['label']     = $tag->getName();
        }

        return $tagById;
    }

    /**
     * Get values for select
     *
     * @return array
     */
    public function getValues()
    {
        $values = $this->getValue();

        if (!is_array($values)) {
            $values = explode(',', $values);
        }

        if (!count($values)) {
            return [];
        }

        /* @var $collection Collection */
        $collection = $this->collectionFactory->create()
            ->addIdFilter($values);

        $options = [];
        foreach ($collection as $tag) {
            $options[] = $tag->getId();
        }

        return $options;
    }

    /**
     * Attach Blog Tag suggest widget initialization
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        $html = '<script type="text/x-magento-init">
            {
                "*": {
                    "Magento_Ui/js/core/app": {
                        "components": {
                            "blogTag": {
                                "component": "uiComponent",
                                "children": {
                                    "blog_select_tag": {
                                        "component": "SM_SumUp/js/components/new-category",
                                        "config": {
                                            "filterOptions": true,
                                            "disableLabel": true,
                                            "chipsEnabled": true,
                                            "levelsVisibility": "1",
                                            "elementTmpl": "ui/grid/filters/elements/ui-select",
                                            "options": ' . json_encode($this->getTagsCollection()) . ',
                                            "value": ' . json_encode($this->getValues()) . ',
                                            "listens": {
                                                "index=create_tag:responseData": "setParsed",
                                                "newOption": "toggleOptionSelected"
                                            },
                                            "config": {
                                                "dataScope": "blog_select_tag",
                                                "sortOrder": 10
                                            }
                                        }
                                    }
                                }
                            }

                        }
                    }
                }
            }
        </script>';

        return $html;

    }
}
