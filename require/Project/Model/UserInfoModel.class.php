<?php

namespace Project\Model;

use Org\O365Api\Api as O365Api;
/**
 * 授权用户管理模型
 *
 * @author Cui
 *
 * @date 2015-12-09
 */
class UserInfoModel extends CommonModel
{

    const USER_INFO_STATUS_ON  =  1; //个人信息表数据状态 启用
    const USER_INFO_STATUS_OFF = -1; //个人信息表数据状态 禁用

    const IS_BIND_OFFICE_ON    =  1; //个人信息表数据状态 启用
    const IS_BIND_OFFICE_OFF   = -1; //个人信息表数据状态 禁用

    const ENVIRONMENT_TYPE     =  1;
    const RESOURCE_TYPE        =  1;

    const BIND                 =  0; //已绑定
    const NOT_BIND             =  1; //未绑定

    /**
     * 根据微信个人id 获取 个人信息
     *
     * @author Yi
     *
     * @date   2016-01-11
     *
     * @param  string     $WechatId 微信个人id
     *
     * @return array
     */
    public function getUserInfoByWechatId( $WechatId, $cId , $field = '*')
    {

        if (empty($WechatId)) {
            return false;
        }

        $where['wuserid'] 	= (string) $WechatId;
        $where['cid'] 		= (int) $cId;
        $where['status'] 	= self::USER_INFO_STATUS_ON;
        $res = $this->where($where)->field($field)->find();

        if (!empty($res)) {
            return $res;
        } else {
            return false;
        }

    }

    /**
     * 根据微信个人id 获取 个人信息
     *
     * @author Yi
     *
     * @date   2016-01-11
     *
     * @param  string     $WechatId 微信个人id
     *
     * @return array
     */
    public function getUserInfoByCid($cid, $field = '*')
    {

        if (empty($cid)) {
            return false;
        }

        $where['cid'] = (int) $cid;
        $where['status'] = self::USER_INFO_STATUS_ON;

        $res = $this->where($where)->field($field)->select();

        if (!empty($res)) {
            return $res;
        } else {
            return false;
        }

    }

    /**
     * 用户绑定
     *
     * @author Yi
     *
     * @date   2016-01-11
     *
     * @param  string     $value 用户企业号Id
     * @param  string     $field 字段名
     *
     * @return array
     */
    public function userBindByCid($value, $field = 'wuserid')
    {
        if (empty($value)) {
            return false;
        }

        $data['is_bind_office'] = self::IS_BIND_OFFICE_ON;
        $where[$field] = $value;

        return $this->where($where)->data($data)->save();
    }

    /**
     * 检查用户是否 绑定
     *
     * @author Yi
     *
     * @date   2016-01-11
     *
     * @param  string     $userId 用户企业号Id
     * @param  string     $field 字段名
     *
     * @return array
     */
    public function checkUserBind( $userId, $field = 'wuserid')
    {
        if (empty($userId)) {
            return false;
        }

        //$data['is_bind_office'] = self::IS_BIND_OFFICE_ON;
        $where[$field] = $userId;

        return $this->where($where)->field('is_bind_office')->find();
    }


    /**
     * 通过传来的微信ID获取用户Email
     *
     * @author WangXueChen
     *
     * @date   2016-03-30
     *
     * @param  array     $list    用户微信ID
     * @param  string    $cid     公司ID
     *
     * @return array
     */
    public function getUserEmail($list, $cid)
    {
        if(is_array($list) && !empty($list)){

            $where['wuserid'] = array(in, $list);
            $where['cid']     = $cid;
            return  $this->where($where)->getField('wuserid,email');

        }else{

            return false;

        }  
    }


    /**
     * 检查用户o365登录绑定
     *
     * @author Yi
     *
     * @date   2016-02-23
     */
    public function checkUserBindNew($checkid)
    {
        $res                = array();

        O365API::init();
        $O365Token  = O365Api::getO365Token(self::ENVIRONMENT_TYPE,self::RESOURCE_TYPE,$checkid);
        if ( isset($O365Token['ErrorCode']) && in_array($O365Token['ErrorCode'],array(10004,10006)) ) {
            $res['status']      = 200;
            $res['checkType']   = self::NOT_BIND;
        } else {
            $res['O365Token']=$O365Token;
            $res['status']      = 200;
            $res['checkType']   = self::BIND;
        }
        //Log::write(print_r($check,ture));
        return($res);

    }

}
