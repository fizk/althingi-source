<?php
namespace Althingi;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\ServiceManager;
use Psr\Log\LoggerInterface;
use Althingi\Controller;
use Althingi\Controller\Aggregate;
use Althingi\Controller\Console;
use Althingi\Service;
use Althingi\Events\EventsListener;
use PhpAmqpLib\Connection\AMQPStreamConnection;

return [
    'router' => [
        'routes' => [
            'index' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'thingmal-current' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/thingmal/nuverandi',
                    'defaults' => [
                        'controller' => Controller\HighlightController::class,
                        'action' => 'get-active-issue'
                    ],
                ],
            ],
            'loggjafarthing-current' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/loggjafarthing/nuverandi',
                    'defaults' => [
                        'controller' => Controller\HighlightController::class,
                        'action' => 'get-current-assembly'
                    ],
                ],
            ],
            'loggjafarthing' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/loggjafarthing[/:id]',
                    'constraints' => [
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\AssemblyController::class,
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'thingflokkar' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/thingflokkar/:party_id/thingmal',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'controller' => Controller\IssueController::class,
                                'action' => 'fetch-party'
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'thingmadur' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/thingmenn/:congressman_id',
                            'constraints' => [
                                'congressman_id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'controller' => Controller\CongressmanController::class,
                                'action' => 'assembly-congressman'
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'onnur-skjol' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route'    => '/onnur-skjol',
                                    'defaults' => [
                                        'controller' => Controller\CongressmanController::class,
                                        'action' => 'assembly-congressman-other-docs'
                                    ],
                                ],
                            ],
                            'speechtime' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route'    => '/raedutimar',
                                    'defaults' => [
                                        'controller' => Controller\CongressmanController::class,
                                        'action' => 'assembly-speech-time'
                                    ],
                                ],
                            ],
                        ]
                    ],
                    'thingmenn' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/thingmenn',
                            'defaults' => [
                                'controller' => Controller\CongressmanController::class,
                                'action' => 'assembly'
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'raedutimar-allir' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route'    => '/raedutimar',
                                    'defaults' => [
                                        'controller' => Controller\CongressmanController::class,
                                        'action' => 'assembly-times'
                                    ],
                                ],
                            ],
                            'fyrirspurnir' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route'    => '/fyrirspurnir',
                                    'defaults' => [
                                        'controller' => Controller\CongressmanController::class,
                                        'action' => 'assembly-questions'
                                    ],
                                ],
                            ],
                            'thingsalyktanir' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route'    => '/thingsalyktanir',
                                    'defaults' => [
                                        'controller' => Controller\CongressmanController::class,
                                        'action' => 'assembly-propositions'
                                    ],
                                ],
                            ],
                            'lagafrumvarp' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route'    => '/lagafrumvorp',
                                    'defaults' => [
                                        'controller' => Controller\CongressmanController::class,
                                        'action' => 'assembly-bills'
                                    ],
                                ],
                            ],
