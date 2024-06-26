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
use Althingi\Injector\ServiceCommitteeMeetingAwareInterface;
use Althingi\Utils\ErrorFormResponse;
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait
};

class CommitteeMeetingController implements
    RestControllerInterface,
    ServiceCommitteeMeetingAwareInterface
{
    use RestControllerTrait;

    private Service\CommitteeMeeting $committeeMeetingService;

    /**
     * @output \Althingi\Model\CommitteeMeeting
     * @200 Success
     * @404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $committeeMeetingId = $request->getAttribute('committee_meeting_id');
        $meeting = $this->committeeMeetingService->get($committeeMeetingId);

        return $meeting
            ? new JsonResponse($meeting)
            : new EmptyResponse(404);
    }

    /**
     * @output \Althingi\Model\CommitteeMeeting[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $committeeId = $request->getAttribute('committee_id');

        $meetings = $this->committeeMeetingService->fetchByAssembly($assemblyId, $committeeId);

        return new JsonResponse($meetings, 206);
    }

    /**
     * @input Althingi\Form\CommitteeMeeting
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $committeeId = $request->getAttribute('committee_id');
        $committeeMeetingId = $request->getAttribute('committee_meeting_id');

        $form = new Form\CommitteeMeeting([
            ...$request->getParsedBody(),
            'committee_id' => $committeeId,
            'assembly_id' => $assemblyId,
            'committee_meeting_id' => $committeeMeetingId,
        ]);
        if ($form->isValid()) {
            $affectedRows = $this->committeeMeetingService->save($form->getModel());
            return new EmptyResponse($affectedRows === 1 ? 201 : 205);
        }

        return new ErrorFormResponse($form);
    }

    /**
     * @input Althingi\Form\CommitteeMeeting
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        $committeeMeetingId = $request->getAttribute('committee_meeting_id');

        if (($committeeMeeting = $this->committeeMeetingService->get($committeeMeetingId)) != null) {
            $form = new Form\CommitteeMeeting([
                ...$committeeMeeting->toArray(),
                ...$request->getParsedBody(),
                'committee_meeting_id' => $request->getAttribute('committee_meeting_id'),
            ]);

            if ($form->isValid()) {
                $this->committeeMeetingService->update($form->getModel());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    public function setCommitteeMeetingService(Service\CommitteeMeeting $committeeMeeting): self
    {
        $this->committeeMeetingService = $committeeMeeting;
        return $this;
    }
}
