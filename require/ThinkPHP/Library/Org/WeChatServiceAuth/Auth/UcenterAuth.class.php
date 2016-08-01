<?php

namespace Org\WeChatServiceAuth\Auth;

use Org\OfficeApi\Api as OfficeApi;
use Org\Util\ApiErrCode;
use Org\WeChatServiceAuth\Auth;
use Org\WeixinApi\Api;
use Think\Log;

class UcenterAuth extends BaseAuth
{
    private $baseUrl; //ucenter Office层 公共地址

    const WEFLAME_UCENTER_DOMAIN    = 'http://o365-uc.weflame.com/';
    const URL_GET_COMPANY_ID        = '/Main/Company/getcompany/apiType/Bycorpid'; //
    const URL_CLOUD_CREATE_ROOT_DIR = '/Cloud/Interface/createRootDir'; //
    const URL_UCENTER_USER_INFO     = '/auth/index/postIndexCheckMemberExists'; //
    const URL_UCENTER_REGISTER_API  = '/auth/index/postIndexMemberRegister'; //
    const URL_CHECK_USER_EXESIT     = '/auth/index/postIndexCheckMemberExists'; //
    const URL_UCENTER_CHANGEWALL    = '/auth/index/postIndexChangeWall'; //
    const URL_GET_DG_INFO_BY_CID    = '/Main/Company/getcompany/apiType/Dg/cid/'; //

    const URL_CREATE_ROOT_DIR       = '/Cloud/CloudInterface/createrootdir';
    const URL_ORGAN_REGISTER        = '/Organ/Member/postmember/apiType/Info'; //
    const URL_ORGAN_GETMEMBER       = '/Organ/Member/getmember/apiType/List'; //

    public function __construct()
    {
        $this->baseUrl = C('HTTP_BASE');
    }

    /**
     * 获取用户部分信息 来源:ucenter
     *
     * @param   $uid
     *
     * @return  mixed
     */
    public function getUserPartDetail($uid)
    {
        $userInfo = $this->getOrganInfo($uid);

        if ($userInfo['status'] == 419 || $userInfo == null) {
            self::$errorCode    = 419;
            self::$errorMessage = '用户不存在!';
            $this->apiToJson();
        }

        $res['uid']             = $userInfo['uid'];
        $res['image_url']       = $userInfo['img_url'];
        $res['avatar']          = $userInfo['avatar'];
        $res['img_base_code']   = $userInfo['img_base_code'];
        $res['username']        = $userInfo['name'];

        return $res;
    }

    /**
     * 获取用户全部信息 来源:ucenter
     *
     * @param   $uid
     *
     * @return  mixed
     */
    public function getUserDetail($uid)
    {
        $userInfo = $this->getOrganInfo($uid);

        if ($userInfo['status'] == 419 || $userInfo == null) {
            self::$errorCode    = 419;
            self::$errorMessage = '用户不存在!';
            $this->apiToJson();
        }

        return $userInfo;
    }

    /**
     * 请求获取用户信息接口 来源:ucenter
     *
     * @param   $uid
     *
     * @return  mixed
     */
    public function getUcInfo($uid)
    {
        $params['uid'] = $uid;

        $url = $this->baseUrl . C('O365_UC_SERVICE_DOMAIN') . self::URL_UCENTER_USER_INFO;

        $response = self::post($url, http_build_query($params));

        return $this->explainApiToJson($response);
    }

    /**
     * 请求获取用户信息接口 来源:Organ
     *
     * @param   $uid
     *
     * @return  mixed
     */
    public function getOrganInfo($uid)
    {
        $params['uid'] = $uid;

        $url = $this->baseUrl . C('O365_APP_SERVICE_DOMAIN') . self::URL_ORGAN_GETMEMBER;

        $response = self::post($url, http_build_query($params));

        $resData = $this->explainApiToJson($response);

        return array_shift($resData);
    }

    /**
     * 根据微信CorpID 获取公司信息
     *
     * @param   $corpid
     *
     * @return  mixed
     */
    public function getCidByCorpid($corpid)
    {
        if (empty($corpid)) {
            self::$errorCode    = ApiErrCode::ERR_UNDEFIND_KEY;
            self::$errorMessage = 'corpid为空!';
            $this->apiToJson();
        }

        $url = $this->baseUrl . C('O365_APP_SERVICE_DOMAIN') . self::URL_GET_COMPANY_ID;

        $res = self::get($url, array('corpid' => $corpid));

        return $this->explainApiToJson($res);
    }

    /**
     * 根据微信CorpID 获取公司信息
     *
     * @param   $corpid
     *
     * @return  mixed
     */
    public function getDgInfoByCid($cid)
    {
        if (empty($cid)) {
            self::$errorCode    = ApiErrCode::ERR_UNDEFIND_KEY;
            self::$errorMessage = 'cid为空!';
            $this->apiToJson();
        }

        $url = $this->baseUrl . C('O365_APP_SERVICE_DOMAIN') . self::URL_GET_DG_INFO_BY_CID . $cid;

        $res = self::get($url);

        return $this->explainApiToJson($res);
    }

