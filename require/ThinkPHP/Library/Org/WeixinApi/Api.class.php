<?php

namespace Org\WeixinApi;

use Org\SuiteWeixinApi\SuiteApi;
use Org\WeixinApi\API\BaseApi;
use Think\Log;

/**
 * 微信API基类.
 *
 * @author Cui.
 */
class Api
{
    private static $error           = ''; // 错误信息;
    private static $selfInstanceMap = []; // 实例列表;
    private static $CORP_ID; // 企业号corp_id;
    private static $CORP_SECRECT; // 企业号corp_secrect
    private static $CORP_SUITE_ID; //企业号第三方套件suite_id
    private static $CORP_SUITE_SECRET; //企业号第三方套件suite_secret
    private static $CORP_SUITE_TICKET; //企业号第三方套件suite_ticket
    private static $CORP_PERMANENT_CODE; //企业号第三方套件permanent_code
    private static $CACHE_KEY_PRE; // 接口缓存驱动类名
    private static $postQueryStr = []; // post数据时 需要携带的查询字符串
    private static $cutTime      = 600;

    const WEIXIN_BASE_API = 'https://qyapi.weixin.qq.com/cgi-bin';

    /**
     * 接口初始化, 必须执行此方法才可以使用接口.
     *
     * @author Cui
     *
     * @date   2015-07-29
     *
     * @param string $corpid      企业号corp_id
     * @param string $corpsecret  企业号corp_secrect
     * @param string $cacheDriver 接口缓存驱动类名
     */
    public static function init($corpid, $corpsecret, $suite = [])
    {
        self::$CORP_ID       = $corpid;
        self::$CORP_SECRECT  = $corpsecret;
        self::$CACHE_KEY_PRE = 'WeixinApi:';

        if ($suite['suite_id'] && $suite['suite_secret'] && $suite['suite_ticket'] && $suite['permanent_code']) {
            self::$CORP_SUITE_ID       = $suite['suite_id'];
            self::$CORP_SUITE_SECRET   = $suite['suite_secret'];
            self::$CORP_SUITE_TICKET   = $suite['suite_ticket'];
            self::$CORP_PERMANENT_CODE = $suite['permanent_code'];

        }

    }

    /**
     * 工厂+多例模式 获取接口实例.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @param string $className 接口类名.
     *
     * @return object
     */
    public static function factory($className)
    {
        $className = __NAMESPACE__ . '\\API\\' . $className . 'Api';
        if (!$className || !is_string($className)) {
            throw new \Exception('类名参数不正确', 1);
        }

        if (!class_exists($className)) {
            throw new \Exception($className . '接口不存在', 1);
        }

        if (!array_key_exists($className, self::$selfInstanceMap)) {
            $api = new $className();
            if (!$api instanceof BaseApi) {
                throw new \Exception($className . ' 必须继承 BaseApi', 1);
            }

            self::$selfInstanceMap[$className] = $api;
        }

        if (!self::$CORP_ID || !self::$CORP_SECRECT) {
            $corpid     = O365_CORP_ID;
            $corpsecret = O365_CORP_SECRET;

            self::init($corpid, $corpsecret);
        }

        return self::$selfInstanceMap[$className];
    }

    /**
     * 设置错误信息.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @param string $errorText 错误信息
     */
    public static function setError($errorText)
    {
        Log::write($errorText, Log::ERR);
        self::$error = $errorText;
    }

    /**
     * 获取错误信息.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @return string
     */
    public static function getError()
    {
        return self::$error;
    }

    /**
     * 设置post操作的get参数.
     *
     * @author Cui
     *
     * @date   2015-08-03
     *
     * @param string $name  参数名
     * @param string $value 值
     */
    public static function setPostQueryStr($name, $value)
    {
        self::$postQueryStr[$name] = $value;
    }

    /**
     * 获取当前操作企业号的corpid.
     *
     * @author Cui
     *
     * @date   2015-07-29
     *
     * @return string
     */
    public static function getCorpId()
    {
        return self::$CORP_ID;
    }

    /**
     * 获取当前操作企业号的corpsecrect.
     *
     * @author Cui
     *
     * @date   2015-07-29
     *
     * @return string
     */
    public static function getSecrect()
    {
        return self::$CORP_SECRECT;
    }

    /**
     * 获取允许访问的token.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @return string
     */
    public static function getAccessToken()
    {
        if (!self::$CORP_SUITE_ID) {
            $key   = 'access_token-' . md5(self::$CORP_SECRECT);
            $token = self::cache($key);
            if (false == $token) {

                $corpId      = self::$CORP_ID;
                $corpSecrect = self::$CORP_SECRECT;
                $module      = 'gettoken';
                $queryStr    = [
                    'corpid'     => $corpId,
                    'corpsecret' => $corpSecrect,
                ];

                $res = self::_get($module, '', $queryStr);

                if (false === $res) {
                    throw new \Exception('获取AccessToken失败!', 1);
                }

                $token = $res['access_token'];
                self::cache($key, $token, 7200 - 300);
            }
        } else {
            $token = self::getSuiteAccessToken();
        }

        return $token;
    }

