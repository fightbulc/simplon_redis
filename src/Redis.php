<?php

namespace Simplon\Redis;

class Redis
{
    /** @var \Redis */
    protected $_redisInstance;

    /** @var bool */
    protected $_enablePipeline = false;

    /** @var array */
    protected $_pipelineQueue = [];

    /** @var array */
    protected $_responseQueue = [];

    // ##########################################

    /**
     * @param $host
     * @param $databaseId
     * @param int $port
     * @param null $password
     *
     * @throws \Exception
     */
    public function __construct($host, $databaseId, $port = 6379, $password = null)
    {
        // redis connector
        $this->_redisInstance = new \Redis();
        $this->_redisInstance->connect($host, $port);

        // select db
        $this->dbSelect($databaseId);

        // auth
        if (!is_null($password))
        {
            if ($this->dbAuth($password) !== 'OK')
            {
                throw new \Exception('DB: authentication failed.', 401);
            }
        }
    }

    // ##########################################

    /**
     * @return \Redis
     */
    protected function _getRedisInstance()
    {
        return $this->_redisInstance;
    }

    // ##########################################

    /**
     * @param $cmdArgs
     *
     * @return mixed
     */
    protected function _ensureStringCommands($cmdArgs)
    {
        foreach ($cmdArgs as $index => $command)
        {
            $cmdArgs[$index] = strval($command);
        }

        return $cmdArgs;
    }

    // ##########################################

    /**
     * @param $cmdArgs
     *
     * @return bool
     */
    public function query($cmdArgs)
    {
        // no connection || no commands?
        if ($this->_getRedisInstance() === false || $cmdArgs === false)
        {
            return false;
        }

        $formattedArgs = [];

        foreach ($cmdArgs as $arg)
        {
            $formattedArgs[] = "'" . addslashes($arg) . "'";
        }

        // query redis
        $command = 'return redis.call(' . join(',', $formattedArgs) . ')';
        $response = $this->_getRedisInstance()->eval($command);

        if ($response !== false && !empty($response))
        {
            return $response;
        }

        return false;
    }

    // ##########################################

    /**
     * @param bool $use
     *
     * @return $this
     */
    public function pipelineEnable($use = true)
    {
        $this->_enablePipeline = $use === true ? true : false;

        return $this;
    }

    // ##########################################

    /**
     * @return bool|array
     */
    public function pipelineExecute()
    {
        // no connection?
        if ($this->_getRedisInstance() === false)
        {
            return false;
        }

        $requestResponsesMulti = [
            'errors'    => [],
            'responses' => [],
        ];

        foreach ($this->_pipelineGetQueue() as $cmdArgs)
        {
            $formattedArgs = [];

            foreach ($cmdArgs as $arg)
            {
                $formattedArgs[] = "'" . addslashes($arg) . "'";
            }

            // query redis
            $command = 'return redis.call(' . join(',', $formattedArgs) . ')';
            $response = $this->_getRedisInstance()->eval($command);

            $commandString = join('-', $cmdArgs);

            if ($response !== false && !empty($response))
            {
                $requestResponsesMulti['responses'][$commandString] = $response;
            }

            $requestResponsesMulti['responses'][$commandString] = $response;
        }

        // reset request/response queues
        $this->_pipelineResetQueue();

        // disable pipeline
        $this->pipelineEnable(false);

        return $requestResponsesMulti;
    }

    // ##########################################

    /**
     * @return bool
     */
    public function pipelineIsEnabled()
    {
        return $this->_enablePipeline;
    }

    // ##########################################

    /**
     * @return array
     */
    protected function _pipelineGetQueue()
    {
        return $this->_pipelineQueue;
    }

    // ##########################################

    /**
     * @param $cmdArgs
     *
     * @return $this
     */
    public function pipelineAddQueueItem($cmdArgs)
    {
        if ($cmdArgs !== false)
        {
            // force strings only
            $cmdArgs = $this->_ensureStringCommands($cmdArgs);

            $this->_pipelineQueue[] = $cmdArgs;
        }

        return $this;
    }

    // ##########################################

