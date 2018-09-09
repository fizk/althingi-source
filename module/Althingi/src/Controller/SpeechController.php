<?php

namespace Althingi\Controller;

use Althingi\Form\Speech as SpeechForm;
use Althingi\Lib\ServiceCongressmanAwareInterface;
use Althingi\Lib\ServicePartyAwareInterface;
use Althingi\Lib\ServicePlenaryAwareInterface;
use Althingi\Lib\ServiceSearchSpeechAwareInterface;
use Althingi\Lib\ServiceSpeechAwareInterface;
use Althingi\Lib\Transformer;
use Althingi\Model\CongressmanPartyProperties;
use Althingi\Model\SpeechAndPosition;
use Althingi\Model\Speech as SpeechModel;
use Althingi\Model\SpeechCongressmanProperties;
use Althingi\Service\Congressman;
use Althingi\Service\Party;
use Althingi\Service\Plenary;
use Althingi\Service\SearchSpeech;
use Althingi\Service\Speech;
use Althingi\Utils\CategoryParam;
use Finite\Exception\Exception;
use Rend\Controller\AbstractRestfulController;
use Rend\Helper\Http\RangeValue;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;
use DateTime;

class SpeechController extends AbstractRestfulController implements
    ServiceSpeechAwareInterface,
    ServiceCongressmanAwareInterface,
    ServicePartyAwareInterface,
    ServiceSearchSpeechAwareInterface,
    ServicePlenaryAwareInterface
{
    use Range;

    use CategoryParam;

    /** @var \Althingi\Service\Speech */
    private $speechService;

    /** @var  \Althingi\Service\SearchSpeech */
    private $speechSearch;

    /** @var \Althingi\Service\Congressman */
    private $congressmanService;

    /** @var \Althingi\Service\Party */
    private $partyService;

    /** @var \Althingi\Service\Plenary */
    private $plenaryService;

    /**
     * Get Speech item and speeches surrounding it.
     *
     * @param mixed $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\SpeechCongressmanProperties
     */
    public function get($id)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $speechId = $id;
        $category = $this->getCategoryFromQuery();

        $count = $this->speechService->countByIssue($assemblyId, $issueId, $category);
        $speeches = $this->speechService->fetch($speechId, $assemblyId, $issueId, 25, $category);
        $positionBegin = (count($speeches) > 0)
            ? $speeches[0]->getPosition()
            : 0 ;
        $positionEnd = (count($speeches) > 0)
            ? $speeches[count($speeches) - 1]->getPosition()
            : 0 ;

        $speechesProperties = array_map(function (SpeechAndPosition $speech) {
            $speech->setText(Transformer::speechToMarkdown($speech->getText()));

            $congressman = $this->congressmanService->get($speech->getCongressmanId());
            $congressmanPartyProperties = (new CongressmanPartyProperties())
                ->setCongressman($congressman)
                ->setParty($this->partyService->getByCongressman($speech->getCongressmanId(), $speech->getFrom()));

            return (new SpeechCongressmanProperties())
                ->setCongressman($congressmanPartyProperties)
                ->setSpeech($speech);
        }, $speeches);

        return (new CollectionModel($speechesProperties))
            ->setOption('Access-Control-Expose-Headers', 'Range-Unit, Content-Range') //TODO should go into Rend
            ->setStatus(206)
            ->setRange($positionBegin, $positionEnd, $count);
    }

    /**
     * Get all speeches by an issue.
     * Paginated.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\SpeechCongressmanProperties
     * @query leit [string]
     */
    public function getList()
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $count = 0;
        $queryParam = $this->params()->fromQuery('leit', null);
        $category = $this->getCategoryFromQuery();

        if ($queryParam) {
            $speeches = $this->speechSearch->fetchByIssue($queryParam, $assemblyId, $issueId);
            $count = count($speeches);
            $range = new RangeValue();
            $speechesAndProperties = array_map(function (SpeechModel $speech) {
                $speech->setText(Transformer::speechToMarkdown($speech->getText()));

                $congressman = $this->congressmanService->get($speech->getCongressmanId());
                $party = $this->partyService->getByCongressman($speech->getCongressmanId(), $speech->getFrom());
                $congressmanPartyProperties = (new CongressmanPartyProperties())
                    ->setCongressman($congressman)
                    ->setParty($party);

                return (new SpeechCongressmanProperties())
                    ->setCongressman($congressmanPartyProperties)
                    ->setSpeech($speech);
            }, $speeches);
        } else {
            $count = $this->speechService->countByIssue($assemblyId, $issueId, $category);
            $range = $this->getRange($this->getRequest(), $count);

            $speeches = $this->speechService->fetchByIssue(
                $assemblyId,
                $issueId,
                $category,
                $range->getFrom(),
                $range->getSize(),
                1500
            );

            $speechesAndProperties = array_map(function (SpeechAndPosition $speech) {
                $speech->setText(Transformer::speechToMarkdown($speech->getText()));

                $congressman = $this->congressmanService->get($speech->getCongressmanId());
                $party = $this->partyService->getByCongressman($speech->getCongressmanId(), $speech->getFrom());
                $congressmanPartyProperties = (new CongressmanPartyProperties())
                    ->setCongressman($congressman)
                    ->setParty($party);

                return (new SpeechCongressmanProperties())
                    ->setCongressman($congressmanPartyProperties)
                    ->setSpeech($speech);
            }, $speeches);
        }

        return (new CollectionModel($speechesAndProperties))
            ->setStatus(206)
            ->setRange($range->getFrom(), (count($speeches) + $range->getFrom()), $count);
    }

    /**
     * Create one speech.
     *
     * @param mixed $id
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Speech
     * @throws \Exception
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
            try {
                $affectedRows = $this->speechService->save($form->getObject());
                return (new EmptyModel())->setStatus($affectedRows === 1 ? 201 : 205);
            } catch (\PDOException $e) {
                /**
                 * @todo damn you althingi.is For some reason, the plenary list is empty for some assemblies
                 *  but then there is a plenary id on the speech entry. So, sometimes a speech is trying to be saved
                 *  but it can't be attached to a plenary as it doesn't exists
                 *  Example: speeches here have a plenary id
                 *  http://www.althingi.is/altext/xml/thingmalalisti/thingmal/?lthing=20&malnr=1 but the plenary list
                 *  it self is empty http://www.althingi.is/altext/xml/thingfundir/?lthing=20
                 */
                if ($e->getCode() == 23000 && strpos($e->getMessage(), 'fk_Speach_Plenary1') !== false) {

                    /** @var  $speech \Althingi\Model\Speech */
                    $speech = $form->getObject();
                    $plenary = (new \Althingi\Model\Plenary())
                        ->setAssemblyId($speech->getAssemblyId())
                        ->setPlenaryId($speech->getPlenaryId())
                        ->setFrom($speech->getFrom())
                        ->setTo($speech->getTo());

                    try {
                        $this->plenaryService->save($plenary);
                        $affectedRows = $this->speechService->save($speech);

                        return (new EmptyModel())->setStatus($affectedRows === 1 ? 201 : 205);
                    } catch (Exception $exception) {
                        throw $exception;
                    }
                } else {
                    throw $e;
                }
            }
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * Update one speech.
     *
     * @param $id
     * @param $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Speech
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
                    ->setStatus(205);
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
     * @return $this
     */
    public function setCongressmanService(Congressman $congressman)
    {
        $this->congressmanService = $congressman;
        return $this;
    }

    /**
     * @param Party $party
     * @return $this
     */
    public function setPartyService(Party $party)
    {
        $this->partyService = $party;
        return $this;
    }

    /**
     * @param Speech $speech
     * @return $this
     */
    public function setSpeechService(Speech $speech)
    {
        $this->speechService = $speech;
        return $this;
    }

    /**
     * @param \Althingi\Service\SearchSpeech $speech
     * @return $this
     */
    public function setSearchSpeechService(SearchSpeech $speech)
    {
        $this->speechSearch = $speech;
        return $this;
    }

    /**
     * @param Plenary $plenary
     * @return $this
     */
    public function setPlenaryService(Plenary $plenary)
    {
        $this->plenaryService = $plenary;
        return $this;
    }
}
