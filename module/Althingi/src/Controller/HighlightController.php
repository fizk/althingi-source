<?php

namespace Althingi\Controller;

use Althingi\Injector\ServiceAssemblyAwareInterface;
use Althingi\Injector\ServiceCabinetAwareInterface;
use Althingi\Injector\ServiceCongressmanAwareInterface;
use Althingi\Injector\ServiceIssueAwareInterface;
use Althingi\Injector\ServiceSpeechAwareInterface;
use Althingi\Injector\ServicePartyAwareInterface;
use Althingi\Utils\Transformer;
use Althingi\Model\AssemblyProperties;
use Althingi\Model\CongressmanPartyProperties;
use Althingi\Model\IssueWithSpeechProperties;
use Althingi\Model\SpeechCongressmanProperties;
use Althingi\Service\Assembly;
use Althingi\Service\Cabinet;
use Althingi\Service\Congressman;
use Althingi\Service\Issue;
use Althingi\Service\Party;
use Althingi\Service\Speech;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ItemModel;

class HighlightController extends AbstractRestfulController implements
    ServiceAssemblyAwareInterface,
    ServiceCabinetAwareInterface,
    ServicePartyAwareInterface,
    ServiceSpeechAwareInterface,
    ServiceCongressmanAwareInterface,
    ServiceIssueAwareInterface
{

    /** @var  \Althingi\Service\Assembly */
    private $assemblyService;

    /** @var  \Althingi\Service\Cabinet */
    private $cabinetService;

    /** @var  \Althingi\Service\Party */
    private $partyService;

    /** @var  \Althingi\Service\Speech */
    private $speechService;

    /** @var  \Althingi\Service\Congressman */
    private $congressmanService;

    /** @var  \Althingi\Service\Issue */
    private $issueService;

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\AssemblyProperties
     * @200 Success
     */
    public function getCurrentAssemblyAction()
    {
        $assembly = $this->assemblyService->getCurrent();

        $assemblyProperties = (new AssemblyProperties())
            ->setAssembly($assembly);
        $cabinets = $this->cabinetService->fetchByAssembly($assembly->getAssemblyId());

        foreach ($cabinets as $cabinet) {
            $assemblyProperties->setMajority(
                $this->partyService->fetchByCabinet($cabinet->getCabinetId())
            );
            $assemblyProperties->setMinority(
                $this->partyService->fetchByAssembly(
                    $assembly->getAssemblyId(),
                    $assemblyProperties->getMajorityPartyIds()
                )
            );
        }

        return (new ItemModel($assemblyProperties))
            ->setStatus(200);
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\IssueWithSpeechProperties
     * @query category
     * @200 Success
     */
    public function getActiveIssueAction()
    {
        $speech = $this->speechService->getLastActive();

        $speech->setText(Transformer::speechToMarkdown($speech->getText()));

        $congressman = $this->congressmanService->get($speech->getCongressmanId());
        $congressmanPartyProperties = (new CongressmanPartyProperties())
            ->setCongressman($congressman)
            ->setParty($this->partyService->getByCongressman($speech->getCongressmanId(), $speech->getFrom()));

        $speechCongressmanProperties = (new SpeechCongressmanProperties())
            ->setCongressman($congressmanPartyProperties)
            ->setSpeech($speech);

        $issue = $this->issueService
            ->getWithDate($speech->getIssueId(), $speech->getAssemblyId(), ['A', 'B']);

        $issueWithSpeech = (new IssueWithSpeechProperties())
            ->setIssue($issue)
            ->setSpeech($speechCongressmanProperties);

        return (new ItemModel($issueWithSpeech))
            ->setStatus(200);
    }

    /**
     * @param Assembly $assembly
     * @return $this
     */
    public function setAssemblyService(Assembly $assembly)
    {
        $this->assemblyService = $assembly;
        return $this;
    }

    /**
     * @param Cabinet $cabinet
     * @return $this
     */
    public function setCabinetService(Cabinet $cabinet)
    {
        $this->cabinetService = $cabinet;
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
     * @param Congressman $congressman
     * @return $this
     */
    public function setCongressmanService(Congressman $congressman)
    {
        $this->congressmanService = $congressman;
        return $this;
    }

    /**
     * @param Issue $issue
     * @return $this
     */
    public function setIssueService(Issue $issue)
    {
        $this->issueService = $issue;
        return $this;
    }
}
