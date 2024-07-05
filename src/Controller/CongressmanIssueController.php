<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use Althingi\Service;
use Althingi\Injector\ServiceIssueAwareInterface;
use Althingi\Router\{
    RestControllerTrait,
    RestControllerInterface
};

/**
 * Class CongressmanSessionController
 * @package Althingi\Controller
 */
class CongressmanIssueController implements
    RestControllerInterface,
    ServiceIssueAwareInterface
{
    use RestControllerTrait;

    private Service\Issue $issueService;

    /**
     * @output \Althingi\Model\Issue[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $issues = $this->issueService->fetchByCongressman(
            $request->getAttribute('congressman_id')
        );

        return new JsonResponse($issues, 206);
    }

    public function setIssueService(Service\Issue $issue): static
    {
        $this->issueService = $issue;
        return $this;
    }
}
