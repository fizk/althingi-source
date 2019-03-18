<?php
namespace Althingi;

use Althingi\Controller\{
    IndexController,
    AssemblyController,
    CongressmanController,
    SessionController,
    CongressmanSessionController,
    PartyController,
    ConstituencyController,
    PlenaryController,
    PlenaryAgendaController,
    IssueController,
    UndocumentedIssueController,
    SpeechController,
    VoteController,
    VoteItemController,
    CongressmanIssueController,
    CongressmanDocumentController,
    DocumentController,
    CommitteeController,
    CabinetController,
    PresidentController,
    PresidentAssemblyController,
    SuperCategoryController,
    CategoryController,
    IssueCategoryController,
    CommitteeMeetingController,
    CommitteeMeetingAgendaController,
    AssemblyCommitteeController,
    HighlightController,
    InflationController,
    Console\SearchIndexerController as ConsoleSearchIndexerController,
    Console\DocumentApiController as ConsoleDocumentApiController,
    Console\IssueStatusController as ConsoleIssueStatusController,
    Aggregate
};

use Althingi\Service\{
    Assembly,
    Cabinet,
    Category,
    Committee,
    CommitteeMeeting,
    CommitteeMeetingAgenda,
    Congressman,
    CongressmanDocument,
    Constituency,
    Document,
    Election,
    Issue,
    IssueCategory,
    Inflation,
    Party,
    Plenary,
    PlenaryAgenda,
    President,
    SearchIssue,
    SearchSpeech,
    Session,
    Speech,
    SuperCategory,
    Vote,
    VoteItem
};

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\ServiceManager;
use Psr\Log\LoggerInterface;

return [
    'router' => [
        'routes' => [
            'index' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'thingmal-current' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/thingmal/nuverandi',
                    'defaults' => [
                        'controller' => HighlightController::class,
                        'action' => 'get-active-issue'
                    ],
                ],
            ],
            'loggjafarthing-current' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/loggjafarthing/nuverandi',
                    'defaults' => [
                        'controller' => HighlightController::class,
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
                        'controller' => AssemblyController::class,
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'thingmenn' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/thingmenn',
                            'defaults' => [
                                'controller' => CongressmanController::class,
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
                                        'controller' => CongressmanController::class,
                                        'action' => 'assembly-times'
                                    ],
                                ],
                            ],
                            'fyrirspurnir' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route'    => '/fyrirspurnir',
                                    'defaults' => [
                                        'controller' => CongressmanController::class,
                                        'action' => 'assembly-questions'
                                    ],
                                ],
                            ],
                            'thingsalyktanir' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route'    => '/thingsalyktanir',
                                    'defaults' => [
                                        'controller' => CongressmanController::class,
                                        'action' => 'assembly-resolutions'
                                    ],
                                ],
                            ],
                            'lagafrumvarp' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route'    => '/lagafrumvorp',
                                    'defaults' => [
                                        'controller' => CongressmanController::class,
                                        'action' => 'assembly-bills'
                                    ],
                                ],
                            ],
