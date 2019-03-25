<?php

namespace Althingi\Injector;

use Althingi\Service\PlenaryAgenda;

interface ServicePlenaryAgendaAwareInterface
{
    /**
     * @param PlenaryAgenda $plenaryAgenda
     */
    public function setPlenaryAgendaService(PlenaryAgenda $plenaryAgenda);
}
