<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Form\Issue;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;

class IssueController extends AbstractRestfulController
{
    use Range;

    public function get($id)
    {
        $assemblyId = $this->params('id', 0);
        $issue = $this->getServiceLocator()->get('Althingi\Service\Issue')
            ->get($id, $assemblyId);

        if (!$issue) {
            return $this->notFoundAction();
        }

        return new ItemModel($issue);
    }

    public function getList()
    {
        /** @var $issueService \Althingi\Service\Issue */
        $issueService = $this->getServiceLocator()
            ->get('Althingi\Service\Issue');

        $assemblyId = $this->params('id', null);
        $count = $issueService->countByAssembly($assemblyId);
        $range = $this->getRange($this->getRequest(), $count);
        $issues = $issueService->fetchByAssembly(
            $assemblyId,
            $range['from'],
            ($range['to']-$range['from'])
        );
        return (new CollectionModel($issues))
            ->setStatus(206)
            ->setRange($range['from'], $range['to'], $count);
    }

    public function put($id, $data)
    {
        $issueService = $this->getServiceLocator()
            ->get('Althingi\Service\Issue');

        $form = (new Issue())
            ->setData(array_merge(
                $data,
                ['assembly_id' => $this->params('id'), 'issue_id' => $id]
            ));

        if ($form->isValid()) {
            $issueService->create($form->getObject());
            return (new EmptyModel())->setStatus(201);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    public function patch($id, $data)
    {
        $assemblyId = $this->params('id', 0);
        $issueService = $this->getServiceLocator()->get('Althingi\Service\Issue');
        $issue = $issueService->get($id, $assemblyId);

        if (!$issue) {
            return $this->notFoundAction();
        }

        $form = new Issue();
        $form->setObject($issue);
        $form->setData($data);

        if ($form->isValid()) {
            $issueService->update($form->getObject());
            return (new EmptyModel())->setStatus(200);
        }

        return (new ErrorModel($form))->setStatus(400);
    }
}
