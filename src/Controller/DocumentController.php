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
use Althingi\Model\KindEnum;
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
        $document = $this->documentService->get(
            $request->getAttribute('id'),
            $request->getAttribute('issue_id'),
            $request->getAttribute('document_id')
        );

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
        $documents = $this->documentService->fetchByIssue(
            $request->getAttribute('id'),
            $request->getAttribute('issue_id')
        );
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
        $form = new Form\Document([
            ...$request->getParsedBody(),
            'assembly_id' => $request->getAttribute('id'),
            'issue_id' => $request->getAttribute('issue_id'),
            'document_id' => $request->getAttribute('document_id'),
            'kind' => KindEnum::A->value,
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
        if (
            ($document = $this->documentService->get(
                $request->getAttribute('id'),
                $request->getAttribute('issue_id'),
                $request->getAttribute('document_id')
            )) != null
        ) {
            $form = new Form\Document([
                ...$document->toArray(),
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
        $document = $this->documentService->getPrimaryDocument(
            $request->getAttribute('id'),
            $request->getAttribute('issue_id')
        );

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
