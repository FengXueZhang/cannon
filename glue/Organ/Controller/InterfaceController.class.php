<?php

namespace Organ\Controller;

use Org\Util\ApiErrCode;
use Org\WeChatServiceAuth\Auth;
use Project\Controller\GlueApiCommonController;

class InterfaceController extends GlueApiCommonController
{
    private $Auth;

    public function _initialize()
    {
        parent::_initialize();
        $this->Auth = Auth::factory('Organ');
        $array      = $this->_getVars();
        
        $this->uid  = $array['uid'];

    }

    /**
     * [getOrganUserList 查询公司用户列表 同时包含星标及拼音用户分组]
     *
     * @author Li Zhi
     *
     * @date   2016-06-23
     *
     * @return [type]
     */
    public function getOrganUserList()
    {
        $array        = [];
        $array['cid'] = $this->cid;
        $array['uid'] = $this->uid;

        $data = $this->Auth->getOrganUserAll($array);
        $this->apiToJson($data);
    }

    /**
     * [getOrganUserByuid 根据uid查询用户信息]
     *
     * @author Li Zhi
     *
     * @date   2016-06-23
     *
     * @return [type]
     */
    public function getOrganUserByuid()
    {
        $to_uid = $this->_getVars()['data']['to_uid'];
        if (!$to_uid) {
            self::$errorCode = ApiErrCode::ERR_UNDEFIND_KEY;
            $this->apiToJson('');
        }

        $array        = [];
        $array['cid'] = $this->cid;
        $array['uid'] = $to_uid;

        $data = $this->Auth->getOrganUsers($array);
        if (count($data) > 0) {
            $data = $data[0];
        }

        $array               = [];
        $array['cid']        = $this->cid;
        $array['uid']        = $this->uid;
        $array['follow_uid'] = $to_uid;

        $follow = $this->Auth->getOrganFollow($array);

        $data['follow'] = false;
        if ($follow) {
            $data['follow'] = true;
        }

        $array        = [];
        $array['cid'] = $this->cid;
        $array['uid'] = $to_uid;

        $dep = $this->Auth->getOrganDepartmentListByUid($array);

        $data['department'] = $dep;

        $this->apiToJson($data);
    }

    /**
     * [getOrganUsers 查询公司用户列表 ]
     *
     * @author Li Zhi
     *
     * @date   2016-06-23
     *
     * @return [type]
     */
    public function getOrganUsers()
    {
        $array        = [];
        $array['cid'] = $this->cid;
        $array['uid'] = $this->uid;

        $data = $this->Auth->getOrganUsers($array);
        $this->apiToJson($data);
    }

    /**
     * [getOrganFollow 查询公司个人星标用户]
     *
     * @author Li Zhi
     *
     * @date   2016-06-23
     *
     * @return [type]
     */
    public function getOrganFollow()
    {
        $array        = [];
        $array['cid'] = $this->cid;
        $array['uid'] = $this->uid;

        $data = $this->Auth->getOrganFollow($array);
        $this->apiToJson($data);
    }

    /**
     * [postOrganFollow 更改用户置顶关注 ]
     *
     * @author Li Zhi
     *
     * @date   2016-06-23
     *
     * @param Post 参数 follow_uid 关注用户id
     *
     * @return [type]
     */
    public function postOrganFollow()
    {
        $follow_uid = $this->_getVars()['data']['follow_uid'];
        if (!$follow_uid) {
            self::$errorCode = ApiErrCode::ERR_UNDEFIND_KEY;
            $this->apiToJson('');
        }

        $array               = [];
        $array['cid']        = $this->cid;
        $array['uid']        = $this->uid;
        $array['follow_uid'] = $follow_uid;
        //post $array['follow_uid']

        $data = $this->Auth->postOrganFollow($array);
        $this->apiToJson($data);
    }

    /**
     * [getOrganUserSearch 用户搜索]
     *
     * @author Li Zhi
     *
     * @date   2016-06-23
     *
     * @param Get 参数name 关键词 （用户名称 拼音 拼音首字母）模糊搜索
     *
     * @return [type]
     */
    public function getOrganUserSearch()
    {
        $name = $this->_getVars()['data']['name'];
        if (!$name) {
            self::$errorCode = ApiErrCode::ERR_UNDEFIND_KEY;
            $this->apiToJson('');
        }

        $array         = [];
        $array['cid']  = $this->cid;
        $array['name'] = $name;

        $data = $this->Auth->getOrganUserSearch($array);
        $this->apiToJson($data);
    }

    /**
     * [getOrganDepartmentListByUid 根据uid查询用户所在部门信息 ]
     *
     * @author Li Zhi
     *
     * @date   2016-06-23
     *
     * @param Get to_uid 被查询用户uid
     *
     * @return [type]
     */
    public function getOrganDepartmentListByUid()
    {
        $to_uid = $this->_getVars()['data']['to_uid'];
        if (!$to_uid) {
            self::$errorCode = ApiErrCode::ERR_UNDEFIND_KEY;
            $this->apiToJson('');
        }

        $array        = [];
        $array['cid'] = $this->cid;
        $array['uid'] = $to_uid;

        $data = $this->Auth->getOrganDepartmentListByUid($array);
        $this->apiToJson($data);
    }

}
