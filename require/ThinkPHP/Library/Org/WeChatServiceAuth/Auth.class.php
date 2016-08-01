<?php

namespace Org\WeChatServiceAuth;

class Auth
{

    public static function factory($className)
    {
        $className = __NAMESPACE__ . '\\Auth\\' . $className . 'Auth';
        if (!$className || !is_string($className)) {
            throw new \Exception('类名参数不正确', 1);
        }

        if (!class_exists($className)) {
            throw new \Exception($className . '接口不存在', 1);
        }

        return new $className;
    }

}
