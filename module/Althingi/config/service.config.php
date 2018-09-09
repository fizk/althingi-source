<?php

use Althingi\Service\Assembly;
use Althingi\Service\Congressman;
use Althingi\Service\Session;
use Althingi\Service\Party;
use Althingi\Service\Constituency;
use Althingi\Service\Plenary;
use Althingi\Service\Issue;
use Althingi\Service\Speech;
use Althingi\Service\Vote;
use Althingi\Service\VoteItem;
use Althingi\Service\CongressmanDocument;
use Althingi\Service\Document;
use Althingi\Service\Committee;
use Althingi\Service\CommitteeMeeting;
use Althingi\Service\CommitteeMeetingAgenda;
use Althingi\Service\Cabinet;
use Althingi\Service\President;
use Althingi\Service\SuperCategory;
use Althingi\Service\Category;
use Althingi\Service\IssueCategory;
use Althingi\Service\Election;
use Althingi\Service\SearchSpeech;
use Althingi\Service\SearchIssue;
use Zend\ServiceManager\ServiceManager;
use Psr\Log\LoggerInterface;
use Althingi\ServiceEvents\ServiceEventsListener;
use Elasticsearch\Client as ElasticsearchClient;
use Elasticsearch\ClientBuilder as ElasticsearchClientBuilder;
use Zend\Cache\Storage\StorageInterface;

