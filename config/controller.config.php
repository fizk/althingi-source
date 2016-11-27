<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 9/04/2016
 * Time: 11:47 AM
 */

use Althingi\Lib\ServiceCongressmanAwareInterface;
use Althingi\Lib\ServiceIssueAwareInterface;
use Althingi\Lib\ServicePartyAwareInterface;
use Althingi\Lib\ServiceAssemblyAwareInterface;
use Althingi\Lib\ServiceDocumentAwareInterface;
use Althingi\Lib\ServiceVoteAwareInterface;
use Althingi\Lib\ServiceVoteItemAwareInterface;
use Althingi\Lib\ServiceSpeechAwareInterface;
use Althingi\Lib\ServiceCommitteeAwareInterface;
use Althingi\Lib\ServiceCommitteeMeetingAgendaAwareInterface;
use Althingi\Lib\ServiceCommitteeMeetingAwareInterface;
use Althingi\Lib\ServiceSessionAwareInterface;
use Althingi\Lib\ServiceConstituencyAwareInterface;
use Althingi\Lib\ServicePlenaryAwareInterface;
use Althingi\Lib\ServiceProponentAwareInterface;
use Althingi\Lib\ServiceCabinetAwareInterface;
use Althingi\Lib\ServicePresidentAwareInterface;
use Althingi\Lib\ServiceSuperCategoryAwareInterface;
use Althingi\Lib\ServiceCategoryAwareInterface;
use Althingi\Lib\ServiceIssueCategoryAwareInterface;
use Althingi\Lib\ServiceElectionAwareInterface;
use Althingi\Service\Assembly;
use Althingi\Service\Congressman;
use Althingi\Service\Session;
use Althingi\Service\Party;
use Althingi\Service\Constituency;
use Althingi\Service\Plenary;
use Althingi\Service\Issue;
use Althingi\Service\Speech;
use Althingi\Service\Vote;
use Althingi\Service\VoteItem;
use Althingi\Service\Proponent;
use Althingi\Service\Document;
use Althingi\Service\Committee;
use Althingi\Service\CommitteeMeeting;
use Althingi\Service\CommitteeMeetingAgenda;
use Althingi\Service\Cabinet;
use Althingi\Service\President;
use Althingi\Service\SuperCategory;
use Althingi\Service\Category;
use Althingi\Service\IssueCategory;
use Althingi\Service\Election;
use Zend\ServiceManager\AbstractPluginManager;

return [
    'initializers' => [
        ServiceCongressmanAwareInterface::class => function ($instance, AbstractPluginManager $sm) {
            if ($instance instanceof ServiceCongressmanAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setCongressmanService($locator->get(Congressman::class));
            }
        },

        ServiceIssueAwareInterface::class => function ($instance, AbstractPluginManager $sm) {
            if ($instance instanceof ServiceIssueAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setIssueService($locator->get(Issue::class));
            }
        },

        ServicePartyAwareInterface::class => function ($instance, AbstractPluginManager $sm) {
            if ($instance instanceof ServicePartyAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setPartyService($locator->get(Party::class));
            }
        },

        ServiceAssemblyAwareInterface::class => function ($instance, AbstractPluginManager $sm) {
            if ($instance instanceof ServiceAssemblyAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setAssemblyService($locator->get(Assembly::class));
            }
        },

        ServiceDocumentAwareInterface::class => function ($instance, AbstractPluginManager $sm) {
            if ($instance instanceof ServiceDocumentAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setDocumentService($locator->get(Document::class));
            }
        },

        ServiceVoteAwareInterface::class => function ($instance, AbstractPluginManager $sm) {
            if ($instance instanceof ServiceVoteAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setVoteService($locator->get(Vote::class));
            }
        },

        ServiceVoteItemAwareInterface::class => function ($instance, AbstractPluginManager $sm) {
            if ($instance instanceof ServiceVoteItemAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setVoteItemService($locator->get(VoteItem::class));
            }
        },

        ServiceSpeechAwareInterface::class => function ($instance, AbstractPluginManager $sm) {
            if ($instance instanceof ServiceSpeechAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setSpeechService($locator->get(Speech::class));
            }
        },

        ServiceCommitteeAwareInterface::class => function ($instance, AbstractPluginManager $sm) {
            if ($instance instanceof ServiceCommitteeAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setCommitteeService($locator->get(Committee::class));
            }
        },

        ServiceCommitteeMeetingAgendaAwareInterface::class => function ($instance, AbstractPluginManager $sm) {
            if ($instance instanceof ServiceCommitteeMeetingAgendaAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setCommitteeMeetingAgendaService($locator->get(CommitteeMeetingAgenda::class));
            }
        },

        ServiceCommitteeMeetingAwareInterface::class => function ($instance, AbstractPluginManager $sm) {
            if ($instance instanceof ServiceCommitteeMeetingAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setCommitteeMeetingService($locator->get(CommitteeMeeting::class));
            }
        },

        ServiceSessionAwareInterface::class => function ($instance, AbstractPluginManager $sm) {
            if ($instance instanceof ServiceSessionAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setSessionService($locator->get(Session::class));
            }
        },

        ServiceConstituencyAwareInterface::class => function ($instance, AbstractPluginManager $sm) {
            if ($instance instanceof ServiceConstituencyAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setConstituencyService($locator->get(Constituency::class));
            }
        },

        ServicePlenaryAwareInterface::class => function ($instance, AbstractPluginManager $sm) {
            if ($instance instanceof ServicePlenaryAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setPlenaryService($locator->get(Plenary::class));
            }
        },

        ServiceProponentAwareInterface::class => function ($instance, AbstractPluginManager $sm) {
            if ($instance instanceof ServiceProponentAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setProponentService($locator->get(Proponent::class));
            }
        },

        ServiceCabinetAwareInterface::class => function ($instance, AbstractPluginManager $sm) {
            if ($instance instanceof ServiceCabinetAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setCabinetService($locator->get(Cabinet::class));
            }
        },

        ServicePresidentAwareInterface::class => function ($instance, AbstractPluginManager $sm) {
            if ($instance instanceof ServicePresidentAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setPresidentService($locator->get(President::class));
            }
        },

        ServiceSuperCategoryAwareInterface::class => function ($instance, AbstractPluginManager $sm) {
            if ($instance instanceof ServiceSuperCategoryAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setSuperCategoryService($locator->get(SuperCategory::class));
            }
        },

        ServiceCategoryAwareInterface::class => function ($instance, AbstractPluginManager $sm) {
            if ($instance instanceof ServiceCategoryAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setCategoryService($locator->get(Category::class));
            }
        },

        ServiceIssueCategoryAwareInterface::class => function ($instance, AbstractPluginManager $sm) {
            if ($instance instanceof ServiceIssueCategoryAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setIssueCategoryService($locator->get(IssueCategory::class));
            }
        },

        ServiceElectionAwareInterface::class => function ($instance, AbstractPluginManager $sm) {
            if ($instance instanceof ServiceElectionAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setElectionService($locator->get(Election::class));
            }
        },
    ]
];
