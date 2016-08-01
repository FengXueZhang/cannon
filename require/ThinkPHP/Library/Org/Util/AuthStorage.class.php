<?php

namespace Org\Util;

use redis;

/**
 * 授权信息存储
 * 
 * @author Cui
 * 
 * @date 2015-12-10
 */
class AuthStorage
{
    private $redis;
    private static $instance;

    /**
     * 获取本类实例
     *
     * @author Cui
     *
     * @date   2015-12-10
     *
     * @return self
     */
    public static function getInstance($host = '', $port = '', $outtime = 60)
    {
        if (! self::$instance) {
            !$host && $host = C('REDIS_HOST');
            !$port && $port = C('REDIS_PORT');

            self::$instance = new self($host, $port, $outtime);
        }
        
        return self::$instance;
    }

    /**
     * 连接redis
     *
     * @author Cui
     *
     * @date   2015-12-10
     *
     * @param  string       $host    主机
     * @param  int          $port    端口
     * @param  int          $outtime 超时时间
     */
    private function __construct($host, $port, $outtime)
    {
        $this->redis = new redis();
        $link = $this->redis->connect($host, $port);
        if (! $link) {
            throw new \Exception("Redis connect error", 1);
        }

        define('AUTH_KEY_PRE', 'PROJ_OPEN_AUTH:');
        define('AUTH_USER_KEY', AUTH_KEY_PRE.'USER');
        define('AUTH_GROUP_PRE', AUTH_KEY_PRE.'GROUP:');
    }

    /**
     * 根据authid获取用户信息
     *
     * @author Cui
     *
     * @date   2015-12-10
     *
     * @param  string     $authId 授权ID
     *
     * @return array
     */
    public function getUserInfo($authId)
    {
        if (! $authId) {
            throw new \Exception("参数错误!", 1);
        }

        $res = $this->redis->hGet(AUTH_USER_KEY, $authId);
        if ($res) {
            $res = json_decode($res, true);
        }

        return $res;
    }

    /**
     * 设置用户信息
     *
     * @author Cui
     *
     * @date   2015-12-10
     *
     * @param  string   $authId 授权ID
     * @param  array    $info   用户信息
     */
    public function setUserInfo($authId, $info)
    {
        if (! $authId || !$info) {
            throw new \Exception("参数错误!", 1);
        }

        return $this->redis->hSet(AUTH_USER_KEY, $authId, json_encode($info));
    }

    /**
     * 获取权限组的相关权限信息
     *
     * @author Cui
     *
     * @date   2015-12-10
     *
     * @param  int     $groupId 权限组ID
     * @param  int     $type    权限类型
     *
     * @return array
     */
    public function getRulesByGroup($groupId, $type)
    {
        $groupId = intval($groupId);
        $type = intval($type);
        if (! $groupId || !$type) {
            throw new \Exception("参数错误!", 1);
        }

        $key = AUTH_GROUP_PRE.$groupId.':'.$type;

        return $this->redis->exists($key);
    }

    /**
     * 设置权限组权限信息
     *
     * @author Cui
     *
     * @date   2015-12-10
     *
     * @param  int              $groupId 权限组ID
     * @param  int              $type    权限类型
     * @param  string|array     $rules   权限
     */
    public function setGroupRules($groupId, $type, $rules)
    {
        $groupId = intval($groupId);
        $type = intval($type);
        if (! $groupId || !$type || !$rules) {
            throw new \Exception("参数错误!", 1);
        }

        $key = AUTH_GROUP_PRE.$groupId.':'.$type;
        if ($rules == '*') {

            return $this->redis->sAdd($key, $rules);
        } 

        foreach ($rules as $value) {
           $this->redis->sAdd($key, $value['rule_name']);
        }

        return true;
    }

    /**
     * 关闭redis
     *
     * @author Cui
     *
     * @date   2015-12-10
     */
    public function __destruct()
    {
        $this->redis->close();
    }
}