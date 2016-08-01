<?php

return array(
    /* ----------------- 自定义项目公共目录命名空间 (勿动) ----------------- */
    'AUTOLOAD_NAMESPACE'         => array(
        'Project' => '../../../require/Project/',
    ),

    /* -------------------- 模板设置 -------------------- */
    'TMPL_PARSE_STRING'          => array(
        '__PUBLIC__'    => 'http://imcdn.iwork365.com',
        '__UPLOAD__'    => 'http://file.mytesto365.com',
        '__PLUGIN__'    => 'http://imcdn.iwork365.com/plugin',
        '__APP_STYLE__' => 'http://imcdn.iwork365.com/app',
    ),

    /* -------------------- 数据库 -------------------- */
    'DB_TYPE'                    => 'mysqli', // 数据库类型
    'DB_HOST'                    => 'rm-m5ei7aj16q76p31v4.mysql.rds.aliyuncs.com', // 服务器地址
    'DB_NAME'                    => '', // 数据库名
    'DB_USER'                    => 'o365_network', // 用户名
    'DB_PWD'                     => 'm5ei7aj16q76p31v4A', // 密码
    'DB_PORT'                    => '3306', // 端口
    'DB_PREFIX'                  => 'iw_', // 数据库表前缀

    /* -------------------- 站点常用 -------------------- */
    'WEB_TITLE'                  => 'O365', // 网站默认标题

    'SHOW_PAGE_TRACE'            => false, // 调试用, 页面Trace信息;

    # JWT keys
    'JWT_KEY'                    => 'newiwork', // 进行日志记录

    'HTTP_BASE'                  => 'http://',

    # o365正式域名
    'O365_SERVICE_DOMAIN'        => 'im.iwork365.com', // 进行日志记录
    'O365_APP_SERVICE_DOMAIN'    => 'im.iwork365.com',
    'O365_FILE_SERVICE_DOMAIN'   => 'imfile.iwork365.com',
    'O365_CDN_SERVICE_DOMAIN'    => 'imcdn.iwork365.com',
    'O365_OFFICE_SERVICE_DOMAIN' => 'imoffice.iwork365.com',
    'O365_UC_SERVICE_DOMAIN'     => 'o365-uc.weflame.com',

    # 小助手uid
    'O365_ASSISTANT_UID'         => 5,

    # 人工智能LUIS临时域名
    'O365_LUIS_DOMAIN'           => 'https://api.projectoxford.ai/luis/v1/application',

    # 接口软化曾域名
    'O365_GLUE_SERVICE_DOMAIN'   => 'im.iwork365.com/api', // 进行日志记录

    # 调试模式
    'LOG_RECORD'                 => true, // 进行日志记录
    'LOG_EXCEPTION_RECORD'       => true, // 是否记录异常信息日志
    'LOG_LEVEL'                  => 'EMERG,ALERT,CRIT,ERR,WARN,NOTIC,INFO,DEBUG,SQL', // 允许记录的日志级别
    'DB_DEBUG'                   => true, // 开启调试模式 记录SQL日志
    'URL_CASE_INSENSITIVE'       => false, // url 严格区分大小写

    # 缓存设置;
    'DATA_CACHE_TIME'            => 0, // 数据缓存有效期 0表示永久缓存
    'DATA_CACHE_PREFIX'          => 'o365_sys:', // 缓存前缀
    'DATA_CACHE_TYPE'            => 'Redis', // 数据缓存类型
    'REDIS_HOST'                 => 'e4c5478c3dac4dac.m.cnqda.kvstore.aliyuncs.com',
    'REDIS_AUTH'                 => 'e4c5478c3dac4dac:HK1997hk',
    'REDIS_PORT'                 => 6379,
    'REDIS_DB'                   => 15,

    # 获取 DragonGate token 地址
    'DG_TOKEN_URL'               => 'https://dragongate.live.com/api/core/GetDGToken',

    # 获取 DragonGate O365 token 地址
    'DG_O365_TOKEN_URL'          => 'https://dragongate.live.com/api/IDMapper/GetO365Token',

    # 文件上传 获取 xHeader 地址
    'DG_XHEADER_TOKEN_URL'       => 'https://o365int.sharepoint.cn/_api/contextinfo/',

    # 文件上传 地址
    'DG_FILE_UPLOAD_URL'         => 'https://o365int.sharepoint.cn/_api/web/lists/',

    # 文件上传 公共地址
    'DG_FILE_UPLOAD_PUBLIC_URL'  => 'https://o365int.sharepoint.cn/_api/',

    # 登录绑定 地址
    'DG_LOGIN_BIND_URL'          => 'https://dragongate.live.com/signin.aspx',

    # 图片、音频本地保存路径
    //'LOCAL_FILE_SAVE_URL'        => '/www/staging/proj_idg/file/',

    # 图片、音频公网保存路径
    //'DOMAIN_FILE_SAVE_URL'       => 'http://file.service.digilinx.cn/',

    'API_CUT_TIME'               => 600,
    'SUITE_AUTH_TYPE'            => 1, //授权类型：0 正式授权， 1 测试授权， 默认值为0

    /*--------------------------以下为api接口链接---------------------*/

    //通讯录部门
    'URL_GETDEPARTMENT_ALL'      => '/Organ/Department/getdepartment/apiType/All', //搜索全部部门
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
