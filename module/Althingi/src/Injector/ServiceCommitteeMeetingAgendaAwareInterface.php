<?php

namespace Althingi\Injector;

use Althingi\Service\CommitteeMeetingAgenda;

interface ServiceCommitteeMeetingAgendaAwareInterface
{
    /**
     * @param CommitteeMeetingAgenda $committeeMeetingAgenda
     */
    public function setCommitteeMeetingAgendaService(CommitteeMeetingAgenda $committeeMeetingAgenda);
}
