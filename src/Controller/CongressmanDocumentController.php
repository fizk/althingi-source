<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Althingi\Form;
use Althingi\Service;
use Althingi\Utils\ErrorFormResponse;
use Althingi\Injector\ServiceProponentAwareInterface;
use Althingi\Model\KindEnum;
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
        $form = new Form\CongressmanDocument([
            ...$request->getParsedBody(),
            'assembly_id' => $request->getAttribute('id'),
            'issue_id' => $request->getAttribute('issue_id'),
            'document_id' => $request->getAttribute('document_id'),
            'congressman_id' => $request->getAttribute('congressman_id'),
            'kind' => KindEnum::A->value,
        ]);

        if ($form->isValid()) {
            $affectedRows = $this->congressmanDocumentService->save($form->getModel());
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
        if (
            ($document = $this->congressmanDocumentService->get(
                $request->getAttribute('id'),
                $request->getAttribute('issue_id'),
                $request->getAttribute('document_id'),
                $request->getAttribute('congressman_id'),
            )) != null
        ) {
            $form = new Form\CongressmanDocument([
                ...$document->toArray(),
                ...$request->getParsedBody(),
                'id' => $request->getAttribute('id'),
                'issue_id' => $request->getAttribute('issue_id'),
                'document_id' => $request->getAttribute('document_id'),
                'congressman_id' => $request->getAttribute('congressman_id'),
            ]);

            if ($form->isValid()) {
                $this->congressmanDocumentService->update($form->getModel());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    public function setCongressmanDocumentService(Service\CongressmanDocument $congressmanDocument): static
    {
        $this->congressmanDocumentService = $congressmanDocument;
        return $this;
    }
}