//                            'raedutimar' => [
//                                'type' => 'Zend\Mvc\Router\Http\Segment',
//                                'options' => [
//                                    'route'    => '/:congressman_id/raedutimar',
//                                    'defaults' => [
//                                        'controller' => Controller\CongressmanController::class,
//                                        'action' => 'assembly-speech-time'
//                                    ],
//                                ],
//                            ],
                            'thingseta' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/:congressman_id/thingseta',
                                    'defaults' => [
                                        'controller' => Controller\CongressmanController::class,
                                        'action' => 'assembly-sessions'
                                    ],
                                ],
                            ],
                            'thingmal' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/:congressman_id/thingmal',
                                    'defaults' => [
                                        'controller' => Controller\CongressmanController::class,
                                        'action' => 'assembly-issues'
                                    ],
                                ],
                            ],
                            'thingmal-samantekt' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/:congressman_id/thingmal-samantekt',
                                    'defaults' => [
                                        'controller' => Controller\CongressmanController::class,
                                        'action' => 'assembly-issues-summary'
                                    ],
                                ],
                            ],
                            'atvaedagreidslur' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/:congressman_id/atvaedagreidslur',
                                    'defaults' => [
                                        'controller' => Controller\CongressmanController::class,
                                        'action' => 'assembly-voting'
                                    ],
                                ],
                            ],
                            'malaflokkar' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/:congressman_id/malaflokkar',
                                    'defaults' => [
                                        'controller' => Controller\CongressmanController::class,
                                        'action' => 'assembly-categories'
                                    ],
                                ],
                            ],
                            'atvaedagreidslur-malaflokkar' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/:congressman_id/atvaedagreidslur-malaflokkar',
                                    'defaults' => [
                                        'controller' => Controller\CongressmanController::class,
                                        'action' => 'assembly-vote-categories'
                                    ],
                                ],
                            ]
                        ]
                    ],
                    'verdbolga' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/verdbolga',
                            'defaults' => [
                                'controller' => Controller\InflationController::class,
                                'action' => 'fetch-assembly'
                            ],
                        ],
                    ],
                    'forsetar' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/forsetar[/:congressman_id]',
                            'defaults' => [
                                'controller' => Controller\PresidentAssemblyController::class,
                                'identifier' => 'congressman_id'
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'samantekt' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/samantekt',
                            'defaults' => [
                                'controller' => Controller\AssemblyController::class,
                                'action' => 'statistics'
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'thingmal' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route'    => '/thingmal',
                                    'defaults' => [
                                        'controller' => Controller\IssueController::class,
                                        'action' => 'statistics'
                                    ],
                                ],
                                'may_terminate' => true,
                            ]
                        ],
                    ],
                    'raduneyti' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/raduneyti',
                            'defaults' => [
                                'controller' => Controller\CabinetController::class,
                                'action' => 'assembly'
                            ],
                        ],
                    ],
                    'thingfundir' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/thingfundir[/:plenary_id]',
                            'defaults' => [
                                'controller' => Controller\PlenaryController::class,
                                'identifier' => 'plenary_id'
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'thingfundir-lidir' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/lidir[/:item_id]',
                                    'defaults' => [
                                        'controller' => Controller\PlenaryAgendaController::class,
                                        'identifier' => 'item_id'
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'thingmal' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/thingmal[/:category[/:issue_id]]',
                            'constraints' => [
                                'issue_id' => '[0-9]+',
                                'category' => '[abAB]',
                            ],
                            'defaults' => [
                                'controller' => Controller\IssueController::class,
                                'identifier' => 'issue_id',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'tengd-mal' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route'    => '/tengdmal',
                                    'defaults' => [
                                        'controller' => Controller\IssueLinkController::class,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'thingmal-raedutimar' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route'    => '/raedutimar',
                                    'defaults' => [
                                        'controller' => Controller\IssueController::class,
                                        'action' => 'speech-times'
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'thingraedur' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/raedur[/:speech_id]',
                                    'defaults' => [
                                        'controller' => Controller\SpeechController::class,
                                        'identifier' => 'speech_id'
                                    ],
                                ],
                            ],

                            'ferli' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route'    => '/ferli',
                                    'defaults' => [
                                        'controller' => Controller\IssueController::class,
                                        'action' => 'progress'
                                    ],
                                ],
                            ],
                            'efnisflokkar' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/efnisflokkar[/:category_id]',
                                    'defaults' => [
                                        'controller' => Controller\IssueCategoryController::class,
                                        'identifier' => 'category_id'
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'thingskjal' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/thingskjal[/:document_id]',
                                    'defaults' => [
                                        'controller' => Controller\DocumentController::class,
                                        'identifier' => 'document_id'
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'flutningsmenn' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route'    => '/flutningsmenn[/:congressman_id]',
                                            'defaults' => [
                                                'controller' => Controller\CongressmanDocumentController::class,
                                                'identifier' => 'congressman_id'
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'atkvaedagreidslur' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/atkvaedagreidslur[/:vote_id]',
                                    'defaults' => [
                                        'controller' => Controller\VoteController::class,
                                        'identifier' => 'vote_id'
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'atkvaedagreidsla' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route'    => '/atkvaedi[/:vote_item_id]',
                                            'defaults' => [
                                                'controller' => Controller\VoteItemController::class,
                                                'identifier' => 'vote_item_id'
                                            ],
                                        ],
                                    ]
                                ]
                            ],
                        ]
                    ],
                    'nefndir' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/nefndir[/:committee_id]',
                            'defaults' => [
                                'controller' => Controller\AssemblyCommitteeController::class,
                                'identifier' => 'committee_id'
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'nefndarfundir' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/nefndarfundir[/:committee_meeting_id]',
                                    'defaults' => [
                                        'controller' => Controller\CommitteeMeetingController::class,
                                        'identifier' => 'committee_meeting_id'
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'dagskralidir' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route'    => '/dagskralidir[/:committee_meeting_agenda_id]',
                                            'defaults' => [
                                                'controller' => Controller\CommitteeMeetingAgendaController::class,
                                                'identifier' => 'committee_meeting_agenda_id'
                                            ],
                                        ],
                                    ]
                                ]
                            ]
                        ],
                    ],
                    'efnisflokkar' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/efnisflokkar',
                            'defaults' => [
                                'controller' => Controller\CategoryController::class,
                                'action' => 'assembly-summary'
                            ],
                        ]
                    ],
                ],
            ],
            'nefndir' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/nefndir[/:committee_id]',
                    'defaults' => [
                        'controller' => Controller\CommitteeController::class,
                        'identifier' => 'committee_id'
                    ],
                ],
            ],
            'thingmenn' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/thingmenn[/:congressman_id]',
                    'defaults' => [
                        'controller' => Controller\CongressmanController::class,
                        'identifier' => 'congressman_id'
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'thingmal' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/thingmal[/:issue_id]',
                            'defaults' => [
                                'controller' => Controller\CongressmanIssueController::class,
                                'identifier' => 'issue_id'
                            ],
                        ]
                    ],
                    'thingseta' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/thingseta[/:session_id]',
                            'defaults' => [
                                'controller' => Controller\SessionController::class,
                                'identifier' => 'session_id'
                            ],
                        ],
                    ],
                    'nefndaseta' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/nefndaseta[/:committee_sitting_id]',
                            'defaults' => [
                                'controller' => Controller\CommitteeSittingController::class,
                                'identifier' => 'committee_sitting_id'
                            ],
                        ],
                    ],
                    'radherraseta' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/radherraseta[/:ministry_sitting_id]',
                            'constraints' => [
                                'ministry_sitting_id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => Controller\MinisterSittingController::class,
                                'identifier' => 'ministry_sitting_id'
                            ],
                        ],
                    ],
                ],
            ],
            'thingflokkar' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/thingflokkar[/:id]',
                    'defaults' => [
                        'controller' => Controller\PartyController::class,
                    ],
                ],
            ],
            'kjordaemi' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/kjordaemi[/:id]',
                    'defaults' => [
                        'controller' => Controller\ConstituencyController::class,
                    ],
                ],
            ],
            'forsetar' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/forsetar[/:id]',
                    'defaults' => [
                        'controller' => Controller\PresidentController::class,
                    ],
                ],
            ],
            'efnisflokkar' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/thingmal/efnisflokkar[/:super_category_id]',
                    'defaults' => [
                        'controller' => Controller\SuperCategoryController::class,
                        'identifier' => 'super_category_id'
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'undirflokkar' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/undirflokkar[/:category_id]',
                            'defaults' => [
                                'controller' => Controller\CategoryController::class,
                                'identifier' => 'category_id'
                            ],
                        ],
                    ]
                ]
            ],
            'verdbolga' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/verdbolga[/:id]',
                    'defaults' => [
                        'controller' => Controller\InflationController::class,
                        'identifier' => 'id'
                    ],
                ],
            ],
            'radherraembaetti' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/radherraembaetti[/:id]',
                    'defaults' => [
                        'controller' => Controller\MinistryController::class,
                        'identifier' => 'id'
                    ],
                ],
            ],
            'raduneyti' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/raduneyti[/:id]',
                    'defaults' => [
                        'controller' => Controller\CabinetController::class,
                        'identifier' => 'id'
                    ],
                ],
            ],
            'samantekt' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/samantekt',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'thingflokkar' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/thingflokkar[/:party_id]',
                            'defaults' => [
                                'controller' => Aggregate\PartyController::class,
                                'identifier' => 'party_id'
                            ],
                        ],
                    ],
                    'kjordaemi' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/kjordaemi[/:constituency_id]',
                            'defaults' => [
                                'controller' => Aggregate\ConstituencyController::class,
                                'identifier' => 'constituency_id'
                            ],
                        ],
                    ],
                    'atkvaedi' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/atkvaedi/:vote_id',
                            'defaults' => [
                                'controller' => Aggregate\VoteController::class,
                                'action' => 'get'
                            ],
                        ],
                    ],
                    'thingmal-flokkar-stada' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/loggjafarthing/:assembly_id/thingmal/flokkar-stada',
                            'defaults' => [
                                'controller' => Aggregate\IssueController::class,
                                'action' => 'count-type-status'
                            ],
                        ],
                    ],
                    'thingmal-government' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/loggjafarthing/:assembly_id/thingmal/stjornarfrumvorp',
                            'defaults' => [
                                'controller' => Aggregate\IssueController::class,
                                'action' => 'count-government'
                            ],
                        ],
                    ],
                    'thingmal' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/loggjafarthing/:assembly_id/thingmal[/:category/:issue_id]',
                            'constraints' => [
                                'category' => '[abAB]',
                                'issue_id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => Aggregate\IssueController::class, //todo
                                'identifier' => 'issue_id'
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'malaflokkar' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/malaflokkar',
                                    'defaults' => [
                                        'controller' => Aggregate\IssueCategoryController::class,
                                        'action' => 'fetch-categories'
                                    ],
                                ],
                            ],
                            'yfir-malaflokkar' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/yfir-malaflokkar',
                                    'defaults' => [
                                        'controller' => Aggregate\IssueCategoryController::class,
                                        'action' => 'fetch-super-categories'
                                    ],
                                ],
                            ],
                            'ferill' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/ferill',
                                    'defaults' => [
                                        'controller' => Aggregate\IssueController::class,
                                        'action' => 'progress'
                                    ],
                                ],
                            ],
                            'thingskjalahopar' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/thingskjalahopar',
                                    'defaults' => [
                                        'controller' => Aggregate\DocumentController::class,
                                        'action' => 'document-types'
                                    ],
                                ],
                            ],
                            'thingskjol' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/thingskjol[/:document_id]',
                                    'constraints' => [
                                        'document_id' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => Aggregate\DocumentController::class,
                                        'identifier' => 'document_id'
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'thingmenn' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route'    => '/thingmenn',
                                            'defaults' => [
                                                'controller' => Aggregate\DocumentController::class,
                                                'action' => 'proponents'
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'thingmenn' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/thingmenn/:congressman_id',
                            'defaults' => [
                                'controller' => Aggregate\CongressmanController::class,
                                'action' => 'get'
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'thingmenn-flokkar' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/thingflokkar',
                                    'defaults' => [
                                        'controller' => Aggregate\CongressmanController::class,
                                        'action' => 'party'
                                    ],
                                ],
                            ],
                            'thingmenn-kjordaemi' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/kjordaemi',
                                    'defaults' => [
                                        'controller' => Aggregate\CongressmanController::class,
                                        'action' => 'constituency'
                                    ],
                                ],
                            ],
                        ]
                    ],
                ]
            ],
            'leit' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/leit',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'index'
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'loggjafarthing' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/loggjafarthing[/:id]',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'controller' => Controller\SearchAssemblyController::class,
                                'action' => 'assembly'
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'thingmal' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/thingmal',
                                    'constraints' => [
                                        'issue_id' => '[0-9]*'
                                    ],
                                    'defaults' => [
                                        'controller' => Controller\SearchIssueController::class,
                                        'action' => 'issue'
                                    ],
                                ],
                            ],
                            'thingmal-raedur' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/thingmal/:issue_id/raedur',
                                    'constraints' => [
                                        'issue_id' => '[0-9]*'
                                    ],
                                    'defaults' => [
                                        'controller' => Controller\SearchSpeechController::class,
                                        'action' => 'issue'
                                    ],
                                ],
                            ],
                            'raedur' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/raedur',
                                    'defaults' => [
                                        'controller' => Controller\SearchSpeechController::class,
                                        'action' => 'assembly'
                                    ],
                                ],
                            ],
                        ]
                    ],
                ]
            ],
        ]
    ],

    'service_manager' => [
        'factories' => [
            'MessageStrategy' => \Rend\View\Strategy\MessageFactory::class,
        ],
    ],

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => function () {
                return (new Controller\IndexController());
            },
            Controller\AssemblyController::class => function (ServiceManager $container) {
                return (new Controller\AssemblyController())
                    ->setAssemblyService($container->get(Service\Assembly::class))
                    ->setCabinetService($container->get(Service\Cabinet::class))
                    ->setCategoryService($container->get(Service\Category::class))
                    ->setElectionService($container->get(Service\Election::class))
                    ->setIssueService($container->get(Service\Issue::class))
                    ->setPartyService($container->get(Service\Party::class))
                    ->setCongressmanService($container->get(Service\Congressman::class))
                    ->setSpeechService($container->get(Service\Speech::class))
                    ->setVoteService($container->get(Service\Vote::class))
                    ->setAssemblyStore($container->get(Store\Assembly::class))
                    ->setIssueStore($container->get(Store\Issue::class))
                    ->setVoteStore($container->get(Store\Vote::class))
                    ->setSpeechStore($container->get(Store\Speech::class))
                    ->setPartyStore($container->get(Store\Party::class))
                    ->setCategoryStore($container->get(Store\Category::class))
                    ->setCongressmanStore($container->get(Store\Congressman::class))
                    ;
            },
            Controller\MinistryController::class => function (ServiceManager $container) {
                return (new Controller\MinistryController())
                    ->setMinistryService($container->get(Service\Ministry::class))
                    ;
            },
            Controller\MinisterSittingController::class => function (ServiceManager $container) {
                return (new Controller\MinisterSittingController())
                    ->setMinisterSittingService($container->get(Service\MinisterSitting::class))
                    ;
            },
            Controller\CongressmanController::class => function (ServiceManager $container) {
                return (new Controller\CongressmanController())
                    ->setPartyService($container->get(Service\Party::class))
                    ->setAssemblyService($container->get(Service\Assembly::class))
                    ->setCongressmanService($container->get(Service\Congressman::class))
                    ->setVoteItemService($container->get(Service\VoteItem::class))
                    ->setCongressmanStore($container->get(Store\Congressman::class))
                    ->setSessionStore($container->get(Store\Session::class))
                    ->setVoteStore($container->get(Store\Vote::class))
                    ->setIssueStore($container->get(Store\Issue::class))
                    ;
            },
            Controller\SessionController::class => function (ServiceManager $container) {
                return (new Controller\SessionController())
                    ->setSessionService($container->get(Service\Session::class));
            },
            Controller\CongressmanSessionController::class => function (ServiceManager $container) {
                return (new Controller\CongressmanSessionController())
                    ->setSessionService($container->get(Service\Session::class));
            },
            Controller\PartyController::class => function (ServiceManager $container) {
                return (new Controller\PartyController())
                    ->setPartyService($container->get(Service\Party::class));
            },
            Controller\ConstituencyController::class => function (ServiceManager $container) {
                return (new Controller\ConstituencyController())
                    ->setConstituencyService($container->get(Service\Constituency::class));
            },
            Controller\PlenaryController::class => function (ServiceManager $container) {
                return (new Controller\PlenaryController())
                    ->setPlenaryService($container->get(Service\Plenary::class));
            },
            Controller\PlenaryAgendaController::class => function (ServiceManager $container) {
                return (new Controller\PlenaryAgendaController())
                    ->setPlenaryAgendaService($container->get(Service\PlenaryAgenda::class))
                    ->setPlenaryService($container->get(Service\Plenary::class))
                    ->setIssueService($container->get(Service\Issue::class))
                    ->setCongressmanService($container->get(Service\Congressman::class))
                    ->setPartyService($container->get(Service\Party::class));
            },
            Controller\IssueController::class => function (ServiceManager $container) {
                return (new Controller\IssueController())
                    ->setAssemblyService($container->get(Service\Assembly::class))
                    ->setIssueService($container->get(Service\Issue::class))
                    ->setCategoryService($container->get(Service\Category::class))
                    ->setIssueStore($container->get(Store\Issue::class))
                    ->setCategoryStore($container->get(Store\Category::class))
                    ;
            },
            Controller\IssueLinkController::class => function (ServiceManager $container) {
                return (new Controller\IssueLinkController())
                    ->setIssueLinkService($container->get(Service\IssueLink::class));
            },
            Controller\SpeechController::class => function (ServiceManager $container) {
                return (new Controller\SpeechController())
                    ->setSpeechService($container->get(Service\Speech::class))
                    ->setCongressmanService($container->get(Service\Congressman::class))
                    ->setPartyService($container->get(Service\Party::class))
                    ->setPlenaryService($container->get(Service\Plenary::class))
                    ->setConstituencyService($container->get(Service\Constituency::class))
                    ->setSpeechStore($container->get(Store\Speech::class));
            },
            Controller\VoteController::class => function (ServiceManager $container) {
                return (new Controller\VoteController())
                    ->setVoteService($container->get(Service\Vote::class));
            },
            Controller\VoteItemController::class => function (ServiceManager $container) {
                return (new Controller\VoteItemController())
                    ->setVoteService($container->get(Service\Vote::class))
                    ->setPartyService($container->get(Service\Party::class))
                    ->setCongressmanService($container->get(Service\Congressman::class))
                    ->setConstituencyService($container->get(Service\Constituency::class))
                    ->setVoteItemService($container->get(Service\VoteItem::class));
            },
            Controller\CongressmanIssueController::class => function (ServiceManager $container) {
                return (new Controller\CongressmanIssueController())
                    ->setIssueService($container->get(Service\Issue::class));
            },
            Controller\CongressmanDocumentController::class => function (ServiceManager $container) {
                return (new Controller\CongressmanDocumentController())
                    ->setCongressmanDocumentService($container->get(Service\CongressmanDocument::class));
            },
            Controller\DocumentController::class => function (ServiceManager $container) {
                return (new Controller\DocumentController())
                    ->setVoteItemService($container->get(Service\VoteItem::class))
                    ->setCongressmanService($container->get(Service\Congressman::class))
                    ->setPartyService($container->get(Service\Party::class))
                    ->setVoteService($container->get(Service\Vote::class))
                    ->setConstituencyService($container->get(Service\Constituency::class))
                    ->setDocumentService($container->get(Service\Document::class))
                    ->setDocumentStore($container->get(Store\Document::class));
            },
            Controller\CommitteeController::class => function (ServiceManager $container) {
                return (new Controller\CommitteeController())
                    ->setCommitteeService($container->get(Service\Committee::class));
            },
            Controller\CabinetController::class => function (ServiceManager $container) {
                return (new Controller\CabinetController())
                    ->setPartyService($container->get(Service\Party::class))
                    ->setCongressmanService($container->get(Service\Congressman::class))
                    ->setAssemblyService($container->get(Service\Assembly::class))
                    ->setCabinetService($container->get(Service\Cabinet::class));
            },
            Controller\PresidentController::class => function (ServiceManager $container) {
                return (new Controller\PresidentController())
                    ->setCongressmanService($container->get(Service\Congressman::class))
                    ->setPartyService($container->get(Service\Party::class))
                    ->setPresidentService($container->get(Service\President::class));
            },
            Controller\PresidentAssemblyController::class => function (ServiceManager $container) {
                return (new Controller\PresidentAssemblyController())
                    ->setPartyService($container->get(Service\Party::class))
                    ->setCongressmanService($container->get(Service\Congressman::class));
            },
            Controller\SuperCategoryController::class => function (ServiceManager $container) {
                return (new Controller\SuperCategoryController())
                    ->setSuperCategoryService($container->get(Service\SuperCategory::class));
            },
            Controller\CategoryController::class => function (ServiceManager $container) {
                return (new Controller\CategoryController())
                    ->setCategoryService($container->get(Service\Category::class));
            },
            Controller\IssueCategoryController::class => function (ServiceManager $container) {
                return (new Controller\IssueCategoryController())
                    ->setCategoryService($container->get(Service\Category::class))
                    ->setIssueCategoryService($container->get(Service\IssueCategory::class));
            },
            Controller\CommitteeMeetingController::class => function (ServiceManager $container) {
                return (new Controller\CommitteeMeetingController())
                    ->setCommitteeMeetingService($container->get(Service\CommitteeMeeting::class));
            },
            Controller\CommitteeMeetingAgendaController::class => function (ServiceManager $container) {
                return (new Controller\CommitteeMeetingAgendaController())
                    ->setCommitteeMeetingAgendaService($container->get(Service\CommitteeMeetingAgenda::class));
            },
            Controller\CommitteeSittingController::class => function (ServiceManager $container) {
                return (new Controller\CommitteeSittingController())
                    ->setCommitteeSitting($container->get(Service\CommitteeSitting::class))
                    ;
            },
            Controller\AssemblyCommitteeController::class => function (ServiceManager $container) {
                return (new Controller\AssemblyCommitteeController())
                    ->setCommitteeService($container->get(Service\Committee::class));
            },
            Controller\HighlightController::class => function (ServiceManager $container) {
                return (new Controller\HighlightController())
                    ->setCongressmanService($container->get(Service\Congressman::class))
                    ->setPartyService($container->get(Service\Party::class))
                    ->setCabinetService($container->get(Service\Cabinet::class))
                    ->setIssueService($container->get(Service\Issue::class))
                    ->setSpeechService($container->get(Service\Speech::class))
                    ->setAssemblyService($container->get(Service\Assembly::class));
            },
            Console\SearchIndexerController::class => function (ServiceManager $container) {
                return (new Console\SearchIndexerController())
                    ->setSpeechService($container->get(Service\Speech::class))
                    ->setIssueService($container->get(Service\Issue::class))
                    ->setElasticSearchClient($container->get(\Elasticsearch\Client::class))
                    ->setLogger($container->get(LoggerInterface::class));
            },
            Console\DocumentApiController::class => function (ServiceManager $container) {
                return (new Console\DocumentApiController());
            },
            Console\IssueStatusController::class => function (ServiceManager $container) {
                return (new Console\IssueStatusController())
                    ->setIssueService($container->get(Service\Issue::class));
            },
            Console\IndexerAssemblyController::class => function (ServiceManager $container) {
                return (new Console\IndexerAssemblyController())
                    ->setAssemblyService($container->get(Service\Assembly::class))
                    ->setLogger($container->get(LoggerInterface::class))
                    ->setQueue($container->get(AMQPStreamConnection::class))
                    ;
            },
            Console\IndexerIssueController::class => function (ServiceManager $container) {
                return (new Console\IndexerIssueController())
                    ->setSpeechService($container->get(Service\Speech::class))
                    ->setIssueService($container->get(Service\Issue::class))
                    ->setIssueCategoryService($container->get(Service\IssueCategory::class))
                    ->setDocumentService($container->get(Service\Document::class))
                    ->setCongressmanDocumentService($container->get(Service\CongressmanDocument::class))
                    ->setVoteService($container->get(Service\Vote::class))
                    ->setAssemblyService($container->get(Service\Assembly::class))
                    ->setVoteItemService($container->get(Service\VoteItem::class))
                    ->setSessionService($container->get(Service\Session::class))
                    ->setLogger($container->get(LoggerInterface::class))
                    ->setQueue($container->get(AMQPStreamConnection::class))
                    ;
            },
            Aggregate\CongressmanController::class => function (ServiceManager $container) {
                return (new Aggregate\CongressmanController())
                    ->setPartyService($container->get(Service\Party::class))
                    ->setCongressmanService($container->get(Service\Congressman::class))
                    ->setConstituencyService($container->get(Service\Constituency::class));
            },
            Aggregate\DocumentController::class => function (ServiceManager $container) {
                return (new Aggregate\DocumentController())
                    ->setDocumentService($container->get(Service\Document::class))
                    ->setCongressmanDocumentService($container->get(Service\CongressmanDocument::class));
            },
            Aggregate\IssueController::class => function (ServiceManager $container) {
                return (new Aggregate\IssueController())
                    ->setIssueService($container->get(Service\Issue::class));
            },
            Aggregate\IssueCategoryController::class => function (ServiceManager $container) {
                return (new Aggregate\IssueCategoryController())
                    ->setCategoryService($container->get(Service\Category::class))
                    ->setSuperCategoryService($container->get(Service\SuperCategory::class));
            },
            Aggregate\VoteController::class => function (ServiceManager $container) {
                return (new Aggregate\VoteController())
                    ->setVoteService($container->get(Service\Vote::class));
            },
            Aggregate\PartyController::class => function (ServiceManager $container) {
                return (new Aggregate\PartyController())
                    ->setPartyService($container->get(Service\Party::class));
            },
            Aggregate\ConstituencyController::class => function (ServiceManager $container) {
                return (new Aggregate\ConstituencyController())
                    ->setConstituencyService($container->get(Service\Constituency::class));
            },
            Controller\InflationController::class => function (ServiceManager $container) {
                return (new Controller\InflationController())
                    ->setInflationService($container->get(Service\Inflation::class))
                    ->setCabinetService($container->get(Service\Cabinet::class))
                    ->setAssemblyService($container->get(Service\Assembly::class));
            },
            Controller\SearchAssemblyController::class => function (ServiceManager $container) {
                return (new Controller\SearchAssemblyController())
                    ->setSearchAssemblyService($container->get(Service\SearchAssembly::class));
            },
            Controller\SearchIssueController::class => function (ServiceManager $container) {
                return (new Controller\SearchIssueController())
                    ->setSearchIssueService($container->get(Service\SearchIssue::class));
            },
            Controller\SearchSpeechController::class => function (ServiceManager $container) {
                return (new Controller\SearchSpeechController)
                    ->setSearchSpeechService($container->get(Service\SearchSpeech::class));
            }
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

    'console' => [
        'router' => [
            'routes' => [
                'speech' => [
                    'options' => [
                        'route' => 'index:speech',
                        'defaults' => [
                            'controller' => Console\SearchIndexerController::class,
                            'action' => 'speech'
                        ],
                    ],
                ],
                'assemblies' => [
                    'options' => [
                        'route' => 'index:assemblies',
                        'defaults' => [
                            'controller' => Console\IndexerAssemblyController::class,
                            'action' => 'assembly'
                        ],
                    ],
                ],
                'assembly' => [
                    'options' => [
                        'route' => 'index:assembly [--assembly=|-a]',
                        'defaults' => [
                            'controller' => Console\IndexerIssueController::class,
                            'action' => 'assembly'
                        ],
                    ],
                ],
                'session' => [
                    'options' => [
                        'route' => 'index:session [--assembly=|-a]',
                        'defaults' => [
                            'controller' => Console\IndexerIssueController::class,
                            'action' => 'session'
                        ],
                    ],
                ],
                'issue' => [
                    'options' => [
                        'route' => 'index:issue [--assembly=|-a] [--issue=|-i] [--category=|-c]',
                        'defaults' => [
                            'controller' => Console\IndexerIssueController::class,
                            'action' => 'issue'
                        ],
                    ],
                ],
                'status' => [
                    'options' => [
                        'route' => 'index:status [--assembly=|-a] [--type=|-t]',
                        'defaults' => [
                            'controller' => Console\IssueStatusController::class,
                            'action' => 'index'
                        ],
                    ],
                ],
                'status-list' => [
                    'options' => [
                        'route' => 'index:status-list',
                        'defaults' => [
                            'controller' => Console\IssueStatusController::class,
                            'action' => 'status-list'
                        ],
                    ],
                ],
                'document' => [
                    'options' => [
                        'route' => 'document:api',
                        'defaults' => [
                            'controller' => Console\DocumentApiController::class,
                            'action' => 'index'
                        ],
                    ],
                ],
            ],
        ],
    ],
];
