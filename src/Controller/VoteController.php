<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\{
    JsonResponse,
    EmptyResponse
};
use Althingi\Form;
use Althingi\Service\Vote;
use Althingi\Injector\ServiceVoteAwareInterface;
use Althingi\Model\KindEnum;
use Althingi\Utils\ErrorFormResponse;
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait
};

class VoteController implements
    RestControllerInterface,
    ServiceVoteAwareInterface
{
    use RestControllerTrait;

    private Vote $voteService;

    /**
     * @output \Althingi\Model\Vote
     * @200 Success
     * @404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $vote = $this->voteService->get(
            $request->getAttribute('vote_id')
        );
        return $vote
            ? new JsonResponse($vote)
            : new EmptyResponse(404);
    }

    /**
     * @output \Althingi\Model\Vote[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $issueId = $request->getAttribute('issue_id');
        $issues = $this->voteService->fetchByIssue($assemblyId, $issueId);

        return new JsonResponse($issues, 206);
    }

    /**
     * @input \Althingi\Form\Vote
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $issueId = $request->getAttribute('issue_id');
        $voteId = $request->getAttribute('vote_id');

        $form = new Form\Vote([
            ...$request->getParsedBody(),
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'vote_id' => $voteId,
            'kind' => KindEnum::A->value
        ]);

        if ($form->isValid()) {
            $affectedRows = $this->voteService->save($form->getModel());
            return new EmptyResponse($affectedRows === 1 ? 201 : 205);
        }

        return new ErrorFormResponse($form);
    }

    /**
     * @input \Althingi\Form\Vote
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $issueId = $request->getAttribute('issue_id');
        $voteId = $request->getAttribute('vote_id');

        if (($vote = $this->voteService->get($voteId)) != null) {
            $form = new Form\Vote([
                ...$vote->toArray(),
                ...$request->getParsedBody(),
                'assembly_id' => $assemblyId,
                'issue_id' => $issueId,
                'vote_id' => $voteId,
            ]);

            if ($form->isValid()) {
                $this->voteService->update($form->getModel());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    /**
     * @200 Success
     */
    public function optionsList(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(200, ['Allow' => 'GET, OPTIONS']);
    }

    /**
     * @200 Success
     */
    public function options(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(200, ['Allow' => 'OPTIONS, PUT, PATCH, GET']);
    }

    public function setVoteService(Vote $vote): static
    {
        $this->voteService = $vote;
        return $this;
    }
}
