

使用方法
-------
composer安装
```
composer require kkong/rate-limiter
```

将源码下载放置到代码目录，然后在需要使用的代码里引用，或在入口文件里引用
```
require 'RateLimiter/vendor/Autoloader.php';

```

是什么？
-----------

一个简单的限流器demo


节流策略
-------------------

目前有3种节流方式,TimeWindow、SlidingTimeWindow和TokenBucket

``` php
$client = new Predis\Client([
    'host'   => '127.0.0.1',
    'port'   => 6379,
]);
//定义实现了StorageInterface的示例
$storage = new \Kkong\RateLimiter\Storage\Redis($client);

/**
 * 自然天，每天限制1000次
 */
$timeWindow = new new \Kkong\RateLimiter\Strategy\TimeWindowStrategy($storage);
$timeWindow->attempt('key', 1000, 86400000);

/**
 * 每10秒限制100次
 */
$slidingWindow = new \Kkong\RateLimiter\Strategy\SlidingWindowStrategy($storage);
$slidingWindow->attempt('key', 1000, 10);

/**
 * 总令牌容量1000，每秒补充10个令牌
 */
$tokenBucket = new \Kkong\RateLimiter\Strategy\TokenBucketStrategy($storage);
$tokenBucket->attempt('key', 1000, 10)
```

存储引擎
-------

暂时只提供1个储存引擎,如果需要继续扩展，需要继承StorageInterface并实现相应的方法

* Redis
