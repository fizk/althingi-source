<?php

namespace Althingi\Injector;

use Althingi\Service\ParliamentarySessionAgenda;

interface ServiceParliamentarySessionAgendaAwareInterface
{
    public function setParliamentarySessionAgendaService(
        ParliamentarySessionAgenda $parliamentarySessionAgenda
    ): static;
}
