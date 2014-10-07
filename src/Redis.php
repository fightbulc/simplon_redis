<?php

namespace Simplon\Redis;

class Redis
{
    /**
     * @var \Redis
     */
    protected $instance;

    /**
     * @var array
     */
    private $namespaceParams = [];

    /**
     * @var int
     */
    private $ttl = 0;

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
     * @return array
     */
    private function getNamespaceParams()
    {
        return (array)$this->namespaceParams;
    }

    /**
     * @return bool
     */
    private function hasNamespaceParams()
    {
        return empty($this->namespaceParams) === false;
    }

    /**
     * @param array $namespaceParams
     *
     * @return Redis
     */
    public function setNamespaceParams(array $namespaceParams)
    {
        $this->namespaceParams = $namespaceParams;

        return $this;
    }

    /**
     * @param $namespace
     *
     * @return string
     */
    private function getNamespaceName($namespace)
    {
        if ($this->hasNamespaceParams() === true)
        {
            foreach ($this->getNamespaceParams() as $key => $val)
            {
                $namespace = str_replace('{' . $key . '}', $val, $namespace);
            }

            // reset params
            $this->setNamespaceParams([]);
        }

        return (string)$namespace;
    }

    /**
     * @return int
     */
    private function getTtl()
    {
        return (int)$this->ttl;
    }

    /**
     * @param int $ttl
     *
     * @return Redis
     */
    public function setTtlSeconds($ttl)
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * @param int $ttl
     *
     * @return Redis
     */
    public function setTtlMinutes($ttl)
    {
        $this->ttl = $ttl * 60;

        return $this;
    }

    /**
     * @param int $ttl
     *
     * @return Redis
     */
    public function setTtlHours($ttl)
    {
        $this->ttl = $ttl * 60 * 60;

        return $this;
    }

    /**
     * @param int $ttl
     *
     * @return Redis
     */
    public function setTtlDays($ttl)
    {
        $this->ttl = $ttl * 60 * 60 * 24;

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
            ->get($this->getNamespaceName($key));
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
            ->set($this->getNamespaceName($key), $value, $this->getTtl());
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
            ->exists($this->getNamespaceName($key));
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
            ->del($this->getNamespaceName($key)) > 0;
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
            ->ttl($this->getNamespaceName($key));
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function keyExpire($key)
    {
        return $this
            ->getInstance()
            ->expire($this->getNamespaceName($key), $this->getTtl());
    }
}