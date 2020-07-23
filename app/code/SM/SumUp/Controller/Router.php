<?php


namespace SM\SumUp\Controller;

use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\Url;
use SM\SumUp\Helper\Data;

/**
 * Class Router
 * @package SM\SumUp\Controller
 */
class Router implements RouterInterface
{
    const URL_SUFFIX_RSS_XML = '.xml';

    /**
     * @var ActionFactory
     */
    public $actionFactory;

    /**
     * @var Data
     */
    public $helper;

    /**
     * @param ActionFactory $actionFactory
     * @param Data $helper
     */
    public function __construct(
        ActionFactory $actionFactory,
        Data $helper
    ) {
        $this->actionFactory = $actionFactory;
        $this->helper        = $helper;
    }

    /**
     * @param RequestInterface $request
     *
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        $routePath = explode('/', $identifier);

        $routeSize = count($routePath);
        if (!$routeSize || ($routeSize > 3) || (array_shift($routePath) !== $this->helper->getRoute())) {
            return null;
        }
        $request->setModuleName('sumup')
            ->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $identifier );
        $controller = array_shift($routePath);
        if (!$controller) {
            $request->setControllerName('post')
                ->setActionName('index')
                ->setPathInfo('index');

            return $this->actionFactory->create(Forward::class);
        }

        $action = array_shift($routePath) ?: 'index';

        switch ($controller) {
            case 'post':
                if (!in_array($action, ['index', 'rss'])) {

                    $post = $this->helper->getObjectByParam($action, 'url_key');
                    $request->setParam('id', $post->getId());
                    $action = 'view';
                }
                break;
            case 'category':
                if (!in_array($action, ['index', 'rss'])) {
                    $category = $this->helper->getObjectByParam($action, 'url_key', Data::TYPE_CATEGORY);
                    $request->setParam('id', $category->getId());
                    $action = 'view';
                }
                break;
            case 'tag':
                $tag = $this->helper->getObjectByParam($action, 'url_key', Data::TYPE_TAG);
                $request->setParam('id', $tag->getId());
                $action = 'view';
                break;

            case 'author':
                $author = $this->helper->getObjectByParam($action, 'url_key', Data::TYPE_AUTHOR);
                $request->setParam('id', $author->getId());
                $action = 'view';
                break;
            default:
                $post = $this->helper->getObjectByParam($controller, 'url_key');
                $request->setParam('id', $post->getId());
                $controller = 'post';
                $action     = 'view';
        }


        $request->setControllerName($controller)
            ->setActionName($action)
            ->setPathInfo('/sumup/' . $controller . '/' . $action);

        return $this->actionFactory->create(Forward::class);
    }


}
