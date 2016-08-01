<?php

namespace Org\WeChatServiceAuth\Auth;

use Org\WeixinApi\Api;

class MainAuth extends BaseAuth
{
    private $baseUrl;

    const URL_GETAPPINSTALL_INFO = '/Main/Company/getappinstall/apiType/Info'; //
    const URL_GETCOMPANY_BYCID   = '/Main/Company/getcompany/apiType/Bycid';

    const URL_GETSUITE_CODE = '/Main/Suite/getsuite/apiType/Code'; //
    const URL_GETSUITE_AUTH = '/Main/Suite/getsuite/apiType/Auth'; //
    const URL_GETSUITE_ID   = '/Main/Suite/getsuite/apiType/Id'; //

    public function __construct()
    {
        $this->baseUrl = C('HTTP_BASE') . C('O365_APP_SERVICE_DOMAIN');
    }

    public function getMainAppInfo($array)
    {
        $t = '/cid/' . $array['cid'] . '/appid/' . $array['appid']; //可能需要改进为加密字符串

        $url = $this->baseUrl . self::URL_GETAPPINSTALL_INFO . $t;

        $param  = [];
        $Header = [];
        $list   = self::post($url, $param, $Header);
        $result = $this->explainApiToJson($list);

        return $result;
    }

    public function getMainCompanyBycid($array)
    {
        $t = '/cid/' . $array['cid']; //可能需要改进为加密字符串

        $url = $this->baseUrl . self::URL_GETCOMPANY_BYCID . $t;

        $param  = [];
        $Header = [];
        $list   = self::post($url, $param, $Header);
        $result = $this->explainApiToJson($list);

        return $result;
    }

    public function getMainAppInstall()
    {
        $t = '/cid/' . $array['cid']; //可能需要改进为加密字符串

        $url = $this->baseUrl . self::URL_GETCOMPANY_BYCID . $t;

        $param  = [];
        $Header = [];
        $list   = self::post($url, $param, $Header);
        $result = $this->explainApiToJson($list);

        return $result;
    }

    public function getSuiteCode($array)
    {
        $t = '/sid/' . $array['sid'];

        $url = $this->baseUrl . self::URL_GETSUITE_CODE . $t;

        $postData        = [];
        $postData['sid'] = $result['sid'];

        $list = self::post($url, $postData);

        $result = $this->explainApiToJson($list);

        return $result;
    }

    public function getSuiteAuth($array)
    {
        $t = '/cid/' . $array['cid'] . '/sid/' . $array['sid'];

        $postData = [];

        $url = $this->baseUrl . self::URL_GETSUITE_AUTH . $t;

        $list = self::post($url, $postData);

        $result = $this->explainApiToJson($list);

        return $result;
    }

    public function setSuiteCorpApi($array)
    {
        if ($array['cid'] && !$array['corpid']) {
            $res             = $this->getMainCompanyBycid($array);
            $array['corpid'] = $res['corp_id'];
        }

        $corpid = $array['corpid'];

        $suite = $this->getSuiteCode($array);

        $array['sid'] = $suite['sid'];

        $companyAuth = $this->getSuiteAuth($array);

        $data                   = [];
        $data['suite_id']       = $suite['suite_id'];
        $data['suite_secret']   = $suite['secret'];
        $data['suite_ticket']   = $suite['ticket'];
        $data['permanent_code'] = $companyAuth['permanent_code'];

        Api::init($corpid, $corpid . '-' . $data['suite_id'], $data);

        return $data;
    }

}
