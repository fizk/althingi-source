<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 9/04/2016
 * Time: 11:52 AM
 */

namespace Althingi\Lib;

use Althingi\Service\Vote;

interface ServiceVoteAwareInterface
{
    /**
     * @param Vote $vote
     */
    public function setVoteService(Vote $vote);
}
