<?php

namespace Althingi\Injector;

use Laminas\Cache\Storage\StorageInterface;

interface CacheAwareInterface
{
    /**
     * @param \Laminas\Cache\Storage\StorageInterface $storage
     * @return mixed
     */
    public function setStorage(StorageInterface $storage);

    /**
     * @return \Laminas\Cache\Storage\StorageInterface
     */
    public function getStorage();
}
