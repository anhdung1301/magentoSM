<?php


namespace SM\SumUp\Block\Sidebar;

use Magento\Framework\Exception\NoSuchEntityException;
use SM\SumUp\Block\Frontend;
use SM\SumUp\Helper\Data;

/**
 * Class Search
 * @package SM\SumUp\Block\Sidebar
 */
class Search extends Frontend
{
    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getSearchBlogData()
    {
        $result    = [];
        $posts     = $this->helperData->getPostList();
        $limitDesc = 100;
        if (!empty($posts)) {
            foreach ($posts as $item) {
                $shortDescription = ($item->getShortDescription() && $limitDesc > 0) ?
                    $item->getShortDescription() : '';
                if (strlen($shortDescription) > $limitDesc) {
                    $shortDescription = mb_substr($shortDescription, 0, $limitDesc, 'UTF-8') . '...';
                }

                $result[] = [
                    'value' => $item->getName(),
                    'url'   => $item->getUrl(),
                    'image' => $this->resizeImage($item->getImage(), '100x'),
                    'desc'  => $shortDescription
                ];
            }
        }

        return Data::jsonEncode($result);
    }


}
