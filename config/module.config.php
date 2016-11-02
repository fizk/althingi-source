<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => [
        'routes' => [
            'index' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'Althingi\Controller\Index',
                        'action' => 'index'
                    ],
                ],
            ],
            'loggjafarthing' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route'    => '/loggjafarthing[/:id]',
                    'defaults' => [
                        'controller' => 'Althingi\Controller\Assembly',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'thingmenn' => [
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => [
                            'route'    => '/thingmenn',
                            'defaults' => [
                                'controller' => 'Althingi\Controller\Congressman',
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
                                        'controller' => 'Althingi\Controller\Congressman',
                                        'action' => 'assembly-speech-time'
                                    ],
                                ],
                            ],
                            'thingseta' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/:congressman_id/thingseta',
                                    'defaults' => [
                                        'controller' => 'Althingi\Controller\Congressman',
                                        'action' => 'assembly-sessions'
                                    ],
                                ],
                            ],
                            'thingmal' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/:congressman_id/thingmal',
                                    'defaults' => [
                                        'controller' => 'Althingi\Controller\Congressman',
                                        'action' => 'assembly-issues'
                                    ],
                                ],
                            ],
                            'atvaedagreidslur' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/:congressman_id/atvaedagreidslur',
                                    'defaults' => [
                                        'controller' => 'Althingi\Controller\Congressman',
                                        'action' => 'assembly-voting'
                                    ],
                                ],
                            ],
                            'malaflokkar' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/:congressman_id/malaflokkar',
                                    'defaults' => [
                                        'controller' => 'Althingi\Controller\Congressman',
                                        'action' => 'assembly-categories'
                                    ],
                                ],
                            ],
                            'atvaedagreidslur-malaflokkar' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/:congressman_id/atvaedagreidslur-malaflokkar',
                                    'defaults' => [
                                        'controller' => 'Althingi\Controller\Congressman',
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
                                'controller' => 'Althingi\Controller\PresidentAssembly',
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
                                'controller' => 'Althingi\Controller\Assembly',
                                'action' => 'statistics'
                            ],
                        ],
                    ],
                    'raduneyti' => [
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => [
                            'route'    => '/raduneyti',
                            'defaults' => [
                                'controller' => 'Althingi\Controller\Cabinet',
                                'action' => 'assembly'
                            ],
                        ],
                    ],
                    'thingfundir' => [
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'    => '/thingfundir[/:plenary_id]',
                            'defaults' => [
                                'controller' => 'Althingi\Controller\Plenary',
                                'identifier' => 'plenary_id'
                            ],
                        ],
                    ],
                    'thingmal' => [
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'    => '/thingmal[/:issue_id]',
                            'defaults' => [
                                'controller' => 'Althingi\Controller\Issue',
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
                                        'controller' => 'Althingi\Controller\Speech',
                                        'identifier' => 'speech_id'
                                    ],
                                ],
                            ],

                            'efnisflokkar' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/efnisflokkar[/:category_id]',
                                    'defaults' => [
                                        'controller' => 'Althingi\Controller\IssueCategory',
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
                                        'controller' => 'Althingi\Controller\Document',
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
                                                'controller' => 'Althingi\Controller\Proponent',
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
                                        'controller' => 'Althingi\Controller\Vote',
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
                                                'controller' => 'Althingi\Controller\VoteItem',
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
                                'controller' => 'Althingi\Controller\NULL',
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
                                        'controller' => 'Althingi\Controller\CommitteeMeeting',
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
                                                'controller' => 'Althingi\Controller\CommitteeMeetingAgenda',
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
                        'controller' => 'Althingi\Controller\Committee',
                        'identifier' => 'committee_id'
                    ],
                ],
            ],
            'thingmenn' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route'    => '/thingmenn[/:congressman_id]',
                    'defaults' => [
                        'controller' => 'Althingi\Controller\Congressman',
                        'identifier' => 'congressman_id'
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'thingmal' => [
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'    => '/thingmal',
                            'defaults' => [
                                'controller' => 'Althingi\Controller\CongressmanIssue',
                            ],
                        ]
                    ],
                    'thingseta' => [
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'    => '/thingseta[/:session_id]',
                            'defaults' => [
                                'controller' => 'Althingi\Controller\Session',
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
//                                                'controller' => 'Althingi\Controller\CongressmanSession',
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
                        'controller' => 'Althingi\Controller\Party',
                    ],
                ],
            ],
            'kjordaemi' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route'    => '/kjordaemi[/:id]',
                    'defaults' => [
                        'controller' => 'Althingi\Controller\Constituency',
                    ],
                ],
            ],
            'forsetar' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route'    => '/forsetar[/:id]',
                    'defaults' => [
                        'controller' => 'Althingi\Controller\President',
                    ],
                ],
            ],
            'efnisflokkar' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route'    => '/thingmal/efnisflokkar[/:super_category_id]',
                    'defaults' => [
                        'controller' => 'Althingi\Controller\SuperCategory',
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
                                'controller' => 'Althingi\Controller\Category',
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
            'Althingi\Controller\Index' => 'Althingi\Controller\IndexController',
            'Althingi\Controller\Assembly' => 'Althingi\Controller\AssemblyController',
            'Althingi\Controller\Congressman' => 'Althingi\Controller\CongressmanController',
            'Althingi\Controller\Session' => 'Althingi\Controller\SessionController',
            'Althingi\Controller\CongressmanSession' => 'Althingi\Controller\CongressmanSessionController',
            'Althingi\Controller\Party' => 'Althingi\Controller\PartyController',
            'Althingi\Controller\Constituency' => 'Althingi\Controller\ConstituencyController',
            'Althingi\Controller\Plenary' => 'Althingi\Controller\PlenaryController',
            'Althingi\Controller\Issue' => 'Althingi\Controller\IssueController',
            'Althingi\Controller\Speech' => 'Althingi\Controller\SpeechController',
            'Althingi\Controller\Vote' => 'Althingi\Controller\VoteController',
            'Althingi\Controller\VoteItem' => 'Althingi\Controller\VoteItemController',
            'Althingi\Controller\CongressmanIssue' => 'Althingi\Controller\CongressmanIssueController',
            'Althingi\Controller\Proponent' => 'Althingi\Controller\ProponentController',
            'Althingi\Controller\Document' => 'Althingi\Controller\DocumentController',
            'Althingi\Controller\Committee' => 'Althingi\Controller\CommitteeController',
            'Althingi\Controller\Cabinet' => 'Althingi\Controller\CabinetController',
            'Althingi\Controller\President' => 'Althingi\Controller\PresidentController',
            'Althingi\Controller\PresidentAssembly' => 'Althingi\Controller\PresidentAssemblyController',
            'Althingi\Controller\SuperCategory' => 'Althingi\Controller\SuperCategoryController',
            'Althingi\Controller\Category' => 'Althingi\Controller\CategoryController',
            'Althingi\Controller\IssueCategory' => 'Althingi\Controller\IssueCategoryController',
            'Althingi\Controller\CommitteeMeeting' => 'Althingi\Controller\CommitteeMeetingController',
            'Althingi\Controller\CommitteeMeetingAgenda' => 'Althingi\Controller\CommitteeMeetingAgendaController',
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
