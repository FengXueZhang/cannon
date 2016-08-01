<?php

namespace Org\WeChatServiceAuth\Auth;

use Org\WeChatServiceAuth\Auth;
use Org\Util\HttpClient;
use Org\Util\ApiErrCode;

class BaseAuth
{
    public static $errorCode    = 200; //api错误码
    public static $errorMessage = ""; //api错误提示
    const COME_URL = 'COME_URL';

    protected $returnParam         = null;    //返回参数进行log存储


    /**
     * [apiToJson  数据出口，整理校验]
     *
     * @author Li Zhi
     *
     * @date   2016-06-01
     *
     * @param  array      $data    [接口数据]
     *
     * @return [type]
     */
    public function apiToJson($data = [])
    {
        header("Content-type: text/json;charset=utf8");
        $content            = [];
        $content["status"]  = self::$errorCode;
        $content["message"] = self::$errorMessage ? self::$errorMessage : ApiErrCode::getErrMsgByCode($content["status"]);
        $content["data"]    = $data;

        $this->returnParam  = $content;

        echo $this->_jsonEncode($content);
        exit;
    }

    /**
     * 组装POST所需要的json数据
     *
     * @author Cui
     *
     * @date   2015-10-02
     *
     * @param  array     $data 待编码的数据
     *
     * @return string
     */
    public function _jsonEncode($data)
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

    public function appRegister($array)
    {
        $url = 'http://o365-office.weflame.com/im/Interface/createImUser';

        $data=array(
            "wechat_id" => $array['openid'],
            "uc_id"     => $array['uid'],
            "cid"       => $array['cid'],
            "image_url" => $array['image_url']
        );

        $Header = array();
        $list   = HttpClient::post($url,$data,$Header);

        return $list['body'];
    }


    public function explainApiToJson( $res )
    {
        $res   = json_decode($res['body'],true);

        if ( $res['status'] != 200 ) {
            self::$errorCode        = $res['status'];
            self::$errorMessage     = $res['message'];
            $this->apiToJson();
        }

        return $res['data'];
    }

    /**
     * 公共跳转方法 主要是为了统一跳转,统一状态码;
     * 会根据当前请求是ajax还是普通的url访问来改变返回方式;
     * ajax返回状态码 status 200:成功 500:失败.
     *
     * @author Cui
     *
     * @param $info void 必须,返回的信息;
     * @param $mode string 成功OR失败 success || error;
     * @param $url string 需要跳转到的url ajax下无用,普通请求默认返回上一页
     * @param $mandatory boolean 强制使用url跳转;
     */
    final protected function location($info, $mode = 'success', $url = '', $mandatory = false)
    {

    }



    /**
     * 用post的方式访问接口.
     *
     * @author Yi
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
        return HttpClient::post($url, $data, $header, $queryStr);
    }


    /**
     * 用get的方式访问接口.
     *
     * @author Yi
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
        return HttpClient::get($url, $queryStr, $header);
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
        return HttpClient::patch($url, $data, $header, $queryStr);
    }


    /**
     * 用delete的方式访问接口.
     *
     * @author Yi
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
        return HttpClient::delete($url, $data, $header, $queryStr);
    }

    /**
     * 用put的方式访问接口.
     *
     * @author Yi
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
        return HttpClient::put($url, $data, $header, $queryStr);
    }

}