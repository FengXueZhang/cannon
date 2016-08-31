<?php
namespace Ucenter\Controller;

use Org\WeChatServiceAuth\Auth;
use Project\Controller\ApiCommonController;
use Org\WeixinApi\Api;
use Think\Log;
use Org\Wechat\Api as MpApi;

class CheckOAuthController extends ApiCommonController
{

    protected static $userInfokey = 'USER_INFO_KEY'; // 企业号corp_secrect

    private $Auth;

    public function _initialize()
    {
        parent::_initialize();
        $this->Auth = Auth::factory('WxEnterprise');
    }

    /**
     * 服务号Oauth
     *
     * @throws \Exception
     */
    public function checkFwhOAuth()
    {
        if (I('get.redirect_uri')) {
            $redirect_url = I('get.redirect_uri');
        }

        $oauthFwh = Api::factory('OauthFwh');
        $oauthFwh->request(__SELF__, '', C('SHOUFUBX_WAP_SERVICE_DOMAIN')); //发起OAuth请求.
        $result_oauth = $oauthFwh->receive(); //接收结果;

        if (I('get.uid')) {
            $uid                 = I('get.uid');
            $result_oauth['uid'] = $uid;
        }
        $result_oauth['channel'] = "WxAuth";
        $result_oauth['appid'] = C('MP_APP_ID');

        $relevance_result      = $this->Auth->relevanceOpenid($result_oauth);

        $logtext = "微信昵称:".$result_oauth['nickname']
                    .",openid:".$result_oauth['openid']
                    .",relevance_id:".$relevance_result['relevance_id']
                    .",uid:".$relevance_result['uid']
                    .",授权登录".$result_oauth['appid'];

        Log::write($logtext, "", "", "/tmp/proj_insurance/".date("Ymd", time())."mpoauth.log");
        Log::write(print_r($relevance_result,true), "", "", "/tmp/proj_insurance/".date("Ymd", time())."mpoauth.log");

        if(S("uid", $relevance_result['uid'], 7200 - 300)) {
            header('Location:' . $redirect_url);
        }else{
            exit;
        }

    }

    /**
     * 获取用户信息
     *
     * @param $uid
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getUserInfo()
    {
        MpApi::init(C("MP_APP_ID"), C("MP_APP_SECRET"), "gh_bc28eadce8b6", C("MP_TOKEN"), C("MP_ENCODINGAESKEY"));
        $Api = MpApi::factory("JSSDK");
        $res = $Api->getSignature();
        var_dump($res);
    }
}