    /**
     * 项目注册方法入口
     *
     * @param   $data
     *
     * @return  null
     */
    public function checkOAuth($data)
    {
        define('O365_CORP_ID', $data['comInfo']['corp_id']);
        define('O365_CORP_SECRET', $data['comInfo']['secret_id']);

        Api::init(O365_CORP_ID, O365_CORP_SECRET, $data['suite']);

        if (!$data['uid']) {
            $uid = $this->userRegister($data);
            Log::write('checkOAuth :' . $uid, '', '', '/tmp/err.log');
        } else {
            $uid = $this->checkUserExists($data['uid']);
            if (!$uid) {
                $uid = $this->userRegister($data);
            }
        }

        return $uid;
    }

    /**
     * 用户注册
     *
     * @param   $data
     *
     * @return  null
     */
    public function userRegister($data)
    {
        $userId    = $this->getUserId();
        $user_info = $this->getUserInfo($userId);

        $url    = $this->baseUrl . C('O365_UC_SERVICE_DOMAIN') . self::URL_UCENTER_REGISTER_API;
        $url_cw = $this->baseUrl . C('O365_UC_SERVICE_DOMAIN') . self::URL_UCENTER_CHANGEWALL;

        $params['corp_id'] = O365_CORP_ID;
        $params['channel'] = 'wx_oauth';
        $params['extra']   = $user_info;
        $params['is_wall'] = 0;

        $response = self::post($url, http_build_query($params));
        $result   = json_decode($response['body'], true);

        $regData        = array_merge($user_info, $data['comInfo']);
        $regData['uid'] = $result['uid'];

        $this->appRegister($regData);

        $params            = [];
        $params['uid']     = $result['uid'];
        $params['is_wall'] = 1;

        $res = self::post($url_cw, $params);

        if (200 == $result['status']) {
            return $result['uid'];
        } else {
            return null;
        }
    }

    /**
     * 获取用户id
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getUserId()
    {
        $UserWxApi = Api::factory('User');
        $UserWxApi->request(__SELF__, '', 'O365_SERVICE_DOMAIN'); //发起OAuth请求.

        $OAuthRes = $UserWxApi->receive(); //接收结果;
        if (false === $OAuthRes) {
            if (method_exists($this, '_oauth_failed')) {
                // 如果子类定义了 验证失败的回调方法则执行
                $this->_oauth_failed();
            }

        }
        return $OAuthRes['userid'];
    }

    /**
     * 检查用户是否存在
     *
     * @param $uid
     *
     * @return null
     */
    public function checkUserExists($uid)
    {
        $params['uid'] = $uid;

        $url = $this->baseUrl . C('O365_UC_SERVICE_DOMAIN') . self::URL_CHECK_USER_EXESIT;

        $response = self::post($url, http_build_query($params));
        $result   = json_decode($response['body'], true);

        if (200 != $result['status']) {
            return null;
        } else {
            return $result['uid'];
        }
    }

    /**
     * 获取用户信息
     *
     * @param   $userId     wechat userid
     *
     * @return  mixed       array  userinfo
     *
     * @throws  \Exception
     */
    public function getUserInfo($userId)
    {
        $user = Api::factory('User');

        $userInfo = $user->getInfoById($userId);

        if (!$userInfo) {
            self::$errorCode    = 405;
            self::$errorMessage = '用户信息获取失败1';
            $this->apiToJson();
        }

        return $userInfo;

    }

    /**
     * 获取AccessToken
     *
     * @return  string      accessToken
     *
     * @throws  \Exception
     */
    public function getAccessToken()
    {
        $accessToken = Api::getAccessToken();

        if (!$accessToken) {
            self::$errorCode    = 498;
            self::$errorMessage = '微信用户信息获取失败!';
            $this->apiToJson();
        }

        return $accessToken;
    }

    /**
     * 应用注册总方法
     *
     * @param $data     register info: personal & company
     */
    public function appRegister($data)
    {
        //云盘默认注册
        $this->cloudRegister($data);
        //通讯录默认注册
        $this->organRegister($data);

    }

    /**
     * 云盘注册
     *
     * @param   $data
     *
     * @throws \Exception
     */
    public function cloudRegister($data)
    {
        $postData['uid']          = $data['uid'];
        $postData['cid']          = $data['cid'];
        $postData['folder_class'] = 1;

        $url = $this->baseUrl . C('O365_APP_SERVICE_DOMAIN') . self::URL_CREATE_ROOT_DIR;
        $res = self::post($url,$postData);
    }

    /**
     * 通讯录注册
     *
     * @param   $data
     *
     * @return  mixed
     */
    public function organRegister($data)
    {
        $url = $this->baseUrl . C('O365_APP_SERVICE_DOMAIN') . self::URL_ORGAN_REGISTER;

        $postData['uid']          = $data['uid'];
        $postData['cid']          = $data['cid'];
        $postData['account']      = $data['userid'];
        $postData['name']         = $data['name'];
        $postData['position']     = $data['position'];
        $postData['gender']       = $data['gender'];
        $postData['email']        = $data['email'];
        $postData['mobile']       = $data['mobile'];
        $postData['weixinid']     = $data['weixinid'];
        $postData['img_url']      = $data['avatar'];
        $postData['is_attention'] = $data['status'];

        $res    = self::post($url, $postData);
        $result = json_decode($res['body'], true);

        return $result;
    }

}
