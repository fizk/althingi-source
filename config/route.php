<?php

use Althingi\Router\Http\Literal;
use Althingi\Router\Http\Segment;
use Althingi\Controller;

return [
    'routes' => [
        'index' => [
            'type' => Literal::class,
            'options' => [
                'route'    => '/',
                'defaults' => [
                    'controller' => Controller\IndexController::class,
                ],
            ],
            'may_terminate' => true,
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
                        'thingseta' => [
                            'type' => Literal::class,
                            'options' => [
                                'route'    => '/thingseta',
                                'defaults' => [
                                    'controller' => Controller\SessionController::class,
                                    'action' => 'assembly-congressman'
                                ],
                            ],
                        ],
                        'radherraseta' => [
                            'type' => Literal::class,
                            'options' => [
                                'route'    => '/radherraseta',
                                'defaults' => [
                                    'controller' => Controller\MinisterSessionController::class,
                                    'action' => 'assembly-sessions'
                                ],
                            ],
                        ],
                        'radherra' => [
                            'type' => Segment::class,
                            'options' => [
                                'route'    => '/radherra[/:ministry_id]',
                                'defaults' => [
                                    'controller' => Controller\MinisterController::class,
                                    'identifier' => 'ministry_id',
                                    'action' => null
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
                        'route'    => '/forsetar[/:president_id]',
                        'defaults' => [
                            'controller' => Controller\PresidentAssemblyController::class,
                            'identifier' => 'president_id'
                        ],
                    ],
                    'may_terminate' => true,
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
                        'route'    => '/thingfundir[/:parliamentary_session_id]',
                        'defaults' => [
                            'controller' => Controller\ParliamentarySessionController::class,
                            'identifier' => 'parliamentary_session_id'
                        ],
                    ],
                    'may_terminate' => true,
                    'child_routes' => [
                        'thingfundir-lidir' => [
                            'type' => Segment::class,
                            'options' => [
                                'route'    => '/lidir[/:item_id]',
                                'defaults' => [
                                    'controller' => Controller\ParliamentarySessionAgendaController::class,
                                    'identifier' => 'item_id'
                                ],
                            ],
                        ],
                    ],
                ],
                'thingmal' => [
                    'type' => Segment::class,
                    'options' => [
                        'route'    => '/thingmal[/:kind[/:issue_id]]',
                        'constraints' => [
                            'issue_id' => '[0-9]+',
                            'kind' => '[abAB]',
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
                        'frumskjal' => [
                            'type' => Literal::class,
                            'options' => [
                                'route'    => '/frumskjal',
                                'defaults' => [
                                    'controller' => Controller\DocumentController::class,
                                    'action' => 'primary-document',
                                ],
                            ],
                        ],
                        'thingskjal' => [
                            'type' => Segment::class,
                            'options' => [
                                'route'    => '/thingskjal[/:document_id]',
                                'defaults' => [
                                    'controller' => Controller\DocumentController::class,
                                    'identifier' => 'document_id',
                                    'constraints' => [
                                        'document_id' => '[0-9]*'
                                    ],
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
                                'nefndir' => [
                                    'type' => Segment::class,
                                    'options' => [
                                        'route'    => '/nefndir[/:document_committee_id]',
                                        'defaults' => [
                                            'controller' => Controller\CommitteeDocumentController::class,
                                            'identifier' => 'document_committee_id'
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
                'thingflokkur' => [
                    'type' => Segment::class,
                    'options' => [
                        'route'    => '/thingflokkur',
                        'defaults' => [
                            'controller' => Controller\PartyController::class,
                            'action' => 'get-by-congressman',
                        ],
                    ],
                ],
                'kjordaemi' => [
                    'type' => Segment::class,
                    'options' => [
                        'route'    => '/kjordaemi',
                        'defaults' => [
                            'controller' => Controller\ConstituencyController::class,
                            'action' => 'get-by-congressman',
                        ],
                    ],
                ],
                'nefndaseta' => [
                    'type' => Segment::class,
                    'options' => [
                        'route'    => '/nefndaseta[/:committee_session_id]',
                        'defaults' => [
                            'controller' => Controller\CommitteeSessionController::class,
                            'identifier' => 'committee_session_id'
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
                            'controller' => Controller\MinisterSessionController::class,
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
                    'identifier' => 'id'
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
        'console' => [
            'type' => Literal::class,
            'options' => [
                'route'    => '/console',
                'defaults' => [
                    'controller' => Controller\Cli\IndexController::class,
                ],
            ],
            'may_terminate' => true,
            'child_routes' => [
                'assembly' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':assembly',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerAssemblyController::class,
                        ],
                    ],
                ],
                'issue' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':issue',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerIssueController::class,
                        ],
                    ],
                ],
                'issue-category' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':issue-category',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerIssueCategoryController::class,
                        ],
                    ],
                ],
                'cabinet' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':cabinet',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerCabinetController::class,
                        ],
                    ],
                ],
                'category' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':category',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerCategoryController::class,
                        ],
                    ],
                ],
                'inflation' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':inflation',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerInflationController::class,
                        ],
                    ],
                ],
                'cabinet' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':cabinet',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerCabinetController::class,
                        ],
                    ],
                ],
                'party' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':party',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerPartyController::class,
                        ],
                    ],
                ],
                'constituency' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':constituency',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerConstituencyController::class,
                        ],
                    ],
                ],
                'ministry' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':ministry',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerMinistryController::class,
                        ],
                    ],
                ],
                'minister-sitting' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':minister-sitting',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerMinisterSessionController::class,
                        ],
                    ],
                ],
                'congressman' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':congressman',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerCongressmanController::class,
                        ],
                    ],
                ],
                'committee' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':committee',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerCommitteeController::class,
                        ],
                    ],
                ],
                'committee-sitting' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':committee-sitting',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerCommitteeSessionController::class,
                        ],
                    ],
                ],
                'congressman-document' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':congressman-document',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerCongressmanDocumentController::class,
                        ],
                    ],
                ],
                'parliamentary-session' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':parliamentary-session',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerParliamentarySessionController::class,
                        ],
                    ],
                ],
                'parliamentary-session-agenda' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':parliamentary-session-agenda',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerParliamentarySessionAgentaController::class,
                        ],
                    ],
                ],
                'president-sitting' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':president-sitting',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerPresidentController::class,
                        ],
                    ],
                ],
                'session' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':session',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerSessionController::class,
                        ],
                    ],
                ],
                'speech' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':speech',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerSpeechController::class,
                        ],
                    ],
                ],
                'document' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':document',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerDocumentController::class,
                        ],
                    ],
                ],
                'committee-document' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':committee-document',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerCommitteeDocumentController::class,
                        ],
                    ],
                ],
                'vote' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':vote',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerDocumentVoteController::class,
                        ],
                    ],
                ],
                'vote-item' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => ':vote-item',
                        'defaults' => [
                            'controller' => Controller\Cli\IndexerDocumentVoteItemController::class,
                        ],
                    ],
                ],
            ],
        ],
    ]
];
