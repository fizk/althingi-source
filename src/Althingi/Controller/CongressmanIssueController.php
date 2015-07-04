<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 8/06/15
 * Time: 9:05 PM
 */

namespace Althingi\Controller;

use Althingi\Form\Session;
use Althingi\View\Model\CollectionModel;
use Althingi\View\Model\EmptyModel;
use Althingi\View\Model\ErrorModel;
use Althingi\View\Model\ItemModel;
use Zend\Form\FormInterface;

/**
 * Class CongressmanSessionController
 * @package Althingi\Controller
 */
class CongressmanIssueController extends AbstractRestfulController
{
    public function getList()
    {
        $congressmanId = $this->params('id');
        $issueService = $this->getServiceLocator()
            ->get('Althingi\Service\Issue');

        $issues = $issueService->fetchByCongressman($congressmanId);

        return (new CollectionModel($issues));

    }
}
