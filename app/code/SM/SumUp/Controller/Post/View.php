<?php



namespace SM\SumUp\Controller\Post;

use Exception;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Json\Helper\Data as JsonData;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use SM\SumUp\Helper\Data;
use SM\SumUp\Helper\Data as HelperBlog;
use SM\SumUp\Model\PostFactory;

/**
 * Class View
 * @package SM\SumUp\Controller\Post
 */
class View extends Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var HelperBlog
     */
    protected $helperBlog;

    /**
     * @var AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var CustomerUrl
     */
    protected $customerUrl;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var JsonData
     */
    protected $jsonHelper;



    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var TimezoneInterface
     */
    protected $timeZone;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var PostFactory
     */
    protected $postFactory;

    /**
     * View constructor.
     *
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     * @param StoreManagerInterface $storeManager
     * @param JsonData $jsonHelper
     * @param DateTime $dateTime
     * @param TimezoneInterface $timezone
     * @param HelperBlog $helperBlog
     * @param PageFactory $resultPageFactory
     * @param AccountManagementInterface $accountManagement
     * @param CustomerUrl $customerUrl
     * @param Session $customerSession
     * @param PostFactory $postFactory
     */
    public function __construct(
        Context $context,
        ForwardFactory $resultForwardFactory,
        StoreManagerInterface $storeManager,
        JsonData $jsonHelper,
        DateTime $dateTime,
        TimezoneInterface $timezone,
        HelperBlog $helperBlog,
        PageFactory $resultPageFactory,
        AccountManagementInterface $accountManagement,
        CustomerUrl $customerUrl,
        Session $customerSession,
        PostFactory $postFactory
    )
    {
        $this->storeManager = $storeManager;
        $this->helperBlog = $helperBlog;
        $this->resultPageFactory = $resultPageFactory;
        $this->accountManagement = $accountManagement;
        $this->customerUrl = $customerUrl;
        $this->session = $customerSession;
        $this->timeZone = $timezone;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->jsonHelper = $jsonHelper;
        $this->dateTime = $dateTime;
        $this->postFactory = $postFactory;

        parent::__construct($context);
    }

    /**
     * @return $this|ResponseInterface|ResultInterface|Page
     * @throws Exception
     */
    public function execute()
    {



        $id = $this->getRequest()->getParam('id');
        $post = $this->helperBlog->getFactoryByType(Data::TYPE_POST)->create()->load($id);
        $page = $this->resultPageFactory->create();
        $pageLayout = ($post->getLayout() === 'empty') ? $this->helperBlog->getSidebarLayout() : $post->getLayout();
        $page->getConfig()->setPageLayout($pageLayout);
        return $post->getEnabled() ? $page : $this->_redirect('noroute');
    }
}
