<?php

namespace Kkong\RateLimiter\Strategy;

use Kkong\RateLimiter\Storage\StorageInterface;

/**
 * 固定时间窗口限流
 */
class TimeWindowStrategy implements StrategyInterface
{
    /**
     * @var StrategyInterface
     */
    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function attempt($key, $limit, $milliseconds)
    {
        $storageKey = $this->getStorageKey($key, $limit, $milliseconds);
        $count = (int)$this->storage->get($storageKey);
        if ($count < $limit) {
            $count++;
            $this->storage->set($storageKey, $count, floor($milliseconds / 1000 * 2));
            return true;
        }
        return false;
    }

    /**
     * @param $key
     * @param $limit
     * @param $milliseconds
     * @return string
     */
    protected function getStorageKey($key, $limit, $milliseconds)
    {
        $window = $milliseconds * (floor((microtime(1) * 1000) / $milliseconds));
        $date = date('YmdHis', $window / 1000);
        return $date . ':' . $key . ':' . $limit . ':' . $milliseconds . ':COUNT';
    }

}