<?php

namespace Project\Api;

use Project\Logic\CommonLogic;

/**
 * App初始化所需信息存储类.
 *
 * @author Yi
 */
class AppStorageApi extends CommonLogic
{
    const KEY = 'APP_CORP_STORAGE';
    const SGIN = 'APP_CORP_STORAGE_SGIN';

    /**
     * 设置App初始化时所需的Session存储信息.
     *
     * @author Cui
     *
     * @date   2015-08-23
     *
     * @param array $data 初始化所需数据
     *                    id            int             公司信息表主键ID
     *                    cid           int             公司ID
     *                    c_name          string          公司名称
     *                    client_id      int             微软云client_id
     *                    client_key       string          微软云client_key
     *                    corp_id         string          微信corp_id
     *                    corp_secret      array           微信corp_secret
     *                    dg_id            int             DragonGate_id
     *                    dg_secret        int             DragonGate_secret
     *                    create_time      int             创建时间
     *                    status          int             状态
     */
    public function _set($data)
    {
        $param = array();
        $param['id'] = $data['id'];
        $param['cid'] = $data['cid'];
        $param['c_name'] = $data['c_name'];
        $param['client_id'] = $data['client_id'];
        $param['client_key'] = $data['client_key'];
        $param['corp_id'] = $data['corp_id'];
        $param['corp_secret'] = $data['corp_secret'];
        $param['dg_id'] = $data['dg_id'];
        $param['dg_secret'] = $data['dg_secret'];
        $param['create_time'] = $data['create_time'];
        $param['status'] = $data['status'];

        session(self::KEY, $param);
        session(self::SGIN, data_auth_sign($param));
    }

    /**
     * 修改存储数组中的某一个键值对
     *
     * @author Cui
     *
     * @date   2015-11-11
     *
     * @param  string     $key   键名
     * @param  void       $value 任意值
     */
    public function _param($key, $value)
    {
        if (false === $this->_check()) {
            return false;
        }

        $data = $this->_get();

        $data[$key] = $value;

        $this->_set($data);
    }

    /**
     * 取出数据.
     *
     * @author Cui
     *
     * @date   2015-08-25
     *
     * @return array
     */
    public function _get()
    {
        return session(self::KEY);
    }

    /**
     * 验证数据.
     *
     * @author Cui
     *
     * @date   2015-08-25
     *
     * @return bool
     */
    public function _check()
    {
        $data = $this->_get();
        if (!$data) {
            return false;
        }

        return data_auth_sign($data) === session(self::SGIN);
    }

    /**
     * 判断是否登录
     *
     * @author Yi
     *
     * @date   2015-11-11
     *
     * @param  string     $key   键名
     * @param  void       $value 任意值
     */
    public function is_login($key, $value)
    {
        if (false === $this->_check()) {
            return false;
        }

        $data = $this->_get();

        $data[$key] = $value;

        $this->_set($data);
    }

}
