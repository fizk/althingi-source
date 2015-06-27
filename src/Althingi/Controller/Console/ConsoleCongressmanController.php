<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 23/05/15
 * Time: 7:42 PM
 */

namespace Althingi\Controller\Console;

use Althingi\Lib\LoggerAwareInterface;
use Althingi\Model\Dom\Session;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Parameters;
use Althingi\Lib\Http\DomClient;
use Althingi\Model\Dom\Congressman as CongressmanModel;
use Althingi\Model\Exception as ModelException;

class ConsoleCongressmanController extends AbstractActionController implements LoggerAwareInterface
{
    use ConsoleHelper;

    /**
     * Get Congressman.
     * If additional parameter is passed (--assembly=int) than only congressman
     * for given assembly will be fetched.
     *
     * @throws \Exception
     */
    public function findCongressmanAction()
    {
        $this->getLogger()->info("Find Congressman -- started");

        $assemblyNumber = $this->params('assembly');
        $dom = (new DomClient())
            ->setClient($this->getClient())
            ->get(($assemblyNumber)
                ? "http://www.althingi.is/altext/xml/thingmenn/?lthing={$assemblyNumber}"
                : "http://www.althingi.is/altext/xml/thingmenn/");

        foreach ($dom->getElementsByTagName('þingmaður') as $congressmanElement) {
            $congressmanId = $this->saveCongressman($congressmanElement);

            if (!$congressmanId) {
                continue;
            }

            //IMAGE
            //TODO do me
            $imageUrl = "http://www.althingi.is/myndir/thingmenn-cache/{$congressmanId}/{$congressmanId}-220.jpg";


            $congressmanDom = (new DomClient())
                ->setClient($this->getClient())
                ->get("http://www.althingi.is/altext/xml/thingmenn/thingmadur/thingseta/?nr={$congressmanId}");

            $this->saveCongressmanSession($congressmanId, $congressmanDom->getElementsByTagName('þingsetur')->item(0));
        }

        $this->getLogger()->info("Find Congressman -- ended");
    }

    /**
     * Save on Congressman to storage.
     * Return his/her ID or false if error.
     *
     * @param \DOMElement $congressmanElement
     * @return int|bool
     */
    private function saveCongressman(\DOMElement $congressmanElement)
    {
        $config = $this->getServiceLocator()->get('Config');
        try {
            $congressman = (new CongressmanModel())
                ->extract($congressmanElement);

            $apiRequest = (new Request())
                ->setMethod('post')
                ->setHeaders((new Headers())->addHeaders([
                    'X-HTTP-Method-Override' => 'PUT'
                ]))
                ->setUri(sprintf('%s/thingmenn/%s', $config['server']['host'], $congressman['id']))
                ->setPost(new Parameters($congressman));
            $apiResult = $this->getClient()->send($apiRequest);

            $this->getLogger()->info("Find Congressman -- [{$congressman['id']}]");
            $this->getLogger()->info(
                $apiResult->getStatusCode(),
                [json_decode($apiResult->getContent())]
            );

            return $congressman['id'];

        } catch (ModelException $e) {
            $this->getLogger()->error(sprintf('file: [%s] -> %s', $this->getClient()->getUri(), $e->getMessage()));
            return false;
        }
    }

    /**
     * Save Congressman's sessions.
     *
     * @param int $id  Congressman ID
     * @param \DOMElement $sessions
     */
    private function saveCongressmanSession($id, \DOMElement $sessions)
    {
        $config = $this->getServiceLocator()->get('Config');

        $this->getLogger()->info("Congressman[{$id}] Session -- start");
        foreach ($sessions->getElementsByTagName('þingseta') as $sessionElement) {
            try {
                $session = (new Session())->extract($sessionElement);
                $apiRequest = (new Request())
                    ->setMethod('post')
                    ->setUri(sprintf('%s/thingmenn/%s/thingseta', $config['server']['host'], $id))
                    ->setPost(new Parameters($session));
                $apiResult = $this->getClient()->send($apiRequest);
                $this->getLogger()->info(
                    $apiResult->getStatusCode(),
                    [json_decode($apiResult->getContent())]
                );

            } catch (ModelException $e) {
                $this->getLogger()->error(sprintf('file: [%s] -> %s', $this->getClient()->getUri(), $e->getMessage()));
            }
        }
        $this->getLogger()->info("Congressman[{$id}] Session -- end");
    }
}
