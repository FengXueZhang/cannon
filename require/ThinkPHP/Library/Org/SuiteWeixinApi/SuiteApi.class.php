<?php

namespace Org\SuiteWeixinApi;

/**
 * 微信API基类.
 *
 * @author Cui.
 */
class SuiteApi extends ApiConfig
{
    //企业号第三方配置参数
    private static $suite_id; #应用套件id
    //private $suite_secret;
    //private $suite_ticket;
    private static $suite_access_token;
    //private $auth_corpid; #授权方corpid
    //private $permanent_code; #永久授权码，通过get_permanent_code获取
    private $url;
    private $json;
    private $errorCode;
    private $pre_auth_code;
    private static $instance;

    public static function getInstance($suiteid = '', $suite_access_token = '')
    {
        self::$suite_id           = $suiteid;
        self::$suite_access_token = $suite_access_token;

        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function sendWx()
    {
        if (self::$suite_access_token) {
            $this->url .= 'suite_access_token=' . self::$suite_access_token;
        }

        # 发送数据;
        $res = PostCurl::getInstance()->send($this->url, $this->json);

        return $res;
    }

    /**
     * [getSuiteToken 用于获取应用套件令牌（suite_access_token）.].
     *
     * @author Li Zhi
     *
     * @date   2016-06-02
     *
     * @param [type] $data [description]
     *
     * @return [type]
     */
    public function getSuiteToken($data)
    {
        $this->url = $this->service . 'get_suite_token';

        $json                 = [];
        $json['suite_id']     = self::$suite_id;
        $json['suite_secret'] = $data['suite_secret'];
        $json['suite_ticket'] = $data['suite_ticket'];

        $this->url = self::URL_SERVICE_SUITETOKEN;

        $this->json = $this->jsonPregEncode($json);

        return $this;
    }

    /**
     * [getPreAuthCode 获取第三方 预授权码]
     *
     * @author Li Zhi
     *
     * @date   2016-06-15
     *
     * @param  [type]     $data [description]
     *
     * @return [type]
     */
    public function getPreAuthCode($data)
    {
        $json             = [];
        $json['suite_id'] = $data['suite_id'];

        $this->url = self::URL_SERVICE_RPEAUTHCODE;

        $this->json = $this->jsonPregEncode($json);

        return $this;
    }

    /**
     * [setSessionInfo 设置第三方 预授权码]
     *
     * @author Li Zhi
     *
     * @date   2016-06-15
     *
     * @param  [type]     $data [description]
     */
    public function setSessionInfo($data)
    {
        $json                              = [];
        $json['pre_auth_code']             = $data['pre_auth_code'];
        $json['session_info']              = [];
        $json['session_info']['appid']     = $data['session_info']['appid'];
        $json['session_info']['auth_type'] = C('SUITE_AUTH_TYPE'); //授权类型：0 正式授权， 1 测试授权， 默认值为0

        $this->url = self::URL_SERVICE_SETSESSIONINFO;

        $this->json = $this->jsonPregEncode($json);

        return $this;
    }

    /**
     * [createAuthUrl 创建授权链接]
     *
     * @author Li Zhi
     *
     * @date   2016-06-15
     *
     * @param  [type]     $data [description]
     *
     * @return [type]
     */
    public function createAuthUrl($data)
    {
        $replacements = [self::$suite_id, $data['pre_auth_code'], $data['redirect_uri'], $data['state']];
        $patterns     = ['/\$suite_id/', '/\$pre_auth_code/', '/\$redirect_uri/', '/\$state/'];

        $url = preg_replace($patterns, $replacements, self::URL_AUTH_URL);

        return $url;
    }

    /**
     * [getPermanentCode 获取永久授权码]
     *
     * @author Li Zhi
     *
     * @date   2016-06-15
     *
     * @param  [type]     $auth_code [description]
     *
     * @return [type]
     */
    public function getPermanentCode($auth_code)
    {
        $json              = [];
        $json['suite_id']  = self::$suite_id;
        $json['auth_code'] = $auth_code;

        $this->url = self::URL_SERVICE_PERMANENTCODE;

        $this->json = $this->jsonPregEncode($json);

        return $this;
    }

    /**
     * [getAuthInfo 获取企业号授权信息]
     *
     * @author Li Zhi
     *
     * @date   2016-06-15
     *
     * @param  [type]     $data [description]
     *
     * @return [type]
     */
    public function getAuthInfo($data)
    {
        $json                   = [];
        $json['suite_id']       = self::$suite_id;
        $json['auth_corpid']    = $data['auth_corpid'];
        $json['permanent_code'] = $data['permanent_code'];

        $this->url = self::URL_SERVICE_AUTHINFO;

        $this->json = $this->jsonPregEncode($json);

        return $this;
    }

    /**
     * [getCorpToken 获取企业号access_token]
     *
     * @author Li Zhi
     *
     * @date   2016-06-15
     *
     * @param  [type]     $data [description]
     *
     * @return [type]
     */
    public function getCorpToken($data)
    {
        $json                   = [];
        $json['suite_id']       = self::$suite_id;
        $json['auth_corpid']    = $data['auth_corpid'];
        $json['permanent_code'] = $data['permanent_code'];

        $this->url = self::URL_SERVICE_CORPTOKEN;

        $this->json = $this->jsonPregEncode($json);

        return $this;
    }

    /**
     * 控制json编译是汉子转码，微信不接收UNICODE字符.
     *
     * @author Li Zhi
     *
     * @date   2016-06-02
     *
     * @param array $data 要转json的数据
     *
     * @return json
     */
    public function jsonPregEncode($data)
    {
        if (!defined('JSON_UNESCAPED_UNICODE')) {
            // 解决php 5.3版本 json转码时 中文编码问题.
            $data = json_encode($data);
            $data = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $data);
        } else {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        }

        return $data;
    }
}