//                            'raedutimar' => [
//                                'type' => 'Zend\Mvc\Router\Http\Segment',
//                                'options' => [
//                                    'route'    => '/:congressman_id/raedutimar',
//                                    'defaults' => [
//                                        'controller' => CongressmanController::class,
//                                        'action' => 'assembly-speech-time'
//                                    ],
//                                ],
//                            ],
                            'thingseta' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/:congressman_id/thingseta',
                                    'defaults' => [
                                        'controller' => CongressmanController::class,
                                        'action' => 'assembly-sessions'
                                    ],
                                ],
                            ],
                            'thingmal' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/:congressman_id/thingmal',
                                    'defaults' => [
                                        'controller' => CongressmanController::class,
                                        'action' => 'assembly-issues'
                                    ],
                                ],
                            ],
                            'thingmal-samantekt' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/:congressman_id/thingmal-samantekt',
                                    'defaults' => [
                                        'controller' => CongressmanController::class,
                                        'action' => 'assembly-issues-summary'
                                    ],
                                ],
                            ],
                            'atvaedagreidslur' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/:congressman_id/atvaedagreidslur',
                                    'defaults' => [
                                        'controller' => CongressmanController::class,
                                        'action' => 'assembly-voting'
                                    ],
                                ],
                            ],
                            'malaflokkar' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route'    => '/:congressman_id/malaflokkar',
                                    'defaults' => [
                                        'controller' => CongressmanController::class,
                                        'action' => 'assembly-categories'
                                    ],
                                ],
                            ],
                            'atvaedagreidslur-malaflokkar' => [
                                'type' => Segment::class,
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
                    'verdbolga' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/verdbolga',
                            'defaults' => [
                                'controller' => InflationController::class,
                                'action' => 'fetch-assembly'
                            ],
                        ],
                    ],
                    'forsetar' => [
                        'type' => Segment::class,
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
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/samantekt',
                            'defaults' => [
                                'controller' => AssemblyController::class,
                                'action' => 'statistics'
                            ],
                        ],
                    ],
                    'raduneyti' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/raduneyti',
                            'defaults' => [
                                'controller' => CabinetController::class,
                                'action' => 'assembly'
                            ],
                        ],
                    ],
                    'thingfundir' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/thingfundir[/:plenary_id]',
                            'defaults' => [
                                'controller' => PlenaryController::class,
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
                                        'controller' => PlenaryAgendaController::class,
                                        'identifier' => 'item_id'
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'bmal' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/bmal[/:issue_id]',
                            'constraints' => [
                                'issue_id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => UndocumentedIssueController::class,
                                'identifier' => 'issue_id',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'thingmal' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/thingmal[/:issue_id]',
                            'constraints' => [
                                'issue_id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => IssueController::class,
                                'identifier' => 'issue_id',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'thingmal-raedutimar' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route'    => '/raedutimar',
                                    'defaults' => [
                                        'controller' => IssueController::class,
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
                                        'controller' => SpeechController::class,
                                        'identifier' => 'speech_id'
                                    ],
                                ],
                            ],

                            'ferli' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route'    => '/ferli',
                                    'defaults' => [
                                        'controller' => IssueController::class,
                                        'action' => 'progress'
                                    ],
                                ],
                            ],
                            'efnisflokkar' => [
                                'type' => Segment::class,
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
                                'type' => Segment::class,
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
                                        'type' => Segment::class,
                                        'options' => [
                                            'route'    => '/flutningsmenn[/:congressman_id]',
                                            'defaults' => [
                                                'controller' => CongressmanDocumentController::class,
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
                                        'controller' => VoteController::class,
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
                        'type' => Segment::class,
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
                                'type' => Segment::class,
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
                                        'type' => Segment::class,
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
                    ],
                    'efnisflokkar' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/efnisflokkar',
                            'defaults' => [
                                'controller' => CategoryController::class,
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
                        'controller' => CommitteeController::class,
                        'identifier' => 'committee_id'
                    ],
                ],
            ],
            'thingmenn' => [
                'type' => Segment::class,
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
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/thingmal[/:issue_id]',
                            'defaults' => [
                                'controller' => CongressmanIssueController::class,
                                'identifier' => 'issue_id'
                            ],
                        ]
                    ],
                    'thingseta' => [
                        'type' => Segment::class,
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
                'type' => Segment::class,
                'options' => [
                    'route'    => '/thingflokkar[/:id]',
                    'defaults' => [
                        'controller' => PartyController::class,
                    ],
                ],
            ],
            'kjordaemi' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/kjordaemi[/:id]',
                    'defaults' => [
                        'controller' => ConstituencyController::class,
                    ],
                ],
            ],
            'forsetar' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/forsetar[/:id]',
                    'defaults' => [
                        'controller' => PresidentController::class,
                    ],
                ],
            ],
            'efnisflokkar' => [
                'type' => Segment::class,
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
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/undirflokkar[/:category_id]',
                            'defaults' => [
                                'controller' => CategoryController::class,
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
                        'controller' => InflationController::class,
                        'identifier' => 'id'
                    ],
                ],
            ],
            'raduneyti' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/raduneyti[/:id]',
                    'defaults' => [
                        'controller' => CabinetController::class,
                        'identifier' => 'id'
                    ],
                ],
            ],
            'samantekt' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/samantekt',
                    'defaults' => [
                        'controller' => IndexController::class,
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
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
                            'route'    => '/loggjafarthing/:assembly_id/thingmal[/:issue_id]',
                            'constraints' => [
                                'issue_id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => Aggregate\DocumentController::class, //todo
                                'identifier' => 'document_id'
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
            ]
        ]
    ],

    'service_manager' => [
        'factories' => [
            'MessageStrategy' => \Rend\View\Strategy\MessageFactory::class,
        ],
    ],

    'controllers' => [
        'factories' => [
            IndexController::class => function () {
                return (new IndexController());
            },
            AssemblyController::class => function (ServiceManager $container) {
                return (new AssemblyController())
                    ->setAssemblyService($container->get(Assembly::class))
                    ->setCabinetService($container->get(Cabinet::class))
                    ->setCategoryService($container->get(Category::class))
                    ->setElectionService($container->get(Election::class))
                    ->setIssueService($container->get(Issue::class))
                    ->setPartyService($container->get(Party::class))
                    ->setSpeechService($container->get(Speech::class))
                    ->setVoteService($container->get(Vote::class));
            },
            CongressmanController::class => function (ServiceManager $container) {
                return (new CongressmanController())
                    ->setVoteService($container->get(Vote::class))
                    ->setSpeechService($container->get(Speech::class))
                    ->setPartyService($container->get(Party::class))
                    ->setIssueService($container->get(Issue::class))
                    ->setAssemblyService($container->get(Assembly::class))
                    ->setCongressmanService($container->get(Congressman::class))
                    ->setIssueCategoryService($container->get(IssueCategory::class))
                    ->setVoteItemService($container->get(VoteItem::class))
                    ->setSessionService($container->get(Session::class));
            },
            SessionController::class => function (ServiceManager $container) {
                return (new SessionController())
                    ->setSessionService($container->get(Session::class));
            },
            CongressmanSessionController::class => function (ServiceManager $container) {
                return (new CongressmanSessionController())
                    ->setSessionService($container->get(Session::class));
            },
            PartyController::class => function (ServiceManager $container) {
                return (new PartyController())
                    ->setPartyService($container->get(Party::class));
            },
            ConstituencyController::class => function (ServiceManager $container) {
                return (new ConstituencyController())
                    ->setConstituencyService($container->get(Constituency::class));
            },
            PlenaryController::class => function (ServiceManager $container) {
                return (new PlenaryController())
                    ->setPlenaryService($container->get(Plenary::class));
            },
            PlenaryAgendaController::class => function (ServiceManager $container) {
                return (new PlenaryAgendaController())
                    ->setPlenaryAgendaService($container->get(PlenaryAgenda::class))
                    ->setPlenaryService($container->get(Plenary::class))
                    ->setIssueService($container->get(Issue::class))
                    ->setCongressmanService($container->get(Congressman::class))
                    ->setPartyService($container->get(Party::class));
            },
            IssueController::class => function (ServiceManager $container) {
                return (new IssueController())
                    ->setPartyService($container->get(Party::class))
                    ->setCongressmanService($container->get(Congressman::class))
                    ->setAssemblyService($container->get(Assembly::class))
                    ->setIssueService($container->get(Issue::class))
                    ->setSpeechService($container->get(Speech::class))
                    ->setVoteService($container->get(Vote::class))
                    ->setDocumentService($container->get(Document::class))
                    ->setSearchIssueService($container->get(SearchIssue::class));
            },
            UndocumentedIssueController::class => function (ServiceManager $container) {
                return (new UndocumentedIssueController())
                    ->setPartyService($container->get(Party::class))
                    ->setCongressmanService($container->get(Congressman::class))
                    ->setAssemblyService($container->get(Assembly::class))
                    ->setIssueService($container->get(Issue::class))
                    ->setSpeechService($container->get(Speech::class))
                    ->setVoteService($container->get(Vote::class))
                    ->setDocumentService($container->get(Document::class))
                    ->setSearchIssueService($container->get(SearchIssue::class));
            },
            SpeechController::class => function (ServiceManager $container) {
                return (new SpeechController())
                    ->setSpeechService($container->get(Speech::class))
                    ->setCongressmanService($container->get(Congressman::class))
                    ->setPartyService($container->get(Party::class))
                    ->setPlenaryService($container->get(Plenary::class))
                    ->setSearchSpeechService($container->get(SearchSpeech::class));
            },
            VoteController::class => function (ServiceManager $container) {
                return (new VoteController())
                    ->setVoteService($container->get(Vote::class));
            },
            VoteItemController::class => function (ServiceManager $container) {
                return (new VoteItemController())
                    ->setVoteService($container->get(Vote::class))
                    ->setPartyService($container->get(Party::class))
                    ->setCongressmanService($container->get(Congressman::class))
                    ->setVoteItemService($container->get(VoteItem::class));
            },
            CongressmanIssueController::class => function (ServiceManager $container) {
                return (new CongressmanIssueController())
                    ->setIssueService($container->get(Issue::class));
            },
            CongressmanDocumentController::class => function (ServiceManager $container) {
                return (new CongressmanDocumentController())
                    ->setCongressmanDocumentService($container->get(CongressmanDocument::class));
            },
            DocumentController::class => function (ServiceManager $container) {
                return (new DocumentController())
                    ->setVoteItemService($container->get(VoteItem::class))
                    ->setCongressmanService($container->get(Congressman::class))
                    ->setPartyService($container->get(Party::class))
                    ->setVoteService($container->get(Vote::class))
                    ->setDocumentService($container->get(Document::class));
            },
            CommitteeController::class => function (ServiceManager $container) {
                return (new CommitteeController())
                    ->setCommitteeService($container->get(Committee::class));
            },
            CabinetController::class => function (ServiceManager $container) {
                return (new CabinetController())
                    ->setPartyService($container->get(Party::class))
                    ->setCongressmanService($container->get(Congressman::class))
                    ->setAssemblyService($container->get(Assembly::class))
                    ->setCabinetService($container->get(Cabinet::class));
            },
            PresidentController::class => function (ServiceManager $container) {
                return (new PresidentController())
                    ->setCongressmanService($container->get(Congressman::class))
                    ->setPartyService($container->get(Party::class))
                    ->setPresidentService($container->get(President::class));
            },
            PresidentAssemblyController::class => function (ServiceManager $container) {
                return (new PresidentAssemblyController())
                    ->setPartyService($container->get(Party::class))
                    ->setCongressmanService($container->get(Congressman::class));
            },
            SuperCategoryController::class => function (ServiceManager $container) {
                return (new SuperCategoryController())
                    ->setSuperCategoryService($container->get(SuperCategory::class));
            },
            CategoryController::class => function (ServiceManager $container) {
                return (new CategoryController())
                    ->setCategoryService($container->get(Category::class));
            },
            IssueCategoryController::class => function (ServiceManager $container) {
                return (new IssueCategoryController())
                    ->setCategoryService($container->get(Category::class))
                    ->setIssueCategoryService($container->get(IssueCategory::class));
            },
            CommitteeMeetingController::class => function (ServiceManager $container) {
                return (new CommitteeMeetingController())
                    ->setCommitteeMeetingService($container->get(CommitteeMeeting::class));
            },
            CommitteeMeetingAgendaController::class => function (ServiceManager $container) {
                return (new CommitteeMeetingAgendaController())
                    ->setCommitteeMeetingAgendaService($container->get(CommitteeMeetingAgenda::class));
            },
            AssemblyCommitteeController::class => function (ServiceManager $container) {
                return (new AssemblyCommitteeController())
                    ->setCommitteeService($container->get(Committee::class));
            },
            HighlightController::class => function (ServiceManager $container) {
                return (new HighlightController())
                    ->setCongressmanService($container->get(Congressman::class))
                    ->setPartyService($container->get(Party::class))
                    ->setCabinetService($container->get(Cabinet::class))
                    ->setIssueService($container->get(Issue::class))
                    ->setSpeechService($container->get(Speech::class))
                    ->setAssemblyService($container->get(Assembly::class));
            },
            ConsoleSearchIndexerController::class => function (ServiceManager $container) {
                return (new ConsoleSearchIndexerController())
                    ->setSpeechService($container->get(Speech::class))
                    ->setIssueService($container->get(Issue::class))
                    ->setElasticSearchClient($container->get(\Elasticsearch\Client::class))
                    ->setLogger($container->get(LoggerInterface::class));
            },
            ConsoleDocumentApiController::class => function (ServiceManager $container) {
                return (new ConsoleDocumentApiController());
            },
            ConsoleIssueStatusController::class => function (ServiceManager $container) {
                return (new ConsoleIssueStatusController())
                    ->setIssueService($container->get(Issue::class));
            },
            Aggregate\CongressmanController::class => function (ServiceManager $container) {
                return (new Aggregate\CongressmanController())
                    ->setPartyService($container->get(Party::class))
                    ->setCongressmanService($container->get(Congressman::class))
                    ->setConstituencyService($container->get(Constituency::class));
            },
            Aggregate\DocumentController::class => function (ServiceManager $container) {
                return (new Aggregate\DocumentController())
                    ->setDocumentService($container->get(Document::class))
                    ->setCongressmanDocumentService($container->get(CongressmanDocument::class));
            },
            Aggregate\IssueController::class => function (ServiceManager $container) {
                return (new Aggregate\IssueController())
                    ->setIssueService($container->get(Issue::class));
            },
            Aggregate\IssueCategoryController::class => function (ServiceManager $container) {
                return (new Aggregate\IssueCategoryController())
                    ->setCategoryService($container->get(Category::class))
                    ->setSuperCategoryService($container->get(SuperCategory::class));
            },
            InflationController::class => function (ServiceManager $container) {
                return (new InflationController())
                    ->setInflationService($container->get(Inflation::class))
                    ->setCabinetService($container->get(Cabinet::class))
                    ->setAssemblyService($container->get(Assembly::class));
            },
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
                            'controller' => ConsoleSearchIndexerController::class,
                            'action' => 'speech'
                        ],
                    ],
                ],
                'issue' => [
                    'options' => [
                        'route' => 'index:issue',
                        'defaults' => [
                            'controller' => ConsoleSearchIndexerController::class,
                            'action' => 'issue'
                        ],
                    ],
                ],
                'status' => [
                    'options' => [
                        'route' => 'index:status [--assembly=|-a] [--type=|-t]',
                        'defaults' => [
                            'controller' => ConsoleIssueStatusController::class,
                            'action' => 'index'
                        ],
                    ],
                ],
                'status-list' => [
                    'options' => [
                        'route' => 'index:status-list',
                        'defaults' => [
                            'controller' => ConsoleIssueStatusController::class,
                            'action' => 'status-list'
                        ],
                    ],
                ],
                'document' => [
                    'options' => [
                        'route' => 'document:api',
                        'defaults' => [
                            'controller' => ConsoleDocumentApiController::class,
                            'action' => 'index'
                        ],
                    ],
                ],
            ],
        ],
    ],
];
