<?php

namespace Org\WeChatServiceAuth\Auth;

use redis;
use Think\Log;

class RedisAuth extends BaseAuth
{

    protected $redis;
    private static $instance;

    const KEY = 'APP_CORP_STORAGE';
    const SGIN = 'APP_CORP_STORAGE_SGIN';


    public function __construct()
    {

        $this->redisLink();
    }

    private function redisLink($host = '', $port = '', $outtime = 60)
    {
        $this->redis = new Redis();

        !$host && $host = C('REDIS_HOST');
        !$port && $port = C('REDIS_PORT');

        $link = $this->redis->connect($host, $port, $outtime);

        if (!$link) {
            self::$errorCode = 401;
            self::$errorMessage = 'Redis连接错误';

            $this->apiToJson();
        }
    }

    public function _set($key = '', $data = [], $outTime = 1000)
    {
        if (empty($key) && empty($data)) return false;
        return $this->redis->set($key, $data, $outTime);
    }

    public function _get($key = '')
    {
        if (empty($key)) return false;
        return $this->redis->get($key);
    }


    /**
     * 设置key超时时间
     *
     * @param string    $key
     * @param int       $outTime
     *
     * @return bool
     */
    public function expire($key = '', $outTime = 60)
    {
        if (empty($key) && empty($outTime)) return false;
        return $this->redis->expire($key,$outTime);
    }

    /**
     * 查看key是否设置超时时间
     *
     * @param string    $key
     *
     * @return bool
     */
    public function ttl($key = '')
    {
        if (empty($key)) return false;
        return $this->redis->ttl($key);
    }


    /**
     * 将多个元素插入有序集合中
     *
     * @param string    $key
     * @param int       $score
     * @param string    $member
     *
     * @return bool
     */
    public function zadd($key = '' ,$score = 0, $member = '')
    {
        if (empty($key) && empty($score) && empty($member)) return false;
        return $this->redis->zAdd($key, $score, $member);
    }

    /**
     * 返回有序集合的基数
     *
     * @param string    $key
     *
     * @return bool
     */
    public function zcard($key = '')
    {
        if (empty($key)) return false;
        return $this->redis->zCard($key);
    }

    /**
     * 根据资源/时间范围进行数目统计
     *
     * @param string    $key
     * @param int       $startTime
     * @param int       $endtime
     *
     * @return bool
     */
    public function zcount($key = '' , $startTime = 0, $endTime = 0)
    {
        if (empty($key) && empty($startTime) && empty($endTime)) return false;
        return $this->redis->zCount($key, $startTime , $endTime);
    }

    /**
     * 对有序集合的排列资源进行正负数值加点
     *
     * @param string    $key
     * @param int       $startTime 正负整形
     * @param int       $endtime
     *
     * @return bool
     */
    public function zincrby($key = '' , $score = 0, $member = 0)
    {
        if (empty($key) && empty($score) && empty($member)) return false;
        return $this->redis->zIncrby($key, $score , $member);
    }

    /**
     * 返回有序集 key 中，指定区间内的成员
     *
     * @param string    $key
     * @param int       $startTime
     * @param int       $endtime
     * @param bool      $ifScores
     * @param string    $withScores
     *
     * @return bool
     */
    public function zrange($key = '' , $startTime = 0, $endTime = 0 , $ifScores = false , $withScores = 'WITHSCORES')
    {
        if (empty($key) && empty($startTime) && empty($endTime)) return false;

        if ($ifScores)
            return $this->redis->zRange($key, $startTime , $endTime);
        else
            return $this->redis->zRange($key, $startTime , $endTime ,$withScores);

    }


    public function zrangebyscore($key = '' , $startTime = '-inf', $endTime = '+inf' , $ifScores = false , $withScores = 'WITHSCORES')
    {
        if (empty($key) && empty($startTime) && empty($endTime)) return false;

        if ($ifScores)
            return $this->redis->zRangebyscore($key, $startTime , $endTime);
        else
            return $this->redis->zRangebyscore($key, $startTime , $endTime ,$withScores);
    }

    /**
     * 返回有序集 key 中成员 member 的排名。其中有序集成员按 score 值递增(从小到大)顺序排列。
     * 排名以 0 为底，也就是说， score 值最小的成员排名为 0 。
     *
     * @param string    $key
     * @param string    $score
     *
     * @return bool
     */
    public function zrank($key = '' ,$member = '')
    {
        if (empty($key) && empty($member)) return false;
        return $this->redis->zRank($key,$member);
    }


    /**
     *
     *
     * @param string    $key
     * @param string    $member
     *
     * @return bool
     */
    public function zrem($key = '' ,$member = '')
    {
        if (empty($key) && empty($member)) return false;
        return $this->redis->zRem($key,$member);
    }



//    public function s
}