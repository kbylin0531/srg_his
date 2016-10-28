<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/22
 * Time: 11:30
 */

namespace Soya\Util;


class Datetime {

    /**
     * 获取日期
     * 返回格式：
     * array (
     *      0 => '2016-03-17 09:55:55',
     *      1 => '2016-03-17',
     *      2 => '09',
     *      3 => '09:55:55',
     * )
     * @param bool|false $refresh
     * @return array 日期各个部分数组
     */
    public static function getDate($refresh=false){
        static $_date = [];
        if($refresh or !$_date){
            //完整时间
            $_date[0] = date('Y-m-d H:i:s');
            $_date[1] = substr($_date[0],0,10);
            $_date[2] = substr($_date[0],11,2);
            $_date[3] = substr($_date[0],11);
        }
        return $_date;
    }


}