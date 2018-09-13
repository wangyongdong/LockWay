<?php

namespace src\Lock;

class FileLock implements LockInterface
{
    /**
     * @var resource
     */
    private $fp;

    /**
     * @var bool|mixed
     */
    private $_single;

    /**
     * @var string
     */
    private $_lockPath;

    /**
     * FileLock constructor.
     * @param array $configuration[path, single]
     */
    public function __construct($configuration = array())
    {
        if (isset($configuration['path']) && is_dir($configuration['path'])) {
            $this->_lockPath = $configuration['path'] . '/';
        } else {
            $this->_lockPath = '/tmp/';
        }
        $this->_single = isset($configuration['single']) ? $configuration['single'] : false;
    }

    /**
     * Get lock
     * @param string $key
     * @param int $timeout
     */
    public function getLock($key, $timeout = self::EXPIRE)
    {
        $file = md5(__FILE__ . $key);
        $this->fp = fopen($this->_lockPath . $file . '.lock', "w+");
        if (true || $this->_single) {
            $op = LOCK_EX + LOCK_NB;
        } else {
            $op = LOCK_EX;
        }
        if (false == flock($this->fp, $op)) {
            throw new \Exception('failed');
        }
        return true;
    }

    /**
     * Release lock
     * @param string $key
     */
    public function releaseLock($key)
    {
        flock($this->fp, LOCK_UN);
        fclose($this->fp);
    }
}