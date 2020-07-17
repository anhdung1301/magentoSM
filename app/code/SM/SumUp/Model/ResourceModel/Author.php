<?php
namespace SM\SumUp\Model\ResourceModel;


class Author extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('sm_blog_author', 'user_id');
    }

}
