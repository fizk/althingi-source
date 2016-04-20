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

    ]
];
