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
use Althingi\Injector\ServiceIssueLinkAwareInterface;
use Althingi\Model\KindEnum;
use Althingi\Utils\ErrorFormResponse;
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait
};

class IssueLinkController implements
    RestControllerInterface,
    ServiceIssueLinkAwareInterface
{
    use RestControllerTrait;

    private Service\IssueLink $issueLinkService;

    /**
     * @output \Althingi\Model\Committee
     * @206 Success
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $issues = $this->issueLinkService->fetchAll(
            $request->getAttribute('id', 0),
            $request->getAttribute('issue_id', 0),
            KindEnum::fromString($request->getAttribute('kind', 'A'))
        );
        return new JsonResponse($issues, 206);
    }

    /**
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $form = new Form\IssueLink([
            ...$request->getParsedBody(),
            'from_assembly_id' => $request->getAttribute('id', 0),
            'from_issue_id' => $request->getAttribute('issue_id', 0),
            'from_kind' => KindEnum::fromString($request->getAttribute('kind', 'A'))->value,
        ]);

        if ($form->isValid()) {
            $object = $form->getModel();
            $affectedRows = $this->issueLinkService->save($object);
            return new EmptyResponse($affectedRows === 1 ? 201 : 205);
        }

        return new ErrorFormResponse($form);
    }

    /**
     * Update? A link is never updated, there are no additional info that can be added to a link,
     * once it has been connected, that it. The Aggregator could try to patch a link, therefor there
     * is this method that just says OK...
     *
     * @202 No update
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(202);
    }

    /**
     * @output \Althingi\Model\Committee[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        return new JsonResponse([], 206);
    }

    public function setIssueLinkService(Service\IssueLink $issueLink): static
    {
        $this->issueLinkService = $issueLink;
        return $this;
    }
}
