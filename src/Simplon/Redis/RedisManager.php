<?php

    namespace Simplon\Redis;

    class RedisManager
    {
        /** @var \Simplon\Redis\Redis */
        private $_redisInstance;

        /** @var RedisBitCommands */
        private $_bitCommandsInstance;

        /** @var RedisHashCommands */
        private $_hashCommandsInstance;

        /** @var RedisListCommands */
        private $_listCommandsInstance;

        /** @var RedisSetCommands */
        private $_setCommandsInstance;

        /** @var RedisSortedSetCommands */
        private $_sortedSetCommandsInstance;

        /** @var RedisStringCommands */
        private $_stringCommandsInstance;

        /** @var RedisHelperCommands */
        private $_helperCommandsInstance;

        // ######################################

        /**
         * @param \Simplon\Redis\Redis $redisBaseInstance
         */
        public function __construct(Redis $redisBaseInstance)
        {
            $this->_redisInstance = $redisBaseInstance;
        }

        // ######################################

        /**
         * @return \Simplon\Redis\Redis
         */
        public function getRedisInstance()
        {
            return $this->_redisInstance;
        }

        // ######################################

        /**
         * @return RedisBitCommands
         */
        public function getBitCommandsInstance()
        {
            if (!$this->_bitCommandsInstance)
            {
                $this->_bitCommandsInstance = new RedisBitCommands($this->getRedisInstance());
            }

            return $this->_bitCommandsInstance;
        }

        // ######################################

        /**
         * @return RedisHashCommands
         */
        public function getHashCommandsInstance()
        {
            if (!$this->_hashCommandsInstance)
            {
                $this->_hashCommandsInstance = new RedisHashCommands($this->getRedisInstance());
            }

            return $this->_hashCommandsInstance;
        }

        // ######################################

        /**
         * @return RedisListCommands
         */
        public function getListCommandsInstance()
        {
            if (!$this->_listCommandsInstance)
            {
                $this->_listCommandsInstance = new RedisListCommands($this->getRedisInstance());
            }

            return $this->_listCommandsInstance;
        }

        // ######################################

        /**
         * @return RedisSetCommands
         */
        public function getSetCommandsInstance()
        {
            if (!$this->_setCommandsInstance)
            {
                $this->_setCommandsInstance = new RedisSetCommands($this->getRedisInstance());
            }

            return $this->_setCommandsInstance;
        }

        // ######################################

        /**
         * @return RedisSortedSetCommands
         */
        public function getSortedSetCommandsInstance()
        {
            if (!$this->_sortedSetCommandsInstance)
            {
                $this->_sortedSetCommandsInstance = new RedisSortedSetCommands($this->getRedisInstance());
            }

            return $this->_sortedSetCommandsInstance;
        }

        // ######################################

        /**
         * @return RedisStringCommands
         */
        public function getStringCommandsInstance()
        {
            if (!$this->_stringCommandsInstance)
            {
                $this->_stringCommandsInstance = new RedisStringCommands($this->getRedisInstance());
            }

            return $this->_stringCommandsInstance;
        }

        // ######################################

        /**
         * @return RedisHelperCommands
         */
        public function getHelperCommandsInstance()
        {
            if (!$this->_helperCommandsInstance)
            {
                $this->_helperCommandsInstance = new RedisHelperCommands();
            }

            return $this->_helperCommandsInstance;
        }
    }
