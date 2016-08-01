<?php

namespace Org\SuiteWeixinApi;

/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 14-7-22
 * Time: 10:08.
 */
class ApiConfig
{
    /*==================================下面是企业号第三方接口=============================== */
    //获取应用套件令牌
    const URL_SERVICE_SUITETOKEN = 'https://qyapi.weixin.qq.com/cgi-bin/service/get_suite_token?';

    //获取预授权码
    const URL_SERVICE_RPEAUTHCODE = 'https://qyapi.weixin.qq.com/cgi-bin/service/get_pre_auth_code?';

    //设置授权配置
    const URL_SERVICE_SETSESSIONINFO = 'https://qyapi.weixin.qq.com/cgi-bin/service/set_session_info?';

    //获取企业号的永久授权码
    const URL_SERVICE_PERMANENTCODE = 'https://qyapi.weixin.qq.com/cgi-bin/service/get_permanent_code?';

    //获取企业号的授权信息
    const URL_SERVICE_AUTHINFO = 'https://qyapi.weixin.qq.com/cgi-bin/service/get_auth_info?';

    //获取企业号access_token
    const URL_SERVICE_CORPTOKEN = 'https://qyapi.weixin.qq.com/cgi-bin/service/get_corp_token?';

    //授权链接
    const URL_AUTH_URL = 'https://qy.weixin.qq.com/cgi-bin/loginpage?suite_id=$suite_id&pre_auth_code=$pre_auth_code&redirect_uri=$redirect_uri&state=$state';

    /*==================================下面是企业号普通接口=============================== */

    //获取token
    const URL_GET_TOKEN = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken?';

    /*==========================下面是部门======================= */
    //创建部门
    const URL_DEPARTMENT_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/department/create?';

    //更新部门
    const URL_DEPARTMENT_UPDATE = 'https://qyapi.weixin.qq.com/cgi-bin/department/update?';

    //删除部门
    const URL_DEPARTMENT_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/department/delete?';

    //获取部门列表
    const URL_DEPARTMENT_GETLIST = 'https://qyapi.weixin.qq.com/cgi-bin/department/list?';

    /*==========================下面是自定义菜单=======================*/
    //创建菜单
    const URL_MENU_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/menu/create?';

    //删除菜单
    const URL_MENU_DETELE = 'https://qyapi.weixin.qq.com/cgi-bin/menu/delete?';

    //获取菜单列表
    const URL_MENU_GETLIST = 'https://qyapi.weixin.qq.com/cgi-bin/menu/get?';

    /*==========================下面是文件上传======================= */
    //获取文件
    const URL_FILE_GETMEDIE = 'https://qyapi.weixin.qq.com/cgi-bin/media/get';

    //上传文件
    const URL_FILE_UPLOAD = 'https://qyapi.weixin.qq.com/cgi-bin/media/upload';

    /*==========================下面是发送信息======================= */
    const URL_MESSAGE_SEND = 'https://qyapi.weixin.qq.com/cgi-bin/message/send?';

    /*==========================下面是用户信息======================= */
    //创建用户
    const URL_USER_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/user/create?';

    //更新用户
    const URL_USER_UPDATE = 'https://qyapi.weixin.qq.com/cgi-bin/user/update?';

    //删除用户
    const URL_USER_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/user/delete?';

    //获取用户
    const URL_USER_GET = 'https://qyapi.weixin.qq.com/cgi-bin/user/get?';

    //获取用户列表
    const URL_USER_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/user/list?';

    //获取企业CODE
    const URL_CONPANY_CODE = 'https://open.weixin.qq.com/connect/oauth2/authorize?';

    //获取用户微分
    const URL_USER_INFO = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo';

    //应用代理
    const URL_APP_PROXY = 'https://qyapi.weixin.qq.com/cgi-bin/agent/get';

    //创建totag
    const URL_CREATE_TOTAG = 'https://qyapi.weixin.qq.com/cgi-bin/tag/create?';

    //更新totag
    const URL_UPDATE_TOTAG = 'https://qyapi.weixin.qq.com/cgi-bin/tag/update?';

    //删除totag
    const URL_DELETE_TOTAG = 'https://qyapi.weixin.qq.com/cgi-bin/tag/delete?';

    //创建标签用户
    const URL_CREATE_TOTAGUSER = 'https://qyapi.weixin.qq.com/cgi-bin/tag/addtagusers?';

    //删除标签用户
    const URL_DELETE_TOTAGUSER = 'https://qyapi.weixin.qq.com/cgi-bin/tag/deltagusers?';

    //二次验证
    const URL_USER_AUTHSUCC = 'https://qyapi.weixin.qq.com/cgi-bin/user/authsucc';

    //userid与openid互换
    const URL_OPEN_CHANGE = 'https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_openid?';

    /*==========================聊天接口======================= */
    //创建会话
    const URL_CHAT_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/chat/create?';

    //获取会话
    const URL_CHAT_GET = 'https://qyapi.weixin.qq.com/cgi-bin/chat/get?';

    //修改会话信息
    const URL_CHAT_UPDATE = 'https://qyapi.weixin.qq.com/cgi-bin/chat/update?';

    //退出会话
    const URL_CHAT_QUIT = 'https://qyapi.weixin.qq.com/cgi-bin/chat/quit?';

    //清除会话未读状态
    const URL_CHAT_CLEARNOTIFY = 'https://qyapi.weixin.qq.com/cgi-bin/chat/clearnotify?';

    //发送聊天
    const URL_CHAT_SEND = 'https://qyapi.weixin.qq.com/cgi-bin/chat/send?';

    //设置成员新消息免打扰
    const URL_CHAT_SETMUTE = 'https://qyapi.weixin.qq.com/cgi-bin/chat/setmute?';

    /*==========================通讯录同步接口======================= */
    //分页拉取数据
    const URL_SYNC_GETPAGE = 'https://qyapi.weixin.qq.com/cgi-bin/sync/getpage?';

    //限制用户api访问次数
    const TOKEN_API_VISIT_NUMBER = 5;

    //限制用户时间内访问次数(单位：毫秒)
    const TOKEN_API_VISIT_TIME = 60;

    //锁住企业时间(单位：毫秒)
    const TOKEN_LOCK_COMPANY_TIME = 3600;

    //电话会议的API地址
    const TELMEETING_API = 'http://211.150.71.180:14900/cincc/';
}
