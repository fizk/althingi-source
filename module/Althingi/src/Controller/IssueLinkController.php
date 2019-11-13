<?php

namespace Althingi\Controller;

use Althingi\Injector\ServiceIssueLinkAwareInterface;
use Althingi\Service\IssueLink;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\CollectionModel;
use Althingi\Form;

class IssueLinkController extends AbstractRestfulController implements
    ServiceIssueLinkAwareInterface
{
    /** @var \Althingi\Service\IssueLink */
    private $issueLinkService;

    /**
     * @param mixed $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Committee
     */
    public function get($id)
    {
        $assemblyId = $this->params('id', 0);
        $issueId = $this->params('issue_id', 0);
        $category = $this->params('category', 'A');

        $issues = $this->issueLinkService->fetchAll($assemblyId, $issueId, $category);
        return (new CollectionModel($issues))
            ->setStatus(206)
            ->setRange(0, count($issues), count($issues));
    }

    public function put($id, $data)
    {
        $assemblyId = $this->params('id', 0);
        $issueId = $this->params('issue_id', 0);
        $category = $this->params('category', 'A');

        $form = new Form\IssueLink();
        $form->bindValues(array_merge($data, [
            'from_assembly_id' => $assemblyId,
            'from_issue_id' => $issueId,
            'from_category' => strtoupper($category),
        ]));

        if ($form->isValid()) {
            $object = $form->getObject();
            $affectedRows = $this->issueLinkService->save($object);
            return (new EmptyModel())
                ->setStatus($affectedRows === 1 ? 201 : 205);
        }

        return (new ErrorModel($form))
            ->setStatus(400);
    }

    /**
     * Update? A link is never updated, there are no additional info that can be added to a link,
     * once it has been connected, that it. The Aggregator could try to patch a link, therefor there
     * is this method that just says OK...
     *
     * @param $id
     * @param $data
     * @return EmptyModel|\Rend\View\Model\ModelInterface
     */
    public function patch($id, $data)
    {
        return (new EmptyModel())
            ->setStatus(205);
    }


    /**
     * @return CollectionModel
     * @output \Althingi\Model\Committee[]
     */
    public function getList()
    {
        return new CollectionModel(['collection']);
    }

    /**
     * @param \Althingi\Service\IssueLink $issueLink
     * @return $this;
     */
    public function setIssueLinkService(IssueLink $issueLink)
    {
        $this->issueLinkService = $issueLink;
        return $this;
    }
}
