<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

use Althingi\Controller\IndexController;
use Althingi\Controller\AssemblyController;
use Althingi\Controller\CongressmanController;
use Althingi\Controller\SessionController;
use Althingi\Controller\CongressmanSessionController;
use Althingi\Controller\PartyController;
use Althingi\Controller\ConstituencyController;
use Althingi\Controller\PlenaryController;
use Althingi\Controller\IssueController;
use Althingi\Controller\SpeechController;
use Althingi\Controller\VoteController;
use Althingi\Controller\VoteItemController;
use Althingi\Controller\CongressmanIssueController;
use Althingi\Controller\ProponentController;
use Althingi\Controller\DocumentController;
use Althingi\Controller\CommitteeController;
use Althingi\Controller\CabinetController;
use Althingi\Controller\PresidentController;
use Althingi\Controller\PresidentAssemblyController;
use Althingi\Controller\SuperCategoryController;
use Althingi\Controller\CategoryController;
use Althingi\Controller\IssueCategoryController;
use Althingi\Controller\CommitteeMeetingController;
use Althingi\Controller\CommitteeMeetingAgendaController;
use Althingi\Controller\AssemblyCommitteeController;

return array(
    'router' => [
        'routes' => [
            'index' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'loggjafarthing' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route'    => '/loggjafarthing[/:id]',
                    'defaults' => [
                        'controller' => AssemblyController::class,
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'thingmenn' => [
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => [
                            'route'    => '/thingmenn',
                            'defaults' => [
                                'controller' => CongressmanController::class,
                                'action' => 'assembly'
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'raedutimar' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/:congressman_id/raedutimar',
                                    'defaults' => [
                                        'controller' => CongressmanController::class,
                                        'action' => 'assembly-speech-time'
                                    ],
                                ],
                            ],
                            'thingseta' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/:congressman_id/thingseta',
                                    'defaults' => [
                                        'controller' => CongressmanController::class,
                                        'action' => 'assembly-sessions'
                                    ],
                                ],
                            ],
                            'thingmal' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/:congressman_id/thingmal',
                                    'defaults' => [
                                        'controller' => CongressmanController::class,
                                        'action' => 'assembly-issues'
                                    ],
                                ],
                            ],
                            'atvaedagreidslur' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/:congressman_id/atvaedagreidslur',
                                    'defaults' => [
                                        'controller' => CongressmanController::class,
                                        'action' => 'assembly-voting'
                                    ],
                                ],
                            ],
                            'malaflokkar' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/:congressman_id/malaflokkar',
                                    'defaults' => [
                                        'controller' => CongressmanController::class,
                                        'action' => 'assembly-categories'
                                    ],
                                ],
                            ],
                            'atvaedagreidslur-malaflokkar' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/:congressman_id/atvaedagreidslur-malaflokkar',
                                    'defaults' => [
                                        'controller' => CongressmanController::class,
                                        'action' => 'assembly-vote-categories'
                                    ],
                                ],
                            ]
                        ]
                    ],
                    'forsetar' => [
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'    => '/forsetar[/:congressman_id]',
                            'defaults' => [
                                'controller' => PresidentAssemblyController::class,
                                'identifier' => 'congressman_id'
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'samantekt' => [
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => [
                            'route'    => '/samantekt',
                            'defaults' => [
                                'controller' => AssemblyController::class,
                                'action' => 'statistics'
                            ],
                        ],
                    ],
                    'raduneyti' => [
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => [
                            'route'    => '/raduneyti',
                            'defaults' => [
                                'controller' => CabinetController::class,
                                'action' => 'assembly'
                            ],
                        ],
                    ],
                    'thingfundir' => [
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'    => '/thingfundir[/:plenary_id]',
                            'defaults' => [
                                'controller' => PlenaryController::class,
                                'identifier' => 'plenary_id'
                            ],
                        ],
                    ],
                    'thingmal' => [
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'    => '/thingmal[/:issue_id]',
                            'defaults' => [
                                'controller' => IssueController::class,
                                'identifier' => 'issue_id'
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'thingraedur' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/raedur[/:speech_id]',
                                    'defaults' => [
                                        'controller' => SpeechController::class,
                                        'identifier' => 'speech_id'
                                    ],
                                ],
                            ],

                            'efnisflokkar' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/efnisflokkar[/:category_id]',
                                    'defaults' => [
                                        'controller' => IssueCategoryController::class,
                                        'identifier' => 'category_id'
                                    ],
                                ],
                                'may_terminate' => true,
                            ],

                            'thingskjal' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/thingskjal[/:document_id]',
                                    'defaults' => [
                                        'controller' => DocumentController::class,
                                        'identifier' => 'document_id'
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'flutningsmenn' => [
                                        'type' => 'Zend\Mvc\Router\Http\Segment',
                                        'options' => [
                                            'route'    => '/flutningsmenn[/:congressman_id]',
                                            'defaults' => [
                                                'controller' => ProponentController::class,
                                                'identifier' => 'congressman_id'
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'atkvaedagreidslur' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/atkvaedagreidslur[/:vote_id]',
                                    'defaults' => [
                                        'controller' => VoteController::class,
                                        'identifier' => 'vote_id'
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'atkvaedagreidsla' => [
                                        'type' => 'Zend\Mvc\Router\Http\Segment',
                                        'options' => [
                                            'route'    => '/atkvaedi[/:vote_item_id]',
                                            'defaults' => [
                                                'controller' => VoteItemController::class,
                                                'identifier' => 'vote_item_id'
                                            ],
                                        ],
                                    ]
                                ]
                            ],
                        ]
                    ],
                    'nefndir' => [
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'    => '/nefndir[/:committee_id]',
                            'defaults' => [
                                'controller' => AssemblyCommitteeController::class,
                                'identifier' => 'committee_id'
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'nefndarfundir' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/nefndarfundir[/:committee_meeting_id]',
                                    'defaults' => [
                                        'controller' => CommitteeMeetingController::class,
                                        'identifier' => 'committee_meeting_id'
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'dagskralidir' => [
                                        'type' => 'Zend\Mvc\Router\Http\Segment',
                                        'options' => [
                                            'route'    => '/dagskralidir[/:committee_meeting_agenda_id]',
                                            'defaults' => [
                                                'controller' => CommitteeMeetingAgendaController::class,
                                                'identifier' => 'committee_meeting_agenda_id'
                                            ],
                                        ],
                                    ]
                                ]
                            ]
                        ],
                    ]
                ],
            ],
            'nefndir' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route'    => '/nefndir[/:committee_id]',
                    'defaults' => [
                        'controller' => CommitteeController::class,
                        'identifier' => 'committee_id'
                    ],
                ],
            ],
            'thingmenn' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route'    => '/thingmenn[/:congressman_id]',
                    'defaults' => [
                        'controller' => CongressmanController::class,
                        'identifier' => 'congressman_id'
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'thingmal' => [
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'    => '/thingmal[/:issue_id]',
                            'defaults' => [
                                'controller' => CongressmanIssueController::class,
                                'identifier' => 'issue_id'
                            ],
                        ]
                    ],
                    'thingseta' => [
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'    => '/thingseta[/:session_id]',
                            'defaults' => [
                                'controller' => SessionController::class,
                                'identifier' => 'session_id'
                            ],
                        ],
                        'may_terminate' => true,
