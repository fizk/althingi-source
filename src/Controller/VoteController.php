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
        $votes = $this->voteService->fetchByIssue(
            $request->getAttribute('id'),
            $request->getAttribute('issue_id')
        );

        return new JsonResponse($votes, 206);
    }

    /**
     * @input \Althingi\Form\Vote
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $form = new Form\Vote([
            ...$request->getParsedBody(),
            'assembly_id' => $request->getAttribute('id'),
            'issue_id' => $request->getAttribute('issue_id'),
            'vote_id' => $request->getAttribute('vote_id'),
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
        if (
            ($vote = $this->voteService->get(
                $request->getAttribute('vote_id')
            )) != null
        ) {
            $form = new Form\Vote([
                ...$vote->toArray(),
                ...$request->getParsedBody(),
                'assembly_id' => $request->getAttribute('id'),
                'issue_id' => $request->getAttribute('issue_id'),
                'vote_id' => $request->getAttribute('vote_id'),
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
