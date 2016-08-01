<?php

namespace Project\Controller;

/**
 * Wechat模块公共控制器.
 *
 * @author Cui
 */
class WechatCommonController extends CommonController
{

    /**
     * JQM错误提示信息模板
     *
     * @param $msg string 信息文本;
     * @param $status string success||error  状态 缺省是error
     */
    protected function msgForJQM($msg, $status = 'error')
    {
        $this->assign('msg', $msg);
        $this->display('Public/msgForJQM');
        exit;
    }
}
