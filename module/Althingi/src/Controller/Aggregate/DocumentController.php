<?php

namespace Althingi\Controller\Aggregate;

use Althingi\Injector\ServiceDocumentAwareInterface;
use Althingi\Injector\ServiceProponentAwareInterface;
use Althingi\Service\CongressmanDocument;
use Althingi\Service\Document;
use Rend\Controller\AbstractRestfulController;
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

    /** @var $issueService \Althingi\Service\CongressmanDocument */
    private $congressmanDocumentService;

    /**
     * @param mixed $id
     * @return ItemModel|\Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Document
     */
    public function get($id)
    {
        $assemblyId = $this->params('assembly_id', null);
        $issueId = $this->params('issue_id', null);
        $documentId = $this->params('document_id', null);

        return (new ItemModel($this->documentService->get($assemblyId, $issueId, $documentId)));
    }

    /**
     * @return CollectionModel|\Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Document[]
     */
    public function getList()
    {
        $assemblyId = $this->params('assembly_id', null);
        $issueId = $this->params('issue_id', null);

        return (new CollectionModel($this->documentService->fetchByIssue($assemblyId, $issueId)));
    }

    /**
     * @return ItemModel
     * @output \Althingi\Model\CongressmanDocument[]
     */
    public function proponentsAction()
    {
        $assemblyId = $this->params('assembly_id', null);
        $issueId = $this->params('issue_id', null);
        $documentId = $this->params('document_id', null);

        return (new ItemModel($this->congressmanDocumentService->fetchByDocument(
            $assemblyId,
            $issueId,
            $documentId
        )));
    }

    /**
     * @return CollectionModel
     * @output \Althingi\Model\ValueAndCount[]
     */
    public function documentTypesAction()
    {
        $assemblyId = $this->params('assembly_id', null);
        $issueId = $this->params('issue_id', null);
        return (new CollectionModel($this->documentService->countTypeByIssue($assemblyId, $issueId)));
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
