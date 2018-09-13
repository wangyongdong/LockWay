<?php

namespace src\Lock;

class RedisLock implements LockInterface
{

    /**
     * @var \Redis
     */
    private $redis = null;

    /**
     * redis 连接参数
     * @var array
     */
    private static $_config = array(
        'host' => '127.0.0.1',
        'port' => 6379,
        'password' => '',
        'index' => 0,           //Db index 0-15
        'timeout' => 0,         //overtime time
        'persistent' => false,  //is persistent
        'expire' => 7000,       //Expiration
    );

    /**
     * RedisLock constructor.
     * @param $configuration
     */
    public function __construct($configuration = array())
    {
        //Set configuration parameters
        if (!empty($configuration)) {
            self::$_config = array_merge(self::$_config, $configuration);
        }
        try {
            $func = self::$_config['persistent'] ? 'pconnect' : 'connect';

            $this->redis = new \Redis();
            $this->redis->$func(self::$_config['host'], self::$_config['port'], self::$_config['timeout']);

            //auth
            if (!empty(self::$_config['password'])) {
                $this->redis->auth(self::$_config['password']);
            }

            //Select index
            $this->redis->select(self::$_config['index']);

        } catch (\Exception $e) {
            echo $e->getMessage() . '<br/>';
        }
    }

    /**
     * Delete key , Support array batch delete [return delete number]
     * @param array|string $key
     */
    public function del($key)
    {
        if (is_array($key) || is_string($key)) {
            $this->redis->del($key);
        }
    }

    /**
     * set value
     * @param string $sKey
     * @param string $sValue
     * @param int $expire
     * @return bool
     */
    public function add($sKey, $sValue, $expire = null)
    {
        if (empty($sKey)) {
            return false;
        }

        $data = $this->redis->get($sKey);
        if (!empty($data)) {
            return false;
        }

        $this->redis->set($sKey, json_encode($sValue));

        if (!empty($expire)) {
            self::$_config['expire'] = !empty($expire) ? $expire : self::$_config['expire'];
            $this->redis->expire($sKey, self::$_config['expire']);
        }

        return true;
    }

    /**
     * Get lock
     * @param string $key
     * @param int $timeout
     */
    public function getLock($key, $timeout = self::EXPIRE)
    {
        $waitime = 2;           //每次睡眠2秒后再去获取锁
        $time = $timeout;       //超时时间
        $totalWaitime = 0;      //获取锁的总耗时

        while ($totalWaitime < $time && false == $this->add($key, 1, $timeout)) {
            sleep($waitime);
            $totalWaitime += $waitime;
        }
        if ($totalWaitime >= $time) {
            throw new \Exception('can not get lock for waiting ' . $totalWaitime . 's.');
        }
    }

    /**
     * Release lock
     * @param string $key
     */
    public function releaseLock($key)
    {
        $this->del($key);
    }
}