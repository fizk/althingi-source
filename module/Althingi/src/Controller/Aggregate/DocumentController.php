<?php

namespace Althingi\Controller\Aggregate;

use Althingi\Injector\ServiceDocumentAwareInterface;
use Althingi\Injector\ServiceProponentAwareInterface;
use Althingi\Injector\StoreDocumentAwareInterface;
use Althingi\Service\CongressmanDocument;
use Althingi\Service\Document;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;

class DocumentController extends AbstractRestfulController implements
    ServiceDocumentAwareInterface,
    ServiceProponentAwareInterface
{
    use Range;

    /** @var $issueService \Althingi\Service\Document */
    private $documentService;

    /** @var $issueService \Althingi\Store\Document */
    private $documentStore;

    /** @var $issueService \Althingi\Service\CongressmanDocument */
    private $congressmanDocumentService;

    /**
     * @param mixed $id
     * @return ItemModel|\Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Document
     * @200 Success
     * @404 Resource not found
     */
    public function get($id)
    {
        $assemblyId = $this->params('assembly_id', null);
        $issueId = $this->params('issue_id', null);
        $documentId = $this->params('document_id', null);

        $document = $this->documentService->get($assemblyId, $issueId, $documentId);
        return $document
            ? (new ItemModel($document))->setStatus(200)
            : (new ErrorModel('Resource not found'))->setStatus(404);
    }

    /**
     * @return CollectionModel|\Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Document[]
     * @206 Success
     */
    public function getList()
    {
        $assemblyId = $this->params('assembly_id', null);
        $issueId = $this->params('issue_id', null);

        $documents = $this->documentService->fetchByIssue($assemblyId, $issueId);

        return (new CollectionModel($documents))
            ->setRange(0, count($documents), count($documents))
            ->setStatus(206);
    }

    /**
     * @return ItemModel
     * @output \Althingi\Model\CongressmanDocument[]
     * @206 Success
     */
    public function proponentsAction()
    {
        $assemblyId = $this->params('assembly_id', null);
        $issueId = $this->params('issue_id', null);
        $documentId = $this->params('document_id', null);

        $proponents = $this->congressmanDocumentService->fetchByDocument(
            $assemblyId,
            $issueId,
            $documentId
        );

        return (new CollectionModel($proponents))
            ->setStatus(206)
            ->setRange(0, count($proponents), count($proponents));
    }

    /**
     * @return CollectionModel
     * @output \Althingi\Model\ValueAndCount[]
     * @206 Success
     */
    public function documentTypesAction()
    {
        $assemblyId = $this->params('assembly_id', null);
        $issueId = $this->params('issue_id', null);
        $types = $this->documentService->countTypeByIssue($assemblyId, $issueId);

        return (new CollectionModel($types))
            ->setRange(0, count($types), count($types))
            ->setStatus(206);
    }

    /**
     * @param Document $document
     * @return $this
     */
    public function setDocumentService(Document $document)
    {
        $this->documentService = $document;
        return $this;
    }

    /**
     * @param CongressmanDocument $congressmanDocument
     * @return $this
     */
    public function setCongressmanDocumentService(CongressmanDocument $congressmanDocument)
    {
        $this->congressmanDocumentService = $congressmanDocument;
        return $this;
    }
}
