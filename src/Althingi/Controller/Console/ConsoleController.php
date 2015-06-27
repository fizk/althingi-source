<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 23/05/15
 * Time: 7:42 PM
 */

namespace Althingi\Controller\Console;

use Althingi\Lib\Http\DomClient;

use Althingi\Model\Dom\Assembly;
use Althingi\Lib\LoggerAwareInterface;
use Althingi\Model\Dom\Constituency;
use Althingi\Model\Dom\Issue;
use Althingi\Model\Dom\Party;
use Althingi\Model\Dom\Plenary;
use Althingi\Model\Dom\Speech;
use Zend\Mvc\Controller\AbstractActionController;

class ConsoleController extends AbstractActionController implements LoggerAwareInterface
{
    use ConsoleHelper;

    public function currentAssemblyAction()
    {
        $this->singleLevelGet(
            'Assembly:current',
            'http://www.althingi.is/altext/xml/loggjafarthing/yfirstandandi',
            'loggjafarthing',
            'þing',
            new Assembly()
        );
    }

    public function findAssemblyAction()
    {
        $this->singleLevelGet(
            'Assembly',
            'http://www.althingi.is/altext/xml/loggjafarthing',
            'loggjafarthing',
            'þing',
            new Assembly()
        );
    }

    public function findPartyAction()
    {
        $this->singleLevelGet(
            'Party',
            'http://www.althingi.is/altext/xml/thingflokkar',
            'thingflokkar',
            'þingflokkur',
            new Party()
        );
    }

    public function findConstituencyAction()
    {
        $this->singleLevelGet(
            'Constituency',
            'http://www.althingi.is/altext/xml/kjordaemi',
            'kjordaemi',
            'kjördæmið',
            new Constituency()
        );
    }

    public function findPlenaryAction()
    {
        $assemblyNumber = $this->params('assembly');
        $dom = (new DomClient())
            ->setClient($this->getClient())
            ->get("http://huginn.althingi.is/altext/xml/thingfundir/?lthing={$assemblyNumber}");

        foreach ($dom->getElementsByTagName('þingfundur') as $element) {
            $element->setAttribute('þing', $assemblyNumber);
        }

        $this->getLogger()->info("Plenary -- start");
        $this->singleLevelPut(
            $dom,
            "loggjafarthing/{$assemblyNumber}/thingfundir",
            'þingfundur',
            new Plenary()
        );
        $this->getLogger()->info("Plenary -- end");
    }

    public function findIssueAction()
    {
        $assemblyNumber = $this->params('assembly');

        $this->getLogger()->info("Issue -- start");
        $dom = (new DomClient())
            ->setClient($this->getClient())
            ->get("http://www.althingi.is/altext/xml/thingmalalisti/?lthing={$assemblyNumber}");

        foreach ($dom->getElementsByTagName('mál') as $element) {
            $dom = (new DomClient())
                ->setClient($this->getClient())
                ->get($element->getElementsByTagName('xml')->item(0)->nodeValue);

            //ISSUE
            $issue = $dom->getElementsByTagName('mál')->item(0);

            //GET þingskjöl
            $congressmanId = $this->getFormanId($dom);
            if ($congressmanId) {
                $issue->setAttribute('þingmaður', $congressmanId);
            }

            $this->singleElementProcess($issue, "loggjafarthing/{$assemblyNumber}/thingmal", new Issue());

            //SPEECH
            $speeches = $dom->getElementsByTagName('ræður')->item(0);
            foreach ($speeches->getElementsByTagName('ræða') as $item) {
                $speechDocument = new \DOMDocument();
                $speechMetaElement = $speechDocument->importNode($item, true);
                $speechDocument->appendChild($speechMetaElement);

                if ($item->getElementsByTagName('xml')->item(0)) {
                    $speechDom = (new DomClient())
                        ->setClient($this->getClient())
                        ->get($item->getElementsByTagName('xml')->item(0)->nodeValue);
                    $speechEl = $speechDom->getElementsByTagName('ræðutexti')->item(0);
                    $speechBodyElement = $speechDocument->importNode($speechEl, true);
                    $speechDocument->documentElement->appendChild($speechBodyElement);

                    $issueEl = $speechDom->getElementsByTagName('mál')->item(0);
                    $issueElement = $speechDocument->importNode($issueEl, true);
                    $speechDocument->documentElement->appendChild($issueElement);
                }


                try {
                    //TODO is there a better way to find the issue_id?
                    $valueObject = (new Speech())->extract($speechDocument->documentElement);
                    $this->getLogger()->info('Storing one Speech');
                    $this->singleElementProcess(
                        $speechDocument->documentElement,
                        "loggjafarthing/{$assemblyNumber}/thingmal/{$valueObject['issue_id']}/raedur",
                        new Speech()
                    );
                    $this->getLogger()->info('end of Storing one Speech');

                } catch (\Exception $e) {
                    echo $e->getMessage();
                    echo $this->getClient()->getUri();
                }





                $speechDocument = null;

            }
        }

        $this->getLogger()->info("Issue -- end");
    }


    private function getFormanId($dom)
    {
        if (!$dom->getElementsByTagName('þingskjöl')->item(0)) {
            return null;
        }

        if (!$dom->getElementsByTagName('þingskjöl')->item(0)
            ->getElementsByTagName('þingskjal')->item(0)) {
            return null;
        }

        if (!$dom->getElementsByTagName('þingskjöl')->item(0)
            ->getElementsByTagName('þingskjal')->item(0)
            ->getElementsByTagName('xml')->item(0)) {
            return null;
        }
        $paperPath = $dom->getElementsByTagName('þingskjöl')->item(0)
            ->getElementsByTagName('þingskjal')->item(0)
            ->getElementsByTagName('xml')->item(0)->nodeValue;

        $paperDom = (new DomClient())
            ->setClient($this->getClient())
            ->get($paperPath);

        if (!$paperDom->getElementsByTagName('flutningsmaður')->item(0)) {
            return null;
        }

        if (!$paperDom->getElementsByTagName('flutningsmaður')->item(0)->hasAttribute('id')) {
            return null;
        }

        return (int) $paperDom->getElementsByTagName('flutningsmaður')->item(0)->getAttribute('id');

    }
}
