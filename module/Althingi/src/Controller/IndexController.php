<?php

namespace Althingi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel([
            'routes' => (new \Althingi\Utils\RouteInspector())->run(require __DIR__ . '/../../config/module.config.php')
        ]);
    }
}
