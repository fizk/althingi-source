<?php

namespace Althingi\Events;

interface EventInterface
{
    public function getName(): string;

    public function setName(string $name): static;

    public function getParams(): array;

    public function setParams(array $params): static;
}
