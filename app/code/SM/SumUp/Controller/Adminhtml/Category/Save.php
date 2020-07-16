<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/22/19
 * Time: 5:24 PM
 */

namespace SM\SumUp\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;
use SM\SumUp\Model\CategoryFactory;


class Save extends \SM\SumUp\Controller\Adminhtml\AbstractSave
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'SM_SumUp::save';
    /**
     * @var ConditionFactory
     */
    protected $modelFactory;
    /**
     * @var string
     */
    protected $idFieldName = 'category_id';

    /**
     * Save constructor.
     * @param Context $context
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        Context $context,
        CategoryFactory $categoryFactory
    ) {
        $this->modelFactory = $categoryFactory;
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getMessageSuccess()
    {
        return __('save condition success');
    }
}
