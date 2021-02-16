<?php

namespace Althingi\Injector;

use Althingi\Service\PlenaryAgenda;

interface ServicePlenaryAgendaAwareInterface
{
    public function setPlenaryAgendaService(PlenaryAgenda $plenaryAgenda): self;
}
