<?php

namespace Kkong\RateLimiter\Storage;

class Redis implements StorageInterface
{

    protected $redisClient;

    public function __construct(\Predis\Client $redisClient)
    {
        $this->redisClient = $redisClient;
    }



    public function get($key)
    {
        return $this->redisClient->get($key);
    }


    public function set($key, $value, $expireTTL = null)
    {
        if ($expireTTL) {
            $this->redisClient->set($key, $value,'ex',$expireTTL);
        } else {
            $this->redisClient->set($key, $value);
        }
    }

    public function getMulti($array)
    {
        return $this->redisClient->pipeline(function ($pipe) use ($array) {
            foreach ($array as $key) {
                $pipe->get($key);
            }
        });
    }

}