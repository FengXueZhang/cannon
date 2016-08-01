<?php

namespace Project\Logic;

/**
 * 逻辑层 基类.
 *
 * @author Cui
 */
class CommonLogic
{
    // 错误信息;
    protected $error = '';

    /**
     * 获取错误信息.
     *
     * @author Cui.
     *
     * @return string;
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 设置错误信息(PS:因为一般情况下, 我们的错误信息都是模型返回的, 所以这里简单封装一下, 具体见代码).
     *
     * @author Cui.
     *
     * @param $model Object TP的model对象.
     */
    public function setError($model)
    {
        $this->error = $model->getError();
    }
}
