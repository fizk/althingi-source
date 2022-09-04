<?php

namespace Althingi\Injector;

use Psr\Log\LoggerInterface;

interface LoggerAwareInterface
{
    /**
     * @param LoggerInterface $logger
     * @return mixed
     */
    public function setLogger(LoggerInterface $logger): self;

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface;
}
