<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 23/05/15
 * Time: 7:42 PM
 */

namespace Althingi\Controller;

use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Parameters;
use Althingi\Lib\Http\DomClient;
use \Althingi\Model\Dom\Assembly as AssemblyModel;

class ConsoleController extends AbstractActionController
{
    public function currentAssemblyAction()
    {
        $config = $this->getServiceLocator()->get('Config');

        //ASSEMBLY
        // - - - - - - - - - - - - - - - -
        $dom = (new DomClient())
            ->setClient($this->getClient())
            ->get('http://www.althingi.is/altext/xml/loggjafarthing/yfirstandandi');

        $thing = $dom->getElementsByTagName('þing')->item(0);

        if (($entry = (new AssemblyModel())->extract($thing))) {
            $apiRequest = (new Request())
                ->setMethod('post')
                ->setHeaders((new Headers())->addHeaders([
                    'X-HTTP-Method-Override' => 'PUT'
                ]))
                ->setUri(sprintf('%s/loggjafarthing/%s', $config['server']['host'], $entry['no']))
                ->setPost(new Parameters($entry));

            $apiResponse = $this->getClient()->send($apiRequest);
            echo $apiResponse->getBody() . PHP_EOL;
        }

        //CONGRESSMAN
        // - - - - - - - - - - - - - - - -

        $parliamentarianUrl = $dom->getElementsByTagName('þingmannalisti')->item(0)->nodeValue;

        $request = new Request();
        $request->setMethod('get')->setUri($parliamentarianUrl);
        $response = $this->getClient()->send($request);
        $content = $response->getBody();

        $dom = new \DOMDocument();
        $dom->loadXML($content);

        foreach ($dom->getElementsByTagName('þingmaður') as $parliamentarian) {
            echo $parliamentarian->getAttribute('id') . PHP_EOL;
        }
    }

    public function findAssemblyAction()
    {
        $config = $this->getServiceLocator()->get('Config');

        $request = new Request();
        $request->setMethod('get')->setUri('http://www.althingi.is/altext/xml/loggjafarthing/');
        $response = $this->getClient()->send($request);
        $content = $response->getBody();

        $dom = new \DOMDocument();
        $dom->loadXML($content);

        /** @var  $thing \DOMElement */
        foreach ($dom->getElementsByTagName('þing') as $thing) {
            if (($entry = (new AssemblyModel())->extract($thing))) {
                $apiRequest = (new Request())
                    ->setMethod('post')
                    ->setHeaders((new Headers())->addHeaders([
                        'X-HTTP-Method-Override' => 'PUT'
                    ]))
                    ->setUri($config['server']['host'] . '/loggjafarthing/' . $entry['no'])
                    ->setPost(new Parameters(['from' => $entry['from'], 'to'=> $entry['to']]));

                $apiResponse = $this->getClient()->send($apiRequest);
                echo $apiResponse->getBody() . PHP_EOL;
            }


        }


    }

    /**
     * @return \Zend\Http\Client
     */
    private function getClient()
    {
        return $this->getServiceLocator()->get('HttpClient');
    }
}