    public static function getSuiteAccessToken()
    {
        $corpId  = self::$CORP_ID;
        $suiteId = self::$CORP_SUITE_ID;

        /*$access_token = S('accessToken:' . $corpId . '_' . $suiteId);
        if ($access_token) {
        return $access_token;
        }*/

        $suiteAccessToken = self::getSuiteToken();

        $data                   = [];
        $data['permanent_code'] = self::$CORP_PERMANENT_CODE;
        $data['auth_corpid']    = self::$CORP_ID;

        $res = SuiteApi::getInstance($suiteId, $suiteAccessToken)->getCorpToken($data)->sendWx();

        if (0 != $res['errcode']) {
            Log::write('getSuiteAccessToken errorCode:' . $res['errcode'] . '; \n\r errMsg:' . $res['errmsg'], '', '', '/tmp/err_corpauth.log');

            return false;
        }
        $accessToken = $res['access_token'];

        $res = S('accessToken:' . $corpId . '_' . $suiteId, $accessToken, $res['expires_in'] - self::$cutTime);
        if (!$res) {
            Log::write('getSuiteAccessToken error', '', '', '/tmp/err_corpauth.log');

            return false;
        }
        return $accessToken;
    }

    /**
     * [getSuiteToken 获取套件token].
     *
     * @author Li Zhi
     *
     * @date   2016-06-12
     *
     * @return [type]
     */
    public static function getSuiteToken()
    {
        $suite_token_key = 'suite:suiteToken:' . self::$CORP_SUITE_ID;

        $suite_access_token = S($suite_token_key);

        if ($suite_access_token) {
            return $suite_access_token;
        }

        $param = [
            'suite_id'     => self::$CORP_SUITE_ID,
            'suite_secret' => self::$CORP_SUITE_SECRET,
            'suite_ticket' => self::$CORP_SUITE_TICKET,
        ];

        $res = SuiteApi::getInstance(self::$CORP_SUITE_ID)->getSuiteToken($param)->sendWx();

        if (0 != $res['errcode']) {
            Log::write('getSuiteToken errorCode:' . $res['errcode'] . '; \n\r errMsg:' . $res['errmsg'], '', '', '/tmp/err_corpauth.log');

            return false;
        }

        S($suite_token_key, $res['suite_access_token'], $res['expires_in'] - self::$cutTime);

        return $res['suite_access_token'];
    }

    /**
     * 用get的方式访问接口.
     *
     * @author Cui
     *
     * @date   2015-07-29
     *
     * @param string $module   指定接口模块
     * @param string $node     指定接口模块的节点
     * @param array  $queryStr 查询字符串
     * @param array  $header   http头部附加信息
     *
     * @return array 错误时返回false
     */
    public static function _get($module, $node = '', $queryStr = [], $header = [])
    {
        if ($module != 'gettoken') {
            $queryStr['access_token'] = self::getAccessToken();
            asort($queryStr);
        }

        $queryStr = http_build_query($queryStr);
        $apiUrl   = rtrim(self::WEIXIN_BASE_API . '/' . $module . '/' . $node, '/');
        $apiUrl .= '?' . $queryStr;

        $header[] = 'Bizmp-Version:2.0';

        Log::write('get接口调用 : ' . $apiUrl, Log::DEBUG);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $res      = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $header = '';
        $body   = $res;

        if ($httpcode == 200) {
            list($header, $body) = explode("\r\n\r\n", $res, 2);
            $header              = self::parseHeaders($header);
        }

        $result['info']   = $body;
        $result['header'] = $header;
        $result['status'] = $httpcode;

        return self::packData($result);
    }

    /**
     * 用post的方式访问接口.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @param string $module     指定接口模块
     * @param string $node       指定接口模块的节点
     * @param array  $data       要发送的数据
     * @param bool   $jsonEncode 是否转换为jsons数据
     *
     * @return array 错误时返回false;
     */
    public static function _post($module, $node = '', $data, $jsonEncode = true)
    {
        $token = self::getAccessToken();
        if (false === $token) {
            return false;
        }

        $postQueryStr                 = self::$postQueryStr;
        $postQueryStr['access_token'] = $token;
        asort($postQueryStr);

        $postQueryStr = http_build_query($postQueryStr);

        // 获取数据后 重置数据;
        self::$postQueryStr = [];

        $apiUrl = rtrim(self::WEIXIN_BASE_API . '/' . $module . '/' . $node, '/');
        $apiUrl .= '?' . $postQueryStr;

        if ($jsonEncode) {
            if (is_array($data)) {
                if (!defined('JSON_UNESCAPED_UNICODE')) {
                    // 解决php 5.3版本 json转码时 中文编码问题.
                    $data = json_encode($data);
                    $data = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $data);
                } else {
                    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
                }
            }
        }

