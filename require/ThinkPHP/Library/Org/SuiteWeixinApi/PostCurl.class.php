<?php

namespace Org\SuiteWeixinApi;

/**
 * 封装对外请求类.
 */
class PostCurl
{
    public $error;
    public $errorCode;
    private static $instance;

    /**
     * 防止创建对象
     */
    private function __construct()
    {
    }

    //阻止用户复制对象实例
    private function __clone()
    {
    }

    //单例方法
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * 接口数据拼装.
     *
     * @author Li Zhi
     *
     * @date   2015-09-01
     *
     * @return [type]
     */
    public function send($url, $data = '')
    {

        //使用 post_url 传参
        $result = $this->postUrl($url, $data);

        if ($result['httpcode'] != 200) {
            $this->error = '与服务器连接失败';

            return false;
        }

        $res = json_decode($result['result'], true);

        if (isset($res['errcode']) && $res['errcode'] != 0) {
            $this->error     = $res['errmsg'];
            $this->errorCode = $res['errcode'];

            return false;
        }

        return $res;
    }

    /**
     * 向$URL POST数据;.
     */
    public function postUrl($URL, $param = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $res = trim(curl_exec($ch));
       /* echo '<pre>';
        var_dump($res);*/
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result             = array();
        $result['result']   = $res;
        $result['httpcode'] = $httpcode;

        return $result;
    }
}
