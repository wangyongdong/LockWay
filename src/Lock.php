<?php
namespace src\Lock;
class Lock
{
    /**
     * LOCK_DB
     */
    const LOCK_DB = 'SQLLock';

    /**
     * LOCK_FILE
     */
    const LOCK_FILE = 'FileLock';

    /**
     * LOCK_REDIS
     */
    const LOCK_REDIS = 'RedisLock';

    /**
     * @var string
     */
    private $_scheme = null;

    /**
     * @var Container
     */
    private $_container = null;

    /**
     * Lock constructor.
     * @param string $scheme
     * @param array $configuration
     * @throws \Exception
     */
    public function __construct($scheme = self::LOCK_REDIS, $configuration = array())
    {
        if (!in_array($scheme, array(self::LOCK_REDIS, self::LOCK_FILE, self::LOCK_DB))) {
            throw new \Exception("lock type [" . $scheme . "] is faild ");
        }

        $this->_scheme = $scheme;
        $this->_container = new Container();

        //Bind to the factory
        $this->_container->bind($this->_scheme, $this->gclass($this->_scheme, $configuration));
    }

    /**
     * @param string $scheme
     * @param array $configuration
     * @return Instance object
     */
    public function gclass($scheme, $configuration = array())
    {
        $className = 'src\Lock\\' . $scheme;
        return new $className($configuration);
    }

    /**
     * @param string $key
     * @param int $timeout
     */
    public function getLock($key, $timeout = LockInterface::EXPIRE)
    {
        $this->_container->make($this->_scheme)->getLock($key, $timeout);
    }

    /**
     * @param string $key
     */
    public function releaseLock($key)
    {
        $this->_container->make($this->_scheme)->releaseLock($key);
    }
}