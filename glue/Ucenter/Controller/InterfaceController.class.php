<?php
namespace Ucenter\Controller;

use Org\Util\ApiErrCode;
use Org\WeChatServiceAuth\Auth;
use Org\WeixinApi\Api;
use Project\Controller\GlueApiCommonController;

class InterfaceController extends GlueApiCommonController
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
     * 获取Ucenter 中当前用户的部分信息
     */
    public function getUserDetail()
    {
        $uid = $this->_getVars()['uid'];

        $result = $this->Auth->getUserPartDetail($uid);

        $this->apiToJson($result);

    }

    /**
     * 获取Ucenter 中其他用户的部分信息
     */
    public function getOtherUserDetail()
    {
        $uid = $this->_getVars()['data']['to_uid'];

        $result = $this->Auth->getUserPartDetail($uid);

        $this->apiToJson($result);

    }

    /**
     * Im获取Ucenter 中其他用户的全部信息
     */
    public function getImUserDetail()
    {
        $toUid = $this->_getVars()['data']['to_uid'];

        $result = $this->Auth->getUserDetail($toUid);

        $this->apiToJson($result);
    }

    /**
     * 获取当前用户:部分信息/用户未读消息/最近联系人列表/群聊信息
     *
     * @throws \Exception
     */
    public function getImAndUserDetail()
    {
        $uid = $this->_getVars()['uid'];

        //获取用户部分信息
        $userInfo = $this->Auth->getUserPartDetail($uid);

        $im = Auth::factory('Im');

        //根据用户Uid 获取用户未读消息
        $noReadList = $im->getNoReadMessageList($uid);

        //获取最近联系人列表
        $chatList = $im->getChatList($uid);

        //根据用户Uid 获取所有群聊房间信息
        $allRoom = $im->getAllRoomByUid($uid);

        $resData                 = [];
        $resData['user_info']    = $userInfo;
        $resData['no_read_list'] = $noReadList;
        $resData['chat_list']    = $chatList;
        $resData['all_room']     = $allRoom;

        $this->apiToJson($resData);
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