    /**
     * @return $this
     */
    protected function _pipelineResetQueue()
    {
        // reset queue
        $this->_pipelineQueue = [];

        return $this;
    }

    // ##########################################

    /**
     * @param $dbId
     *
     * @return array
     */
    protected function _getDbSelectQuery($dbId)
    {
        return ['SELECT', (string)$dbId];
    }

    // ##########################################

    /**
     * @param $dbId
     *
     * @return bool|mixed
     */
    public function dbSelect($dbId)
    {
        $response = $this->_getRedisInstance()->select($dbId);

        if ($response !== false)
        {
            return $response;
        }

        return false;
    }

    // ##########################################

    /**
     * @param $password
     *
     * @return array
     */
    protected function _getDbAuthQuery($password)
    {
        return ['AUTH', $password];
    }

    // ##########################################

    /**
     * @param $password
     *
     * @return bool|mixed
     */
    public function dbAuth($password)
    {
        $response = $this->query($this->_getDbAuthQuery($password));

        if ($response !== false)
        {
            return $response;
        }

        return false;
    }

    // ##########################################

    /**
     * @return array
     */
    protected function _getDbFlushQuery()
    {
        return ['FLUSHDB'];
    }

    // ##########################################

    /**
     * @param bool $confirm
     *
     * @return bool|mixed
     */
    public function dbFlush($confirm = false)
    {
        if ($confirm === true)
        {
            $response = $this->query($this->_getDbFlushQuery());

            if ($response !== false)
            {
                return $response;
            }
        }

        return false;
    }

    // ##########################################

    /**
     * @param array $keys
     *
     * @return array
     */
    protected function _getKeyDeleteMultiQuery(array $keys)
    {
        return array_merge(['DEL'], $keys);
    }

    // ##########################################

    /**
     * @param $key
     *
     * @return bool|mixed
     */
    public function keyDelete($key)
    {
        $response = $this->query($this->_getKeyDeleteMultiQuery([$key]));

        if ($response !== false)
        {
            return $response;
        }

        return false;
    }

    // ##########################################

    /**
     * @param array $keys
     *
     * @return bool|mixed
     */
    public function keyDeleteMulti(array $keys)
    {
        $response = $this->query($this->_getKeyDeleteMultiQuery($keys));

        if ($response !== false)
        {
            return $response;
        }

        return false;
    }

    // ##########################################

    /**
     * @param $key
     * @param $seconds
     *
     * @return array|bool
     */
    protected function _getKeySetExpireQuery($key, $seconds = -1)
    {
        if ($seconds > 0)
        {
            return ['EXPIRE', $key, (string)$seconds];
        }

        return false;
    }

    // ##########################################

    /**
     * @param $key
     * @param $seconds
     *
     * @return bool|mixed
     */
    public function keySetExpire($key, $seconds = -1)
    {
        $response = $this->query($this->_getKeySetExpireQuery($key, $seconds));

        if ($response !== false)
        {
            return $response;
        }

        return false;
    }

    // ##########################################

    /**
     * @param array $keys
     * @param $seconds
     *
     * @return array|bool
     */
    public function keySetExpireMulti(array $keys, $seconds = -1)
    {
        if ($seconds > 0)
        {
            $this->pipelineEnable(true);

            foreach ($keys as $key)
            {
                $this->pipelineAddQueueItem($this->_getKeySetExpireQuery($key, $seconds));
            }

            $response = $this->pipelineExecute();

            if ($response !== false)
            {
                return $response;
            }
        }

        return false;
    }

    // ##########################################

    /**
     * @return array
     */
    protected function _getKeysGetCount()
    {
        return ['DBSIZE'];
    }

    // ##########################################

    /**
     * @return bool|mixed
     */
    public function keysGetCount()
    {
        $response = $this->query($this->_getKeysGetCount());

        if ($response !== false)
        {
            return $response;
        }

        return false;
    }

    // ##########################################

    /**
     * @param $pattern
     *
     * @return array
     */
    protected function _getKeysGetByPatternQuery($pattern)
    {
        return ['KEYS', $pattern];
    }

    // ##########################################

