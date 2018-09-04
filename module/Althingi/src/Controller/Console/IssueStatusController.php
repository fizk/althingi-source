<?php

namespace Althingi\Controller\Console;

use Althingi\Lib\ServiceIssueAwareInterface;
use Althingi\Service\Issue;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\Client;
use Zend\Dom\Query;
use Zend\ProgressBar\ProgressBar;
use Zend\ProgressBar\Adapter\Console as ConsoleAdapter;

class IssueStatusController extends AbstractActionController implements
    ServiceIssueAwareInterface
{
    /** @var  \Althingi\Service\Issue */
    private $issueService;

    public function indexAction()
    {
        $assemblyNumber = $this->params('assembly');
        $type = $this->params('type');
        $issues = $this->issueService->fetchByAssembly($assemblyNumber, 0, 5000, null, [$type]);

        $ids = array_map(function (\Althingi\Model\Issue $issue) {
            return $issue->getIssueId();
        }, $issues);

        $count = 0;
        $progressBar = new ProgressBar(new ConsoleAdapter(), 0, count($ids));
        $client = new Client();
        $result = [];

        foreach ($ids as $id) {
            $client->setUri(
                "http://www.althingi.is/thingstorf/thingmalalistar-eftir-thingum/ferill/?ltg={$assemblyNumber}&mnr={$id}"
            );
            $client->send();
            $response = $client->getResponse();
            $dom = new Query($response->getBody());
            $items = $dom->execute('.related .status li');
            $progressBar->update($count++);
            $result[$id] = implode(',', array_merge([$id], array_map(function (\DOMElement $item) {
                $done =  $item->hasAttribute('class') ? ' x ' : '';
                return "\"{$item->nodeValue} : {$done}\"";
            }, iterator_to_array($items))));
        }

        $progressBar->finish();

        print_r(implode("\n", $result));
    }

    public function statusListAction()
    {

        $type = ['frhnál. með rökst.', 'frhnál. með rökst.'];

        /** @var  $pdo \PDO */
        $pdo = $this->getServiceLocator()->get(\PDO::class);
        $statement = $pdo->prepare('select assembly_id, issue_id from Document where type = :type');
        $statement->execute(['type' => $type[0]]);
        $objects = $statement->fetchAll();

        $count = 0;
        $progressBar = new ProgressBar(new ConsoleAdapter(), 0, count($objects));
        $client = new Client();
        $result = [];

        foreach ($objects as $id) {
            $client->setUri(
                "http://www.althingi.is/thingstorf/thingmalalistar-eftir-thingum/ferill/?ltg={$id->assembly_id}&mnr={$id->issue_id}"
            );
            try {
                $client->send();
            } catch (\Zend\Http\Client\Adapter\Exception\TimeoutException $e) {
                sleep(10);
                $client->send();
            }

            $response = $client->getResponse();
            $dom = new Query($response->getBody());
            $items = $dom->execute('.related .status li');
            $progressBar->update($count++);
            $result[] = implode(',', array_map(function (\DOMElement $item) {
                return "\"{$item->nodeValue}\"";
            }, iterator_to_array($items)));
        }

        $progressBar->finish();

        $csv = implode("\n", $result);

        $match = [];
        preg_match_all("/[a-zA-Z0-9\\.: áðéíóúýþæöÁÉÍÓÚÝÞÆÖ]*\",\"{$type[1]}(.+?)/", $csv, $match);

        $hey = array_reduce($match[0], function ($a, $b) {
            if (!key_exists($b, $a)) {
                $a[$b] = $b;
            }
            return $a;
        }, []);

        echo implode(PHP_EOL, array_values($hey));
    }

    /**
     * @param \Althingi\Service\Issue $issue
     */
    public function setIssueService(Issue $issue)
    {
        $this->issueService = $issue;
    }
}
