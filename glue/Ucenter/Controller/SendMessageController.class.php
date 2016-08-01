<?php
namespace Ucenter\Controller;

use Org\Util\ApiErrCode;
use Org\WeChatServiceAuth\Auth;
use Org\WeixinApi\Api;
use Project\Controller\ApiCommonController;

class SendMessageController extends ApiCommonController
{

    protected static $userInfokey = 'USER_INFO_KEY'; // 企业号corp_secrect

    private $Auth;

    /**
     * 构造函数
     *
     * @throws \Exception
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->Auth = Auth::factory('Ucenter');
    }


    /**
     * 向微信推送/发送微信text类型消息
     *
     * @throws \Exception
     */
    public function getMessageText()
    {

        $array = $this->_getVars();

        $uidList = $this->_getVars()['data']['uid_list'];
        $message = $this->_getVars()['data']['message'];

        if (mb_strlen($message) > 30) {
            $message = mb_substr($message, 0, 30);
        }

        $message = urldecode($message);

        $rex = "/^\d+(,\d+)*$/";

        if ($uidList == '' || !preg_match($rex, $uidList)) {
            self::$errorCode    = ApiErrCode::ERR_UNDEFIND_KEY;
            self::$errorMessage = "参数uid_list错误";
        }

        $array = [];
        //$array['cid']  = $this->cid;
        $array['uids'] = $uidList;

        $Organ = Auth::factory('Organ');

        $data = $Organ->getOrganUsers($array);

        if (!$data) {
            self::$errorCode    = ApiErrCode::ERR_ERROR_KEY;
            self::$errorMessage = "用户数据不存在";
            $this->apiToJson();
        }

        $uid_arr = [];

        foreach ($data as $key => $value) {

            if ($value['is_attention'] == 1) {
                $uid_arr[$value['cid']][$value['uid']] = $value['account'];
            } else {
                self::$errorMessage .= '( uid_' . $value['uid'] . ' error: 该账号 ' . (($value['is_attention'] == 4) ? '未关注' : '被冻结') . ' ) | ';
            }
        }

        $UserWxApi = Api::factory('Message');
        $Main      = Auth::factory('Main');

        foreach ($uid_arr as $key => $value) {
            $array        = [];
            $array['cid'] = $key;

            $suite = $Main->setSuiteCorpApi($array);

            $app = $Main->getMainAppInfo($array);

            $agentid = $app['agent_id'];

            $result = $UserWxApi->touser(implode('|', $value))->text($message)->send($agentid);
            if (!$result) {
                self::$errorMessage .= '( ' . implode('|', $value) . ' error:' . Api::getError() . ') | ';
            }
        }

        $this->apiToJson($result);
    }

}
