<?php

namespace src\Lock;

class SQLLock implements LockInterface
{
    /**
     * @var \PDO
     */
    private $_db = null;

    /**
     * @var array
     */
    private static $_config = array(
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '123456',
    );

    /**
     * SQLLock constructor.
     * @param array $configuration
     */
    public function __construct($configuration = array())
    {
        //Set configuration parameters
        if (!empty($configuration)) {
            self::$_config = array_merge(self::$_config, $configuration);
        }
        try {
            $this->_db = new \PDO('mysql:host=' . self::$_config['host'] . ';', self::$_config['username'], self::$_config['password']);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Get lock
     * @param string $key
     * @param int $timeout
     */
    public function getLock($key, $timeout = self::EXPIRE)
    {
        $sql = "SELECT GET_LOCK('" . $key . "', '" . $timeout . "')";
        $res = $this->_db->query($sql);
        return $res;
    }

    /**
     * Release lock
     * @param string $key
     */
    public function releaseLock($key)
    {
        $sql = "SELECT RELEASE_LOCK('" . $key . "')";
        return $this->_db->query($sql);
    }
}