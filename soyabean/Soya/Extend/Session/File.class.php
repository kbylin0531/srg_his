<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/22
 * Time: 12:05
 */

namespace Soya\Extend\Session;
use Soya\Core\Exception;

/**
 * Class File php默认的文件驱动
 * @package Kbylin\System\Core\Session
 */
class File implements SessionInterface{

    /**
     * 清空全部session
     * @return void
     */
    public function clear(){
        $_SESSION = [];
    }

    /**
     * 清除指定名称的session
     * @param string|array $name 如果为null将清空全部
     * @return mixed
     */
    public function delete($name){
        if(is_string($name)){
            if(strpos($name,'.')){
                list($name1,$name2) =   explode('.',$name);
                unset($_SESSION[$name1][$name2]);
            }else{
                unset($_SESSION[$name]);
            }
        }elseif(is_array($name)){
            foreach($name as $val){
                $this->delete($val);
            }
        }else{
            Exception::throwing($name);
        }
    }


    /**
     * 检查是否设置了指定名称的session
     * @param string $name
     * @return bool
     */
    public function has($name){
        if(strpos($name,'.')){ // 支持数组
            list($name1,$name2) =   explode('.',$name);
            return isset($_SESSION[$name1][$name2]);
        }else{
            return isset($_SESSION[$name]);
        }
    }
    /**
     * 获取指定名称的session的值
     * @param null|string $name 为null时获取全部session
     * @return mixed
     */
    public function get($name=null){
        if(isset($name)){
            if(strpos($name,'.')){
                list($name1,$name2) =   explode('.',$name);
                return isset($_SESSION[$name1][$name2])?$_SESSION[$name1][$name2]:null;
            }else{
                return isset($_SESSION[$name])?$_SESSION[$name]:null;
            }
        }
        return $_SESSION;
    }


    /**
     * 设置session
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function set($name,$value){
        if(strpos($name,'.')){
            list($name1,$name2) =   explode('.',$name,2);
            $_SESSION[$name1][$name2] = $value;
        }else{
            $_SESSION[$name] = $value;
        }
    }

}