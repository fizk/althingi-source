<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 22/03/2016
 * Time: 10:45 AM
 */

namespace Althingi\Controller;

use Althingi\Lib\ServiceVoteItemAwareInterface;
use Althingi\Model\Proponent as ProponentModel;
use Althingi\Model\ProponentPartyProperties as ProponentPartyPropertiesModel;
use Althingi\Service\Congressman;
use Althingi\Service\Party;
use Althingi\Service\Vote;
use Althingi\Service\VoteItem;
use Althingi\Model\Document as DocumentModel;
use Althingi\Model\DocumentProperties as DocumentPropertiesModel;
use Althingi\Form\Document as DocumentForm;
use Althingi\Lib\ServiceCongressmanAwareInterface;
use Althingi\Lib\ServiceDocumentAwareInterface;
use Althingi\Lib\ServicePartyAwareInterface;
use Althingi\Lib\ServiceVoteAwareInterface;
use Althingi\Service\Document;
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

    public function getList()
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');

        $documents = array_map(function (DocumentModel $document) use ($assemblyId, $issueId) {

            $votes = $this->voteService->fetchByDocument($assemblyId, $issueId, $document->getDocumentId());
            $congressmen = array_map(function (ProponentModel $proponent) use ($document) {
                return (new ProponentPartyPropertiesModel())
                    ->setCongressman($proponent)
                    ->setParty($this->partyService->getByCongressman(
                        $proponent->getCongressmanId(),
                        $document->getDate()
                    ));
            }, $this->congressmanService->fetchProponents($assemblyId, $document->getDocumentId()));

            $documentProperties = (new DocumentPropertiesModel())
                ->setDocument($document)
                ->setVotes($votes)
                ->setProponents($congressmen);

//            $date = $document->date;
//            array_walk($document->votes, function ($vote) use ($date) {
//                $vote->items = $this->voteItemService->fetchByVote($vote->vote_id);
//
//                array_walk($vote->items, function ($voteItem) use ($date) {
//                    $voteItem->congressman = $this->congressmanService->get($voteItem->congressman_id);
//                    $voteItem->congressman->party = $this->partyService->getByCongressman(
//                        $voteItem->congressman_id,
//                        new DateTime($date)
//                    );
//                });
//            });

            return $documentProperties;
        }, $this->documentService->fetchByIssue($assemblyId, $issueId));

        return (new CollectionModel($documents))
            ->setRange(0, count($documents), count($documents));
    }

    public function put($id, $data)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $documentId = $this->params('document_id');

        $form = new DocumentForm();
        $form->bindValues(array_merge(
            $data,
            ['assembly_id' => $assemblyId, 'issue_id' => $issueId, 'document_id' => $documentId]
        ));

        if ($form->isValid()) {
            $this->documentService->create($form->getObject());
            return (new EmptyModel())->setStatus(201);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    public function patch($id, $data)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $documentId = $this->params('document_id');

        if (($assembly = $this->documentService->get($assemblyId, $issueId, $documentId)) != null) {
            $form = new DocumentForm();
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
     * @param Document $document
     */
    public function setDocumentService(Document $document)
    {
        $this->documentService = $document;
    }

    /**
     * @param Congressman $congressman
     */
    public function setCongressmanService(Congressman $congressman)
    {
        $this->congressmanService = $congressman;
    }

    /**
     * @param Party $party
     */
    public function setPartyService(Party $party)
    {
        $this->partyService= $party;
    }

    /**
     * @param Vote $vote
     */
    public function setVoteService(Vote $vote)
    {
        $this->voteService = $vote;
    }

    /**
     * @param VoteItem $voteItem
     */
    public function setVoteItemService(VoteItem $voteItem)
    {
        $this->voteItemService = $voteItem;
    }
}
