<?php

namespace Org\WeixinApi\API;

use Org\WeixinApi\Api;
use Org\Util\HttpClient;
use Think\Exception;
use Think\Log;

/**
 * 微信服务号Oauth
 *
 */
class OauthFwhApi extends BaseApi
{

    public $appid  = "wxd7334ab9fffcab57";
    public $secret = "a1608c02a07d5f4ae6834bc9109a18cc";

    /**
     * 创建微信OAuth协议的链接.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @param string $redirectUri 协议的回调地址
     * @param string $state       可携带的参数, 选填.
     *
     * @return string 协议地址
     */
    public function createOAuthUrl($redirectUri, $state = '', $costomDomain = '')
    {
        if (!$redirectUri) {
            $this->setError('参数错误!');

            return false;
        }

        if (!empty($costomDomain)) {
            $host = 'http://' . $costomDomain;
        } else {
            $host = isset($_SERVER['HTTP_HOST']) ? 'http://' . $_SERVER['HTTP_HOST'] : '';
        }

        $api   = 'https://open.weixin.qq.com/connect/oauth2/authorize';
        $state = $state ? $state = base64_encode($state) : '';

        $url                  = [];
        $url['appid']         = $this->appid;
        $url['redirect_uri']  = $host . $redirectUri;
        $url['response_type'] = 'code';
        $url['scope']         = 'snsapi_base';
        $url['state']         = $state;
        $url                  = http_build_query($url);

        $url .= '#wechat_redirect';
        $url = $api . '?' . $url;

        return $url;
    }

    /**
     * 发起OAuth认证请求.
     *
     * @author Cui
     *
     * @date   2015-07-27
     */
    public function request($redirectUri, $state = '', $costomDomain = '')
    {
        $code = I('get.code', false, 'trim');
        if ($code) {
            return;
        }

        $url = $this->createOAuthUrl($redirectUri, $state, $costomDomain);

        header('Location:' . $url);
        exit;
    }

    /**
     * 获取OAuth回调的信息.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @return array 回调信息.
     */
    public function receive()
    {
        $code = I('get.code', false, 'trim');

        if (!$code) {
            $this->setError('非法参数');

            return false;
        }

        $res = $this->getUserInfo($code);

        return $res;
    }

    public function getUserInfo($code)
    {
        $array = $this->getAccessTokenAndOpenid($code);
        if (!$array['openid']) {
            throw new \Exception("获取openid失败");
        }

        $key       = 'user_info_' . $array['openid'] . $this->appid;
        $user_info = self::cache($key);
        if (false == $user_info) {
            $access_token = $this->getAccessToken();
            $url          = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $access_token . '&openid=' . $array['openid'] . '&lang=zh_CN';
            $response     = HttpClient::get($url);
            if (200 != $response['status']) {
                throw new \Exception('获取用户信息失败!');
            }

            $user_info = json_decode($response['body'], true);
            self::cache($key, $user_info, 7200 - 300);
        }

        return $user_info;
    }

    /**
     * Oauth获取access_token 和 openid
     *
     * @param $code
     *
     * @return mixed
     * @throws \Exception
     */
    public function getAccessTokenAndOpenid($code)
    {
        $url      = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this->appid . '&secret=' . $this->secret . '&code=' . $code . '&grant_type=authorization_code';
        $response = HttpClient::get($url);
        $result   = json_decode($response['body'], true);
        if (200 != $response['status'] || !$result['access_token']) {
            throw new \Exception('获取AccessToken失败!');
        }

        return $result;
    }

    /**
     * 获取access_token UnionID机制
     *
     * @return mixed | string
     * @throws \Exception
     */
    public function getAccessToken()
    {
        $key          = 'access_token_' . $this->appid;
        $access_token = self::cache($key);

        if (false == $access_token) {
            $url      = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->appid . '&secret=' . $this->secret;
            $response = HttpClient::get($url);
            $result   = json_decode($response['body'], true);
            if (200 != $response['status'] || !$result['access_token']) {
                throw new \Exception('获取AccessToken失败!');
            }

            $access_token = $result['access_token'];
            self::cache($key, $access_token, 7200 - 300);
        }

        return $access_token;
    }

    /**
     * 缓存方法
     *
     * @param string $name    缓存名
     * @param string $value   缓存值 如果不输入值 则根据缓存名返回缓存值.
     * @param int    $expires 缓存过期时间 默认0 即永不超时. 单位秒
     *
     * @return mixed
     */
    public static function cache($name, $value = '', $expires = 0)
    {
        return S("WeixinApi" . $name, $value, $expires);
    }
}
