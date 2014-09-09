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
     * @param $dbIndex
     *
     * @return Redis
     */
    public function selectDb($dbIndex)
    {
        $this->instance->select($dbIndex);

        return $this;
    }
} 