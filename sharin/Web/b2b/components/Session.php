<?php
class Session {

    public static function hasStarted(){
        return function_exists ( 'session_status' ) ? ( PHP_SESSION_ACTIVE == session_status () ) : ( '' === session_id () );
    }

    public static function start(){
        self::hasStarted() or session_start();
    }

    /**
     * 设置session
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public static function set($name,$value){
        self::start();
        if(strpos($name,'.')){
            list($name1,$name2) =   explode('.',$name,2);
            $_SESSION[$name1][$name2] = $value;
        }else{
            $_SESSION[$name] = $value;
        }
    }

    /**
     * 获取指定名称的session的值
     * @param null|string $name 为null时获取全部session
     * @param null $replacement
     * @return mixed return null if not set
     */
    public static function get($name=null,$replacement=null){
        self::start();
        if(isset($name)){
            if(strpos($name,'.')){
                list($name1,$name2) =   explode('.',$name);
                return isset($_SESSION[$name1][$name2])?$_SESSION[$name1][$name2]:$replacement;
            }else{
                return isset($_SESSION[$name])?$_SESSION[$name]:$replacement;
            }
        }
        return $_SESSION;
    }

    /**
     * 检查是否设置了指定名称的session
     * @param string $name
     * @return bool
     */
    public static function has($name){
        self::start();
        if(strpos($name,'.')){ // 支持数组
            list($name1,$name2) =   explode('.',$name);
            return isset($_SESSION[$name1][$name2]);
        }else{
            return isset($_SESSION[$name]);
        }
    }


    /**
     * 删除所有session
     * @return void
     */
    public static function clear(){
        self::start();
        $_SESSION = [];
    }

    /**
     * 清除指定名称的session
     * @param string|array $name 如果为null将清空全部
     * @return bool
     */
    public static function delete($name){
        self::start();
        if(is_string($name)){
            if(strpos($name,'.')){
                list($name1,$name2) =   explode('.',$name);
                unset($_SESSION[$name1][$name2]);
            }else{
                unset($_SESSION[$name]);
            }
        }elseif(is_array($name)){
            foreach($name as $val){
                self::delete($val);
            }
        }else{
            return false;
        }
        return true;
    }

    /**
     * 每隔一段时间调用一次并将结果返回
     * @param int $spacing 间隔事件，以秒计
     * @param callable $call
     * @return mixed
     */
    public static function waitOn($spacing,callable $call){
        $lasttime = Session::get('last_request_time',false);
        if($lasttime){
            $time = time() - $lasttime;
            if($time <= $spacing){
                sleep($spacing - $time + 1);
            }
        }
        $content = $call();
        Session::set('last_request_time',time());
        return $content;
    }


}