<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\{
    JsonResponse,
    EmptyResponse
};
use Althingi\Injector\ServiceIssueAwareInterface;
use Althingi\Form;
use Althingi\Model\KindEnum;
use Althingi\Service;
use Althingi\Router\RestControllerTrait;
use Althingi\Utils\ErrorFormResponse;

class IssueController implements
    RequestHandlerInterface,
    ServiceIssueAwareInterface
{
    use RestControllerTrait;

    private Service\Issue $issueService;

    /**
     * Get one issie.
     *
     * @output \Althingi\Model\IssueProperties
     * @query category
     * @200 Success
     * @404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id', 0);
        $issueId = $request->getAttribute('issue_id', 0);
        $kind = KindEnum::fromString($request->getAttribute('kind', 'a'));

        $issue = $this->issueService->get($issueId, $assemblyId, $kind);

        return $issue
            ? new JsonResponse($issue)
            : new EmptyResponse(404);
    }

    /**
     * Get issues per assembly.
     *
     * @output \Althingi\Model\IssueProperties[]
     * @query type [string]
     * @query order [string]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id', null);
        $typeQuery = $request->getQueryParams()['type'] ?? null;
        $kindQuery = $request->getQueryParams()['kind'] ?? null;
        $orderQuery = $request->getQueryParams()['order'] ?? null;
        $types = $typeQuery ? explode(',', $typeQuery) : [];
        $kinds = $kindQuery ? explode(',', $kindQuery) : [];
        $categories = array_map(function ($category) {
            return strtoupper($category);
        }, explode(',', $request->getAttribute('category', 'a,b')));

        // $count = $this->issueStore->countByAssembly($assemblyId, $types, $kinds, $categories);
        // $range = $this->getRange($this->getRequest(), $count);

        $issues = $this->issueService->fetchByAssembly(
            $assemblyId,
            0, // $range->getFrom(),
            null, // $range->getSize(),
            $orderQuery,
            $types,
            $kinds,
            $categories
        );

        return new JsonResponse($issues, 206);
    }

    /**
     * Save one issue.
     *
     * @input \Althingi\Form\Issue
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $kind = KindEnum::fromString($request->getAttribute('kind', 'a'));
        $issueId = $request->getAttribute('issue_id');

        $form = new Form\Issue([
            ...$request->getParsedBody(),
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'kind' => $kind->value,
        ]);

        if ($form->isValid()) {
            $affectedRows = $this->issueService->save($form->getModel());
            return new EmptyResponse($affectedRows === 1 ? 201 : 205);
        }

        return new ErrorFormResponse($form);
    }

    /**
     * Update one issue.
     *
     * @input \Althingi\Form\Issue
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $kind = KindEnum::fromString($request->getAttribute('kind', 'a'));
        $issue = $this->issueService->get(
            $request->getAttribute('issue_id'),
            $assemblyId,
            $kind,
        );

        if (! $issue) {
            return new EmptyResponse(404);
        }

        $form = new Form\Issue([
            ...$issue->toArray(),
            ...$request->getParsedBody(),
            'assembly_id' => $request->getAttribute('id'),
            'issue_id' => $request->getAttribute('issue_id'),
        ]);

        if ($form->isValid()) {
            $this->issueService->update($form->getModel());
            return new EmptyResponse(205);
        }

        return new ErrorFormResponse($form);
    }

    /**
     * List options for Assembly collection.
     *
     * @200 Success
     */
    public function optionsList(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(200, [
            'Allow' => 'GET, OPTIONS'
        ]);
    }

    /**
     * List options for Assembly entry.
     *
     * @200 Success
     */
    public function options(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(200, [
            'Allow' => 'GET, OPTIONS, PUT, PATCH'
        ]);
    }

    /**
     * Set service.
     *
     * @return $this;
     */
    public function setIssueService(Service\Issue $issue): static
    {
        $this->issueService = $issue;
        return $this;
    }
}
