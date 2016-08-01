<?php
// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG', true);

// 定义公共文件目录
define('COMMON_PATH', __DIR__ . '/Project/');

// 定义运行时目录
define('LOG_PATH', '../../Logs/' . APP_NAME . '/');
define('RUNTIME_PATH', '../../Runtime/');
define('CACHE_PATH', RUNTIME_PATH . 'Cache/' . APP_NAME . '/'); // 应用模板缓存目录
define('TEMP_PATH', RUNTIME_PATH . 'Temp/' . APP_NAME . '/');
define('CONF_PATH', '../../Conf/');

// 不生成 index.html
define('BUILD_DIR_SECURE', false);

// 引入ThinkPHP入口文件
require __DIR__ . '/ThinkPHP/ThinkPHP.php';
