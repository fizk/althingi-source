<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 6/06/15
 * Time: 11:19 AM
 */

namespace Althingi\Event;

use Althingi\View\Model\ErrorModel;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ModelInterface;

class ApplicationErrorHandler
{
    public function __invoke(MvcEvent $event)
    {

        if (!$event->isError()) {
            return;
        }

        $currentModel = $event->getResult();
        $exception = $currentModel->getVariable('exception');

        if ($currentModel instanceof ModelInterface && $currentModel->reason) {
            switch ($currentModel->reason) {
                case 'error-controller-cannot-dispatch':
                    $currentModel = (new ErrorModel('The requested controller was unable to dispatch the request.'))
                        ->setStatus(500);
                    break;
                case 'error-controller-not-found':
                    $currentModel = (new ErrorModel('The requested controller could '.
                        'not be mapped to an existing controller class.'))
                        ->setStatus(404);
                    break;
                case 'error-controller-invalid':
                    $currentModel = (new ErrorModel('The requested controller was not dispatchable.'))
                        ->setStatus(500);
                    break;
                case 'error-router-no-match':
                    $currentModel = (new ErrorModel('The requested URL could not be matched by routing.'))
                        ->setStatus(404);
                    break;
                default:
                    $currentModel = (new ErrorModel($currentModel->message))
                        ->setStatus(500);
                    break;
            }
        }

        if ($exception) {
            switch ($exception->getCode()) {
                case 23000: //PDO Exception - Integrity constraint violation: 1062 Duplicate entry
                    $currentModel =  (new ErrorModel(new \Exception('Entry already exists', 0, $exception)))
                        ->setStatus(409); //409 Conflict
                    break;  //General 404 error
                case 404:
                    $currentModel =  (new ErrorModel($exception))
                        ->setStatus(404);
                    break;
                default:
                    $currentModel =  (new ErrorModel($exception));
                    break;
            }
        }

        $currentModel->setTerminal(true);
        $event->setResult($currentModel);
        $event->setViewModel($currentModel);
    }
}
