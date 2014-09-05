<?php
namespace NGS\Cache;

class MemcachedProvider implements CacheProviderInterface
{
    protected $memcached;
    protected $prefix;

    public function __construct(\Memcached $memcached, $prefix = '')
    {
        $this->memcached = $memcached;
        $this->prefix = $prefix;
    }

    public function get($key)
    {
        return $this->memcached->get($this->prefix . $key);
    }

    public function set($key, $value)
    {
        return $this->memcached->set($this->prefix . $key, $value);
    }
}