    /**
     * @param $pattern
     *
     * @return bool|mixed
     */
    public function keysGetByPattern($pattern)
    {
        $response = $this->query($this->_getKeysGetByPatternQuery($pattern));

        if ($response !== false)
        {
            return $response;
        }

        return false;
    }

    // ##########################################

    /**
     * @param $key
     *
     * @return array
     */
    protected function _getKeyExistsQuery($key)
    {
        return ['EXISTS', $key];
    }

    // ##########################################

    /**
     * @param $key
     *
     * @return bool|mixed
     */
    public function keyExists($key)
    {
        $response = $this->query($this->_getKeyExistsQuery($key));

        if ($response !== false)
        {
            return true;
        }

        return false;
    }

    // ##########################################

    /**
     * @param $key
     *
     * @return array
     */
    protected function _getKeyGetExpirationQuery($key)
    {
        return ['TTL', $key];
    }

    // ##########################################

    /**
     * @param $key
     *
     * @return bool|mixed
     */
    public function keyGetExpiration($key)
    {
        $response = $this->query($this->_getKeyGetExpirationQuery($key));

        if ($response !== false)
        {
            return $response;
        }

        return false;
    }

    // ##########################################

    /**
     * @param $key
     *
     * @return array
     */
    protected function _getKeyRenameQuery($key)
    {
        return ['RENAMENX', $key];
    }

    // ##########################################

    /**
     * @param $key
     *
     * @return bool|mixed
     */
    public function keyRename($key)
    {
        $response = $this->query($this->_getKeyRenameQuery($key));

        if ($response !== false)
        {
            return $response;
        }

        return false;
    }

    // ##########################################

    /**
     * @param $key
     *
     * @return array
     */
    protected function _getKeyRemoveExpirationQuery($key)
    {
        return ['PERSIST', $key];
    }

    // ##########################################

    /**
     * @param $key
     *
     * @return bool|mixed
     */
    public function keyRemoveExpiration($key)
    {
        $response = $this->query($this->_getKeyRemoveExpirationQuery($key));

        if ($response !== false)
        {
            return $response;
        }

        return false;
    }

    // ##########################################

    /**
     * @param $key
     *
     * @return array
     */
    protected function _getKeyIncrementQuery($key)
    {
        return ['INCR', $key];
    }

    // ##########################################

    /**
     * @param $key
     *
     * @return bool|mixed
     */
    public function keyIncrement($key)
    {
        $response = $this->query($this->_getKeyIncrementQuery($key));

        if ($response !== false)
        {
            return $response;
        }

        return false;
    }

    // ##########################################

    /**
     * @param $key
     * @param $value
     *
     * @return array
     */
    protected function _getKeyIncrementByQuery($key, $value = 1)
    {
        return ['INCRBY', $key, (string)$value];
    }

    // ##########################################

    /**
     * @param $key
     * @param int $value
     *
     * @return bool|mixed
     */
    public function keyIncrementBy($key, $value = 1)
    {
        $response = $this->query($this->_getKeyIncrementByQuery($key, $value));

        if ($response !== false)
        {
            return $response;
        }

        return false;
    }

    // ##########################################

    /**
     * @param $key
     *
     * @return array
     */
    protected function _getKeyDecrementQuery($key)
    {
        return ['DECR', $key];
    }

    // ##########################################

    /**
     * @param $key
     *
     * @return bool|mixed
     */
    public function keyDecrement($key)
    {
        $response = $this->query($this->_getKeyDecrementQuery($key));

        if ($response !== false)
        {
            return $response;
        }

        return false;
    }

    // ##########################################

    /**
     * @param $key
     * @param $value
     *
     * @return array
     */
    protected function _getKeyDecrementByQuery($key, $value = 1)
    {
        return ['DECRBY', $key, (string)$value];
    }

    // ##########################################

    /**
     * @param $key
     * @param int $value
     *
     * @return bool|mixed
     */
    public function keyDecrementBy($key, $value = 1)
    {
        $response = $this->query($this->_getKeyDecrementByQuery($key, $value));

        if ($response !== false)
        {
            return $response;
        }

        return false;
    }
}