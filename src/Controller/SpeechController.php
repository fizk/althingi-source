<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\{
    JsonResponse,
    EmptyResponse
};
use Althingi\Service\Constituency;
use Althingi\Utils\Transformer;
use Althingi\Model;
use Althingi\Service;
use Althingi\Form;
use Althingi\Injector\{
    ServiceConstituencyAwareInterface,
    ServiceCongressmanAwareInterface,
    ServicePartyAwareInterface,
    ServiceParliamentarySessionAwareInterface,
    ServiceSpeechAwareInterface
};
use Althingi\Model\KindEnum;
use Althingi\Utils\{
    ErrorFormResponse,
    ErrorExceptionResponse
};
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait
};

class SpeechController implements
    RestControllerInterface,
    ServiceSpeechAwareInterface,
    ServiceCongressmanAwareInterface,
    ServicePartyAwareInterface,
    ServiceParliamentarySessionAwareInterface,
    ServiceConstituencyAwareInterface
{
    use RestControllerTrait;

    private Service\Speech $speechService;
    private Service\Congressman $congressmanService;
    private Service\Party $partyService;
    private Service\ParliamentarySession $parliamentarySessionService;
    private Service\Constituency $constituencyService;

    /**
     * Get Speech item and speeches surrounding it.
     *
     * @param mixed $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\SpeechCongressmanProperties
     * @query category
     * @206 Success
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $issueId = $request->getAttribute('issue_id');
        $kind = KindEnum::fromString($request->getAttribute('kind', 'a'));
        $speechId = $request->getAttribute('speech_id');

        $count = $this->speechService->countByIssue($assemblyId, $issueId, $kind);
        $speeches = $this->speechService->fetch($speechId, $assemblyId, $issueId, 25, $kind);
        $positionBegin = (count($speeches) > 0)
            ? $speeches[0]->getPosition()
            : 0 ;
        $positionEnd = (count($speeches) > 0)
            ? $speeches[count($speeches) - 1]->getPosition()
            : 0 ;

        $speechesProperties = array_map(function (Model\SpeechAndPosition $speech) {
            $speech->setText(Transformer::speechToMarkdown($speech->getText()));

            $congressmanPartyProperties = (new Model\CongressmanPartyProperties())
                ->setCongressman($this->congressmanService->get(
                    $speech->getCongressmanId()
                ))->setParty($this->partyService->getByCongressman(
                    $speech->getCongressmanId(),
                    $speech->getFrom()
                ))->setConstituency($this->constituencyService->getByCongressman(
                    $speech->getCongressmanId(),
                    $speech->getFrom()
                ));

            return (new Model\SpeechCongressmanProperties())
                ->setCongressman($congressmanPartyProperties)
                ->setSpeech($speech);
        }, $speeches);

        return new JsonResponse($speechesProperties, 206, [
            'Access-Control-Expose-Headers' => 'Range-Unit, Content-Range',
            'Range-Unit' => 'items',
            'Content-Range' => "items {$positionBegin}-{$positionEnd}/{$count}"
        ]);
    }

    /**
     * Get all speeches by an issue.
     * Paginated.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\SpeechCongressmanProperties
     * @query leit [string]
     * @query category
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $speechesAndProperties = $this->speechService->fetchAllByIssue(
            $request->getAttribute('id'),
            $request->getAttribute('issue_id'),
            KindEnum::fromString($request->getAttribute('kind', 'a')),
        );

        return new JsonResponse($speechesAndProperties, 206);
    }

    /**
     * Create one speech.
     *
     * @param mixed $id
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Speech
     * @throws \Exception
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $form = new Form\Speech([
            ...$request->getParsedBody(),
            'speech_id' => $request->getAttribute('speech_id'),
            'issue_id' => $request->getAttribute('issue_id'),
            'assembly_id' => $request->getAttribute('id'),
            'kind' => KindEnum::fromString($request->getAttribute('kind', 'a'))->value,
        ]);

        if ($form->isValid()) {
            try {
                $affectedRows = $this->speechService->save($form->getModel());
                return new EmptyResponse($affectedRows === 1 ? 201 : 205);
            } catch (\PDOException $e) {
                /**
                 * @todo damn you althingi.is For some reason, the ParliamentarySession list
                 *  is empty for some assemblies but then there is a ParliamentarySession id
                 *  on the speech entry. So, sometimes a speech is trying to be saved but it
                 *  can't be attached to a ParliamentarySession as it doesn't exists
                 *  Example: speeches here have a ParliamentarySession id
                 *  http://www.althingi.is/altext/xml/thingmalalisti/thingmal/?lthing=20&malnr=1
                 *  but the ParliamentarySession list it self is empty
                 *  http://www.althingi.is/altext/xml/thingfundir/?lthing=20
                 */
                if ($e->errorInfo[1] === 1452) {
                    /** @var \althingi\Model\Speech */
                    $speech = $form->getModel();
                    $parliamentarySession = (new Model\ParliamentarySession())
                        ->setAssemblyId($speech->getAssemblyId())
                        ->setParliamentarySessionId($speech->getParliamentarySessionId())
                        ->setFrom($speech->getFrom())
                        ->setTo($speech->getTo());

                    try {
                        $this->parliamentarySessionService->save($parliamentarySession);
                        $affectedRows = $this->speechService->save($speech);

                        return new EmptyResponse($affectedRows === 1 ? 201 : 205);
                    } catch (\Throwable $exception) {
                        return new ErrorExceptionResponse($exception);
                    }
                } else {
                    return new ErrorExceptionResponse($e);
                }
            }
        }
        return new ErrorFormResponse($form);
    }

    /**
     * Update one speech.
     *
     * @param $id
     * @param $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Speech
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        if (
            ($speech = $this->speechService->get(
                $request->getAttribute('speech_id')
            )) != null
        ) {
            $form = new Form\Speech([
                ...$speech->toArray(),
                ...$request->getParsedBody(),
                'speech_id' => $request->getAttribute('speech_id'),
            ]);

            if ($form->isValid()) {
                $this->speechService->update($form->getModel());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    /**
     * List options for Speech collection.
     *
     * @return \Rend\View\Model\ModelInterface
     * @200 Success
     */
    public function optionsList(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(200, [
            'Allow' => 'GET, OPTIONS',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Headers' => 'Range'
        ]);
    }

    /**
     * List options for Speech entry.
     *
     * @return \Rend\View\Model\ModelInterface
     * @200 Success
     */
    public function options(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(200, [
            'Allow' => 'GET, OPTIONS, PUT, PATCH',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Headers' => 'Range'
        ]);
    }

    /**
     * @param \Althingi\Service\Congressman $congressman
     * @return $this
     */
    public function setCongressmanService(Service\Congressman $congressman): static
    {
        $this->congressmanService = $congressman;
        return $this;
    }

    /**
     * @param \Althingi\Service\Party $party
     * @return $this
     */
    public function setPartyService(Service\Party $party): static
    {
        $this->partyService = $party;
        return $this;
    }

    /**
     * @param \Althingi\Service\Speech $speech
     * @return $this
     */
    public function setSpeechService(Service\Speech $speech): static
    {
        $this->speechService = $speech;
        return $this;
    }

    /**
     * @param \Althingi\Service\ParliamentarySession $parliamentarySession
     * @return $this
     */
    public function setParliamentarySession(Service\ParliamentarySession $parliamentarySession): static
    {
        $this->parliamentarySessionService = $parliamentarySession;
        return $this;
    }

    /**
     * @param Constituency $constituency
     * @return $this
     */
    public function setConstituencyService(Constituency $constituency): static
    {
        $this->constituencyService = $constituency;
        return $this;
    }
}
