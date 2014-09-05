<?php
namespace NGS\Cache;

class AggregateCache
{
    private static $cache;

    public static function setCacheProvider(CacheProviderInterface $cacheProvider)
    {
        self::$cache = $cacheProvider;
    }

    private static function getCachedName($name)
    {
        // strip '\' from class names
        return str_replace('\\', '', $name);
    }

    public static function save($name, array $items)
    {
        return self::$cache->set(self::getCachedName($name), $items);
    }

    public static function load($name)
    {
        return self::$cache->get(self::getCachedName($name));
    }
}
