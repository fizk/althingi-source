<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 22/03/2016
 * Time: 10:45 AM
 */

namespace Althingi\Controller;

use Althingi\Lib\ServiceVoteItemAwareInterface;
use Althingi\Service\Congressman;
use Althingi\Service\Party;
use Althingi\Service\Vote;
use Althingi\Service\VoteItem;
use DateTime;
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

    public function getList()
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');

        $documents = $this->documentService->fetchByIssue(
            $assemblyId,
            $issueId
        );
        array_walk($documents, function ($document) use ($assemblyId, $issueId) {

            $document->votes = $this->voteService->fetchByDocument(
                $assemblyId,
                $issueId,
                $document->document_id
            );

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

            $document->proponents = $this->congressmanService->fetchProponents(
                $assemblyId,
                $document->document_id
            );
            array_walk($document->proponents, function ($proponent) use ($document) {
                $proponent->party = $this->partyService->getByCongressman(
                    $proponent->congressman_id,
                    new DateTime($document->date)
                );
            });
        });

        return (new CollectionModel($documents))
            ->setOption('Access-Control-Allow-Origin', '*')
            ->setOption('Access-Control-Expose-Headers', 'Range-Unit, Content-Range') //TODO should go into Rend
            ->setRange(0, count($documents), count($documents));
    }

    public function put($id, $data)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $documentId = $this->params('document_id');

        $form = (new DocumentForm())
            ->setData(array_merge(
                $data,
                ['assembly_id' => $assemblyId, 'issue_id' => $issueId, 'document_id' => $documentId]
            ));

        if ($form->isValid()) {
            $this->documentService->create($form->getObject());
            return (new EmptyModel())->setStatus(201);
        }

        return (new ErrorModel($form))->setStatus(400);
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
