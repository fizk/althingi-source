<?php
namespace Althingi\Hydrator;

use DateTime;

trait HydrateDate
{
    private function hydrateDate($date)
    {
        if (is_null($date)) {
            return null;
        }

        if (is_string($date)) {
            return new DateTime($date);
        }

        if ($date instanceof DateTime) {
            return $date;
        }
    }
}
