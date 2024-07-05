<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\{
    JsonResponse,
    EmptyResponse
};
use Althingi\Form;
use Althingi\Service;
use Althingi\Injector\ServiceCommitteeDocumentAwareInterface;
use Althingi\Model\KindEnum;
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait,
    RouterAwareInterface
};
use Althingi\Router\RouteInterface;
use Althingi\Utils\{
    ErrorFormResponse,
    ErrorExceptionResponse
};

/**
 * Class CommitteeSittingController
 * @package Althingi\Controller
 */
class CommitteeDocumentController implements
    RestControllerInterface,
    ServiceCommitteeDocumentAwareInterface,
    RouterAwareInterface
{
    use RestControllerTrait;

    private RouteInterface $router;
    private Service\CommitteeDocument $committeeDocumentService;

    /**
     * @output \Althingi\Model\CommitteeDocument
     * @200 Success
     * @404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $committeeSitting = $this->committeeDocumentService->get(
            $request->getAttribute('document_committee_id')
        );
        return $committeeSitting
            ? new JsonResponse($committeeSitting)
            : new EmptyResponse(404);
    }

    /**
     * @output \Althingi\Model\CommitteeDocument[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $committeeDocuments = $this->committeeDocumentService
            ->fetchByDocument(
                $request->getAttribute('id'),
                $request->getAttribute('issue_id'),
                $request->getAttribute('document_id')
            );

        return new JsonResponse($committeeDocuments, 206);
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
     * @input \Althingi\Form\CommitteeDocument
     * @201 Created
     * @409 Conflict
     * @400 Invalid input
     */
    public function post(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $issueId = $request->getAttribute('issue_id');
        $documentId = $request->getAttribute('document_id');

        $statusCode = 201;
        $committeeDocumentId = 0;

        $form = new Form\CommitteeDocument([
            ...$request->getParsedBody(),
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'kind' => KindEnum::A->value,
            'document_id' => $documentId,
        ]);

        if ($form->isValid()) {
            /** @var \Althingi\Model\CommitteeDocument */
            $committeeDocument = $form->getModel();

            try {
                $committeeDocumentId = $this->committeeDocumentService->create($committeeDocument);
                $statusCode = 201;
            } catch (\PDOException $e) {
                if ($e->errorInfo[1] === 1062) {
                    $committeeDocumentId = $this->committeeDocumentService->getIdentifier(
                        $committeeDocument->getDocumentId(),
                        $committeeDocument->getAssemblyId(),
                        $committeeDocument->getIssueId(),
                        KindEnum::A,
                        $committeeDocument->getCommitteeId(),
                        $committeeDocument->getPart()
                    );
                    $statusCode = 409;
                } else {
                    return new ErrorExceptionResponse($e);
                }
            }

            return new EmptyResponse($statusCode, [
                'Location' => $this->router->assemble([
                    'id' => $assemblyId,
                    'issue_id' => $issueId,
                    'kind' => KindEnum::A->value,
                    'document_id' => $documentId,
                    'document_committee_id' => $committeeDocumentId
                ], ['name' => 'loggjafarthing/thingmal/thingskjal/nefndir'])
            ]);
        }

        return new ErrorFormResponse($form);
    }

    /**
     * @input \Althingi\Form\CommitteeDocument
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        if (
            ($committeeDocument = $this->committeeDocumentService->get(
                $request->getAttribute('document_committee_id')
            )) != null
        ) {
            $form = new Form\CommitteeDocument([
                ...$committeeDocument->toArray(),
                ...$request->getParsedBody(),
                'document_committee_id' => $request->getAttribute('document_committee_id'),
            ]);

            if ($form->isValid()) {
                $this->committeeDocumentService->update($form->getModel());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    public function setCommitteeDocumentService(Service\CommitteeDocument $committeeDocument): static
    {
        $this->committeeDocumentService = $committeeDocument;
        return $this;
    }

    public function setRouter(RouteInterface $router): static
    {
        $this->router = $router;
        return $this;
    }
}
