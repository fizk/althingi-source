<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 17/05/15
 * Time: 9:04 PM
 */

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
use Althingi\Lib\DatabaseAwareInterface;
use Althingi\Lib\LoggerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Rend\View\Strategy\MessageFactory;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

return [
    'invokables' => [
        Assembly::class => Assembly::class,
        Congressman::class => Congressman::class,
        Session::class => Session::class,
        Party::class => Party::class,
        Constituency::class => Constituency::class,
        Plenary::class => Plenary::class,
        Issue::class => Issue::class,
        Speech::class => Speech::class,
        Vote::class => Vote::class,
        VoteItem::class => VoteItem::class,
        CongressmanDocument::class => CongressmanDocument::class,
        Document::class => Document::class,
        Committee::class => Committee::class,
        CommitteeMeeting::class => CommitteeMeeting::class,
        CommitteeMeetingAgenda::class => CommitteeMeetingAgenda::class,
        Cabinet::class => Cabinet::class,
        President::class => President::class,
        SuperCategory::class => SuperCategory::class,
        Category::class => Category::class,
        IssueCategory::class => IssueCategory::class,
        Election::class => Election::class,
    ],

    'factories' => [
        'MessageStrategy' => MessageFactory::class,

        PDO::class => function (ServiceManager $sm) {
            $dbHost = getenv('DB_HOST') ?: 'localhost';
            $dbPort = getenv('DB_PORT') ?: 3306;
            $dbName = getenv('DB_NAME') ?: 'althingi';
            return new PDO(
                "mysql:host={$dbHost};port={$dbPort};dbname={$dbName}",
                getenv('DB_USER'),
                getenv('DB_PASSWORD'),
                [
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                ]
            );
        },

        LoggerInterface::class => function (ServiceManager $sm) {
            $logger = new Logger('althingi');
            $logger->pushHandler(new StreamHandler('php://stdout'));
            return $logger;
        },
    ],

    'initializers' => [
        DatabaseAwareInterface::class => function ($instance, ServiceManager $sm) {
            if ($instance instanceof DatabaseAwareInterface) {
                $instance->setDriver($sm->get(PDO::class));
            }
        },
        LoggerAwareInterface::class => function ($instance, ServiceManager $sm) {
            if ($instance instanceof LoggerAwareInterface) {
                $instance->setLogger($sm->get(LoggerInterface::class));
            }
        },
    ],
];
