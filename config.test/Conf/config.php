<?php

return array(
    /* ----------------- 自定义项目公共目录命名空间 (勿动) ----------------- */
    'AUTOLOAD_NAMESPACE'         => array(
        'Project' => '../../../require/Project/',
    ),

    /* -------------------- 模板设置 -------------------- */
    'TMPL_PARSE_STRING'          => array(
        '__PUBLIC__'    => 'http://cdn.shoufubx.com',
        '__UPLOAD__'    => 'http://file.shoufubx.com',
        '__PLUGIN__'    => 'http://cdn.shoufubx.com/plugin',
        '__APP_STYLE__' => 'http://cdn.shoufubx.com/app',
    ),

    /* -------------------- 数据库 -------------------- */
    'DB_TYPE'                    => 'mysqli', // 数据库类型
    'DB_HOST'                    => '192.168.1.242', // 服务器地址
    'DB_NAME'                    => '', // 数据库名
    'DB_USER'                    => 'insurance', // 用户名
    'DB_PWD'                     => 'insurance', // 密码
    'DB_PORT'                    => '3306', // 端口
    'DB_PREFIX'                  => '', // 数据库表前缀

    /* -------------------- 站点常用 -------------------- */
    'WEB_TITLE'                  => 'ShouFuBX', // 网站默认标题

    'SHOW_PAGE_TRACE'            => false, // 调试用, 页面Trace信息;

    # JWT keys
    'JWT_KEY'                    => 'shoufubx', // 进行日志记录

    'HTTP_BASE'                  => 'http://',

    # 首富保险正式域名
    'SHOUFUBX_SERVICE_DOMAIN'        => 'glue.shoufubx.com', // 进行日志记录
    'SHOUFUBX_APP_SERVICE_DOMAIN'    => 'app.shoufubx.com',
    'SHOUFUBX_FILE_SERVICE_DOMAIN'   => 'file.shoufubx.com',
    'SHOUFUBX_CDN_SERVICE_DOMAIN'    => 'cdn.shoufubx.com',
    'SHOUFUBX_UC_SERVICE_DOMAIN'     => 'ucenter.shoufubx.com',

    # 接口软化曾域名
    'O365_GLUE_SERVICE_DOMAIN'   => 'shoufubx.com/api', // 进行日志记录

    # 调试模式
    'LOG_RECORD'                 => true, // 进行日志记录
    'LOG_EXCEPTION_RECORD'       => true, // 是否记录异常信息日志
    'LOG_LEVEL'                  => 'EMERG,ALERT,CRIT,ERR,WARN,NOTIC,INFO,DEBUG,SQL', // 允许记录的日志级别
    'DB_DEBUG'                   => true, // 开启调试模式 记录SQL日志
    'URL_CASE_INSENSITIVE'       => false, // url 严格区分大小写

    # 缓存设置;
    'DATA_CACHE_TIME'            => 0, // 数据缓存有效期 0表示永久缓存
    'DATA_CACHE_PREFIX'          => 'shoufubx:', // 缓存前缀
    'DATA_CACHE_TYPE'            => 'Redis', // 数据缓存类型
    'REDIS_HOST'                 => '192.168.1.242',
    'REDIS_PORT'                 => 6379,
    //'REDIS_DB'                 => '15',
    //'REDIS_AUTH'               => 'e4c5478c3dac4dac:HK1997hk',

    # 图片、音频本地保存路径
    'LOCAL_FILE_SAVE_URL'        => '/www/staging/proj_idg/file/',

    # 图片、音频公网保存路径
    'DOMAIN_FILE_SAVE_URL'       => 'http://file.service.digilinx.cn/',

    'API_CUT_TIME'               => 600,
    'SUITE_AUTH_TYPE'            => 1, //授权类型：0 正式授权， 1 测试授权， 默认值为0

    /*--------------------------以下为api接口链接---------------------*/

    //通讯录部门
    'URL_GETDEPARTMENT_ALL'      => '/Organ/
    /getdepartment/apiType/All', //搜索全部部门
    'URL_GETDEPARTMENT_BYUID'    => '/Organ/Department/getdepartment/apiType/Byuid', //用户或部门所在部门
    'URL_GETDEPARTMENT_LIST'     => '/Organ/Department/getdepartment/apiType/List', //搜索部门信息
    'URL_GETDEPARTMENT_CHECK'    => '/Organ/Department/getdepartment/apiType/Check', //检查交集
    'URL_GETDEPARTMENT_CHILD'    => '/Organ/Department/getdepartment/apiType/Childid', //子部门

    //通讯录人员
    'URL_GETMEMBER_LIST'         => '/Organ/Member/getmember/apiType/List', //查询用户列表
    'URL_GETMEMBER_BYDEPID'      => '/Organ/Member/getmember/apiType/Bydepid', //根据部门查询用户列表
    'URL_GETMEMBER_LEADER'       => '/Organ/Member/getmember/apiType/Leader', // 前人员直接负责人
    'URL_GETMEMBER_INDIRECT'     => '/Organ/Member/getmember/apiType/Indirect', // 前人员直接负责人

);
