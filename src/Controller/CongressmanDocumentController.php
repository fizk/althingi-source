<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Althingi\Form;
use Althingi\Service;
use Althingi\Utils\ErrorFormResponse;
use Althingi\Injector\ServiceProponentAwareInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\ServerRequest;
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait
};

class CongressmanDocumentController implements
    RestControllerInterface,
    ServiceProponentAwareInterface
{
    use RestControllerTrait;
    private Service\CongressmanDocument $congressmanDocumentService;

    /**
     * @input Althingi\Form\CongressmanDocument
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $issueId = $request->getAttribute('issue_id');
        $documentId = $request->getAttribute('document_id');
        $congressmanId = $request->getAttribute('congressman_id');

        $form = (new Form\CongressmanDocument())
            ->setData(array_merge(
                $request->getParsedBody(),
                [
                    'assembly_id' => $assemblyId,
                    'issue_id' => $issueId,
                    'document_id' => $documentId,
                    'congressman_id' => $congressmanId,
                    'category' => 'A',
                ]
            ));

        if ($form->isValid()) {
            $affectedRows = $this->congressmanDocumentService->save($form->getObject());
            return new EmptyResponse($affectedRows === 1 ? 201 : 205);
        }

        return new ErrorFormResponse($form);
    }

    /**
     * @input Althingi\Form\CongressmanDocument
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $issueId = $request->getAttribute('issue_id');
        $documentId = $request->getAttribute('document_id');
        $congressmanId = $request->getAttribute('congressman_id');

        if (($proponent = $this->congressmanDocumentService
                ->get($assemblyId, $issueId, $documentId, $congressmanId)) != null) {
            $form = new Form\CongressmanDocument();
            $form->bind($proponent);
            $form->setData($request->getParsedBody());

            if ($form->isValid()) {
                $this->congressmanDocumentService->update($form->getData());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    public function setCongressmanDocumentService(Service\CongressmanDocument $congressmanDocument): self
    {
        $this->congressmanDocumentService = $congressmanDocument;
        return $this;
    }
}
