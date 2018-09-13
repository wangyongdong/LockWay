<?php
namespace src\Lock;

/**
 * Interface LockInterface
 */
interface LockInterface {
    /**
     * Define the expiration time of the lock, in seconds
     */
    const EXPIRE = 20;

    /**
     * @param string $key
     * @param int $timeout
     * @return mixed
     */
    public function getLock($key, $timeout);

    /**
     * @param string $key
     * @return mixed
     */
    public function releaseLock($key);
}