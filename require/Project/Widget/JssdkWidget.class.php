<?php

namespace Project\Widget;

use Org\WeixinApi\Api;

/**
 * 微信端jssdk组件.
 *
 * @author Cui
 * @Date 2015/03/02
 */
class JssdkWidget
{
    /**
     * 引用weixinjsSDK配置.
     */
    public function jsConfig()
    {
        $JSSDK = Api::factory('JSSDK');

        $appId = Api::getCorpId();
        $signature = $JSSDK->getSignature();
        $nonceStr = $JSSDK->getNonceStr();
        $timeStamp = $JSSDK->getTimeStamp();

        $html = <<<SCRIPT
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
<script>
//layer_loading('组件加载中...');
var apiList = [
                "checkJsApi",
                "onMenuShareTimeline",
                "onMenuShareAppMessage",
                "onMenuShareQQ",
                "onMenuShareWeibo",
                "hideMenuItems",
                "showMenuItems",
                "hideAllNonBaseMenuItem",
                "showAllNonBaseMenuItem",
                "translateVoice",
                "startRecord",
                "stopRecord",
                "onRecordEnd",
                "playVoice",
                "pauseVoice",
                "stopVoice",
                "uploadVoice",
                "downloadVoice",
                "chooseImage",
                "previewImage",
                "uploadImage",
                "downloadImage",
                "getNetworkType",
                "openLocation",
                "getLocation",
                "hideOptionMenu",
                "showOptionMenu",
                "closeWindow",
                "scanQRCode",
                "chooseWXPay",
                "openProductSpecificView",
                "addCard",
                "chooseCard",
                "openCard",
                "openEnterpriseChat",
                "openEnterpriseContact"
            ];

var config = {
    debug: false,
    appId: '$appId',
    timestamp: $timeStamp,
    nonceStr: '$nonceStr',
    signature: '$signature',
    jsApiList: apiList
}

wx.config(config);

wx.error(function (res) {
    alert_info("系统组件加载失败,部分功能失效.");
});

wx.ready(function(){
    //load_stop()
});
</script>
SCRIPT;
//注意 heredoc格式, 此处必须顶格;

        return $html;
    }

    /**
     * 引用weixinjsSDK联系人控件配置.
     */
    public function contactConfig()
    {
        $JSSDK = Api::factory('JSSDK');
        $group = $JSSDK->getContactTicket();
        $signature = $JSSDK->getContactSignature();
        $nonceStr = $JSSDK->getNonceStr();
        $timeStamp = $JSSDK->getTimeStamp();

        $html = <<<SCRIPT
<script>
var wx_contact_config = {
    groupId:"{$group['group_id']}",
    timestamp:"{$timeStamp}",
    nonceStr:"{$nonceStr}",
    signature:"{$signature}"
};
</script>
SCRIPT;

        return $html;
    }
}
