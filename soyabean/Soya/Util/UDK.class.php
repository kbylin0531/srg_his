<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/28
 * Time: 16:31
 */

namespace Soya\Util;

/**
 * Class UDK
 * User Development Kits
 * @package Soya\Util
 */
class UDK {

    /**
     * 数据签名认证
     * @param  mixed  $data 被认证的数据
     * @return string       签名
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public static function getDataSign($data) {
        //数据类型检测
        if(!is_array($data)){
            $data = (array)$data;
        }
        ksort($data); //排序
        $code = http_build_query($data); //url编码并生成query字符串
        $sign = sha1($code); //生成签名
        return $sign;
    }

}