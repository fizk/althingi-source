<?php

namespace Althingi\QueueActions;

use Althingi\Utils\MessageBrokerInterface;
use Althingi\Presenters\IndexablePresenter;

class Remove
{
    private MessageBrokerInterface $client;
    private bool $forced;

    public function __construct(MessageBrokerInterface $client, bool $isForced = false)
    {
        $this->client = $client;
        $this->forced = $isForced;
    }

    public function __invoke(IndexablePresenter $presenter, array $params = []): bool
    {
        if ($params['rows'] > 0 || $this->forced === true) {
            $this->client->produce('service', "{$presenter->getType()}.remove", [
                'id' => $presenter->getIdentifier(),
                'body' => $presenter->getData(),
                'index' => $presenter->getIndex(),
            ]);
            return true;
        }
        return false;
    }
}
