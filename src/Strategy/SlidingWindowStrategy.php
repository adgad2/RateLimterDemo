<?php

namespace Kkong\RateLimiter\Strategy;

use Kkong\RateLimiter\Storage\StorageInterface;

/**
 * 滑动时间窗口限流
 */
class SlidingWindowStrategy implements StrategyInterface
{
    /**
     * @var StrategyInterface
     */
    private $storage;


    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function attempt($key, $limit, $windowSize)
    {
        $currentTime = time();
        $windowStart = $currentTime - $windowSize + 1;

        $cacheKeyList = [];
        while ($windowStart <= $currentTime) {
            $cacheKeyList[] = $this->getStorageKey($key, $windowStart);
            $windowStart++;
        }

        $countArr = $this->storage->getMulti($cacheKeyList);
        $count = array_sum($countArr);

        if ($count < $limit) {
            $currentTimeKey = $this->getStorageKey($key, $currentTime);
            $lastCount = (int)$this->storage->get($currentTimeKey);
            $this->storage->set($currentTimeKey, $lastCount + 1, $windowSize * 2);
            return true;
        }
        return false;

    }

    protected function getStorageKey($key, $cacheKeyTime)
    {
        $date = date('YmdHis', $cacheKeyTime);
        return $date . ':' . $key . ':COUNT';
    }

}