<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/27
 * Time: 21:42
 */

namespace Soya\Extend;
/**
 * Class Logic
 * 处理逻辑运算的管理器
 * @package Soya\Extend
 */
class Logic extends \Soya{
    /**
     * @return Logic
     * @param mixed $i
     * @return object
     */
    public static function getInstance($i=null){
        return parent::getInstance(SINGLE_INSTANCE);
    }

}