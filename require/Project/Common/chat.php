<?php

/**
 * Redis聊天室函数库.
 */

/**
 * redis聊天室 频道订阅的回调函数 PS:Redis 订阅API方法 第三个参数只能传递String 也就是函数名 所以需要预定义函数;.
 */
function redis_subscribe_callback($instance, $channelName, $message)
{
    $Chat = API('RedisChat');
    $message = $Chat->decodeContent(array($message));
    $message = array_shift($message);

    if (false === $Chat->isMyMessage($message)) {
        $json = array();
        $json['status'] = 200;
        $json['info'] = $message;
        $json = json_encode($json);
        exit($json);
    }
}

/**
 * 计算24的中文名称.
 *
 * @param $hours int 小时 0-23
 * @param $minutes int 分钟
 *
 * @return string;
 */
function chat_hours_format($hours, $minutes)
{
    $minutes = sprintf('%02d', $minutes);
    if ($hours >= 24 && $hours < 5) {
        $hours == 24 && $hours = 00;

        return '凌晨 ' . $hours . ':' . $minutes;
    }

    if ($hours >= 5 && $hours < 9) {
        return '早上 ' . $hours . ':' . $minutes;
    }

    if ($hours >= 9 && $hours < 12) {
        return '上午 ' . $hours . ':' . $minutes;
    }

    if ($hours >= 12 && $hours < 13) {
        return '中午 ' . $hours . ':' . $minutes;
    }

    if ($hours >= 13 && $hours < 18) {
        return '下午 ' . $hours . ':' . $minutes;
    }

    if ($hours >= 18 && $hours < 24) {
        return '晚上 ' . $hours . ':' . $minutes;
    }
}

/**
 * 转换聊天记录的显示时间.
 *
 * @param $timestamp int 时间戳
 * @param $flag 关闭重新计算 缺省是true 关闭;
 *
 * @return string;
 */
function get_chat_date($timestamp, $flag = true)
{
    static $pretimestamp;

    //如果相差五分钟则显示一条时间;
    if (!$pretimestamp || $flag == false) {
        $pretimestamp = 0;
    }

    $sin = 5 * 60;
    $showTime = '';

    if ($timestamp - $pretimestamp > $sin) {
        $showTime = $timestamp;
    }

    if ($showTime) {
        $nowTime = time();
        $chatDate = getdate($showTime);
        $nowDate = getdate($nowTime);

        if ($chatDate['year'] == $nowDate['year']) {
            if ($chatDate['mon'] == $nowDate['mon']) {
                if ($chatDate['mday'] == $nowDate['mday']) {
                    $showTime = chat_hours_format($chatDate['hours'], $chatDate['minutes']);
                } elseif ($nowDate['mday'] - $chatDate['mday'] == 1) {
                    $showTime = '昨天 ' . chat_hours_format($chatDate['hours'], $chatDate['minutes']);
                } else {
                    $showTime = $chatDate['mon'] . '月' . $chatDate['mday'] . '日 ' . chat_hours_format($chatDate['hours'], $chatDate['minutes']);
                }
            } else {
                $showTime = $chatDate['mon'] . '月' . $chatDate['mday'] . '日 ' . chat_hours_format($chatDate['hours'], $chatDate['minutes']);
            }
        } else {
            $showTime = $chatDate['year'] . '年' . $chatDate['mon'] . '月' . $chatDate['mday'] . '日 ' . chat_hours_format($chatDate['hours'], $chatDate['minutes']);
        }
    }

    $pretimestamp = $timestamp;

    return $showTime;
}

/**
 * 拼装聊天链接.
 *
 * @param $userid int 用户id;
 * @param $userRoleId 用户的roleID;
 */
function get_chat_url($userid, $userRoleId)
{
    $teacherRoleId = \User\Model\RoleModel::ROLE_ID_TEACHER;
    $parentRoleId = \User\Model\RoleModel::ROLE_ID_PARENT;

    switch ($userRoleId) {
        case $teacherRoleId:
            $type = 'teacher';
            break;

        case $parentRoleId:
            $type = 'parent';
            break;
    }

    $url = U('Wechat/Chat/room', array('to' => $type, 'sgin' => $userid));

    return $url;
}
