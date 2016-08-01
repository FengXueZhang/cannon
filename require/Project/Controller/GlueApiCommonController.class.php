<?php

namespace Project\Controller;

use Org\WeChatServiceAuth\Auth;
use Think\Log;

class GlueApiCommonController extends ApiCommonController
{
    protected $user_register_api     = 'http://o365-uc.weflame.com/auth/index/postIndexMemberRegister';
    protected $check_user_exists_api = 'http://o365-uc.weflame.com/auth/index/postIndexCheckMemberExists';

    protected static $userInfokey = 'USER_INFO_KEY'; // 企业号corp_secrect

    public function _initialize()
    {
        parent::_initialize();

        $this->checkJwt();
    }

    private function getAuthorizationBearer()
    {

        $AUTHORIZATION = $_SERVER['HTTP_AUTHORIZATION'];

        $sign = null;

        list($Bearer, $sign) = explode(' ', $AUTHORIZATION);

        return $sign;
    }

    public function checkJwt()
    {
        $sign = $this->getAuthorizationBearer();

        if (strlen($sign) > 80) {
            $userDate = $this->_jwtExplain($sign);

            if (empty($sign) || !empty($userDate->status) || empty($userDate->uid)) {
                self::$errorCode    = $userDate->status;
                self::$errorMessage = $userDate->message;
                $this->apiToJson();
            }

            $this->uid                = $userDate->uid;
            $this->request['uid']     = $userDate->uid;
            $this->cid                = $userDate->cid;
            $this->request['cid']     = $userDate->cid;
            $this->corp_id            = $userDate->corp_id;
            $this->request['corp_id'] = $userDate->corp_id;
        } else {
            $this->uid                = $sign;
            $this->request['uid']     = $sign;
            $this->cid                = '29433';
            $this->request['cid']     = '29433';
            $this->corp_id            = 'wx56fe9d4af5698818';
            $this->request['corp_id'] = 'wx56fe9d4af5698818';
        }
    }

    private function apiLog()
    {

        $log['appName']        = APP_NAME;
        $log['moduleName']     = MODULE_NAME;
        $log['controllerName'] = CONTROLLER_NAME;
        $log['actionName']     = ACTION_NAME;
        $log['requestParam']   = $this->_getVars();
        $log['returnParam']    = $this->_getreturnParam();

        Log::write(json_encode($log, JSON_UNESCAPED_UNICODE));
    }

    public function __destruct()
    {
        $this->apiLog();
    }

    /**
     * 返回数据方法
     *
     * @param  int    $errCode 返回的状态码
     * @param  string $errMsg  返回的提示信息
     * @param  array  $body    返回的内容
     */
    final protected function response($errCode, $errMsg, $body = [])
    {
        if (!$errCode || !$errMsg) {
            E('err_code 或 err_msg 必填');
        }

        $response            = [];
        $response['status']  = $errCode;
        $response['message'] = $errMsg;
        if (!empty($body) && is_array($body)) {
            $response['data'] = $body;
        }

        if (!defined('JSON_UNESCAPED_UNICODE')) {
            // 解决php 5.3版本 json转码时 中文编码问题.
            $response = json_encode($response);
            $response = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $response);
        } else {
            $response = json_encode($response, JSON_UNESCAPED_UNICODE);
        }
        exit($response);
    }
}