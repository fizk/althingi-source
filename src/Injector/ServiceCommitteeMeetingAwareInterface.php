<?php

namespace Althingi\Injector;

use Althingi\Service\CommitteeMeeting;

interface ServiceCommitteeMeetingAwareInterface
{
    public function setCommitteeMeetingService(CommitteeMeeting $committeeMeeting): static;
}
