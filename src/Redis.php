<?php

namespace Simplon\Redis;

class Redis
{
    /**
     * @var \Redis
     */
    protected $instance;

    /**
     * @param $host
     * @param $dbIndex
     * @param int $port
     * @param null $password
     */
    public function __construct($host, $dbIndex, $port = 6379, $password = null)
    {
        // set object
        $this->instance = new \Redis();

        // connect
        $this->instance->connect($host, $port);

        // select db
        $this->selectDb($dbIndex);
    }

    /**
     * @return \Redis
     */
    private function getInstance()
    {
        return $this->instance;
    }

    /**
     * @param $dbIndex
     *
     * @return Redis
     */
    public function selectDb($dbIndex)
    {
        $this->getInstance()->select($dbIndex);

        return $this;
    }

    /**
     * @param string $key
     *
     * @return bool|string
     */
    public function keyGet($key)
    {
        return $this
            ->getInstance()
            ->get($key);
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return bool
     */
    public function keySet($key, $value)
    {
        return $this
            ->getInstance()
            ->set($key, $value);
    }

    /**
     * @param $key
     * @param $value
     * @param int $ttl
     *
     * @return bool
     */
    public function keySetEx($key, $value, $ttl = -1)
    {
        return $this
            ->getInstance()
            ->setex($key, $ttl, $value);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function keyExists($key)
    {
        return $this
            ->getInstance()
            ->exists($key);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function keyDel($key)
    {
        return $this
            ->getInstance()
            ->del($key) > 0;
    }

    /**
     * @param string $key
     *
     * @return int
     */
    public function keyTtl($key)
    {
        return $this
            ->getInstance()
            ->ttl($key);
    }

    /**
     * @param string $key
     * @param int $ttl
     *
     * @return bool
     */
    public function keyExpire($key, $ttl)
    {
        return $this
            ->getInstance()
            ->expire($key, $ttl);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function keyPersist($key)
    {
        return $this
            ->getInstance()
            ->persist($key);
    }
}