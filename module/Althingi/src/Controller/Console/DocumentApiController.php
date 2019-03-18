<?php

namespace Althingi\Controller\Console;

use Zend\Mvc\Controller\AbstractActionController;

class DocumentApiController extends AbstractActionController
{
    public function indexAction()
    {
        echo json_encode(
            (new \Althingi\Utils\RouteInspector())->run(require __DIR__ . '/../../../config/module.config.php'),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }
}
