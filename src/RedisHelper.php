<?php

namespace Simplon\Redis;

/**
 * RedisHelper
 * @package Simplon\Redis
 * @author Tino Ehrich (tino@bigpun.me)
 */
class RedisHelper
{
    /**
     * @param $namespace
     * @param array $params
     *
     * @return string
     */
    public static function renderNamespace($namespace, array $params = [])
    {
        foreach ($params as $key => $val)
        {
            $namespace = str_replace('{' . $key . '}', $val, $namespace);
        }

        return (string)$namespace;
    }

    /**
     * @param int $ttl
     *
     * @return int
     */
    public static function ttlMinutesToSeconds($ttl)
    {
        return $ttl * 60;
    }

    /**
     * @param int $ttl
     *
     * @return int
     */
    public static function ttlHoursToSeconds($ttl)
    {
        return $ttl * 60 * 60;
    }

    /**
     * @param int $ttl
     *
     * @return int
     */
    public static function ttlDaysToSeconds($ttl)
    {
        return $ttl * 60 * 60 * 24;
    }
}