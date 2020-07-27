<?php


namespace SM\SumUp\Block;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Theme\Block\Html\Pager;
use SM\SumUp\Model\Config\Source\DisplayType;
use SM\SumUp\Model\ResourceModel\Post\Collection;

/**
 * Class Listpost
 * @package SM\SumUp\Block\Post
 */
class Listpost extends Frontend
{
    /**
     * @return Collection
     * @throws LocalizedException
     */
    public function getPostCollection()
    {
        $collection = $this->getCollection();

        if ($collection && $collection->getSize()) {
            $pager = $this->getLayout()->createBlock(Pager::class, 'mpblog.post.pager');
            $perPageValues = (string) $this->helperData->getConfigGeneral('pagination');
            $perPageValues = explode(',', $perPageValues);
            $perPageValues = array_combine($perPageValues, $perPageValues);
            $pager->setAvailableLimit($perPageValues)
                ->setCollection($collection);
            $this->setChild('pager', $pager);
        }
        return $collection;
    }

    /**
     * find /n in text
     *
     * @param $description
     *
     * @return string
     */
    public function maxShortDescription($description)
    {
        if (is_string($description)) {
            $html = '';
            foreach (explode("\n", trim($description)) as $value) {
                $html .= '<p>' . $value . '</p>';
            }

            return $html;
        }

        return $description;
    }

    /**
     * @return Collection
     */
    protected function getCollection()
    {
        try {
            return $this->helperData->getPostCollection(null, null, $this->store->getStore()->getId());
        } catch (Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }

        return null;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return bool
     */
    public function isGridView()
    {
        return $this->helperData->getBlogConfig('general/display_style') == DisplayType::GRID;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link'  => $this->_storeManager->getStore()->getBaseUrl()
            ])
                ->addCrumb($this->helperData->getRoute(), $this->getBreadcrumbsData());
        }
        return parent::_prepareLayout();
    }

    /**
     * @return array
     */
    protected function getBreadcrumbsData()
    {
        $label = $this->helperData->getBlogName();

        $data = [
            'label' => $label,
            'title' => $label,
            'link' => '/sumup'
        ];


        return $data;
    }
    /**
     * Retrieve HTML title value separator (with space)
     *
     * @return string
     */
    public function getTitleSeparator()
    {
        $separator = (string) $this->helperData->getConfigValue('catalog/seo/title_separator');

        return ' ' . $separator . ' ';
    }

    /**
     * @param bool $meta
     *
     * @return array|Phrase
     */
    public function getBlogTitle($meta = false)
    {
        $pageTitle = $this->helperData->getConfigGeneral('name') ?: __('Blog');
        if ($meta) {
            $title = $this->helperData->getSeoConfig('meta_title') ?: $pageTitle;

            return [$title];
        }

        return $pageTitle;
    }
}
