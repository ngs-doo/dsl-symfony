<?php
namespace NGS\Cache;

interface CacheProviderInterface
{
    public function get($key);

    public function set($key, $value);
}
