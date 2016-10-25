<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 17/05/15
 * Time: 9:04 PM
 */

return [
    'invokables' => [
        'Althingi\Service\Assembly' => 'Althingi\Service\Assembly',
        'Althingi\Service\Congressman' => 'Althingi\Service\Congressman',
        'Althingi\Service\Session' => 'Althingi\Service\Session',
        'Althingi\Service\Party' => 'Althingi\Service\Party',
        'Althingi\Service\Constituency' => 'Althingi\Service\Constituency',
        'Althingi\Service\Plenary' => 'Althingi\Service\Plenary',
        'Althingi\Service\Issue' => 'Althingi\Service\Issue',
        'Althingi\Service\Speech' => 'Althingi\Service\Speech',
        'Althingi\Service\Vote' => 'Althingi\Service\Vote',
        'Althingi\Service\VoteItem' => 'Althingi\Service\VoteItem',
        'Althingi\Service\Proponent' => 'Althingi\Service\Proponent',
        'Althingi\Service\Document' => 'Althingi\Service\Document',
        'Althingi\Service\Committee' => 'Althingi\Service\Committee',
        'Althingi\Service\Cabinet' => 'Althingi\Service\Cabinet',
        'Althingi\Service\President' => 'Althingi\Service\President',
        'Althingi\Service\SuperCategory' => 'Althingi\Service\SuperCategory',
        'Althingi\Service\Category' => 'Althingi\Service\Category',
        'Althingi\Service\IssueCategory' => 'Althingi\Service\IssueCategory',
    ],

    'factories' => [
        'MessageStrategy' => 'Rend\View\Strategy\MessageFactory',

        'HttpClient' => function ($sm) {
            return new \Zend\Http\Client();
        },

        'PDO' => function ($sm) {
            $config = $sm->get('config');
            return new PDO(
                $config['db']['dns'],
                $config['db']['user'],
                $config['db']['password'],
                [
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                ]
            );
        },
        'Psr\Log' => function ($sm) {
            $logger = new \Monolog\Logger('althingi');
            $logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout'));
            return $logger;
        },
    ],

    'initializers' => [
        'Althingi\Lib\DatabaseAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\DatabaseAwareInterface) {
                $instance->setDriver($sm->get('PDO'));
            }
        },
        'Althingi\Lib\LoggerAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \Althingi\Lib\LoggerAwareInterface) {
                $instance->setLogger($sm->get('Psr\Log'));
            }
        }
    ],
];
