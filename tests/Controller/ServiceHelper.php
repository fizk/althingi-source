<?php
/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/24/16
 * Time: 3:30 PM
 */

namespace Althingi\Controller;

trait ServiceHelper
{
    private $services = [];

    private function buildServices(array $services = [])
    {
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);

        foreach ($services as $service) {
            $this->services[$service] = \Mockery::mock($service);
            $serviceManager->setService($service, $this->services[$service]);
        }
    }

    /**
     * @param $name
     * @return \Mockery\MockInterface
     */
    private function getMockService($name)
    {
        return $this->services[$name];
    }

    private function destroyServices()
    {
        foreach ($this->services as $service) {
            $service = null;
        }

        $this->services = [];
    }
}