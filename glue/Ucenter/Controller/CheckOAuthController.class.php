<?php
namespace Ucenter\Controller;

use Org\Util\ApiErrCode;
use Org\WeChatServiceAuth\Auth;
use Project\Controller\ApiCommonController;
use Think\Log;
use Org\WeixinApi\Api;

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
     * OAuth登录注册流程
     */
    public function checkOAuth()
    {
//        $this->result_url = '';
//        $this->corp_id    = 'wx56fe9d4af5698818';
        if (empty($this->result_url) || empty($this->corp_id)) {
            self::$errorCode    = ApiErrCode::ERR_UNDEFIND_KEY;
            self::$errorMessage = 'result_url/corp_id为空!';
            $this->apiToJson();
        }

        $companyInfo = $this->getCompanyInfo($this->corp_id);

        $regData['uid']                = $this->uid;
        $regData['comInfo']            = $companyInfo;
        $regData['comInfo']['corp_id'] = $this->corp_id;

        define('O365_CORP_ID', $companyInfo['corp_id']);
        define('O365_CORP_SECRET', $companyInfo['secret_id']);

        $main  = Auth::factory('Main');
        $array = [];
        //$array['sid'] = 1;
        //
        $suite = $main->getSuiteCode($array);

        $array        = [];
        $array['sid'] = $suite['sid'];
        $array['cid'] = $companyInfo['cid'];

        $companyAuth = $main->getSuiteAuth($array);

        $regData['suite']['suite_id']       = $suite['suite_id'];
        $regData['suite']['suite_secret']   = $suite['secret'];
        $regData['suite']['suite_ticket']   = $suite['ticket'];
        $regData['suite']['permanent_code'] = $companyAuth['permanent_code'];

        $uc  = Auth::factory('Ucenter');
        $uid = $uc->checkOAuth($regData);

        $resData['uid']     = $uid;
        $resData['cid']     = $companyInfo['cid'];
        $resData['corp_id'] = $this->corp_id;

        $sign = $this->_jwtCompile($resData, C('JWT_KEY'));

        cookie('uid', $resData['uid']);
        cookie('sign', $sign);

        $log['appName']        = APP_NAME;
        $log['moduleName']     = MODULE_NAME;
        $log['controllerName'] = CONTROLLER_NAME;
        $log['actionName']     = ACTION_NAME;
        $log['requestParam']   = $this->_getVars();
        $log['returnParam']    = $this->_getreturnParam();

        Log::write(json_encode($log, JSON_UNESCAPED_UNICODE));

        $result = $this->Auth->checkRelevance(['uid' => $resData['uid'], 'appid' => 'wxd7334ab9fffcab57']);
        if ($result && $result['relevance_id']) {
            header('Location:' . $this->result_url);
        } else {
            $fwh_oauth_url = "http://o365.weflame.com/api/ucenter/CheckOAuth/checkFwhOAuth?uid=" . $resData['uid'] . "&redirect_url=" . urlencode($this->result_url);
            header('Location:' . $fwh_oauth_url);
            //$this->checkFwhOAuth($resData['uid'], $this->result_url);
        }
    }

    /**
     * 服务号Oauth
     *
     * @throws \Exception
     */
    public function checkFwhOAuth()
    {
        if (I('get.source')) {
            $source = I('get.source');
        }

        if (I('get.key')) {
            $key = I('get.key');
        }

        if (I('get.redirect_url')) {
            $redirect_url = I('get.redirect_url');
        }

        $oauthFwh = Api::factory('OauthFwh');
        $oauthFwh->request(__SELF__, '', C('SHOUFUBX_SERVICE_DOMAIN')); //发起OAuth请求.
        $result_oauth = $oauthFwh->receive(); //接收结果;
        if (I('get.uid')) {
            $uid                 = I('get.uid');
            $result_oauth['uid'] = $uid;
        }

        $result_oauth['appid'] = "wx214dcfa5440ff2ee";
        $relevance_result      = $this->Auth->relevanceOpenid($result_oauth);
        if ('pc' != $source && $redirect_url) {
            header('Location:' . $redirect_url);
        } else {
            if ('pc' == $source && $key) {
                S($key . "_uid", $relevance_result['uid'], 7200 - 300);
                exit("<h1 style='text-align: center;'>登录成功</h1>");
            } else {
                exit("<h1 style='text-align: center;'>登录失败</h1>");
            }
        }
    }

    /**
     * 获取公司信息
     *
     * @param $corp_id
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getCompanyInfo($corp_id)
    {
        $uc = Auth::factory('Ucenter');

        return $uc->getCidByCorpid($corp_id);
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
    public function getUserInfo($uid)
    {
        $uc = Auth::factory('Ucenter');

        return $uc->getUserDetail($uid);
    }

    /**
     * OAuth正式登录注册流程(后期会转到checkOAuth方法中)
     */
    public function test()
    {
        define('O365_CORP_ID', 'wx56fe9d4af5698818');
        define('O365_CORP_SECRET', 'hzxyspOtpErP2wX4LgTzR4wkb3rGJI0_1o4aNUXrnnTYbTtTL-c7SZBpKokF-1fa');
//        $this->result_url = '';
        $this->corp_id = 'wx56fe9d4af5698818';
        if (empty($this->result_url) || empty($this->corp_id)) {
            self::$errorCode    = ApiErrCode::ERR_UNDEFIND_KEY;
            self::$errorMessage = 'result_url/corp_id为空!';
            $this->apiToJson();
        }

        $companyInfo = $this->getCompanyInfo($this->corp_id);

        $regData['uid']                = $this->uid;
        $regData['comInfo']            = $companyInfo;
        $regData['comInfo']['corp_id'] = $this->corp_id;

        $main  = Auth::factory('Main');
        $array = [];
        //$array['sid'] = 1;
        //
        $suite = $main->getSuiteCode($array);

        $array        = [];
        $array['sid'] = $suite['sid'];
        $array['cid'] = $companyInfo['cid'];

        $companyAuth = $main->getSuiteAuth($array);

        $regData['suite']['suite_id']       = $suite['suite_id'];
        $regData['suite']['suite_secret']   = $suite['secret'];
        $regData['suite']['suite_ticket']   = $suite['ticket'];
        $regData['suite']['permanent_code'] = $companyAuth['permanent_code'];

        $uc  = Auth::factory('Ucenter');
        $uid = $uc->checkOAuth($regData);

        $resData['uid']     = $uid;
        $resData['cid']     = $companyInfo['cid'];
        $resData['corp_id'] = $this->corp_id;

        $sign = $this->_jwtCompile($resData, C('JWT_KEY'));

        cookie('uid', $resData['uid']);
        cookie('sign', $sign);

        $log['appName']        = APP_NAME;
        $log['moduleName']     = MODULE_NAME;
        $log['controllerName'] = CONTROLLER_NAME;
        $log['actionName']     = ACTION_NAME;
        $log['requestParam']   = $this->_getVars();
        $log['returnParam']    = $this->_getreturnParam();

        Log::write(json_encode($log, JSON_UNESCAPED_UNICODE));

        header('Location:' . $this->result_url);
//        header('Location:' . $this->result_url . '&uid=' . $resData['uid'] . '&sign=' . $sign);
    }

}
