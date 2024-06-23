<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\{
    JsonResponse,
    EmptyResponse
};
use Althingi\Service;
use Althingi\Form;
use Althingi\Injector\ServiceDocumentAwareInterface;
use Althingi\Utils\ErrorFormResponse;
use Althingi\Router\{
    RestControllerTrait,
    RestControllerInterface
};

class DocumentController implements
    RestControllerInterface,
    ServiceDocumentAwareInterface
{
    use RestControllerTrait;
    private Service\Document $documentService;

    /**
     * @output \Althingi\Model\Document
     * @200 Success
     * @404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $issueId = $request->getAttribute('issue_id');
        $documentId = $request->getAttribute('document_id');

        $document = $this->documentService->get($assemblyId, $issueId, $documentId);

        return $document
            ? new JsonResponse($document)
            : new EmptyResponse(404);
    }

    /**
     * @output \Althingi\Model\DocumentProperties
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $issueId = $request->getAttribute('issue_id');
        $documents = $this->documentService->fetchByIssue($assemblyId, $issueId);
        return new JsonResponse($documents, 206);
    }

    /**
     * @input \Althingi\Form\Document
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $issueId = $request->getAttribute('issue_id');
        $documentId = $request->getAttribute('document_id');

        $form = new Form\Document([
            ...$request->getParsedBody(),
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'document_id' => $documentId,
            'category' => 'A',
        ]);

        if ($form->isValid()) {
            $affectedRows = $this->documentService->save($form->getModel());
            return new EmptyResponse($affectedRows === 1 ? 201 : 205);
        }

        return new ErrorFormResponse($form);
    }

    /**
     * @input \Althingi\Form\Document
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $issueId = $request->getAttribute('issue_id');
        $documentId = $request->getAttribute('document_id');

        if (($document = $this->documentService->get($assemblyId, $issueId, $documentId)) != null) {
            $form = new Form\Document([
                ...(new \Althingi\Hydrator\Document())->extract($document),
                ...$request->getParsedBody(),
                'id' => $request->getAttribute('id'),
                'issue_id' => $request->getAttribute('issue_id'),
                'document_id' => $request->getAttribute('document_id'),
            ]);

            if ($form->isValid()) {
                $this->documentService->update($form->getModel());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    /**
     * @todo This migt not be the right way of finding who are the proponents of an Issue.
     * To understand who is putting forward an issue, there is initially the primary document,
     * The congressman who are responsible for that document, are responsible for the issue.
     * The problem is, how to identify what is the primary document.
     *
     * Returns the primary document, or the first document of all documents
     * for a given issue: by sorting all document by an issue and returning the oldest one.
     */
    public function primaryDocumentAction(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $issueId = $request->getAttribute('issue_id');
        $document = $this->documentService->getPrimaryDocument($assemblyId, $issueId);

        return $document
            ? new JsonResponse($document)
            : new EmptyResponse(404);
    }

    public function setDocumentService(Service\Document $document)
    {
        $this->documentService = $document;
        return $this;
    }
}
