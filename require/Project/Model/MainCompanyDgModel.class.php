<?php

namespace Project\Model;

class MainCompanyDgModel extends CommonModel
{
    protected $connection = array(
        'db_type'   => 'mysql',
        'db_user'   => 'o365_network',
        'db_pwd'    => 'o365_network',
        'db_host'   => '192.168.1.242',
        'db_port'   => '3306',
        'db_name'   => 'n_o365_main',
        'db_prefix' => 'iw_' // 数据库表前缀
    );

    const COMP_INFO_STATUS_ON   = 1; //公司信息表数据状态 启用
    const COMP_INFO_STATUS_OFF  = -1; //公司信息表数据状态 禁用

    //公司信息默认获取字段
    const MAIN_DG_DEFALUT_FIELD = 'cid,client_id,client_key,dg_id,dg_secret,status';

    public function getMainDgByCid( $cid, $field = self::MAIN_DG_DEFALUT_FIELD )
    {
        if (empty($cid)) {
            return false;
        }

        $where['cid'] = (int) $cid;
        $where['status'] = self::COMP_INFO_STATUS_ON;

        $res = $this->where($where)->field($field)->find();

        if (!empty($res)) {
            return $res;
        } else {
            return false;
        }
    }
}