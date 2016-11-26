<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Form\Assembly as AssemblyForm;
use Althingi\Lib\CommandGetAssemblyAwareInterface;
use Althingi\Lib\ServiceAssemblyAwareInterface;
use Althingi\Lib\ServiceCabinetAwareInterface;
use Althingi\Lib\ServiceCategoryAwareInterface;
use Althingi\Lib\ServiceCommitteeAwareInterface;
use Althingi\Lib\ServiceElectionAwareInterface;
use Althingi\Lib\ServiceIssueAwareInterface;
use Althingi\Lib\ServicePartyAwareInterface;
use Althingi\Lib\ServiceSpeechAwareInterface;
use Althingi\Lib\ServiceVoteAwareInterface;
use Althingi\Service\Assembly;
use Althingi\Service\Cabinet;
use Althingi\Service\Category;
use Althingi\Service\Committee;
use Althingi\Service\Election;
use Althingi\Service\Issue;
use Althingi\Service\Party;
use Althingi\Service\Speech;
use Althingi\Service\Vote;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;

class AssemblyCommitteeController extends AbstractRestfulController implements
    ServiceCommitteeAwareInterface
{

    /** @var  \Althingi\Service\Committee */
    private $committeeService;

    public function get($id)
    {
        $committeeId = $this->params('committee_id');
        $committee = $this->committeeService->get($committeeId);

        return $committee
            ? (new ItemModel($committee))
            : $this->notFoundAction();
    }

    public function getList()
    {
        $assemblyId = $this->params('id');
        $committees = $this->committeeService->fetchByAssembly($assemblyId);

        return (new CollectionModel($committees));
    }

    /**
     * @param Committee $committee
     */
    public function setCommitteeService(Committee $committee)
    {
        $this->committeeService = $committee;
    }
}
