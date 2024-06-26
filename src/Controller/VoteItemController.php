<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\{
    JsonResponse,
    EmptyResponse
};
use Althingi\Form;
use Althingi\Model;
use Althingi\Injector\{
    ServiceCongressmanAwareInterface,
    ServiceConstituencyAwareInterface,
    ServicePartyAwareInterface,
    ServiceVoteAwareInterface,
    ServiceVoteItemAwareInterface
};
use Althingi\Model\KindEnum;
use Althingi\Service\{
    Constituency,
    Congressman,
    Party,
    Vote,
    VoteItem
};
use Althingi\Utils\{
    ErrorFormResponse,
    ErrorExceptionResponse
};
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait,
    RouteInterface,
    RouterAwareInterface
};

class VoteItemController implements
    RestControllerInterface,
    ServiceVoteItemAwareInterface,
    ServiceVoteAwareInterface,
    ServiceCongressmanAwareInterface,
    ServicePartyAwareInterface,
    ServiceConstituencyAwareInterface,
    RouterAwareInterface
{
    use RestControllerTrait;
    private RouteInterface $router;
    private VoteItem $voteItemService;
    private Vote $voteService;
    private Congressman $congressmanService;
    private Party $partyService;
    private Constituency $constituencyService;

    /**
     * @input \Althingi\Form\VoteItem
     * @201 Created
     * @409 Conflict
     * @500 Service Error
     * @400 Invalid input
     */
    public function post(ServerRequest $request): ResponseInterface
    {
        $voteId = $request->getAttribute('vote_id');

        $form = new Form\VoteItem([
            ...$request->getParsedBody(),
            'vote_id' => $voteId,
        ]);

        if ($form->isValid()) {
            /** @var \Althingi\Model\VoteItem */
            $formData = $form->getModel();

            try {
                $this->voteItemService->create($formData);
                return new EmptyResponse(201);
            } catch (\PDOException $e) {
                if ($e->errorInfo[1] === 1062) {
                    /** @var \Althingi\Model\VoteItemAndAssemblyIssue */
                    $voteObject = $this->voteItemService->getByVote(
                        $formData->getVoteId(),
                        $formData->getCongressmanId()
                    );
                    return new EmptyResponse(409, [
                        'Location' => $this->router->assemble([
                            'id' => $voteObject->getAssemblyId(),
                            'issue_id' => $voteObject->getIssueId(),
                            'vote_id' => $voteObject->getVoteId(),
                            'vote_item_id' => $voteObject->getVoteItemId(),
                            'kind' => KindEnum::A->value
                        ], ['name' => 'loggjafarthing/thingmal/atkvaedagreidslur/atkvaedagreidsla'])
                    ]);
                } else {
                    return new ErrorExceptionResponse($e);
                }
            }
        }

        return new ErrorFormResponse($form);
    }

    /**
     * @output \Althingi\Model\VoteItemAndCongressman[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $vote = $this->voteService->get($request->getAttribute('vote_id'));

        $votes = $this->voteItemService->fetchByVote($vote->getVoteId());
        $date = $vote->getDate();

        $voteItems = array_map(function (Model\VoteItem $voteItem) use ($date) {
            $congressmanAndParty = (new Model\CongressmanPartyProperties())
                ->setCongressman($this->congressmanService->get(
                    $voteItem->getCongressmanId()
                ))->setParty($this->partyService->getByCongressman(
                    $voteItem->getCongressmanId(),
                    $date
                ))->setConstituency($this->constituencyService->getByCongressman(
                    $voteItem->getCongressmanId(),
                    $date
                ));

            return (new Model\VoteItemAndCongressman())
                ->setVoteItem($voteItem)
                ->setCongressman($congressmanAndParty);
        }, $votes);

        return new JsonResponse($voteItems, 206);
    }

    /**
     * @input \Althingi\Form\VoteItem
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        $voteItemId = $request->getAttribute('vote_item_id');

        if (($voteItem = $this->voteItemService->get($voteItemId)) !== null) {
            $form = new Form\VoteItem([
                ...$voteItem->toArray(),
                ...$request->getParsedBody(),
            ]);

            if ($form->isValid()) {
                $this->voteItemService->update($form->getModel());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    public function setVoteItemService(VoteItem $voteItem): self
    {
        $this->voteItemService = $voteItem;
        return $this;
    }

    public function setVoteService(Vote $vote): self
    {
        $this->voteService = $vote;
        return $this;
    }

    public function setCongressmanService(Congressman $congressman): self
    {
        $this->congressmanService = $congressman;
        return $this;
    }

    public function setPartyService(Party $party): self
    {
        $this->partyService = $party;
        return $this;
    }

    public function setConstituencyService(Constituency $constituency): self
    {
        $this->constituencyService = $constituency;
        return $this;
    }

    public function setRouter(RouteInterface $router): self
    {
        $this->router = $router;
        return $this;
    }
}
