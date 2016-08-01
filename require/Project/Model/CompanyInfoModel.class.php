<?php

namespace Project\Model;

/**
 * 授权用户管理模型
 *
 * @author Cui
 *
 * @date 2015-12-09
 */
class CompanyInfoModel extends CommonModel
{

    const COMP_INFO_STATUS_ON = 1; //公司信息表数据状态 启用
    const COMP_INFO_STATUS_OFF = -1; //公司信息表数据状态 禁用

    /**
     * 根据公司Id获取 公司信息
     *
     * @author Yi
     *
     * @date   2016-01-11
     *
     * @param  string     $cid 公司ID
     *
     * @return array
     */
    public function getCompanyInfoByCid($cid, $field = '*')
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


    /**
     * 根据微信CorpId获取 公司信息
     *
     * @author Yi
     *
     * @date   2016-01-11
     *
     * @param  string     $cid 公司ID
     *
     * @return array
     */
    public function getCompanyInfoByCorpId($corpId, $field = '*')
    {
        if (empty($corpId)) {
            return false;
        }

        $where['corp_id'] 	= $corpId;
        $where['status'] 	= self::COMP_INFO_STATUS_ON;

        $res = $this->where($where)->field($field)->find();

        if (!empty($res)) {
            return $res;
        } else {
            return false;
        }

    }

}
