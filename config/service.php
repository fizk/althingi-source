<?php

use Althingi\Service;
use Althingi\Controller;
use Althingi\Events\{
    AddEvent,
    UpdateEvent,
    DeleteEvent
};
use Althingi\QueueActions\{
    Add,
    Update,
    Delete
};
use Althingi\Events\{
    RequestSuccessEvent,
    RequestFailureEvent,
    RequestUnsuccessEvent
};
use Althingi\Router\Http\TreeRouteStack;
use Althingi\Router\RouteInterface;
use Althingi\Utils\{
    MessageBrokerInterface,
    BlackHoleMessageBroker,
    KafkaMessageBroker
};
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use RdKafka\Producer as KafkaProducer;
use RdKafka\Conf as KafkaConfig;
use League\Event\{
    EventDispatcher,
    PrioritizedListenerRegistry
};
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

return [
    'factories' => [
        Controller\IndexController::class => function (ContainerInterface $container) {
            return (new Controller\IndexController())
                ->setOpenApi($container->get(\Althingi\Utils\OpenAPI::class));
        },
        Controller\AssemblyController::class => function (ContainerInterface $container) {
            return (new Controller\AssemblyController())
                ->setAssemblyService($container->get(Service\Assembly::class))
                ;
        },
        Controller\MinistryController::class => function (ContainerInterface $container) {
            return (new Controller\MinistryController())
                ->setMinistryService($container->get(Service\Ministry::class));
        },
        Controller\MinisterController::class => function (ContainerInterface $container) {
            return (new Controller\MinisterController())
                ->setMinistryService($container->get(Service\Ministry::class));
        },
        Controller\MinisterSittingController::class => function (ContainerInterface $container) {
            return (new Controller\MinisterSittingController())
                ->setMinisterSittingService($container->get(Service\MinisterSitting::class))
                ->setRouter($container->get(RouteInterface::class))
                ;
        },
        Controller\CongressmanController::class => function (ContainerInterface $container) {
            return (new Controller\CongressmanController())
                ->setCongressmanService($container->get(Service\Congressman::class))
                ;
        },
        Controller\SessionController::class => function (ContainerInterface $container) {
            return (new Controller\SessionController())
                ->setSessionService($container->get(Service\Session::class))
                ->setRouter($container->get(RouteInterface::class))
                ;
        },
        Controller\PartyController::class => function (ContainerInterface $container) {
            return (new Controller\PartyController())
                ->setPartyService($container->get(Service\Party::class));
        },
        Controller\ConstituencyController::class => function (ContainerInterface $container) {
            return (new Controller\ConstituencyController())
                ->setConstituencyService($container->get(Service\Constituency::class));
        },
        Controller\PlenaryController::class => function (ContainerInterface $container) {
            return (new Controller\PlenaryController())
                ->setPlenaryService($container->get(Service\Plenary::class));
        },
        Controller\PlenaryAgendaController::class => function (ContainerInterface $container) {
            return (new Controller\PlenaryAgendaController())
                ->setPlenaryAgendaService($container->get(Service\PlenaryAgenda::class))
                ->setIssueService($container->get(Service\Issue::class))
                ;
        },
        Controller\IssueController::class => function (ContainerInterface $container) {
            return (new Controller\IssueController())
                ->setIssueService($container->get(Service\Issue::class))
                ;
        },
        Controller\IssueLinkController::class => function (ContainerInterface $container) {
            return (new Controller\IssueLinkController())
                ->setIssueLinkService($container->get(Service\IssueLink::class));
        },
        Controller\SpeechController::class => function (ContainerInterface $container) {
            return (new Controller\SpeechController())
                ->setSpeechService($container->get(Service\Speech::class))
                ->setCongressmanService($container->get(Service\Congressman::class))
                ->setPartyService($container->get(Service\Party::class))
                ->setPlenaryService($container->get(Service\Plenary::class))
                ->setConstituencyService($container->get(Service\Constituency::class))
                ;
        },
        Controller\VoteController::class => function (ContainerInterface $container) {
            return (new Controller\VoteController())
                ->setVoteService($container->get(Service\Vote::class));
        },
        Controller\VoteItemController::class => function (ContainerInterface $container) {
            return (new Controller\VoteItemController())
                ->setVoteService($container->get(Service\Vote::class))
                ->setPartyService($container->get(Service\Party::class))
                ->setCongressmanService($container->get(Service\Congressman::class))
                ->setConstituencyService($container->get(Service\Constituency::class))
                ->setVoteItemService($container->get(Service\VoteItem::class))
                ->setRouter($container->get(RouteInterface::class))
                ;
        },
        Controller\CongressmanIssueController::class => function (ContainerInterface $container) {
            return (new Controller\CongressmanIssueController())
                ->setIssueService($container->get(Service\Issue::class));
        },
        Controller\CongressmanDocumentController::class => function (ContainerInterface $container) {
            return (new Controller\CongressmanDocumentController())
                ->setCongressmanDocumentService($container->get(Service\CongressmanDocument::class));
        },
        Controller\DocumentController::class => function (ContainerInterface $container) {
            return (new Controller\DocumentController())
                ->setDocumentService($container->get(Service\Document::class))
                ;
        },
        Controller\CommitteeController::class => function (ContainerInterface $container) {
            return (new Controller\CommitteeController())
                ->setCommitteeService($container->get(Service\Committee::class));
        },
        Controller\CommitteeDocumentController::class => function (ContainerInterface $container) {
            return (new Controller\CommitteeDocumentController)
                ->setCommitteeDocument($container->get(Service\CommitteeDocument::class))
                ->setRouter($container->get(RouteInterface::class))
                ;
        },
        Controller\CabinetController::class => function (ContainerInterface $container) {
            return (new Controller\CabinetController())
                ->setAssemblyService($container->get(Service\Assembly::class))
                ->setCabinetService($container->get(Service\Cabinet::class));
        },
        Controller\PresidentController::class => function (ContainerInterface $container) {
            return (new Controller\PresidentController())
                ->setPresidentService($container->get(Service\President::class))
                ->setRouter($container->get(RouteInterface::class))
                ;
        },
        Controller\PresidentAssemblyController::class => function (ContainerInterface $container) {
            return (new Controller\PresidentAssemblyController())
                ->setCongressmanService($container->get(Service\Congressman::class))
                ;
        },
        Controller\SuperCategoryController::class => function (ContainerInterface $container) {
            return (new Controller\SuperCategoryController())
                ->setSuperCategoryService($container->get(Service\SuperCategory::class));
        },
        Controller\CategoryController::class => function (ContainerInterface $container) {
            return (new Controller\CategoryController())
                ->setCategoryService($container->get(Service\Category::class));
        },
        Controller\IssueCategoryController::class => function (ContainerInterface $container) {
            return (new Controller\IssueCategoryController())
                ->setCategoryService($container->get(Service\Category::class))
                ->setIssueCategoryService($container->get(Service\IssueCategory::class));
        },
        Controller\CommitteeMeetingController::class => function (ContainerInterface $container) {
            return (new Controller\CommitteeMeetingController())
                ->setCommitteeMeetingService($container->get(Service\CommitteeMeeting::class));
        },
        Controller\CommitteeMeetingAgendaController::class => function (ContainerInterface $container) {
            return (new Controller\CommitteeMeetingAgendaController())
                ->setCommitteeMeetingAgendaService($container->get(Service\CommitteeMeetingAgenda::class));
        },
        Controller\CommitteeSittingController::class => function (ContainerInterface $container) {
            return (new Controller\CommitteeSittingController())
                ->setCommitteeSitting($container->get(Service\CommitteeSitting::class))
                ->setRouter($container->get(RouteInterface::class))
                ;
        },
        Controller\AssemblyCommitteeController::class => function (ContainerInterface $container) {
            return (new Controller\AssemblyCommitteeController())
                ->setCommitteeService($container->get(Service\Committee::class));
        },
        Controller\InflationController::class => function (ContainerInterface $container) {
            return (new Controller\InflationController())
                ->setInflationService($container->get(Service\Inflation::class))
                ->setCabinetService($container->get(Service\Cabinet::class))
                ->setAssemblyService($container->get(Service\Assembly::class));
        },


        Controller\Cli\IndexerAssemblyController::class => function (ContainerInterface $container) {
            return (new Controller\Cli\IndexerAssemblyController())
                ->setAssemblyService($container->get(Service\Assembly::class))
                ->setEventDispatcher($container->get(EventDispatcherInterface::class))
                ;
        },
        Controller\Cli\IndexerCabinetController::class => function (ContainerInterface $container) {
            return (new Controller\Cli\IndexerCabinetController())
                ->setCabinetService($container->get(Service\Cabinet::class))
                ->setEventDispatcher($container->get(EventDispatcherInterface::class))
                ;
        },
        Controller\Cli\IndexerCommitteeSittingController::class => function (ContainerInterface $container) {
            return (new Controller\Cli\IndexerCommitteeSittingController())
                ->setCommitteeSitting($container->get(Service\CommitteeSitting::class))
                ->setEventDispatcher($container->get(EventDispatcherInterface::class))
                ;
        },
        Controller\Cli\IndexerCongressmanDocumentController::class => function (ContainerInterface $container) {
            return (new Controller\Cli\IndexerCongressmanDocumentController())
                ->setCongressmanDocumentService($container->get(Service\CongressmanDocument::class))
                ->setEventDispatcher($container->get(EventDispatcherInterface::class))
                ;
        },
        Controller\Cli\IndexerSessionController::class => function (ContainerInterface $container) {
            return (new Controller\Cli\IndexerSessionController())
                ->setSessionService($container->get(Service\Session::class))
                ->setEventDispatcher($container->get(EventDispatcherInterface::class))
                ;
        },
        Controller\Cli\IndexerCongressmanController::class => function (ContainerInterface $container) {
            return (new Controller\Cli\IndexerCongressmanController())
                ->setCongressmanService($container->get(Service\Congressman::class))
                ->setEventDispatcher($container->get(EventDispatcherInterface::class))
                ;
        },
        Controller\Cli\IndexerPartyController::class => function (ContainerInterface $container) {
            return (new Controller\Cli\IndexerPartyController())
                ->setPartyService($container->get(Service\Party::class))
                ->setEventDispatcher($container->get(EventDispatcherInterface::class))
                ;
        },
        Controller\Cli\IndexerConstituencyController::class => function (ContainerInterface $container) {
            return (new Controller\Cli\IndexerConstituencyController())
                ->setConstituencyService($container->get(Service\Constituency::class))
                ->setEventDispatcher($container->get(EventDispatcherInterface::class))
                ;
        },
        Controller\Cli\IndexerCommitteeController::class => function (ContainerInterface $container) {
            return (new Controller\Cli\IndexerCommitteeController())
                ->setCommitteeService($container->get(Service\Committee::class))
                ->setEventDispatcher($container->get(EventDispatcherInterface::class))
                ;
        },
        Controller\Cli\IndexerPlenaryController::class => function (ContainerInterface $container) {
            return (new Controller\Cli\IndexerPlenaryController())
                ->setPlenaryService($container->get(Service\Plenary::class))
                ->setEventDispatcher($container->get(EventDispatcherInterface::class))
                ;
        },
        Controller\Cli\IndexerPlenaryAgentaController::class => function (ContainerInterface $container) {
            return (new Controller\Cli\IndexerPlenaryAgentaController())
                ->setPlenaryAgendaService($container->get(Service\PlenaryAgenda::class))
                ->setEventDispatcher($container->get(EventDispatcherInterface::class))
                ;
        },
        Controller\Cli\IndexController::class => function (ContainerInterface $container) {
            return (new Controller\Cli\IndexController())
                ;
        },

        Service\Assembly::class => function (ContainerInterface $sm) {
            return (new Service\Assembly())
                ->setDriver($sm->get(PDO::class))
                ->setEventDispatcher($sm->get(EventDispatcherInterface::class))
                ;
        },
        Service\Congressman::class => function (ContainerInterface $sm) {
            return (new Service\Congressman())
                ->setDriver($sm->get(PDO::class))
                ->setEventDispatcher($sm->get(EventDispatcherInterface::class))
                ;
        },
        Service\Committee::class => function (ContainerInterface $sm) {
            return (new Service\Committee())
                ->setDriver($sm->get(PDO::class));
        },
        Service\CommitteeMeeting::class => function (ContainerInterface $sm) {
            return (new Service\CommitteeMeeting())
                ->setDriver($sm->get(PDO::class));
        },
        Service\CommitteeMeetingAgenda::class => function (ContainerInterface $sm) {
            return (new Service\CommitteeMeetingAgenda())
                ->setDriver($sm->get(PDO::class));
        },
        Service\CommitteeSitting::class => function (ContainerInterface $sm) {
            return (new Service\CommitteeSitting())
                ->setDriver($sm->get(PDO::class))
                ->setEventDispatcher($sm->get(EventDispatcherInterface::class))
                ;
        },
        Service\CommitteeDocument::class => function (ContainerInterface $sm) {
            return (new Service\CommitteeDocument())
                ->setDriver($sm->get(PDO::class))
                ->setEventDispatcher($sm->get(EventDispatcherInterface::class))
                ;
        },
        Service\Cabinet::class => function (ContainerInterface $sm) {
            return (new Service\Cabinet())
                ->setDriver($sm->get(PDO::class))
                ->setEventDispatcher($sm->get(EventDispatcherInterface::class))
                ;
        },
        Service\Constituency::class => function (ContainerInterface $sm) {
            return (new Service\Constituency())
                ->setDriver($sm->get(PDO::class));
        },
        Service\Category::class => function (ContainerInterface $sm) {
            return (new Service\Category())
                ->setDriver($sm->get(PDO::class));
        },
        Service\CongressmanDocument::class => function (ContainerInterface $sm) {
            return (new Service\CongressmanDocument())
                ->setDriver($sm->get(PDO::class))
                ->setEventDispatcher($sm->get(EventDispatcherInterface::class))
                ;
        },
        Service\Document::class => function (ContainerInterface $sm) {
            return (new Service\Document())
                ->setDriver($sm->get(PDO::class))
                ->setEventDispatcher($sm->get(EventDispatcherInterface::class))
                ;
        },
        Service\Election::class => function (ContainerInterface $sm) {
            return (new Service\Election())
                ->setDriver($sm->get(PDO::class));
        },
        Service\Issue::class => function (ContainerInterface $sm) {
            return (new Service\Issue())
                ->setDriver($sm->get(PDO::class))
                ->setEventDispatcher($sm->get(EventDispatcherInterface::class))
                ;
        },
        Service\IssueLink::class => function (ContainerInterface $sm) {
            return (new Service\IssueLink())
                ->setDriver($sm->get(PDO::class))
                ->setEventDispatcher($sm->get(EventDispatcherInterface::class))
                ;
        },
        Service\IssueCategory::class => function (ContainerInterface $sm) {
            return (new Service\IssueCategory())
                ->setDriver($sm->get(PDO::class))
                ->setEventDispatcher($sm->get(EventDispatcherInterface::class))
                ;
        },
        Service\Party::class => function (ContainerInterface $sm) {
            return (new Service\Party())
                ->setDriver($sm->get(PDO::class))
                ->setEventDispatcher($sm->get(EventDispatcherInterface::class))
                ;
        },
        Service\President::class => function (ContainerInterface $sm) {
            return (new Service\President())
                ->setDriver($sm->get(PDO::class))
                ->setEventDispatcher($sm->get(EventDispatcherInterface::class))
                ;
        },
        Service\Plenary::class => function (ContainerInterface $sm) {
            return (new Service\Plenary())
                ->setDriver($sm->get(PDO::class));
        },
        Service\Ministry::class => function (ContainerInterface $sm) {
            return (new Service\Ministry())
                ->setDriver($sm->get(PDO::class))
                ->setEventDispatcher($sm->get(EventDispatcherInterface::class))
                ;
        },
        Service\MinisterSitting::class => function (ContainerInterface $sm) {
            return (new Service\MinisterSitting())
                ->setDriver($sm->get(PDO::class))
                ->setEventDispatcher($sm->get(EventDispatcherInterface::class))
                ;
        },
        Service\PlenaryAgenda::class => function (ContainerInterface $sm) {
            return (new Service\PlenaryAgenda())
                ->setDriver($sm->get(PDO::class));
        },
        Service\Session::class => function (ContainerInterface $sm) {
            return (new Service\Session())
                ->setDriver($sm->get(PDO::class))
                ->setEventDispatcher($sm->get(EventDispatcherInterface::class))
                ;
        },
        Service\Speech::class => function (ContainerInterface $sm) {
            return (new Service\Speech())
                ->setDriver($sm->get(PDO::class))
                ->setEventDispatcher($sm->get(EventDispatcherInterface::class))
                ;
        },
        Service\SuperCategory::class => function (ContainerInterface $sm) {
            return (new Service\SuperCategory())
                ->setDriver($sm->get(PDO::class));
        },
        Service\Vote::class => function (ContainerInterface $sm) {
            return (new Service\Vote())
                ->setDriver($sm->get(PDO::class))
                ->setEventDispatcher($sm->get(EventDispatcherInterface::class))
                ;
        },
        Service\VoteItem::class => function (ContainerInterface $sm) {
            return (new Service\VoteItem())
                ->setDriver($sm->get(PDO::class))
                ->setEventDispatcher($sm->get(EventDispatcherInterface::class))
                ;
        },
        Service\Inflation::class => function (ContainerInterface $sm) {
            return (new Service\Inflation())
                ->setDriver($sm->get(PDO::class));
        },

        PDO::class => function (ContainerInterface $sm) {
            $dbHost = getenv('DB_HOST') ?: 'localhost';
            $dbPort = getenv('DB_PORT') ?: 3306;
            $dbName = getenv('DB_NAME') ?: 'althingi';
            $dbUser = getenv('DB_USER') ?: 'root';
            $dbPass = getenv('DB_PASSWORD') ?: '';

            return new PDO(
                "mysql:host={$dbHost};port={$dbPort};dbname={$dbName}",
                $dbUser,
                $dbPass,
                [
                    PDO::MYSQL_ATTR_INIT_COMMAND =>
                    "SET NAMES 'utf8', " .
                    "sql_mode='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION,NO_AUTO_VALUE_ON_ZERO';",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                ]
            );
        },

        EventDispatcherInterface::class => function (ContainerInterface $container, $requestedName) {
            $logger = $container->get(Psr\Log\LoggerInterface::class);
            $provider = new PrioritizedListenerRegistry();

            $provider->subscribeTo(RequestSuccessEvent::class, function (RequestSuccessEvent $event) use ($logger) {
                $logger->debug((string) $event);
            });
            $provider->subscribeTo(RequestUnsuccessEvent::class, function (RequestUnsuccessEvent $event) use ($logger) {
                $logger->error((string) $event);
            });

            $provider->subscribeTo(RequestFailureEvent::class, function (RequestFailureEvent $event) use ($logger) {
                $logger->error((string) $event);
            });

            $provider->subscribeTo(AddEvent::class, function (AddEvent $event) use ($logger, $container) {
                $result = (new Add($container->get(MessageBrokerInterface::class), strtolower(getenv('QUEUE_FORCED')) === 'true'))(
                    $event->getPresenter(),
                    $event->getParams()
                );
                $logger->debug((string) $event);
            });

            $provider->subscribeTo(UpdateEvent::class, function (UpdateEvent $event) use ($logger, $container) {
                $result = (new Update($container->get(MessageBrokerInterface::class), strtolower(getenv('QUEUE_FORCED')) === 'true'))(
                    $event->getPresenter(),
                    $event->getParams()
                );
                $logger->debug((string) $event);
            });

            $provider->subscribeTo(DeleteEvent::class, function (DeleteEvent $event) use ($logger, $container) {
                $result = (new Delete($container->get(MessageBrokerInterface::class), strtolower(getenv('QUEUE_FORCED')) === 'true'))(
                    $event->getPresenter(),
                    $event->getParams()
                );
                $logger->debug((string) $event);
            });

            return new EventDispatcher($provider);
        },

        Psr\Log\LoggerInterface::class => function (ContainerInterface $container, $requestedName) {
            return (new Logger('source'))
                ->pushHandler((new StreamHandler('php://stdout', Logger::DEBUG))
                        ->setFormatter(new LineFormatter("[%datetime%] %level_name% %message%\n"))
                );
        },

        MessageBrokerInterface::class => function (ContainerInterface $container) {
            switch(strtolower(getenv('BROKER'))) {
                case 'kafka':
                    return new KafkaMessageBroker(
                        $container->get(KafkaProducer::class)
                    );
                    break;
                default:
                    return (new BlackHoleMessageBroker())
                        ->setLogger($container->get(LoggerInterface::class))
                        ;
                    break;
            }
        },

        KafkaProducer::class => function (ContainerInterface $sm) {
            $conf = new KafkaConfig();
            $conf->set('log_level', (string) LOG_DEBUG);
            $conf->set('metadata.broker.list', getenv('BROKER_HOST'));
            $conf->set('bootstrap.servers', getenv('BROKER_HOST'));
            $conf->set('debug', 'all');
            $rk = new KafkaProducer($conf);
            $rk->addBrokers(getenv('BROKER_HOST') ?: "127.0.01:9092");
            return $rk;
        },

        Althingi\Utils\OpenAPI::class => function (ContainerInterface $sm) {
            return (new Althingi\Utils\OpenAPI())
                ->setHost(getenv('DOCUMENT_SERVER') ?: 'loggjafarthing.einarvalur.co/api')
                ->setDefinition(getenv('DOCUMENT_DEFINITION') ?: '/api/openapi')
                ->setSchema(['http']);
        },

        Althingi\Injector\StallingAwareInterface::class => function () {
            return getenv('INDEXER_STALL_TIME') ?: 50000;
        },
        RouteInterface::class => function () {
            return TreeRouteStack::factory(require __DIR__ . '/route.php');
        },
    ],
];
