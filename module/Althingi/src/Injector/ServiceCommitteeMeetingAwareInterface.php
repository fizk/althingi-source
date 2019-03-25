<?php

namespace Althingi\Injector;

use Althingi\Service\CommitteeMeeting;

interface ServiceCommitteeMeetingAwareInterface
{
    /**
     * @param CommitteeMeeting $committeeMeeting
     */
    public function setCommitteeMeetingService(CommitteeMeeting $committeeMeeting);
}
