<?php

namespace Project\Controller;

use Think\Controller;

/**
 * 公共控制器, 所有控制器必须先继承此控制器.
 * 以后如果需要更改框架核心控制器时可以不修改源码.
 *
 * @author Cui
 */
class CommonController extends Controller
{
    /**
     * 公共跳转方法 主要是为了统一跳转,统一状态码;
     * 会根据当前请求是ajax还是普通的url访问来改变返回方式;
     * ajax返回状态码 status 200:成功 500:失败.
     *
     * @author Cui
     *
     * @param $info void 必须,返回的信息;
     * @param $mode string 成功OR失败 success || error;
     * @param $url string 需要跳转到的url ajax下无用,普通请求默认返回上一页
     * @param $mandatory boolean 强制使用url跳转;
     */
    final protected function location($info, $mode = 'success', $url = '', $mandatory = false)
    {
        if (IS_AJAX && false === $mandatory) {
            $data = array();
            $data['status'] = ($mode == 'success' ? 200 : 500);
            $data['info'] = $info;
            $this->ajaxReturn($data);
        } else {
            $jump = ($mode == 'success' ? 'success' : 'error');
            $this->$jump($info, $url);
        }

        exit;
    }
}
