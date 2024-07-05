<?php

namespace Althingi\Utils;

interface ExtractionInterface
{
    public function extract(object $object): array;
}
