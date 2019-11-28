<?php

namespace Althingi\Controller;

use Althingi\Utils\OpenAPI;
use Rend\View\Model\ItemModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    /** @var \Althingi\Utils\OpenAPI */
    private $openApi;

    public function indexAction()
    {
        return new ViewModel([
            'definition' => $this->openApi->getDefinition()
        ]);
    }

    public function openApiAction()
    {
        $routes = $this->openApi->transform(
            (new \Althingi\Utils\RouteInspector())
                ->run(require __DIR__ . '/../../config/module.config.php')
        );

        return (new ItemModel($routes));
    }

    public function setOpenApi(OpenAPI $openAPI)
    {
        $this->openApi = $openAPI;
        return $this;
    }
}
