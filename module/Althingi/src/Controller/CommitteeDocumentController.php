<?php

namespace Althingi\Controller;

use Althingi\Form;
use Althingi\Injector\ServiceCommitteeDocumentAwareInterface;
use Althingi\Injector\ServiceCommitteeSittingAwareInterface;
use Althingi\Injector\ServiceSessionAwareInterface;
use Althingi\Service\CommitteeDocument;
use Althingi\Service\CommitteeSitting;
use Althingi\Service\Session;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;

/**
 * Class CommitteeSittingController
 * @package Althingi\Controller
 */
class CommitteeDocumentController extends AbstractRestfulController implements
    ServiceCommitteeDocumentAwareInterface
{
    /** @var  \Althingi\Service\CommitteeDocument */
    private $committeeDocumentService;

    /**
     * Get committee attached to a document.
     * This is therefor a list.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\CommitteeDocument
     * @200 Success
     * @404 Resource not found
     */
    public function get($id)
    {
        $committeeSitting = $this->committeeDocumentService->get($id);
        return $committeeSitting
            ? (new ItemModel($committeeSitting))->setStatus(200)
            : (new ErrorModel('Resource Not Found'))->setStatus(404);
    }

    /**
     * Get all sessions my congressman.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\CommitteeDocument[]
     * @206 Success
     */
    public function getList()
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $documentId = $this->params('document_id');

        $committeeDocuments = $this->committeeDocumentService->fetchByDocument($assemblyId, $issueId, $documentId);
        $sessionsCount = count($committeeDocuments);

        return (new CollectionModel($committeeDocuments))
            ->setStatus(206)
            ->setRange(0, $sessionsCount, $sessionsCount);
    }

    /**
     * Create a new Congressman session.
     *
     * @todo CommitteeDocuments do not have IDs coming from althingi.is.
     *  They are created on this server. To be able to update
     *  these entries, the server has to provide the client with
     *  the URI created on the server. This method will try to
     *  create a resource and if the DB responds with a 23000 (ER_DUP_KEY)
     *  it will then try to find the server's ID and create an URI
     *  from that and pass it back in the HTTP's header Location as
     *  well as issuing a 409 response code. The client can then
     *  try to do a PATCH request with the URI provided.
     *
     *  If althingi.is will start to provide a CommitteeDocumentsIDs, then this will
     *  not be needed as the resource wil be stores via PUSH request.
     *
     *  To facilitate that, create a self::push() method and remove
     *  \Althingi\Service\CommitteeDocuments::getIdentifier()
     *
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\CommitteeDocument
     * @201 Created
     * @409 Conflict
     * @400 Invalid input
     */
    public function post($data)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $documentId = $this->params('document_id');

        $statusCode = 201;
        $committeeDocumentId = 0;

        $form = new Form\CommitteeDocument();
        $form->setData(array_merge($data, [
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'category' => 'A',
            'document_id' => $documentId,
        ]));

        if ($form->isValid()) {
            /** @var $committeeDocument \Althingi\Model\CommitteeDocument */
            $committeeDocument = $form->getObject();

            try {
                $committeeDocumentId = $this->committeeDocumentService->create($committeeDocument);
                $statusCode = 201;
            } catch (\PDOException $e) {
                if ($e->errorInfo[1] === 1062) {
                    $committeeDocumentId = $this->committeeDocumentService->getIdentifier(
                        $committeeDocument->getDocumentId(),
                        $committeeDocument->getAssemblyId(),
                        $committeeDocument->getIssueId(),
                        'A',
                        $committeeDocument->getCommitteeId(),
                        $committeeDocument->getPart()
                    );
                    $statusCode = 409;
                } else {
                    return (new ErrorModel($e))
                        ->setStatus(500);
                }
            }

            return (new EmptyModel())
                ->setLocation(
                    $this->url()->fromRoute(
                        'loggjafarthing/thingmal/thingskjal/nefndir',
                        [
                            'id' => $assemblyId,
                            'issue_id' => $issueId,
                            'category' => 'a',
                            'document_id' => $documentId,
                            'document_committee_id' => $committeeDocumentId
                        ]
                    )
                )
                ->setStatus($statusCode);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * @param $id
     * @param $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\CommitteeDocument
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch($id, $data)
    {
        if (($session = $this->committeeDocumentService->get($id)) != null) {
            $form = new Form\CommitteeDocument();
            $form->bind($session);
            $form->setData($data);

            if ($form->isValid()) {
                $this->committeeDocumentService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(205);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return (new ErrorModel('Resource Not Found'))
            ->setStatus(404);
    }

    /**
     * @param CommitteeDocument $committeeDocument
     * @return $this;
     */
    public function setCommitteeDocument(CommitteeDocument $committeeDocument)
    {
        $this->committeeDocumentService = $committeeDocument;
        return $this;
    }
}
