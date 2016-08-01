<?php

namespace Project\Logic;

//use Common\WeixinApi\ApiClient as Api;
use Org\WeixinApi\Api;

/**
 * 用户模块逻辑层 基类.
 *
 * @author Cui
 */
class UserLogic extends CommonLogic
{
    // 用户数据在session中的键位
    const USER_SESSION_KEY 	= 'USER_INFO';
    const USER_SESSION_SIGN = 'USER_SIGN';
    const KEY 				= 'APP_CORP_STORAGE';

    // 存储用户信息
    private $userInfo;

    /**
     * 判断当前用户是否登录.
     *
     * @author Cui.
     *
     * @return boolean;
     */
    public function isLogined()
    {
        $data = session(self::USER_SESSION_KEY);
        if (false == $data) {
            return false;
        }

        return data_auth_sign($data) === session(self::USER_SESSION_SIGN);
    }

    /**
     * 判断当前用户是否登录.
     *
     * @author Cui.
     *
     * @return array;
     */
    public function getLoginedUserInfo()
    {
        if (!$this->userInfo) {
            $this->userInfo = session(self::USER_SESSION_KEY);
        }

        return $this->userInfo;
    }


    /**
     * 查看user是否存在,并存入缓存
     *
     * @author Yi
     *
     * @date   2015-08-15
     *
     * @param  string $userId     用户微信id
     *
     * @return boolean.
     */
    public function checkUserId( $wuserid )
    {
        if (!empty($wuserid) && !empty($_SESSION[self::KEY]['cid'])) {
            $data = D('Project/UserInfo')->getUserInfoByWechatId( $wuserid , $_SESSION[self::KEY]['cid']);

            if (!empty($data)) {
                defined('O365_WE_CHAT_USER_ID') ? '' : define('O365_WE_CHAT_USER_ID', $wuserid);
                session(self::USER_SESSION_KEY, $data);
                session(self::USER_SESSION_SIGN, data_auth_sign($data));
                return $data;
            } else {
                return false;
            }

        }
        return false;
    }

}
