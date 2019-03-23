<?php

use Althingi\Service;
use Althingi\Store;
use Zend\ServiceManager\ServiceManager;
use Psr\Log\LoggerInterface;
use Althingi\ElasticSearchActions\ElasticSearchEventsListener;
use Althingi\QueueActions\QueueEventsListener;
use Althingi\Events\EventsListener;
use Elasticsearch\Client as ElasticsearchClient;
use Elasticsearch\ClientBuilder as ElasticsearchClientBuilder;
use Zend\Cache\Storage\StorageInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;

return [
    'factories' => [
        Service\Assembly::class => function (ServiceManager $sm) {
            return (new Service\Assembly())
                ->setDriver($sm->get(PDO::class));
        },
        Service\Congressman::class => function (ServiceManager $sm) {
            return (new Service\Congressman())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class));
        },
        Service\Committee::class => function (ServiceManager $sm) {
            return (new Service\Committee())
                ->setDriver($sm->get(PDO::class));
        },
        Service\CommitteeMeeting::class => function (ServiceManager $sm) {
            return (new Service\CommitteeMeeting())
                ->setDriver($sm->get(PDO::class));
        },
        Service\CommitteeMeetingAgenda::class => function (ServiceManager $sm) {
            return (new Service\CommitteeMeetingAgenda())
                ->setDriver($sm->get(PDO::class));
        },
        Service\Cabinet::class => function (ServiceManager $sm) {
            return (new Service\Cabinet())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class));
        },
        Service\Constituency::class => function (ServiceManager $sm) {
            return (new Service\Constituency())
                ->setDriver($sm->get(PDO::class));
        },
        Service\Category::class => function (ServiceManager $sm) {
            return (new Service\Category())
                ->setDriver($sm->get(PDO::class));
        },
        Service\CongressmanDocument::class => function (ServiceManager $sm) {
            return (new Service\CongressmanDocument())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class));
        },
        Service\Document::class => function (ServiceManager $sm) {
            return (new Service\Document())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class));
        },
        Service\Election::class => function (ServiceManager $sm) {
            return (new Service\Election())
                ->setDriver($sm->get(PDO::class));
        },
        Service\Issue::class => function (ServiceManager $sm) {
            return (new Service\Issue())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class));
        },
        Service\IssueCategory::class => function (ServiceManager $sm) {
            return (new Service\IssueCategory())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class));
        },
        Service\Party::class => function (ServiceManager $sm) {
            return (new Service\Party())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class));
        },
        Service\President::class => function (ServiceManager $sm) {
            return (new Service\President())
                ->setDriver($sm->get(PDO::class));
        },
        Service\Plenary::class => function (ServiceManager $sm) {
            return (new Service\Plenary())
                ->setDriver($sm->get(PDO::class));
        },
        Service\PlenaryAgenda::class => function (ServiceManager $sm) {
            return (new Service\PlenaryAgenda())
                ->setDriver($sm->get(PDO::class));
        },
        Service\Session::class => function (ServiceManager $sm) {
            return (new Service\Session())
                ->setDriver($sm->get(PDO::class));
        },
        Service\SearchSpeech::class => function (ServiceManager $sm) {
            return (new Service\SearchSpeech())
                ->setElasticSearchClient($sm->get(ElasticsearchClient::class));
        },
        Service\SearchIssue::class => function (ServiceManager $sm) {
            return (new Service\SearchIssue())
                ->setElasticSearchClient($sm->get(ElasticsearchClient::class));
        },
        Service\Speech::class => function (ServiceManager $sm) {
            return (new Service\Speech())
                ->setDriver($sm->get(PDO::class))
                ->setEventManager($sm->get(EventsListener::class));
        },
        Service\SuperCategory::class => function (ServiceManager $sm) {
            return (new Service\SuperCategory())
                ->setDriver($sm->get(PDO::class));
        },
        Service\Vote::class => function (ServiceManager $sm) {
            return (new Service\Vote())
                ->setDriver($sm->get(PDO::class));
        },
        Service\VoteItem::class => function (ServiceManager $sm) {
            return (new Service\VoteItem())
                ->setDriver($sm->get(PDO::class));
        },
        Service\Inflation::class => function (ServiceManager $sm) {
            return (new Service\Inflation())
                ->setDriver($sm->get(PDO::class));
        },
        Store\Assembly::class => function (ServiceManager $sm) {
            return (new Store\Assembly())
                ->setStore($sm->get(\MongoDB\Database::class));
        },
        Store\Issue::class => function (ServiceManager $sm) {
            return (new Store\Issue())
                ->setStore($sm->get(\MongoDB\Database::class));
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

        EventsListener::class => function (ServiceManager $sm) {
            $eventManager = (new \Zend\EventManager\EventManager());

//            $elasticSearchEventsListener = (new ElasticSearchEventsListener())
//                ->setElasticSearchClient($sm->get(ElasticsearchClient::class))
//                ->setLogger($sm->get(LoggerInterface::class));
//            $elasticSearchEventsListener->attach($eventManager);

            $queueEventsListener = (new QueueEventsListener())
                ->setLogger($sm->get(LoggerInterface::class))
                ->setQueue($sm->get(AMQPStreamConnection::class))
                ->setIsForced(strtolower(getenv('QUEUE_FORCED')) === 'true');
            $queueEventsListener->attach($eventManager);

            return $eventManager;
        },

        LoggerInterface::class => function (ServiceManager $sm) {
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
                    $options = (new Zend\Cache\Storage\Adapter\RedisOptions())
                        ->setTtl(60 * 60)
                        ->setServer([
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

        AMQPStreamConnection::class => function (ServiceManager $sm) {
            $queueAdapter = getenv('QUEUE');
            switch (strtolower($queueAdapter)) {
                case 'rabbitmq':
                    return AMQPStreamConnection::create_connection(
                        [
                            [
                                'host' => getenv('QUEUE_HOST') ?: 'localhost',
                                'port' => getenv('QUEUE_PORT') ?: 5672,
                                'user' => getenv('QUEUE_USER') ?: 'guest',
                                'password' => getenv('QUEUE_PASSWORD') ?: 'guest',
                                'vhost' => getenv('QUEUE_VHOST') ?: '/'
                            ]
                        ]
                    );
                    break;
                default:
                    return new \Althingi\Utils\RabbitMQBlackHoleClient();
                    break;
            }
        },

        \MongoDB\Database::class => function (ServiceManager $sm) {
            $host = getenv('STORAGE_HOST') ? : 'localhost';
            $port = getenv('STORAGE_PORT') ? : 27017;

            //mongodb://${user}:${pwd}@127.0.0.1:27017"

            return (new \MongoDB\Client("mongodb://{$host}:{$port}"))
                ->selectDatabase('althingi');
        }
    ],
];
