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
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'docs' => [
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => [
                            'route'    => 'docs',
                            'defaults' => [
                                'controller' => 'Althingi\Controller\Index',
                                'action'     => 'docs',
                            ],
                        ],
                    ],
                    'client-router' => [
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'options' => [
                            'regex'    => '(?<category>(thingmenn|loggjafarthing))(.*)',
                            'spec' => '%category%',
                            'defaults' => [
                                'controller' => 'Althingi\Controller\Index',
                                'action'     => 'index',
                            ],
                        ],
                    ],
                ],
            ],
            'home' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/api',
                    'defaults' => [
                        'controller' => 'Althingi\Controller\Index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
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
                                ]
                            ],
                        ],
                    ],
                    'thingmenn' => [
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'    => '/thingmenn[/:id]',
                            'defaults' => [
                                'controller' => 'Althingi\Controller\Congressman',
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
                                        'identifier' => 'issue_id'
                                    ],
                                ]
                            ],
                        ],
                    ],
                    'thingseta' => [
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'    => '/thingmenn/:id/thingseta',
                            'defaults' => [
                                'controller' => 'Althingi\Controller\Session',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'fundur' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/:session_id',
                                    'defaults' => [
                                        'controller' => 'Althingi\Controller\CongressmanSession',
                                    ],
                                ],
                            ],
                        ]
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
                ]
            ],

        ],
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
            'Althingi\Controller\CongressmanIssue' => 'Althingi\Controller\CongressmanIssueController',

            'Althingi\Controller\Console' => 'Althingi\Controller\Console\ConsoleController',
            'Althingi\Controller\ConsoleCongressman' => 'Althingi\Controller\Console\ConsoleCongressmanController',
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
            'ViewJsonStrategy',
            'MessageStrategy',
        ],
    ],
    // Placeholder for console routes
    'console' => [
        'router' => [
            'routes' => [
                'load-assembly' => [
                    'options' => [
                        'route'    => 'load:assembly',
                        'defaults' => [
                            'controller' => 'Althingi\Controller\Console',
                            'action'     => 'find-assembly'
                        ]
                    ]
                ],
                'current-assembly' => [
                    'options' => [
                        'route'    => 'load:assembly:current',
                        'defaults' => [
                            'controller' => 'Althingi\Controller\Console',
                            'action'     => 'current-assembly'
                        ]
                    ]
                ],
                'congressman' => [
                    'options' => [
                        'route'    => 'load:congressman [--assembly=|-a]',
                        'defaults' => [
                            'controller' => 'Althingi\Controller\ConsoleCongressman',
                            'action'     => 'find-congressman'
                        ]
                    ]
                ],
                'party' => [
                    'options' => [
                        'route'    => 'load:party',
                        'defaults' => [
                            'controller' => 'Althingi\Controller\Console',
                            'action'     => 'find-party'
                        ]
                    ]
                ],
                'constituency' => [
                    'options' => [
                        'route'    => 'load:constituency',
                        'defaults' => [
                            'controller' => 'Althingi\Controller\Console',
                            'action'     => 'find-constituency'
                        ]
                    ]
                ],
                'plenary' => [
                    'options' => [
                        'route'    => 'load:plenary [--assembly=|-a]',
                        'defaults' => [
                            'controller' => 'Althingi\Controller\Console',
                            'action'     => 'find-plenary'
                        ]
                    ]
                ],
                'issue' => [
                    'options' => [
                        'route'    => 'load:issue [--assembly=|-a]',
                        'defaults' => [
                            'controller' => 'Althingi\Controller\Console',
                            'action'     => 'find-issue'
                        ]
                    ]
                ],
            ],
        ],
    ],
);
