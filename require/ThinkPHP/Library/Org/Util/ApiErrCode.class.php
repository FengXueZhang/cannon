<?php

namespace Org\Util;

/**
 * 接口全局返回码
 */
class ApiErrCode
{
    // 返回的状态码
    const SUCCESS_CODE       = 200; //接口成功访问
    const UNAUTHORIZED_CODE  = 401; //未授权
    const NOT_ALLOWED_CODE   = 403; //无权访问
    const NOT_FOUND_CODE     = 404; //访问的接口不存在
    const EXPIRE_CODE        = 412; //签名过期， 即请求接口所携带的时间戳小于服务器时间 10秒或者 大于10秒
    const HTTP_TYPE_ERR_CODE = 416; //访问的http类型不允许
    const SGIN_ERR_CODE      = 417; //签名验证失败
    const ALLOW_IP_CODE      = 418; //访问IP受限
    const PARAMS_ERR_CODE    = 419; //必要参数缺失 注意 该状态的err_msg应由具体的执行的方法根据业务逻辑定义
    const FAILURE_CODE       = 500; //具体的业务逻辑错误状态码 注意 该状态的err_msg应由具体的执行的方法根据业务逻辑定义

    //考勤api相关错误码
    const ERR_LOCATION     = 450; // 打卡位置不在允许范围
    const ERR_ALLOW_TIME   = 451; // 打卡时间错误
    const SUCCESS_ARRIVE   = 452; // 正常签到
    const SUCCESS_LEAVE    = 453; // 正常签退
    const EXCEPTION_ARRIVE = 454; // 迟到
    const EXCEPTION_LEAVE  = 455; // 早退
    const ERR_MEMBER       = 456; //人员信息不匹配


    const ERR_UNDEFIND_KEY            = 40000; //缺少必要参数
    const ERR_SUITE_UNDEFIND_ID       = 40001; //缺少suite_id
    const ERR_SUITE_UNDEFIND_TICKET   = 40002; //缺少suite_ticket
    const ERR_COMPANY_UNDEFIND_CORPID = 40003; //缺少corpid
    const ERR_ERROR_KEY               = 40005; //参数错误

    const AI_UNDEFIND_KEY               = 60000; //缺少必要参数
    const AI_MESSAGE_ANALYSIS_FAIL      = 60001; //信息解释失败



    //IM相关错误码
    const ERR_PARAMETER_NOT_FOUND   = 1001;     //缺少参数
    const ERR_DATABASE              = 1002;     //数据库错误

    const ERR_API_REEOR             = 1100;     //API调用时错误

    // 状态码所对应的返回信息
    public static $ERROR_MESSAGE = array(
        '200'   => '操作成功.',
        '401'   => '未授权的Vendor.',
        '403'   => '无权访问.',
        '404'   => '访问的接口不存在.',
        '412'   => '签名已过期,请检查时间参数的设置.',
        '416'   => '只允许GET或POST的方式访问接口.',
        '417'   => '签名验证失败,请检查签名规则.',
        '418'   => '您的IP不在服务器允许访问的IP列表中.',
        '450'   => '打卡位置不在允许范围.',
        '451'   => '打卡时间错误.',
        '452'   => '正常签到.',
        '453'   => '正常签退.',
        '454'   => '迟到.',
        '455'   => '早退.',
        '456'   => '签到人员信息不匹配.',
        '40000' => '缺少必要参数',
        '40001' => '缺少suite_id',
        '40002' => '缺少suite_ticket',
        '40003' => '缺少corpid',
        '40005' => '参数错误',
        '60001' => '信息解释失败',
    );

    /**
     * 根据状态码获取其所对应的状态信息.
     *
     * @author Cui
     *
     * @date   2015-10-01
     *
     * @param int $code 预设的状态码
     *
     * @return string 预设的状态信息
     */
    public static function getErrMsgByCode($code)
    {
        if (!array_key_exists($code, self::$ERROR_MESSAGE)) {
            //E('状态码或状态码所对应的信息未设置');
        }

        return self::$ERROR_MESSAGE[$code];
    }
}
