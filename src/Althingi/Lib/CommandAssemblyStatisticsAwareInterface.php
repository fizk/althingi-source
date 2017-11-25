<?php

namespace Althingi\Lib;

use Althingi\Command\AssemblyStatistics;

interface CommandAssemblyStatisticsAwareInterface
{
    /**
     * @param \Althingi\Command\AssemblyStatistics $assemblyStatistics
     */
    public function setAssemblyStatisticsCommand(AssemblyStatistics $assemblyStatistics);
}
