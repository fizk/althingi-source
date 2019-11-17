<?php

namespace AlthingiTest\Utils;

use Althingi\Utils\RouteInspector;
use PHPUnit\Framework\TestCase;
use Zend\Router\Http\Segment;

class RouteInspectorTest extends TestCase
{
    public function testTraverser()
    {
        $routes = [
            'nefndir' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/nefndir[/:committee_id]',
                    'defaults' => [
                        'controller' => \Althingi\Controller\CommitteeController::class,
                        'identifier' => 'committee_id'
                    ],
                ],
            ],
            'annad' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/loggjafarthing[/:id]',
                    'constraints' => [
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller' => \Althingi\Controller\AssemblyController::class,
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'hundur' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/annad[/:committee_id]',
                            'defaults' => [
                                'controller' => \Althingi\Controller\CommitteeController::class,
                                'identifier' => 'committee_id'
                            ],
                        ],
                    ],
                ]
            ]
        ];

        $inspector = new RouteInspector();

        $expectedKeys = [
            '/nefndir[/:committee_id]',
            '/loggjafarthing[/:id]',
            '/loggjafarthing[/:id]/annad[/:committee_id]'
        ];
        $actual = $inspector->flattenRoutes($routes);

        $this->assertEquals($expectedKeys, array_keys($actual));
    }

    public function testExtract()
    {
        $list = [
            '/nefndir[/:committee_id]' => [
                'options' => [
                    'defaults' => [
                        'controller' => 'Althingi\Controller\CommitteeController',
                        'action' => null
                    ]
                ]
            ] ,
            '/loggjafarthing/statistics' => [
                'options' => [
                    'defaults' => [
                        'controller' => 'Althingi\Controller\AssemblyController',
                        'action' => 'statistics'
                    ]
                ]
            ]  ,
            '/loggjafarthing[/:id]/annad[/:committee_id]' => [
                'options' => [
                    'defaults' => [
                        'controller' => 'Althingi\Controller\CommitteeController'
                    ]
                ]
            ]
        ];

        $expected = [
            '/nefndir[/:committee_id]',
            '/nefndir',
            '/loggjafarthing/statistics',
            '/loggjafarthing[/:id]/annad[/:committee_id]',
            '/loggjafarthing[/:id]/annad',
        ];
        $inspector = new RouteInspector();
        $actual = $inspector->extractOptions($list);
        $this->assertEquals($expected, array_keys($actual));
    }

    public function testParseController()
    {
        $reflectionClass = new \ReflectionClass(\Althingi\Controller\AssemblyController::class);
        $inspector = new RouteInspector();

        $expectedKeys = [
            '/assembly[/:id]',
            '/assembly',
        ];

        $actual = $inspector->pController($reflectionClass, '/assembly[/:id]');
        $this->assertEquals($expectedKeys, array_keys($actual));
    }

    public function testExtractAction()
    {
        $reflectionClass = new \ReflectionClass(\Althingi\Controller\AssemblyController::class);
        $inspector = new RouteInspector();

        $actual = $inspector->extractAction($reflectionClass->getMethod('get'), 'GET');
        $this->assertTrue(true);
    }
}
