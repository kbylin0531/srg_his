<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/8/16
 * Time: 6:56 PM
 */

namespace Application\Home\Common\Library;


use Soya\Core\URI;
use Soya\Util\Helper\StringHelper;

class Utils {

    /**
     * 插件显示内容里生成访问插件的url
     *
     * @param string $url url
     * @param array $param 参数
     * @return bool|string
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    function addons_url($url, $param = array()) {
        // 凡星：修复如user_center://user_center/add 识别错误的问题
        $addons = null;
        $urlArr = explode ( '://', $url );
        if (stripos ( $urlArr [0], '_' ) !== false) {
            $addons = $urlArr [0];
            $url = HTTP_PREFIX . $urlArr [1];
        }
        $url = parse_url ( $url );
        $addons and $url ['scheme'] = $addons;

        $addons = StringHelper::toCStyle ( $url ['scheme'] );
        $controller = StringHelper::toCStyle ( $url ['host'] ) ;
        $action = trim ( strtolower ( $url ['path'] ));

        /* 解析URL带的参数 */
        if (isset ( $url ['query'] )) {
            parse_str ( $url ['query'], $query );
            $param = array_merge ( $query, $param );
        }

        /* 基础参数 */
        $params = array (
            '_addons' => ucfirst ( $addons ),
            '_controller' => ucfirst ( $controller ),
            '_action' => $action
        );
        $params = array_merge ( $params, $param ); // 添加额外参数

        $qurl = is_dir ( PATH_BASE.'Addon/' . $params ['_addons'] ) ? "Home/Addons/plugin" : "Home/Addons/execute";

        return URI::url( $qurl, $params );
    }


}