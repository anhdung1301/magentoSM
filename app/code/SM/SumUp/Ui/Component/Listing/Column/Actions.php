<?php


namespace SM\SumUp\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;


class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $actions = $this->getData('action_list');
                foreach ($actions as $key => $action) {
                    $params = $action['params'];
                    foreach ($params as $field => $param) {
                        $params[$field] = $item[$param];
                    }

                    $item[$this->getData('name')][$key] = [
                        'href'   => $this->urlBuilder->getUrl(
                            $action['path'],
                            ['id' => $item[$action['params']['id']]]
                        ),
                        'label'  => $action['label'],
                        'hidden' => false,
                    ];
                }
            }
        }

        return $dataSource;
    }
}
