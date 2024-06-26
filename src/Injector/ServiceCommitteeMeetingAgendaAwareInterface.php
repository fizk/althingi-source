<?php

namespace Althingi\Injector;

use Althingi\Service\CommitteeMeetingAgenda;

interface ServiceCommitteeMeetingAgendaAwareInterface
{
    public function setCommitteeMeetingAgendaService(CommitteeMeetingAgenda $committeeMeetingAgenda): static;
}