return [
    'factories' => [
        Assembly::class => function (ServiceManager $sm) {
            return (new Assembly())
                ->setDriver($sm->get(PDO::class));
        },
        Congressman::class => function (ServiceManager $sm) {
            return (new Congressman())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(ServiceEventsListener::class));
        },
        Committee::class => function (ServiceManager $sm) {
            return (new Committee())
                ->setDriver($sm->get(PDO::class));
        },
        CommitteeMeeting::class => function (ServiceManager $sm) {
            return (new CommitteeMeeting())
                ->setDriver($sm->get(PDO::class));
        },
        CommitteeMeetingAgenda::class => function (ServiceManager $sm) {
            return (new CommitteeMeetingAgenda())
                ->setDriver($sm->get(PDO::class));
        },
        Cabinet::class => function (ServiceManager $sm) {
            return (new Cabinet())
                ->setDriver($sm->get(PDO::class));
        },
        Constituency::class => function (ServiceManager $sm) {
            return (new Constituency())
                ->setDriver($sm->get(PDO::class));
        },
        Category::class => function (ServiceManager $sm) {
            return (new Category())
                ->setDriver($sm->get(PDO::class));
        },
        CongressmanDocument::class => function (ServiceManager $sm) {
            return (new CongressmanDocument())
                ->setDriver($sm->get(PDO::class));
        },
        Document::class => function (ServiceManager $sm) {
            return (new Document())
                ->setDriver($sm->get(PDO::class));
        },
        Election::class => function (ServiceManager $sm) {
            return (new Election())
                ->setDriver($sm->get(PDO::class));
        },
        Issue::class => function (ServiceManager $sm) {
            $sm->get(LoggerInterface::class);
            return (new Issue())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(ServiceEventsListener::class));
        },
        IssueCategory::class => function (ServiceManager $sm) {
            return (new IssueCategory())
                ->setDriver($sm->get(PDO::class));
        },
        Party::class => function (ServiceManager $sm) {
            return (new Party())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(ServiceEventsListener::class));
        },
        President::class => function (ServiceManager $sm) {
            return (new President())
                ->setDriver($sm->get(PDO::class));
        },
        Plenary::class => function (ServiceManager $sm) {
            return (new Plenary())
                ->setDriver($sm->get(PDO::class));
        },
        Session::class => function (ServiceManager $sm) {
            return (new Session())
                ->setDriver($sm->get(PDO::class));
        },
        SearchSpeech::class => function (ServiceManager $sm) {
            return (new SearchSpeech())
                ->setElasticSearchClient($sm->get(ElasticsearchClient::class));
        },
        SearchIssue::class => function (ServiceManager $sm) {
            return (new SearchIssue())
                ->setElasticSearchClient($sm->get(ElasticsearchClient::class));
        },
        Speech::class => function (ServiceManager $sm) {
            return (new Speech())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(ServiceEventsListener::class));
        },
        SuperCategory::class => function (ServiceManager $sm) {
            return (new SuperCategory())
                ->setDriver($sm->get(PDO::class));
        },
        Vote::class => function (ServiceManager $sm) {
            return (new Vote())
                ->setDriver($sm->get(PDO::class));
        },
        VoteItem::class => function (ServiceManager $sm) {
            return (new VoteItem())
                ->setDriver($sm->get(PDO::class));
        },

        PDO::class => function (ServiceManager $sm) {
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
                        "SET NAMES 'utf8', ".
                        "sql_mode='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                ]
            );
        },

        ElasticsearchClient::class => function (ServiceManager $sm) {
            $searchAdapter = getenv('SEARCH');
            switch (strtolower($searchAdapter)) {
                case 'elasticsearch':
                    $esHost = getenv('ES_HOST') ?: 'localhost';
                    $esProto = getenv('ES_PROTO') ?: 'http';
                    $esPort = getenv('ES_PORT') ?: 9200;
                    $esUser = getenv('ES_USER') ?: 'elastic';
                    $esPass = getenv('ES_PASSWORD') ?: 'changeme';

                    $hosts = [
                        "{$esProto}://{$esUser}:{$esPass}@{$esHost}:{$esPort}",
                    ];
                    $client = ElasticsearchClientBuilder::create()
                        ->setLogger($sm->get(LoggerInterface::class))
                        ->setHosts($hosts)
                        ->build();

                    return $client;
                    break;
                default:
                    return new \Althingi\Utils\ElasticBlackHoleClient();
                    break;
            }
        },

        ServiceEventsListener::class => function (ServiceManager $sm) {
            $eventManager = (new \Zend\EventManager\EventManager());
            $serviceEventsListener = (new ServiceEventsListener())
                ->setElasticSearchClient($sm->get(ElasticsearchClient::class))
                ->setLogger($sm->get(LoggerInterface::class));
            $serviceEventsListener->attach($eventManager);

            return $eventManager;
        },

        LoggerInterface::class => function (ServiceManager $sm) {
            $handlers = [];
            $logger = (new \Monolog\Logger('althingi-api'))
                ->pushProcessor(new \Monolog\Processor\MemoryPeakUsageProcessor())
                ->pushProcessor(new \Monolog\Processor\MemoryUsageProcessor());

            if (! empty(getenv('LOG_PATH')) && getenv('LOG_PATH')) {
                $handlers[] = new \Monolog\Handler\StreamHandler(getenv('LOG_PATH') ? : 'php://stdout');
            }

            $formattedHandlers = array_map(function (\Monolog\Handler\HandlerInterface $handler) {
                switch (strtolower(getenv('LOG_FORMAT'))) {
                    case 'logstash':
                        $handler->setFormatter(new \Monolog\Formatter\LogstashFormatter('althingi-api'));
                        break;
                    case 'json':
                        $handler->setFormatter(new \Monolog\Formatter\JsonFormatter());
                        break;
                    case 'line':
                        $handler->setFormatter(new \Monolog\Formatter\LineFormatter());
                        break;
                    case 'color':
                        $handler->setFormatter(new \Bramus\Monolog\Formatter\ColoredLineFormatter());
                        break;
                }
                return $handler;
            }, $handlers);

            array_walk($formattedHandlers, function ($handler) use ($logger) {
                $logger->pushHandler($handler);
            });

            return $logger;
        },

        StorageInterface::class => function (ServiceManager $sm) {
            switch (strtolower(getenv('CACHE_TYPE'))) {
                case 'file':
                    return (new Zend\Cache\Storage\Adapter\Filesystem())
                        ->setOptions(
                            (new \Zend\Cache\Storage\Adapter\FilesystemOptions())->setCacheDir('./data/cache')
                        );
                    break;
                case 'memory':
                    $options = (new Zend\Cache\Storage\Adapter\RedisOptions())->setServer([
                        'host' => getenv('CACHE_HOST') ?: 'localhost',
                        'port' => getenv('CACHE_PORT') ?: 6379
                    ]);
                    return new Zend\Cache\Storage\Adapter\Redis($options);
                    break;
                default:
                    return new \Zend\Cache\Storage\Adapter\BlackHole();
                    break;
            }
        },
    ],
];
