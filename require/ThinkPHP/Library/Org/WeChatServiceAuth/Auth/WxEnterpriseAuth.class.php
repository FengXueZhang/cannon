<?php

namespace Org\WeChatServiceAuth\Auth;

use Org\WeixinApi\Api;
use Think\Log;

class WxEnterpriseAuth extends BaseAuth
{
    protected        $user_register_api     = 'http://ucenter.shoufubx.com/auth/index/postIndexMemberRegister';
    protected        $check_user_exists_api = 'http://ucenter.shoufubx.com/auth/index/postIndexCheckMemberExists';
    protected        $relevance_openid_api  = 'http://ucenter.shoufubx.com/auth/index/postIndexRelevanceOpenid';
    protected        $check_relevance_api   = 'http://ucenter.shoufubx.com/auth/index/postIndexCheckRelevance';
    protected static $userInfokey           = 'USER_INFO_KEY'; // 企业号corp_secrect
    const SHOUFUBX_UCENTER_DOMAIN = 'http://ucenter.shoufubx.com/';

    private $WxApi;

    public function __construct()
    {
    }

    protected function setCompanyParm()
    {
    }

    public function checkOAuth($uid)
    {
        if (!$uid) {
            $resData = $this->userRegister();
        } else {
            $resData = $this->checkUserExists($uid);
            if (!$uid) {
                $resData = $this->userRegister();
            }
        }

        $key = 'user_info_' . $resData['uid'] . '-' . md5(self::$userInfokey);

        S($key, $resData, 1800);

        return $resData;
    }

    /**
     * 微信服务号Oauth
     *
     * @param array $param
     *
     * @return bool
     * @throws \Exception
     */
    public function relevanceOpenid(array $param)
    {
        $response = self::post($this->relevance_openid_api, http_build_query($param));
        $result   = json_decode($response['body'], true);
        if (200 != $response['status'] || 200 != $result['status']) {
            return null;
        } else {
            return $result['data'];
        }
    }

    public function checkRelevance(array $param)
    {
        $response = self::post($this->check_relevance_api, $param);
        $result   = json_decode($response['body'], true);
        if (200 != $response['status'] || 200 != $result['status']) {
            return null;
        } else {
            return $result['data'];
        }
    }

    public function getUserId()
    {
        define('SHOUFU_CORP_ID', 'wx214dcfa5440ff2ee');
        define('SHOUFU_CORP_SECRET', 'UnCQxT_3tOCY7wewpAZ_9Pm6GKcrKfxgMwS7ef6CgYGxpdYkjtnFC6d2nSKQlBGT');

        $UserWxApi = Api::factory('User');
        $UserWxApi->request(__SELF__, '', 'SHOUFUBX_SERVICE_DOMAIN'); //发起OAuth请求.

        $OAuthRes = $UserWxApi->receive(); //接收结果;
        if (false === $OAuthRes) {
            if (method_exists($this, '_oauth_failed')) {
                // 如果子类定义了 验证失败的回调方法则执行
                $this->_oauth_failed();
            }

            if (!empty($_SESSION[self::COME_URL])) {
                $this->location('权限验证失败', 'error');
            }
        }

        return $OAuthRes['userid'];
    }

    public function getAccessToken()
    {
        $url      = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=wx214dcfa5440ff2ee&corpsecret=UnCQxT_3tOCY7wewpAZ_9Pm6GKcrKfxgMwS7ef6CgYGxpdYkjtnFC6d2nSKQlBGT';
        $response = self::get($url);
        $result   = json_decode($response['body'], true);
        if (200 != $response['status']) {
            return null;
        } else {
            return $result['access_token'];
        }
    }

    public function getUserInfo($user_id)
    {
        $access_token = $this->getAccessToken();

        $url      = 'https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token=' . $access_token . '&userid=' . $user_id;
        $response = self::get($url);
        $result   = json_decode($response['body'], true);

        if (200 == $response['status']) {
            return $result;
        } else {
            return null;
        }
    }

    public function userRegister()
    {
        $user_id   = $this->getUserId();
        $user_info = $this->getUserInfo($user_id);
        $open_id   = $this->getOpenId($user_id);

        $params['corp_id'] = 'wx1294cf54ea91b3c2';
        $params['from']    = '至德讯通工会';
        $params['channel'] = 'wx_oauth';
        $params['extra']   = $user_info;

        $sessionData['user_info'] = $user_info;
        $sessionData['openid']    = $open_id['openid'];

        $response = self::post($this->user_register_api, http_build_query($params));
        $result   = json_decode($response['body'], true);

        if (200 == $response['status'] && 200 == $result['status']) {
            $sessionData['uid'] = $result['uid'];

            return $sessionData;
        } else {
            return null;
        }
    }

    public function getOpenId($user_id)
    {
        $access_token = $this->getAccessToken();

        $url            = 'https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_openid?access_token=' . $access_token;
        $data['userid'] = $user_id;

        $response = self::post($url, json_encode($data));
        $result   = json_decode($response['body'], true);

        if (200 == $response['status']) {
            return $result;
        } else {
            return null;
        }
    }

    public function checkUserExists($uid)
    {
        $params['uid'] = $uid;
        $response      = self::post($this->check_user_exists_api, http_build_query($params));
        $result        = json_decode($response['body'], true);

        $user_info = $this->getUserInfo($uid);
        $open_id   = $this->getOpenId($uid);

        $sessionData['user_info'] = $user_info;
        $sessionData['openid']    = $open_id['openid'];

        if (200 != $response['status'] || 200 != $result['status']) {
            return null;
        } else {
            $sessionData['uid'] = $result['uid'];

            return $sessionData;
        }
    }

    public function getUcInfo($uid)
    {
        $params['uid'] = $uid;

        $response = self::post(self::WEFLAME_UCENTER_DOMAIN . '/auth/index/postIndexCheckMemberExists', http_build_query($params));

        $result = json_decode($response['body'], true);

        return $result;
    }

}