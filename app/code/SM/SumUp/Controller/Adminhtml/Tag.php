<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SM\SumUp\Controller\Adminhtml;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;

/**
 * Class Condition
 * @package NiceForNow\HairCare\Controller\Adminhtml
 */
abstract class Tag extends Action
{

    const ADMIN_RESOURCE = 'SM_SumUp::Tag';
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Condition constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     */

    public function __construct(Context $context, Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * @param $resultPage
     * @return mixed
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('SM_SumUp::sm_blog_tag')
            ->addBreadcrumb(__('TAG'), __('TAG'))
            ->addBreadcrumb(__('Static Tag'), __('Static Tag'));
        return $resultPage;
    }
}
