<?php

namespace Kkong\RateLimiter\Strategy;

use Kkong\RateLimiter\Storage\StorageInterface;

/**
 * 令牌桶限流
 */
class TokenBucketStrategy implements StrategyInterface
{
    /**
     * @var StrategyInterface
     */
    private $storage;
    protected $lastRefillTimeKey = 'Kkong:RateLimiter:TokenBucket:lastRefillTime';

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function attempt($key, $capacity, $refillRate = null)
    {
        $storageKey = $this->getStorageKey($key);
        $count = $this->storage->get($storageKey);
        if (is_null($count)) {
            $this->setToken($key, $capacity - 1);
            return true;
        }

        if ($count < $capacity) {
            $currentTime = time();
            $lastRefillTime = (int)$this->storage->get($this->lastRefillTimeKey);
            $refillAmount = ($currentTime - $lastRefillTime) * $refillRate;
            $tokens = min($capacity, $count + $refillAmount);
            if ($tokens >= 1) {
                $this->setToken($key, $tokens - 1);
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public function setToken($key, $tokens)
    {
        $storageKey = $this->getStorageKey($key);
        $this->storage->set($storageKey, $tokens);
        $this->storage->set($this->lastRefillTimeKey, time());
    }


    protected function getStorageKey($key)
    {

        return 'TokenBucket' . $key . ':COUNT';
    }

}