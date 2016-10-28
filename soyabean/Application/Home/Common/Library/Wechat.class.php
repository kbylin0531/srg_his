<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/8/16
 * Time: 6:41 PM
 */

namespace Application\Home\Common\Library;


use Soya\Extend\Session;

class Wechat {

    public static function getToken($token = NULL){
        $session = Session::getInstance();
        $stoken = $session->get( 'token' );
        

        return $token;
    }

    public static function getAccessToken(){
        return '';
    }


    private static function getContentFromUrl($url){
        $context = stream_context_create ( array (
            'http' => array (
                'timeout' => 30
            )
        ) ); // 超时时间，单位为秒

        return file_get_contents ( $url, 0, $context );
    }


    function _autoUpdateUser() {
        // 获取openid列表
        $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=' . self::getAccessToken(); // 只取第一页数据
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=' . self::getAccessToken();
    }
    // 与微信的用户组保持同步
    function _updateWechatGroup() {
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/get?access_token=' . get_access_token ();
    }

}