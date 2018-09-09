<?php

namespace Althingi\Lib;

use Althingi\Service\CommitteeMeeting;

interface ServiceCommitteeMeetingAwareInterface
{
    /**
     * @param CommitteeMeeting $committeeMeeting
     */
    public function setCommitteeMeetingService(CommitteeMeeting $committeeMeeting);
}
