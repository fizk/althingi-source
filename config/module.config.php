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
            'home' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'Althingi\Controller\Index',
                        //'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'loggjafarthing' => [
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'    => 'loggjafarthing[/:id]',
                            'defaults' => [
                                'controller' => 'Althingi\Controller\Loggjafarthing',
                            ],
                        ],
                    ]
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
            'Althingi\Controller\Loggjafarthing' => 'Althingi\Controller\LoggjafarthingController',
            'Althingi\Controller\Console' => 'Althingi\Controller\ConsoleController',
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
                ]

            ],
        ],
    ],
);
