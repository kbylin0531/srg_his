<?php

/**
 * Created by linzhv@outlook.com
 * User: asus
 * Date: 16-6-14
 * Time: 10:35
 */
namespace Soya\Core;
use ReflectionMethod;
use Soya\Exception\Dispatcher\ActionNotFoundException;
use Soya\Exception\Dispatcher\ControllerNotFoundException;
use Soya\Exception\Dispatcher\ModuleNotFoundException;
use Soya\Util\SEK;

/**
 * Class Dispatcher
 * 将URI解析结果调度到指定的控制器下的方法下
 * @package Soya\Core
 */
class Dispatcher extends \Soya {

    const CONF_NAME = 'dispatcher';
    const CONF_CONVENTION = [
        //空缺时默认补上,Done!
        'INDEX_MODULE'      => 'Home',
        'INDEX_CONTROLLER'  => 'Index',
        'INDEX_ACTION'      => 'index',

    ];

    private $_module = null;
    private $_controller = null;
    private $_action = null;

    /**
     * 匹配空缺补上默认
     * @param string|array $modules
     * @param string $ctrler
     * @param string $action
     * @return $this
     */
    public function fill($modules,$ctrler,$action){
        $config = self::getConfig();
        $this->_module      = $modules?$modules:$config['INDEX_MODULE'];
        $this->_controller  = $ctrler?$ctrler:$config['INDEX_CONTROLLER'];
        $this->_action      = $action?$action:$config['INDEX_ACTION'];
        is_array($modules) and $this->_module = SEK::toModulesString($modules,'/');
        return $this;
    }

    /**
     * 制定对应的方法
     * @param string $modules
     * @param string $ctrler
     * @param string $action
     * @return mixed
     * @throws Exception
     */
    public function exec($modules=null,$ctrler=null,$action=null){
        null === $modules   and $modules = $this->_module;
        null === $ctrler    and $ctrler = $this->_controller;
        null === $action    and $action = $this->_action;

        self::trace($modules,$ctrler,$action);

        $modulepath = PATH_BASE."Application/{$modules}";//linux 不识别 \\

        strpos($modules,'/') and $modules = str_replace('/','\\',$modules);

        //模块检测
        if(!is_dir($modulepath)){
            ModuleNotFoundException::throwing($modules);
        }

        //在执行方法之前定义常量,为了能在控制器的构造函数中使用这三个常量
        define('REQUEST_MODULE',$modules);//请求的模块
        define('REQUEST_CONTROLLER',$ctrler);//请求的控制器
        define('REQUEST_ACTION',$action);//请求的操作

        //控制器名称及存实性检测
        $className = "Application\\{$modules}\\Controller\\{$ctrler}Controller";
        class_exists($className) or ControllerNotFoundException::throwing($modules,$className);
        $classInstance =  new $className();
        //方法检测
        method_exists($classInstance,$action) or ActionNotFoundException::throwing($modules,$className,$action);
        $method = new ReflectionMethod($classInstance, $action);

        $result = null;
        if ($method->isPublic() and !$method->isStatic()) {//仅允许非静态的公开方法
            //方法的参数检测
            if ($method->getNumberOfParameters()) {//有参数
                $args = self::fetchMethodArguments($method);
                //执行方法
                $result = $method->invokeArgs($classInstance, $args);
            } else {//无参数的方法调用
                $result = $method->invoke($classInstance);
            }
        } else {
            Exception::throwing($className, $action);
        }


        \Soya::recordStatus('execute_end');
        return $result;
    }



    /**
     * 获取传递给盖饭昂奋的参数
     * @param ReflectionMethod $targetMethod
     * @return array
     * @throws Exception
     */
    private static function fetchMethodArguments(ReflectionMethod $targetMethod){
        //获取输入参数
        $vars = $args = [];
        switch(strtoupper($_SERVER['REQUEST_METHOD'])){
            case 'POST':$vars    =  array_merge($_GET,$_POST);  break;
            case 'PUT':parse_str(file_get_contents('php://input'), $vars);  break;
            default:$vars  =  $_GET;
        }
        //获取方法的固定参数
        $methodParams = $targetMethod->getParameters();
        //遍历方法的参数
        foreach ($methodParams as $param) {
            $paramName = $param->getName();

            if(isset($vars[$paramName])){
                $args[] =   $vars[$paramName];
            }elseif($param->isDefaultValueAvailable()){
                $args[] =   $param->getDefaultValue();
            }else{
                return Exception::throwing("目标缺少参数'{$param}'!");
            }
        }
        return $args;
    }

    /**
     * 加载当前访问的模块的指定配置
     * 配置目录在模块目录下的'Common/Conf'
     * @param string $name 配置名称,多个名称以'/'分隔
     * @param string $type 配置类型,默认为php
     * @return array
     */
    public static function load($name,$type=Configger::TYPE_PHP){
        if(!defined('REQUEST_MODULE')) return Exception::throwing('\'load\'必须在\'exec\'方法之后调用!');//前提是正确制定过exec方法
        $path = PATH_BASE.'Application/'.REQUEST_MODULE.'/Common/Conf/';
//        \Soya\dumpout($path);
        $storage = Storage::getInstance();
        if($storage->has($path) === Storage::IS_DIR){
            $file = "{$path}/{$name}.".$type;
            return Configger::load($file);
        }
        return [];
    }

}