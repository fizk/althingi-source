<?php

namespace Althingi\Controller;

use Althingi\Model;
use Althingi\Service;
use Althingi\Form;
use Althingi\Injector\ServiceVoteItemAwareInterface;
use Althingi\Injector\ServiceCongressmanAwareInterface;
use Althingi\Injector\ServiceDocumentAwareInterface;
use Althingi\Injector\ServicePartyAwareInterface;
use Althingi\Injector\ServiceVoteAwareInterface;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\ItemModel;

class DocumentController extends AbstractRestfulController implements
    ServiceDocumentAwareInterface,
    ServiceVoteAwareInterface,
    ServiceCongressmanAwareInterface,
    ServicePartyAwareInterface,
    ServiceVoteItemAwareInterface
{
    /** @var  \Althingi\Service\Document */
    private $documentService;

    /** @var  \Althingi\Service\Vote */
    private $voteService;

    /** @var  \Althingi\Service\VoteItem */
    private $voteItemService;

    /** @var  \Althingi\Service\Congressman */
    private $congressmanService;

    /** @var  \Althingi\Service\Party */
    private $partyService;

    /**
     * @param mixed $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Document
     */
    public function get($id)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $documentId = $this->params('document_id');

        if (($document = $this->documentService->get($assemblyId, $issueId, $documentId)) != null) {
            return (new ItemModel($document));
        } else {
            return $this->notFoundAction();
        }
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\DocumentProperties
     */
    public function getList()
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');

        $documents = array_map(function (Model\Document $document) use ($assemblyId, $issueId) {
            $votes = $this->voteService->fetchByDocument($assemblyId, $issueId, $document->getDocumentId());
            $congressmen = array_map(function (Model\Proponent $proponent) use ($document) {
                return (new Model\ProponentPartyProperties())
                    ->setCongressman($proponent)
                    ->setParty($this->partyService->getByCongressman(
                        $proponent->getCongressmanId(),
                        $document->getDate()
                    ));
            }, $this->congressmanService->fetchProponents($assemblyId, $document->getDocumentId()));

            $documentProperties = (new Model\DocumentProperties())
                ->setDocument($document)
                ->setVotes($votes)
                ->setProponents($congressmen);

            return $documentProperties;
        }, $this->documentService->fetchByIssue($assemblyId, $issueId));
        $documentsCount = count($documents);

        return (new CollectionModel($documents))
            ->setStatus(206)
            ->setRange(0, $documentsCount, $documentsCount);
    }

    /**
     * @param mixed $id
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Document
     */
    public function put($id, $data)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $documentId = $this->params('document_id');

        $form = new Form\Document();
        $form->bindValues(array_merge(
            $data,
            [
                'assembly_id' => $assemblyId,
                'issue_id' => $issueId,
                'document_id' => $documentId,
                'category' => 'A',
            ]
        ));

        if ($form->isValid()) {
            $affectedRows = $this->documentService->save($form->getObject());
            return (new EmptyModel())->setStatus($affectedRows === 1 ? 201 : 205);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * @param $id
     * @param $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Document
     */
    public function patch($id, $data)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $documentId = $this->params('document_id');

        if (($assembly = $this->documentService->get($assemblyId, $issueId, $documentId)) != null) {
            $form = new Form\Document();
            $form->bind($assembly);
            $form->setData($data);

            if ($form->isValid()) {
                $this->documentService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(205);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return $this->notFoundAction();
    }

    /**
     * @param \Althingi\Service\Document $document
     * @return $this
     */
    public function setDocumentService(Service\Document $document)
    {
        $this->documentService = $document;
        return $this;
    }

    /**
     * @param \Althingi\Service\Congressman $congressman
     * @return $this
     */
    public function setCongressmanService(Service\Congressman $congressman)
    {
        $this->congressmanService = $congressman;
        return $this;
    }

    /**
     * @param \Althingi\Service\Party $party
     * @return $this
     */
    public function setPartyService(Service\Party $party)
    {
        $this->partyService = $party;
        return $this;
    }

    /**
     * @param \Althingi\Service\Vote $vote
     * @return $this
     */
    public function setVoteService(Service\Vote $vote)
    {
        $this->voteService = $vote;
        return $this;
    }

    /**
     * @param \Althingi\Service\VoteItem $voteItem
     * @return $this
     */
    public function setVoteItemService(Service\VoteItem $voteItem)
    {
        $this->voteItemService = $voteItem;
        return $this;
    }
}
