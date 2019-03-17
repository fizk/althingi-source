<?php

namespace Althingi\Controller\Aggregate;

use Althingi\Lib\ServiceCongressmanAwareInterface;
use Althingi\Lib\ServiceConstituencyAwareInterface;
use Althingi\Lib\ServiceDocumentAwareInterface;
use Althingi\Lib\ServicePartyAwareInterface;
use Althingi\Lib\ServiceProponentAwareInterface;
use Althingi\Service\Congressman;
use Althingi\Service\CongressmanDocument;
use Althingi\Service\Constituency;
use Althingi\Service\Document;
use Althingi\Service\Party;
use Althingi\Utils\CategoryParam;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;
use DateTime;

class DocumentController extends AbstractRestfulController implements
    ServiceDocumentAwareInterface,
    ServiceProponentAwareInterface
{
    use Range;

    use CategoryParam;

    /** @var $issueService \Althingi\Service\Document */
    private $documentService;

    /** @var $issueService \Althingi\Service\CongressmanDocument */
    private $congressmanDocumentService;

    public function get($id)
    {
        $assemblyId = $this->params('assembly_id', null);
        $issueId = $this->params('issue_id', null);
        $documentId = $this->params('document_id', null);

        return (new ItemModel($this->documentService->get($assemblyId, $issueId, $documentId)));
    }

    public function getList()
    {
        $assemblyId = $this->params('assembly_id', null);
        $issueId = $this->params('issue_id', null);

        return (new CollectionModel($this->documentService->fetchByIssue($assemblyId, $issueId)));
    }

    public function proponentsAction()
    {
        $assemblyId = $this->params('assembly_id', null);
        $issueId = $this->params('issue_id', null);
        $documentId = $this->params('document_id', null);

        return (new ItemModel($this->congressmanDocumentService->countProponents(
            $assemblyId,
            $issueId,
            $documentId
        )));
    }

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
