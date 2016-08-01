<?php

namespace Org\WeChatServiceAuth\Auth;

class OrganAuth extends BaseAuth
{
    private $baseUrl;

    const URL_GETMEMBER_LIST      = '/Organ/Member/getmember/apiType/List'; //
    const URL_GETFOLLOW_LIST      = '/Organ/Follow/getfollow/apiType/List'; //
    const URL_POSTFOLLOW_FOLLOW   = '/Organ/Follow/postfollow/apiType/Follow'; //
    const URL_GETDEPARTMENT_BYUID = '/Organ/Department/getdepartment/apiType/Byuid'; //

    public function __construct()
    {
        $this->baseUrl = C('HTTP_BASE') . C('O365_APP_SERVICE_DOMAIN');
    }

    /**
     * [getOrganUserAll 搜索全部用户及星标，并整理数据]
     *
     * @author Li Zhi
     *
     * @date   2016-07-01
     *
     * @param  [type]     $array [description]
     *
     * @return [type]
     */
    public function getOrganUserAll($array)
    {
        $deps = $this->getOrganUserList($array);

        $follow = $this->getOrganFollow($array);

        $content = [];
        foreach ($deps as $key => $value) {
            $content[$key] = [];
            foreach ($value as $k => $v) {
                if (array_key_exists($k, $follow)) {
                    $v['follow'] = true;
                }
                $content[$key][] = $v;
            }
        }

        $refollow = [];
        foreach ($follow as $key => $value) {
            $refollow[] = $value;
        }

        return ['follow' => $refollow, 'content' => $content];
    }

    /**
     * [getOrganUserList 查询用户列表]
     *
     * @author Li Zhi
     *
     * @date   2016-07-01
     *
     * @param  [type]     $array [description]
     *
     * @return [type]
     */
    public function getOrganUserList($array)
    {
        $t = '/cid/' . $array['cid']; //可能需要改进为加密字符串

        $url = $this->baseUrl . self::URL_GETMEMBER_LIST . $t;

        $param  = [];
        $Header = [];
        $list   = self::post($url, $param, $Header);
        $result = $this->explainApiToJson($list);

        $content = [];
        $other   = [];
        foreach ($result as $key => $value) {
            $arr                     = [];
            $arr['uid']              = $value['uid'];
            $arr['name']             = $value['name'];
            $arr['name_spell_all']   = $value['name_spell_all'];
            $arr['name_spell_first'] = $value['name_spell_first'];
            $arr['img_url']          = $value['img_url'];
            $arr['account']          = $value['account'];
            $arr['mobile']           = $value['mobile'];
            $arr['check']            = false;
            $arr['follow']           = false;

            $tk = strtoupper(substr(trim($value['name_spell_first']), 0, 1));
            if (ctype_alpha($tk)) {
                $content[$tk][$value['uid']] = $arr;
            } else {
                $other[$value['uid']] = $arr;
            }
        }

        ksort($content);

        $content['#'] = $other;

        return $content;
    }

    /**
     * [getOrganUsers 查询用户信息]
     *
     * @author Li Zhi
     *
     * @date   2016-07-01
     *
     * @param  [type]     $array [description]
     *
     * @return [type]
     */
    public function getOrganUsers($array)
    {
        $t = '/cid/' . $array['cid']; //可能需要改进为加密字符串

        $param                                  = [];
        isset($array['uid']) && $param['uid']   = $array['uid'];
        isset($array['uids']) && $param['uids'] = $array['uids'];

        $url = $this->baseUrl . self::URL_GETMEMBER_LIST . $t;

        $Header = [];
        $list   = self::post($url, $param, $Header);
        $result = $this->explainApiToJson($list);

        return $result;
    }

    /**
     * [getOrganFollow 查询用户的星标用户]
     *
     * @author Li Zhi
     *
     * @date   2016-07-01
     *
     * @param  [type]     $array [description]
     *
     * @return [type]
     */
    public function getOrganFollow($array)
    {
        $url = $this->baseUrl . self::URL_GETFOLLOW_LIST . $t;

        $param        = [];
        $param['uid'] = $array['uid'];

        isset($array['follow_uid']) && $param['follow_uid'] = $array['follow_uid'];

        $Header = [];
        $list   = self::post($url, $param, $Header);

        $result = $this->explainApiToJson($list);

        $follow = [];
        if ($result) {
            foreach ($result as $key => $value) {
                $arr                     = [];
                $arr['uid']              = $value['uid'];
                $arr['name']             = $value['name'];
                $arr['name_spell_all']   = $value['name_spell_all'];
                $arr['name_spell_first'] = $value['name_spell_first'];
                $arr['img_url']          = $value['img_url'];
                $arr['account']          = $value['account'];
                $arr['mobile']           = $value['mobile'];
                $arr['check']            = false;

                $follow[$value['uid']] = $arr;
            }
        }

        return $follow;
    }

    /**
     * [postOrganFollow 星标或取消星标某个用户]
     *
     * @author Li Zhi
     *
     * @date   2016-07-01
     *
     * @param  [type]     $array [description]
     *
     * @return [type]
     */
    public function postOrganFollow($array)
    {
        $t = '/cid/' . $array['cid']; //可能需要改进为加密字符串

        $url = $this->baseUrl . self::URL_POSTFOLLOW_FOLLOW . $t;

        $param               = [];
        $param['uid']        = $array['uid'];
        $param['follow_uid'] = $array['follow_uid'];

        $Header = [];
        $list   = self::post($url, $param, $Header);
        $result = $this->explainApiToJson($list);

        return $result;
    }

    /**
     * [getOrganUserSearch 用户安装名称关键字模糊搜索]
     *
     * @author Li Zhi
     *
     * @date   2016-07-01
     *
     * @param  [type]     $array [description]
     *
     * @return [type]
     */
    public function getOrganUserSearch($array)
    {
        $t = '/cid/' . $array['cid']; //可能需要改进为加密字符串

        $url = $this->baseUrl . self::URL_GETMEMBER_LIST . $t;

        $param              = [];
        $param['name']      = $array['name'];
        $param['name_type'] = 'key_3';

        $Header = [];
        $list   = self::post($url, $param, $Header);
        $result = $this->explainApiToJson($list);

        foreach ($result as $key => $value) {
            $value['check'] = false;
            $arr[]          = $value;
        }

        return $arr;
    }

    /**
     * [getOrganDepartmentListByUid 查询用户所在部门]
     *
     * @author Li Zhi
     *
     * @date   2016-07-01
     *
     * @param  [type]     $array [description]
     *
     * @return [type]
     */
    public function getOrganDepartmentListByUid($array)
    {
        $t = '/cid/' . $array['cid']; //可能需要改进为加密字符串

        $url = $this->baseUrl . self::URL_GETDEPARTMENT_BYUID . $t;

        $param        = [];
        $param['uid'] = $array['uid'];

        $Header = [];
        $list   = self::post($url, $param, $Header);
        $result = $this->explainApiToJson($list);

        return $result;
    }

}
