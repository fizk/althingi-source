<?php

namespace Althingi\Injector;

use Zend\Cache\Storage\StorageInterface;

interface CacheAwareInterface
{
    /**
     * @param \Zend\Cache\Storage\StorageInterface $storage
     * @return mixed
     */
    public function setStorage(StorageInterface $storage);

    /**
     * @return \Zend\Cache\Storage\StorageInterface
     */
    public function getStorage();
}
