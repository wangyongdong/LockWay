<?php
require_once __DIR__ . '/../autoload.php';
use src\Lock\Lock;

$total = '100'; //余额

pay(1100411, 20);

//用户支付
function pay($userid, $money) {
    if(false == is_int($userid) || false == is_int($money)) {
        echo '类型错误';
        return false;
    }

    try{
        //创建锁(推荐使用Redis)
        $lock = new Lock(Lock::LOCK_REDIS);
        //获取锁
        $lockKey = 'pay' . $userid;
        $lock->getLock($lockKey,8);
        //取出总额
        $total = getUserLeftMoney($userid);
        //花费大于剩余
        if($money > $total) {
            return '剩余数量不足';
        } else {
            echo $total.'<br/>';
            //余额
            $left = $total - $money;
            //更新余额
            $ret = setUserLeftMoney($userid, $left);
            echo '剩余：' . $ret . '<br/>';
        }
        //释放锁
        $lock->releaseLock($lockKey);
    } catch (Exception $e) {
        echo $e->getMessage();
        //释放锁
//        $lock->releaseLock($lockKey);
    }
}

//取出用户的余额
function getUserLeftMoney($userid) {
    if(false == is_int($userid)) {
        return 0;
    }
    global $total;
    return $total;
//    $sql = "select account form user_account where userid = " . $userid;
//    $mysql = new mysql();//mysql数据库
//    return $mysql->query($sql);
}
//更新用户余额
function setUserLeftMoney($userid, $money) {
    if(false == is_int($userid) || false == is_int($money)) {
        return false;
    }
//    $sql = "update user_account set account = ${money} where userid = " . $userid;
//    $mysql = new mysql();//mysql数据库
//    return $mysql->execute($sql);
    global $total;
    return $total = $money;
}
