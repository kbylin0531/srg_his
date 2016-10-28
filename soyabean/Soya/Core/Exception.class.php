<?php
/**
 * Created by linzhv@outlook.com
 * User: asus
 * Date: 16-6-14
 * Time: 10:50
 */
namespace Soya\Core;
use Exception as phpException;
use Soya\Extend\Logger;

/**
 * Class Exception
 * @package Soya\Core
 */
class Exception extends phpException {
    /**
     * Exception constructor.
     * @param ...
     */
    public function __construct(){
        if(func_num_args() > 0){//无参数时仅仅创建对象
            $args = func_get_args();
            $this->message = var_export($args,true);
        }
    }

    /**
     * 设置错误信息
     * @param array|string $messages
     */
    public function setMessage($messages){
        $args = func_get_args();
        if(count($args) > 1){
            $this->message = var_export($args,true);
        }elseif(is_string($messages)){
            $this->message = $messages;
        }else{
            $this->message = var_export($messages,true);
        }
    }

    /**
     * 直接抛出异常信息
     * @return false
     * @throws Exception
     */
    public static function throwing(){
        $exception = new Exception();
        call_user_func_array([$exception,'setMessage'],func_get_args());//用于可变参数的情况
        throw $exception;
    }




}