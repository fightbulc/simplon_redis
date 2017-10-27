<?php

namespace Simplon\Redis;

class RedisHelper
{
    /**
     * @param string $namespace
     * @param array $params
     *
     * @return string
     */
    public static function renderNamespace(string $namespace, array $params = []): string
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
    public static function ttlMinutesToSeconds(int $ttl): int
    {
        return $ttl * 60;
    }

    /**
     * @param int $ttl
     *
     * @return int
     */
    public static function ttlHoursToSeconds(int $ttl): int
    {
        return $ttl * 60 * 60;
    }

    /**
     * @param int $ttl
     *
     * @return int
     */
    public static function ttlDaysToSeconds(int $ttl): int
    {
        return $ttl * 60 * 60 * 24;
    }
}