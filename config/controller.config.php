<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 9/04/2016
 * Time: 11:47 AM
 */


return [
    'initializers' => [
        'Althingi\Lib\ServiceCongressmanAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\ServiceCongressmanAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setCongressmanService($locator->get('Althingi\Service\Congressman'));
            }
        },

        'Althingi\Lib\ServiceIssueAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\ServiceIssueAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setIssueService($locator->get('Althingi\Service\Issue'));
            }
        },

        'Althingi\Lib\ServicePartyAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\ServicePartyAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setPartyService($locator->get('Althingi\Service\Party'));
            }
        },

        'Althingi\Lib\ServiceAssemblyAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\ServiceAssemblyAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setAssemblyService($locator->get('Althingi\Service\Assembly'));
            }
        },

        'Althingi\Lib\ServiceDocumentAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\ServiceDocumentAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setDocumentService($locator->get('Althingi\Service\Document'));
            }
        },

        'Althingi\Lib\ServiceVoteAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\ServiceVoteAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setVoteService($locator->get('Althingi\Service\Vote'));
            }
        },

        'Althingi\Lib\ServiceVoteItemAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\ServiceVoteItemAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setVoteItemService($locator->get('Althingi\Service\VoteItem'));
            }
        },

        'Althingi\Lib\ServiceSpeechAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\ServiceSpeechAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setSpeechService($locator->get('Althingi\Service\Speech'));
            }
        },

        'Althingi\Lib\ServiceCommitteeAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\ServiceCommitteeAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setCommitteeService($locator->get('Althingi\Service\Committee'));
            }
        },

        'Althingi\Lib\ServiceCommitteeAgendaAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\ServiceCommitteeMeetingAgendaAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setCommitteeMeetingAgendaService($locator->get('Althingi\Service\CommitteeMeetingAgenda'));
            }
        },

        'Althingi\Lib\ServiceCommitteeMeetingAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\ServiceCommitteeMeetingAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setCommitteeMeetingService($locator->get('Althingi\Service\CommitteeMeeting'));
            }
        },

        'Althingi\Lib\ServiceSessionAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\ServiceSessionAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setSessionService($locator->get('Althingi\Service\Session'));
            }
        },

        'Althingi\Lib\ServiceConstituencyAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\ServiceConstituencyAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setConstituencyService($locator->get('Althingi\Service\Constituency'));
            }
        },

        'Althingi\Lib\ServicePlenaryAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\ServicePlenaryAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setPlenaryService($locator->get('Althingi\Service\Plenary'));
            }
        },

        'Althingi\Lib\ServiceProponentAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\ServiceProponentAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setProponentService($locator->get('Althingi\Service\Proponent'));
            }
        },

        'Althingi\Lib\ServiceCabinetAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\ServiceCabinetAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setCabinetService($locator->get('Althingi\Service\Cabinet'));
            }
        },

        'Althingi\Lib\ServicePresidentAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\ServicePresidentAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setPresidentService($locator->get('Althingi\Service\President'));
            }
        },

        'Althingi\Lib\ServiceSuperCategoryAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\ServiceSuperCategoryAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setSuperCategoryService($locator->get('Althingi\Service\SuperCategory'));
            }
        },

        'Althingi\Lib\ServiceCategoryAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\ServiceCategoryAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setCategoryService($locator->get('Althingi\Service\Category'));
            }
        },

        'Althingi\Lib\ServiceIssueCategoryAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\ServiceIssueCategoryAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setIssueCategoryService($locator->get('Althingi\Service\IssueCategory'));
            }
        },

        'Althingi\Lib\ServiceElectionAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\ServiceElectionAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setElectionService($locator->get('Althingi\Service\Election'));
            }
        },

        'Althingi\Lib\CommandGetAssemblyAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\CommandGetAssemblyAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setGetAssemblyCommand($locator->get('Althingi\Command\GetAssembly'));
            }
        },

    ]
];
