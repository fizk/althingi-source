<?php

namespace Althingi\Utils;

interface HydrationInterface
{
    public function hydrate(array $data, object $object): object;
}
