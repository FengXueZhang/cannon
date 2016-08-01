<?php

namespace Org\Util;
use Think\Log;
class HttpClient
{
    private static $error = ''; // 错误信息;

    /**
     * 用get的方式访问接口.
     *
     * @author Cui
     *
     * @date   2015-07-29
     *
     * @param string $url      目标地址
     * @param array  $queryStr 查询字符串
     * @param array  $header   http头部附加信息
     *
     * @return array 错误时返回false
     */
    public static function get($url, $queryStr = array(), $header = array())
    {
        if (!$url) {
            E('参数错误!');
        }

        $queryStr = http_build_query($queryStr);
        if ($queryStr) {
            $url .= '?' . $queryStr;
        }

        $header[] = "Expect:";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $res = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $header = '';
        $body = $res;

        list($header, $body) = explode("\r\n\r\n", $res, 2);
        $header = self::parseHeaders($header);

        $result['body'] = $body;
        $result['header'] = $header;
        $result['status'] = $httpcode;

        return $result;
    }

    /**
     * 用post的方式访问接口.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @param string $url      目标地址
     * @param array  $data 要发送的数据
     * @param array  $header   http头部附加信息
     * @param array  $queryStr 查询字符串
     *
     * @return array 错误时返回false;
     */
    public static function post($url, $data = array(), $header = array(), $queryStr = array())
    {
        if (!$url) {
            E('参数错误!');
        }

        if ($queryStr) {
            $queryStr = http_build_query($queryStr);
            $url .= '?' . $queryStr;
        }

        $header[] = "Expect:";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

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

        $res = trim(curl_exec($ch));
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $header = '';
        $body = $res;

        list($header, $body) = explode("\r\n\r\n", $res, 2);
        $header = self::parseHeaders($header);

        $result['body'] = $body;
        $result['header'] = $header;
        $result['status'] = $httpcode;

        return $result;
    }


    /**
     * 用patch的方式访问接口.
     *
     * @author Yi
     *
     * @date   2016-04-26
     *
     * @param string $url      目标地址
     * @param array  $data 要发送的数据
     * @param array  $header   http头部附加信息
     * @param array  $queryStr 查询字符串
     *
     * @return array 错误时返回false
     */
    public static function patch($url, $data = array(), $header = array(), $queryStr = array())
    {
        if (!$url) {
            E('参数错误!');
        }

        $queryStr = http_build_query($queryStr);
        if ($queryStr) {
            $url .= '?' . $queryStr;
        }

        $header[] = "Expect:";
        $path = "/tmp/err.log";
        
        //$data = json_encode($data, JSON_UNESCAPED_UNICODE);
        Log::write(print_r($data, true), "", "", $path);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $res = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $header = '';
        $body = $res;

        list($header, $body) = explode("\r\n\r\n", $res, 2);
        $header = self::parseHeaders($header);
        
        $result['body'] = $body;
        $result['header'] = $header;
        $result['status'] = $httpcode;

        return $result;
    }


    /**
     * 用delete的方式访问接口.
     *
     * @author WangXueChen
     *
     * @date   2016-04-26
     *
     * @param string $url      目标地址
     * @param array  $data     要发送的数据
     * @param array  $header   http头部附加信息
     * @param array  $queryStr 查询字符串
     *
     * @return array 错误时返回false
     */
    public static function delete($url, $data = array(), $header = array(), $queryStr = array())
    {
        if (!$url) {
            E('参数错误!');
        }

        $queryStr = http_build_query($queryStr);
        if ($queryStr) {
            $url .= '?' . $queryStr;
        }

        $header[] = "Expect:";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $res = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $header = '';
        $body = $res;

        list($header, $body) = explode("\r\n\r\n", $res, 2);
        $header = self::parseHeaders($header);

        $result['body'] = $body;
        $result['header'] = $header;
        $result['status'] = $httpcode;

        return $result;
    }


    /**
     * 用put的方式访问接口.
     *
     * @author WangXueChen
     *
     * @date   2016-04-26
     *
     * @param string $url      目标地址
     * @param array  $data     要发送的数据
     * @param array  $header   http头部附加信息
     * @param array  $queryStr 查询字符串
     *
     * @return array 错误时返回false
     */
    public static function put($url, $data = array(), $header = array(), $queryStr = array())
    {
        if (!$url) {
            E('参数错误!');
        }

        $queryStr = http_build_query($queryStr);
        if ($queryStr) {
            $url .= '?' . $queryStr;
        }

        $header[] = "Expect:";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $res = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $header = '';
        $body = $res;

        list($header, $body) = explode("\r\n\r\n", $res, 2);
        $header = self::parseHeaders($header);

        $result['body'] = $body;
        $result['header'] = $header;
        $result['status'] = $httpcode;

        return $result;
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

        $headers = array();
        $key = '';

        foreach (explode("\n", $raw_headers) as $i => $h) {
            $h = explode(':', $h, 2);

            if (isset($h[1])) {
                if (!isset($headers[$h[0]])) {
                    $headers[$h[0]] = trim($h[1]);
                } elseif (is_array($headers[$h[0]])) {
                    $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1])));
                } else {
                    $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1])));
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
}
