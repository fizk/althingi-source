<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 9/04/2016
 * Time: 11:52 AM
 */

namespace Althingi\Lib;

use Althingi\Service\CommitteeMeeting;

interface ServiceCommitteeMeetingAwareInterface
{
    /**
     * @param CommitteeMeeting $committeeMeeting
     */
    public function setCommitteeMeetingService(CommitteeMeeting $committeeMeeting);
}