//                                'child_routes' => [
//                                    'fundur' => [
//                                        'type' => 'Zend\Mvc\Router\Http\Segment',
//                                        'options' => [
//                                            'route'    => '/:session_id',
//                                            'defaults' => [
//                                                'controller' => CongressmanSessionController::class,
//                                            ],
//                                        ],
//                                    ],
//                                ]
                    ],
                ],
            ],
            'thingflokkar' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route'    => '/thingflokkar[/:id]',
                    'defaults' => [
                        'controller' => PartyController::class,
                    ],
                ],
            ],
            'kjordaemi' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route'    => '/kjordaemi[/:id]',
                    'defaults' => [
                        'controller' => ConstituencyController::class,
                    ],
                ],
            ],
            'forsetar' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route'    => '/forsetar[/:id]',
                    'defaults' => [
                        'controller' => PresidentController::class,
                    ],
                ],
            ],
            'efnisflokkar' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route'    => '/thingmal/efnisflokkar[/:super_category_id]',
                    'defaults' => [
                        'controller' => SuperCategoryController::class,
                        'identifier' => 'super_category_id'
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'undirflokkar' => [
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'    => '/undirflokkar[/:category_id]',
                            'defaults' => [
                                'controller' => CategoryController::class,
                                'identifier' => 'category_id'
                            ],
                        ],
                    ]
                ]
            ]
        ]
    ],

    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
        'aliases' => [
            'translator' => 'MvcTranslator',
        ],
    ],
    'translator' => [
        'locale' => 'en_US',
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ],
        ],
    ],

    'controllers' =>[
        'invokables' => [
            IndexController::class => IndexController::class,
            AssemblyController::class => AssemblyController::class,
            CongressmanController::class => CongressmanController::class,
            SessionController::class => SessionController::class,
            CongressmanSessionController::class => CongressmanSessionController::class,
            PartyController::class => PartyController::class,
            ConstituencyController::class => ConstituencyController::class,
            PlenaryController::class => PlenaryController::class,
            IssueController::class => IssueController::class,
            SpeechController::class => SpeechController::class,
            VoteController::class => VoteController::class,
            VoteItemController::class => VoteItemController::class,
            CongressmanIssueController::class => CongressmanIssueController::class,
            ProponentController::class => ProponentController::class,
            DocumentController::class => DocumentController::class,
            CommitteeController::class => CommitteeController::class,
            CabinetController::class => CabinetController::class,
            PresidentController::class => PresidentController::class,
            PresidentAssemblyController::class => PresidentAssemblyController::class,
            SuperCategoryController::class => SuperCategoryController::class,
            CategoryController::class => CategoryController::class,
            IssueCategoryController::class => IssueCategoryController::class,
            CommitteeMeetingController::class => CommitteeMeetingController::class,
            CommitteeMeetingAgendaController::class => CommitteeMeetingAgendaController::class,
            AssemblyCommitteeController::class => AssemblyCommitteeController::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'MessageStrategy',
        ],
    ],
    // Placeholder for console routes
    'console' => [
        'router' => [
            'routes' => [],
        ],
    ],
);
