<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Form\Speech as SpeechForm;
use Althingi\Lib\ServiceCongressmanAwareInterface;
use Althingi\Lib\ServicePartyAwareInterface;
use Althingi\Lib\ServiceSpeechAwareInterface;
use Althingi\Lib\Transformer;
use Althingi\Service\Congressman;
use Althingi\Service\Party;
use Althingi\Service\Speech;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;
use DateTime;

class SpeechController extends AbstractRestfulController implements
    ServiceSpeechAwareInterface,
    ServiceCongressmanAwareInterface,
    ServicePartyAwareInterface
{
    use Range;

    /** @var \Althingi\Service\Speech */
    private $speechService;

    /** @var \Althingi\Service\Congressman */
    private $congressmanService;

    /** @var \Althingi\Service\Party */
    private $partyService;

    /**
     * Get Speech item and speeches surrounding it.
     *
     * @param mixed $id
     * @return \Rend\View\Model\ModelInterface
     */
    public function get($id)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $speechId = $id;

        $count = $this->speechService->countByIssue($assemblyId, $issueId);
        $speeches = $this->speechService->fetch($speechId, $assemblyId, $issueId);
        $positionBegin = (count($speeches) > 0)
            ? $speeches[0]->position
            : 0 ;
        $positionEnd = (count($speeches) > 0)
            ? $speeches[count($speeches) - 1]->position
            : 0 ;

        array_walk($speeches, function ($speech) {
            $speech->text = Transformer::speechToMarkdown($speech->text);
            $speech->congressman = $this->congressmanService->get($speech->congressman_id);
            $speech->congressman->party = $this->partyService->getByCongressman(
                $speech->congressman_id,
                new DateTime($speech->from)
            );
        });

        return (new CollectionModel($speeches))
            ->setOption('Access-Control-Allow-Origin', '*')
            ->setOption('Access-Control-Expose-Headers', 'Range-Unit, Content-Range') //TODO should go into Rend
            ->setStatus(206)
            ->setRange($positionBegin, $positionEnd, $count);
    }

    /**
     * Get all speeches by an issue.
     * Paginated.
     *
     * @return \Rend\View\Model\ModelInterface
     */
    public function getList()
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');

        $count = $this->speechService->countByIssue($assemblyId, $issueId);
        $range = $this->getRange($this->getRequest(), $count);

        $speeches = $this->speechService->fetchByIssue(
            $assemblyId,
            $issueId,
            $range['from'],
            ($range['to']-$range['from'])
        );

        array_walk($speeches, function ($speech) {
            $speech->text = Transformer::speechToMarkdown($speech->text);
            $speech->congressman = $this->congressmanService->get($speech->congressman_id);
            $speech->congressman->party = $this->partyService->getByCongressman(
                $speech->congressman_id,
                new DateTime($speech->from)
            );
        });

        return (new CollectionModel($speeches))
            ->setOption('Access-Control-Allow-Origin', '*')
            ->setOption('Access-Control-Expose-Headers', 'Range-Unit, Content-Range') //TODO should go into Rend
            ->setStatus(206)
            ->setRange($range['from'], $range['to'], $count);
    }

    /**
     * Create one speech.
     *
     * @param mixed $id
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function put($id, $data)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');

        $form = new SpeechForm();
        $form->setData(array_merge(
            $data,
            ['speech_id' => $id, 'issue_id' => $issueId, 'assembly_id' => $assemblyId]
        ));

        if ($form->isValid()) {
            $this->speechService->create($form->getObject());
            return (new EmptyModel())->setStatus(201);
        }
        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * Update one speech.
     *
     * @param $id
     * @param $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function patch($id, $data)
    {
        $speechId = $this->params('speech_id');

        if (($speech = $this->speechService->get($speechId)) != null) {
            $form = new SpeechForm();
            $form->bind($speech);
            $form->setData($data);

            if ($form->isValid()) {
                $this->speechService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(204);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return $this->notFoundAction();
    }

    /**
     * List options for Speech collection.
     *
     * @return \Rend\View\Model\ModelInterface
     */
    public function optionsList()
    {
        return (new EmptyModel())
            ->setStatus(200)
            ->setAllow(['GET', 'OPTIONS'])
            ->setOption('Access-Control-Allow-Origin', '*')
            ->setOption('Access-Control-Allow-Headers', 'Range');
    }

    /**
     * List options for Speech entry.
     *
     * @return \Rend\View\Model\ModelInterface
     */
    public function options()
    {
        return (new EmptyModel())
            ->setStatus(200)
            ->setAllow(['GET', 'OPTIONS', 'PUT', 'PATCH',])
            ->setOption('Access-Control-Allow-Origin', '*')
            ->setOption('Access-Control-Allow-Headers', 'Range');
    }

    /**
     * @param Congressman $congressman
     */
    public function setCongressmanService(Congressman $congressman)
    {
        $this->congressmanService = $congressman;
    }

    /**
     * @param Party $party
     */
    public function setPartyService(Party $party)
    {
        $this->partyService = $party;
    }

    /**
     * @param Speech $speech
     */
    public function setSpeechService(Speech $speech)
    {
        $this->speechService = $speech;
    }
}
