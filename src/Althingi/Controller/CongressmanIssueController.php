<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 8/06/15
 * Time: 9:05 PM
 */

namespace Althingi\Controller;

use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;

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
