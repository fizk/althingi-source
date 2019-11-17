<?php

namespace Althingi\Controller;

use Althingi\Utils\OpenAPI;
use Rend\View\Model\ItemModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function openApiAction()
    {
        $routes = (new OpenAPI())->transform(
            (new \Althingi\Utils\RouteInspector())
                ->run(require __DIR__ . '/../../config/module.config.php')
        );

        return (new ItemModel($routes));
    }
}
