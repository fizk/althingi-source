<?php

namespace Althingi\Controller;

use Althingi\Injector\ServiceCongressmanAwareInterface;
use Althingi\Injector\ServiceConstituencyAwareInterface;
use Althingi\Injector\ServicePartyAwareInterface;
use Althingi\Injector\ServicePlenaryAwareInterface;
use Althingi\Injector\ServiceSearchSpeechAwareInterface;
use Althingi\Injector\ServiceSpeechAwareInterface;
use Althingi\Injector\StoreSpeechAwareInterface;
use Althingi\Service\Constituency;
use Althingi\Store\Speech;
use Althingi\Utils\Transformer;
use Althingi\Model;
use Althingi\Service;
use Althingi\Form;
use Rend\Controller\AbstractRestfulController;
use Rend\Helper\Http\RangeValue;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;
use Finite\Exception\Exception;

class SpeechController extends AbstractRestfulController implements
    ServiceSpeechAwareInterface,
    ServiceCongressmanAwareInterface,
    ServicePartyAwareInterface,
    ServicePlenaryAwareInterface,
    ServiceConstituencyAwareInterface,
    StoreSpeechAwareInterface
{
    use Range;

    /** @var \Althingi\Service\Speech */
    private $speechService;

    /** @var \Althingi\Store\Speech */
    private $speechStore;

    /** @var \Althingi\Service\Congressman */
    private $congressmanService;

    /** @var \Althingi\Service\Party */
    private $partyService;

    /** @var \Althingi\Service\Plenary */
    private $plenaryService;

    /** @var \Althingi\Service\Constituency */
    private $constituencyService;

    /**
     * Get Speech item and speeches surrounding it.
     *
     * @param mixed $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\SpeechCongressmanProperties
     * @query category
     */
    public function get($id)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $category = strtoupper($this->params('category', 'a'));
        $speechId = $id;

        $count = $this->speechService->countByIssue($assemblyId, $issueId, $category);
        $speeches = $this->speechService->fetch($speechId, $assemblyId, $issueId, 25, $category);
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
     * @query category
     */
    public function getList()
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $category = strtoupper($this->params('category', 'a'));

        // Store
        $count = $this->speechStore->countByIssue($assemblyId, $issueId, $category);
        $range = $this->getRange($this->getRequest(), $count);

        $speechesAndProperties = $this->speechStore->fetchByIssue(
            $assemblyId,
            $issueId,
            $category,
            $range->getFrom(),
            $range->getSize(),
            1500
        );

        // DB
//        $count = $this->speechService->countByIssue($assemblyId, $issueId, $category);
//        $range = $this->getRange($this->getRequest(), $count);
//
//        $speeches = $this->speechService->fetchByIssue(
//            $assemblyId,
//            $issueId,
//            $category,
//            $range->getFrom(),
//            $range->getSize(),
//            1500
//        );
//
//        $speechesAndProperties = array_map(function (Model\SpeechAndPosition $speech) {
//            $speech->setText(Transformer::speechToMarkdown($speech->getText()));
//            $congressmanPartyProperties = (new Model\CongressmanPartyProperties())
//                ->setCongressman($this->congressmanService->get(
//                    $speech->getCongressmanId()
//                ))->setParty($this->partyService->getByCongressman(
//                    $speech->getCongressmanId(),
//                    $speech->getFrom()
//                ))->setConstituency($this->constituencyService->getByCongressman(
//                    $speech->getCongressmanId(),
//                    $speech->getFrom()
//                ));
//
//            return (new Model\SpeechCongressmanProperties())
//                ->setCongressman($congressmanPartyProperties)
//                ->setSpeech($speech);
//        }, $speeches);

        return (new CollectionModel($speechesAndProperties))
            ->setStatus(206)
            ->setRange($range->getFrom(), (count($speechesAndProperties) + $range->getFrom()), $count);
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
        $category = strtoupper($this->params('category', 'a'));

        $form = new Form\Speech();
        $form->setData(array_merge(
            $data,
            ['speech_id' => $id, 'issue_id' => $issueId, 'assembly_id' => $assemblyId, 'category' => $category]
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
                    $plenary = (new Model\Plenary())
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
            $form = new Form\Speech();
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
     * @param \Althingi\Service\Congressman $congressman
     * @return $this
     */
    public function setCongressmanService(Service\Congressman $congressman)
    {
        $this->congressmanService = $congressman;
        return $this;
    }

    /**
     * @param \Althingi\Service\Party $party
     * @return $this
     */
    public function setPartyService(Service\Party $party)
    {
        $this->partyService = $party;
        return $this;
    }

    /**
     * @param \Althingi\Service\Speech $speech
     * @return $this
     */
    public function setSpeechService(Service\Speech $speech)
    {
        $this->speechService = $speech;
        return $this;
    }

    /**
     * @param \Althingi\Service\Plenary $plenary
     * @return $this
     */
    public function setPlenaryService(Service\Plenary $plenary)
    {
        $this->plenaryService = $plenary;
        return $this;
    }

    /**
     * @param Constituency $constituency
     * @return $this
     */
    public function setConstituencyService(Constituency $constituency)
    {
        $this->constituencyService = $constituency;
        return $this;
    }

    /**
     * @param \Althingi\Store\Speech $speech
     * @return $this
     */
    public function setSpeechStore(Speech $speech)
    {
        $this->speechStore = $speech;
        return $this;
    }
}