        Log::write('post接口调用地址 : ' . $apiUrl, Log::DEBUG);
        Log::write('post接口调用数据 : ' . print_r($data, true), Log::DEBUG);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, false);

        // 对上传操作做的特殊判断
        if (class_exists('\CURLFile')) {
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
        } else {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
            }
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $res      = trim(curl_exec($ch));
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $header = '';
        $body   = $res;
        if ($httpcode == 200) {
            list($header, $body) = explode("\r\n\r\n", $res, 2);
            $header              = self::parseHeaders($header);
        }

        $result['info']   = $body;
        $result['header'] = $header;
        $result['status'] = $httpcode;

        return self::packData($result);
    }

    /**
     * 对接口返回的数据进行验证和组装.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @param array $apiReturnData 由_post|| _get方法返回的数据.
     *
     * @return array
     */
    private static function packData($apiReturnData)
    {
        if ($apiReturnData['status'] != 200) {
            self::setError('微信接口服务器连接失败.');

            return false;
        }

        $status        = $apiReturnData['status'];
        $info          = $apiReturnData['info'];
        $header        = $apiReturnData['header'];
        $apiReturnData = json_decode($info, true);

        $log             = [];
        $log['httpcode'] = $status;
        $log['response'] = $info;

        if ($status != 200 && !$apiReturnData) {
            self::setError($info);

            return false;
        }

        // 获取文件的特殊设置.
        if (!$apiReturnData) {
            $log['response']          = [];
            $apiReturnData            = [];
            $apiReturnData['content'] = base64_encode($info);
            $apiReturnData['type']    = $header['Content-Type'];
            $apiReturnData['size']    = $header['Content-Length'];

            if (isset($header['Content-disposition'])) {
                $res = preg_match('/".+"/', $header['Content-disposition'], $matchArr);

                if ($res && $matchArr) {
                    $apiReturnData['filename']   = reset($matchArr);
                    $log['response']['filename'] = $apiReturnData['filename'];
                }
            }

            $log['response']['type'] = $apiReturnData['type'];
            $log['response']['size'] = $apiReturnData['size'];
        }

        if (isset($apiReturnData['errcode']) && $apiReturnData['errcode'] != 0) {
            self::setError('错误码:' . $apiReturnData['errcode'] . ', 错误信息:' . $apiReturnData['errmsg']);

            return false;
        }

        if (isset($apiReturnData['errcode'])) {
            unset($apiReturnData['errcode']);
        }

        if (count($apiReturnData) > 1 && isset($apiReturnData['errmsg'])) {
            unset($apiReturnData['errmsg']);
        }

        if (count($apiReturnData) == 1) {
            $apiReturnData = reset($apiReturnData);
        }

        return $apiReturnData;
    }

    /**
     * 解析头部信息.
     *
     * @author 互联网
     *
     * @date   2015-08-03
     *
     * @param array $raw_headers http header
     *
     * @return array
     */
    public static function parseHeaders($raw_headers)
    {
        if (function_exists('http_parse_headers')) {
            return http_parse_headers($raw_headers);
        }

        $headers = [];
        $key     = '';

        foreach (explode("\n", $raw_headers) as $i => $h) {
            $h = explode(':', $h, 2);

            if (isset($h[1])) {
                if (!isset($headers[$h[0]])) {
                    $headers[$h[0]] = trim($h[1]);
                } elseif (is_array($headers[$h[0]])) {
                    $headers[$h[0]] = array_merge($headers[$h[0]], [trim($h[1])]);
                } else {
                    $headers[$h[0]] = array_merge([$headers[$h[0]]], [trim($h[1])]);
                }

                $key = $h[0];
            } else {
                if (substr($h[0], 0, 1) == "\t") {
                    $headers[$key] .= "\r\n\t" . trim($h[0]);
                } elseif (!$key) {
                    $headers[0] = trim($h[0]);
                }
                trim($h[0]);
            }
        }

        return $headers;
    }

    /**
     * 缓存方法.
     *
     * @author Cui
     *
     * @date   2015-07-29
     *
     * @param string $name    缓存名
     * @param string $value   缓存值 如果不输入值 则根据缓存名返回缓存值.
     * @param string $expires 缓存过期时间 默认0 即永不超时. 单位秒
     */
    public static function cache($name, $value = '', $expires = 0)
    {
        return S(self::$CACHE_KEY_PRE . $name, $value, $expires);
    }
}
