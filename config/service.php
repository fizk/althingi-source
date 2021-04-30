<?php

use Althingi\Service;
use Althingi\Controller;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Althingi\QueueActions\QueueEventsListener;
use Althingi\Events\EventsListener;
use Althingi\Router\Http\TreeRouteStack;
use Althingi\Router\RouteInterface;
use Althingi\Utils\BlackHoleMessageBroker;
use Althingi\Utils\KafkaMessageBroker;
use Althingi\Utils\MessageBrokerInterface;
use Althingi\Utils\AmqpMessageBroker;
use Laminas\Cache\Storage\StorageInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;

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


        Controller\IndexerAssemblyController::class => function (ContainerInterface $container) {
            return (new Controller\IndexerAssemblyController())
                ->setAssemblyService($container->get(Service\Assembly::class))
                ->setEventManager($container->get(EventsListener::class))
            ;
        },

        Service\Assembly::class => function (ContainerInterface $sm) {
            return (new Service\Assembly())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class));
        },
        Service\Congressman::class => function (ContainerInterface $sm) {
            return (new Service\Congressman())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class));
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
                ->setEventManager($sm->get(EventsListener::class));
        },
        Service\CommitteeDocument::class => function (ContainerInterface $sm) {
            return (new Service\CommitteeDocument())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class));
        },
        Service\Cabinet::class => function (ContainerInterface $sm) {
            return (new Service\Cabinet())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class));
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
                ->setEventManager($sm->get(EventsListener::class));
        },
        Service\Document::class => function (ContainerInterface $sm) {
            return (new Service\Document())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class));
        },
        Service\Election::class => function (ContainerInterface $sm) {
            return (new Service\Election())
                ->setDriver($sm->get(PDO::class));
        },
        Service\Issue::class => function (ContainerInterface $sm) {
            return (new Service\Issue())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class));
        },
        Service\IssueLink::class => function (ContainerInterface $sm) {
            return (new Service\IssueLink())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class));
        },
        Service\IssueCategory::class => function (ContainerInterface $sm) {
            return (new Service\IssueCategory())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class));
        },
        Service\Party::class => function (ContainerInterface $sm) {
            return (new Service\Party())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class));
        },
        Service\President::class => function (ContainerInterface $sm) {
            return (new Service\President())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class));
        },
        Service\Plenary::class => function (ContainerInterface $sm) {
            return (new Service\Plenary())
                ->setDriver($sm->get(PDO::class));
        },
        Service\Ministry::class => function (ContainerInterface $sm) {
            return (new Service\Ministry())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class));
        },
        Service\MinisterSitting::class => function (ContainerInterface $sm) {
            return (new Service\MinisterSitting())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class));
        },
        Service\PlenaryAgenda::class => function (ContainerInterface $sm) {
            return (new Service\PlenaryAgenda())
                ->setDriver($sm->get(PDO::class));
        },
        Service\Session::class => function (ContainerInterface $sm) {
            return (new Service\Session())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class));
        },
        Service\Speech::class => function (ContainerInterface $sm) {
            return (new Service\Speech())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class));
        },
        Service\SuperCategory::class => function (ContainerInterface $sm) {
            return (new Service\SuperCategory())
                ->setDriver($sm->get(PDO::class));
        },
        Service\Vote::class => function (ContainerInterface $sm) {
            return (new Service\Vote())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class))
                ;
        },
        Service\VoteItem::class => function (ContainerInterface $sm) {
            return (new Service\VoteItem())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class))
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
                    "sql_mode='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                ]
            );
        },

        EventsListener::class => function (ContainerInterface $sm) {
            $eventManager = (new \Laminas\EventManager\EventManager());

            $queueEventsListener = (new QueueEventsListener())
                ->setLogger($sm->get(LoggerInterface::class))
                ->setMessageBroker($sm->get(MessageBrokerInterface::class))
                ->setIsForced(strtolower(getenv('QUEUE_FORCED')) === 'true');
            $queueEventsListener->attach($eventManager);

            return $eventManager;
        },

        LoggerInterface::class => function (ContainerInterface $sm) {
            // return (new \Monolog\Logger('aggregator'))
            //     ->pushHandler((new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Logger::DEBUG))
            //     ->setFormatter(new \Monolog\Formatter\LineFormatter("[%datetime%] %level_name% %message%\n")));

            $handlers = [];
            $logger = (new \Monolog\Logger('althingi-api'))
            ->pushProcessor(new \Monolog\Processor\MemoryPeakUsageProcessor(true, false))
                ->pushProcessor(new \Monolog\Processor\MemoryUsageProcessor(true, false));

            if (getenv('LOG_PATH') === false || strtolower(getenv('LOG_PATH')) === 'none') {
                return $logger->setHandlers([new \Monolog\Handler\NullHandler()]);
            } else {
                $handlers[] = new \Monolog\Handler\StreamHandler(getenv('LOG_PATH'));
            }

            $formattedHandlers = array_map(function (\Monolog\Handler\HandlerInterface $handler) {
                $handler->setFormatter(new \Monolog\Formatter\LineFormatter());
                return $handler;
            }, $handlers);

            array_walk($formattedHandlers, function ($handler) use ($logger) {
                $logger->pushHandler($handler);
            });

            return $logger;
        },

        StorageInterface::class => function (ContainerInterface $sm) {
            switch (strtolower(getenv('CACHE_TYPE'))) {
                case 'file':
                    return (new Laminas\Cache\Storage\Adapter\Filesystem())
                        ->setOptions(
                            (new \Laminas\Cache\Storage\Adapter\FilesystemOptions())->setCacheDir('./data/cache')
                        );
                    break;
                case 'memory':
                    $options = (new Laminas\Cache\Storage\Adapter\RedisOptions())
                        ->setTtl(60)
                        ->setServer([
                            'host' => getenv('CACHE_HOST') ?: 'localhost',
                            'port' => getenv('CACHE_PORT') ?: 6379
                        ]);
                    return new Laminas\Cache\Storage\Adapter\Redis($options);
                    break;
                default:
                    return new \Laminas\Cache\Storage\Adapter\BlackHole();
                    break;
            }
        },

        MessageBrokerInterface::class => function (ContainerInterface $container) {
            switch(strtolower(getenv('BROKER'))) {
                case 'kafka':
                    return new KafkaMessageBroker("10.0.0.1:9092,10.0.0.2:9092");
                    break;
                case 'amqp':
                    return new AmqpMessageBroker(
                        $container->get(AMQPStreamConnection::class)
                    );
                    break;
                default:
                    return (new BlackHoleMessageBroker())
                        ->setLogger($container->get(LoggerInterface::class))
                        ;
                    break;
            }
        },

        AMQPStreamConnection::class => function (ContainerInterface $sm) {
            return AMQPStreamConnection::create_connection(
                [
                    [
                        'host' => getenv('BROKER_HOST') ?: 'localhost',
                        'port' => getenv('BROKER_PORT') ?: 5672,
                        'user' => getenv('BROKER_USER') ?: 'guest',
                        'password' => getenv('BROKER_PASSWORD') ?: 'guest',
                        'vhost' => getenv('BROKER_VHOST') ?: '/'
                    ]
                ]
            );
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
