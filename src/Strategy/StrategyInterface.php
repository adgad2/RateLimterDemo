<?php

namespace Kkong\RateLimiter\Strategy;

interface StrategyInterface
{
    public function attempt($key, $limit, $milliseconds);

}