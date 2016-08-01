<?php

namespace Project\Controller;

use Firebase\JWT\JWT;
use Org\Util\ApiErrCode;
use Org\Util\HttpClient;
use Think\Log;


/**
 * Wechat模块公共控制器.
 *
 * @author Li Zhi
 */
class ApiCommonController extends CommonController
{
    protected $wechat_id = 789632145;
    protected $cid       = 29433;
    protected $corp_id   = '';
    protected $uid;
    protected $sign                = ''; //jwt认证
    protected $data                = [];
    protected $request             = null; //请求参数进行log存储/调用
    protected $result_url          = [];
    protected $returnParam         = null; //返回参数进行log存储
    protected static $errorCode    = 200; //api错误码
    protected static $errorMessage = ""; //api错误提示

    const URL_NEWS_PUSH_INFTERFACE = '/message/api/broadcast/user'; //

    protected function _initialize()
    {
        $this->_formatResources();

    }

    protected function _jwtCompile($data = [], $key = '')
    {
        Vendor('Jwt.JWT');
        $key = $this->_formatJwtKey($key);

        if (!empty($data)) {
            return JWT::encode($data, $key);
        }

        return false;
    }

    protected function _jwtExplain($sign = '', $key = '', $code = 'HS256')
    {
        if (empty($sign)) {
            $sign = $this->sign;
        }

        if (empty($sign)) {
            self::$errorCode    = ApiErrCode::ERR_UNDEFIND_KEY;
            self::$errorMessage = 'sign为空!';
            $this->apiToJson();
        }

        Vendor('Jwt.JWT');

        $key = $this->_formatJwtKey($key);

        $code = [$code];

        if (!empty($sign)) {
            return JWT::decode($sign, $key, $code);
        }

        return false;
    }

    protected function _formatJwtKey($key)
    {
        return base64_decode((empty($key)) ? C('JWT_KEY') : $key);
    }

    /**
     * [_getVarsNames  获取类中属性]
     *
     * @author Yi
     *
     * @date   2016-06-01
     *
     * @return array
     */
    protected function _getVarsNames()
    {
        return get_class_vars(__CLASS__);
    }

    /**
     * [_getResources  返回post/get请求的数据]
     *
     * @author Yi
     *
     * @date   2016-06-01
     *
     * @return array
     */
    protected function _getResources()
    {
        if (IS_GET) {
            return $data = $this->_isJsonOwn($_GET);
        } elseif (IS_POST) {
            return $data = $this->_isJsonOwn($_POST);
        }

    }

    /**
     * [_formatResources  对类中的属性值进行赋值]
     *
     * @author Yi
     *
     * @date   2016-06-01
     *
     */
    protected function _formatResources()
    {
        $request = $this->_getResources();

        $classVars = $this->_getVarsNames();

        $result = [];

        foreach ($classVars as $ckey => $cval) {
            if (!empty($request[$ckey])) {
                $this->$ckey    = $request[$ckey];
                $result[$ckey]  = $request[$ckey];
                $request[$ckey] = null;unset($request[$ckey]);
            }
        }

        $this->data     = array_merge($this->data, $request);
        $result['data'] = array_merge($this->data, $request);
        $this->request  = $result;
    }

    protected function _isGetCheck()
    {
        if (!IS_GET) {
            self::$errorCode    = 401;
            self::$errorMessage = "不是GET请求!该接口需要GET请求方式!";
            $this->apiToJson();
        }
    }

    protected function _isPostCheck()
    {
        if (!IS_POST) {
            self::$errorCode    = 401;
            self::$errorMessage = "不是POST请求!该接口需要POST请求方式!";
            $this->apiToJson();
        }
    }

    /**
     * [_getVars  返回格式化后的请求参数]
     *
     * @author Yi
     *
     * @date   2016-06-01
     *
     * @return array
     */
    public function _getVars()
    {
        return $this->request;
    }

    /**
     * [_getVars  返回格式化后的请求参数]
     *
     * @author Yi
     *
     * @date   2016-06-01
     *
     * @return array
     */
    public function _getreturnParam()
    {
        return $this->returnParam;
    }

    public function _isJsonOwn($data)
    {
        $decode = json_decode($data, ture);

        if (is_null($decode))
//            $this->msgForJQM('非JSON格式数据!');
        {
            return $data;
        } else {
            return $decode;
        }

    }

    public function _before_run()
    {
        //echo 'before_' . ACTION_NAME;
        //$this->checkIp();
        self::$errorCode = ApiErrCode::SUCCESS_CODE;

        $cid               = I('get.cid', 0);
        $cid && $this->cid = $cid;

        $uid               = I('post.uid', 0);
        $uid && $this->uid = $uid;
    }

    public function _after_run()
    {
        //echo 'after_' . ACTION_NAME;
    }

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
        //$this->checkData($data);
        $content            = [];
        $content["status"]  = self::$errorCode;
        $content["message"] = self::$errorMessage ? self::$errorMessage : ApiErrCode::getErrMsgByCode($content["status"]);
        $content["data"]    = $data;

        $this->returnParam = $content;

        echo $this->_jsonEncode($content);
        exit;
    }

    /**
     * [checkIp 预留数据检查]
     *
     * @author Li Zhi
     *
     * @date   2016-06-01
     *
     * @return [type]
     */
    private function checkData($data)
    {
        return true;
    }

    /**
     * [checkIp 预留ip检查]
     *
     * @author Li Zhi
     *
     * @date   2016-06-01
     *
     * @return [type]
     */
    private function checkIp()
    {
        return true;
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

    /**
     * 前端消息推送接口
     *
     * @author Yi
     *
     * @date   2015-10-02
     *
     * @param  array     $data 待编码的数据
     *
     * @return string
     */
    public function _newsPushInterface($data)
    {

        $url = 'http://'.C('O365_SERVICE_DOMAIN') .':8888'. self::URL_NEWS_PUSH_INFTERFACE;
//        echo $url = 'http://192.168.199.149:8888'. self::URL_NEWS_PUSH_INFTERFACE;
//        echo $url = 'http://192.168.199.236:8888'. self::URL_NEWS_PUSH_INFTERFACE;

        if (empty($data)) {
            self::$errorCode    = ApiErrCode::ERR_UNDEFIND_KEY;
            self::$errorMessage = '向前端推送的消息为空!';
            $this->apiToJson();
        }


        $data['content']    = json_encode($data['content'],JSON_UNESCAPED_UNICODE);

        $postData           = http_build_query($data);

        $res                = HttpClient::post($url, $postData);

        $log['appName']         = APP_NAME;
        $log['moduleName']      = MODULE_NAME;
        $log['controllerName']  = CONTROLLER_NAME;
        $log['actionName']      = ACTION_NAME;
        $log['url']             = $url;
        $log['data']            = $data;
        $log['res']             = $res;

        Log::write(json_encode($log, JSON_UNESCAPED_UNICODE));

        return $res;
    }

}
