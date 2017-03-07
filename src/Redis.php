<?php

namespace Simplon\Redis;

/**
 * Redis
 * @package Simplon\Redis
 * @author  Tino Ehrich (tino@bigpun.me)
 */
class Redis
{
    /**
     * @var string
     */
    private $host;
    /**
     * @var int
     */
    private $dbIndex;
    /**
     * @var int
     */
    private $port;
    /**
     * @var null|string
     */
    private $password;
    /**
     * @var \Redis
     */
    private $instance;

    /**
     * @param string $host
     * @param int $dbIndex
     * @param int $port
     * @param null|string $password
     */
    public function __construct(string $host, int $dbIndex, int $port = 6379, ?string $password = null)
    {
        $this->host = $host;
        $this->dbIndex = $dbIndex;
        $this->port = $port;
        $this->password = $password;
    }

    /**
     * @param $dbIndex
     *
     * @return Redis
     */
    public function selectDb(int $dbIndex): self
    {
        $this->getInstance()->select($dbIndex);

        return $this;
    }

    /**
     * @param string $key
     *
     * @return null|string
     */
    public function keyGet(string $key): ?string
    {
        if ($val = $this->getInstance()->get($key))
        {
            return $val;
        }

        return null;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return bool
     */
    public function keySet(string $key, string $value): bool
    {
        return $this->getInstance()->set($key, $value);
    }

    /**
     * @param string $key
     * @param string $value
     * @param int $ttl
     *
     * @return bool
     */
    public function keySetEx(string $key, string $value, int $ttl = -1): bool
    {
        return $this->getInstance()->setex($key, $ttl, $value);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function keyExists(string $key): bool
    {
        return $this->getInstance()->exists($key);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function keyDel(string $key): bool
    {
        return $this->getInstance()->del($key) > 0;
    }

    /**
     * @param string $key
     *
     * @return int
     */
    public function keyTtl(string $key): int
    {
        return $this->getInstance()->ttl($key);
    }

    /**
     * @param string $key
     * @param int $ttl
     *
     * @return bool
     */
    public function keyExpire(string $key, int $ttl): bool
    {
        return $this->getInstance()->expire($key, $ttl);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function keyPersist(string $key): bool
    {
        return $this->getInstance()->persist($key);
    }

    /**
     * @return \Redis
     */
    private function getInstance(): \Redis
    {
        if (!$this->instance)
        {
            // set object
            $this->instance = new \Redis();

            // connect
            $this->instance->connect($this->host, $this->port);

            // select db
            $this->selectDb($this->dbIndex);
        }

        return $this->instance;
    }
}