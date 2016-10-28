<?php
namespace Sharin;

use Exception;
use Sharin\Core\Trace;
use Sharin\Behaviours\StaticCacheBehaviour;
use Sharin\Core\Configger;
use Sharin\Exceptions\ParameterInvalidException;

/**
 * Class Utils
 * @package Sharin
 */
final class Utils {

    /**
     * 数据签名认证
     * @param  mixed  $data 被认证的数据
     * @return string       签名
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public static function dataSign($data) {
        is_array($data) or $data = [$data];
        ksort($data);
        return sha1(http_build_query($data));
    }
    /**
     * 加载显示模板
     * @param string $tpl template name in folder 'Tpl'
     * @param array|null $vars vars array to extract
     * @param bool $clean it will clean the output cache if set to true
     * @param bool $isfile 判断是否是模板文件
     */
    public static function loadTemplate($tpl,array $vars=null, $clean=true, $isfile=false){
        $clean and ob_get_level() > 0 and ob_end_clean();
        $vars and extract($vars, EXTR_OVERWRITE);
        $path = ($isfile or is_file($tpl))?$tpl:SR_PATH_FRAMEWORK."/Template/{$tpl}.php";
        is_file($path) or $path = SR_PATH_FRAMEWORK.'/Template/systemerror.php';
        include $path;
    }
    /**
     * 将C风格字符串转换成JAVA风格字符串
     * C风格      如： sub_string
     * JAVA风格   如： SubString
     * @param string $str
     * @param int $ori it will translate c to java style if $ori is set to true value and java to c style on false
     * @return string
     */
    public static function styleStr($str,$ori=1){
        static $cache = [];
        $key = "{$str}.{$ori}";
        if(!isset($cache[$key])){
            $cache[$key] = $ori?
                ucfirst(preg_replace_callback('/_([a-zA-Z])/',function($match){return strtoupper($match[1]);},$str)):
                strtolower(ltrim(preg_replace('/[A-Z]/', '_\\0', $str), '_'));
        }
        return $cache[$key];
    }

    /**
     * 自动从运行环境中获取URI
     * 直接访问：
     *  http://www.xor.com:8056/                => '/'
     *  http://localhost:8056/_xor/             => '/_xor/'  ****** BUG *******
     * @param bool $reget 是否重新获取，默认为false
     * @return null|string
     */
    public static function pathInfo($reget=false){
        static $uri = '/';
        if($reget or '/' === $uri){
            if(isset($_SERVER['PATH_INFO'])){
                //如果设置了PATH_INFO则直接获取之
                $uri = $_SERVER['PATH_INFO'];
            }else{
                $scriptlen = strlen($_SERVER['SCRIPT_NAME']);
                if(strlen($_SERVER['REQUEST_URI']) > $scriptlen){
                    $pos = strpos($_SERVER['REQUEST_URI'],$_SERVER['SCRIPT_NAME']);
                    if(false !== $pos){
                        //在不支持PATH_INFO...或者PATH_INFO不存在的情况下(URL省略将被认定为普通模式)
                        //REQUEST_URI获取原生的URL地址进行解析(返回脚本名称后面的部分)
                        if(0 === $pos){//PATHINFO模式
                            $uri = substr($_SERVER['REQUEST_URI'], $scriptlen);
                        }else{
                            //重写模式
                            $uri = $_SERVER['REQUEST_URI'];
                        }
                    }
                }else{}//URI短于SCRIPT_NAME，则PATH_INFO等于'/'
            }
        }
        return $uri;
    }

    /**
     * 检查继承关系
     * @param $classnm
     * @param $name
     * @return bool
     */
    public static function checkParent($classnm,$name){
        $list = class_parents($classnm,false);
        return isset($list[$name]);
    }

    /**
     * 检查接口实现关系
     * @param $classnm
     * @param $name
     * @return bool
     */
    public static function checkInterface($classnm,$name){
        $list = class_implements($classnm,false);
        return isset($list[$name]);
    }

    /**
     * 检查trait使用关系
     * @param $classnm
     * @param $name
     * @return bool
     */
    public static function checkTrait($classnm,$name){
        $list = class_uses($classnm,false);
        return isset($list[$name]);
    }

    /**
     * 调用类的静态方法
     * 注意，调用callable的时候如果是静态方法，则不能带小括号，就像函数名称一样
     *      例如：$callable = "{$clsnm}::{$method}";将永远返回false
     * @param string $clsnm class name
     * @param string $method method name
     * @return mixed|null
     */
    public static function callStatic($clsnm,$method){
        if(class_exists($clsnm,false)) {
            $callable = "{$clsnm}::{$method}";
            if(is_callable($callable)){
                try{
                    return $clsnm::$method();
                }catch (\Exception $e){
                    if(SR_DEBUG_MODE_ON) Developer::trace($e->getMessage());
                }
            }
        }
        return null;
    }

    /**
     * 转换成php处理文件系统时所用的编码
     * 即UTF-8转GB2312
     * @param string $str 待转化的字符串
     * @param string $strencode 该字符串的编码格式
     * @return string|false 转化失败返回false
     */
    public static function toSystemEncode($str,$strencode='UTF-8'){
        return iconv($strencode,SR_OS_ENCODING.'//IGNORE',$str);
    }

    /**
     * 转换成程序使用的编码
     * 即GB2312转UTF-8
     * @param string $str 待转换的字符串
     * @param string $program_encoding
     * @return string|false 转化失败返回false
     */
    public static function toProgramEncode($str, $program_encoding='UTF-8'){
        return iconv(SR_OS_ENCODING,"{$program_encoding}//IGNORE",$str);
    }

    /**
     * 获取类常量
     * use defined() to avoid error of E_WARNING level
     * @param string $class 完整的类名称
     * @param string $constant 常量名称
     * @param mixed $replacement 不存在时的代替
     * @return mixed
     */
    public static function constant($class,$constant,$replacement=null){
        if(!class_exists($class,true)) return $replacement;
        $constant = "{$class}::{$constant}";
        return defined($constant)?constant($constant):$replacement;
    }

    /**
     * 将参数二的配置合并到参数一种，如果存在参数一数组不存在的配置项，跳过其设置
     * @param array $dest dest config
     * @param array $sourse sourse config whose will overide the $dest config
     * @param bool|false $cover it will merge the target in recursion while $cover is true
     *                  (will perfrom a high efficiency for using the built-in function)
     * @return mixed
     */
    public static function merge(array $dest,array $sourse,$cover=false){
        foreach($sourse as $key=>$val){
            $exists = key_exists($key,$dest);
            if($cover){
                //覆盖模式
                if($exists and is_array($dest[$key])){
                    //键存在 为数组
                    $dest[$key] = self::merge($dest[$key],$val,true);
                }else{
                    //key not exist or not array 直接覆盖
                    $dest[$key] = $val;
                }
            }else{
                //非覆盖模式
                $exists and $dest[$key] = $val;
            }
        }
        return $dest;
    }

    /**
     * 过滤掉数组中与参数二计算值相等的值，可以是保留也可以是剔除
     * @param array $array
     * @param callable|array|mixed $comparer
     * @param bool $leave
     * @return void
     */
    public static function filter(array &$array, $comparer=null, $leave=true){
        static $result = [];
        $flag = is_callable($comparer);
        $flag2 = is_array($comparer);
        foreach ($array as $key=>$val){
            if($flag?$comparer($key,$val):($flag2?in_array($val,$comparer):($comparer === $val))){
                if($leave){
                    unset($array[$key]);
                }else{
                    $result[$key] = $val;
                }
            }
        }
        $leave or $array = $result;
    }

    /**
     * 从字面商判断$path是否被包含在$scope的范围内
     * @param string $path 路径
     * @param string $scope 范围
     * @return bool
     */
    public static function checkInScope($path, $scope) {
        if (false !== strpos($path, '\\')) $path = str_replace('\\', '/', $path);
        if (false !== strpos($scope, '\\')) $scope = str_replace('\\', '/', $scope);
        $path = rtrim($path, '/');
        $scope = rtrim($scope, '/');
        return (SR_IS_WIN ? stripos($path, $scope) : strpos($path, $scope)) === 0;
    }
}

/**
 * Class Behaviour
 * 行为控制,各个行为点的结合代表着应用的生命周期
 * @package Sharin
 */
final class Behaviour {
    /**
     * 标签集合
     * key可能是某种有意义的方法，也可能是一个标识符
     * value可能是闭包函数或者类名称
     * 如果是value类名称，则key可能是其调用的方法的名称（此时会检查这个类中是否存在这个方法），也可能是一个标识符
     * @var array
     */
    private static $tags = [
        ON_INIT    => [],
        ON_INITED  => [],
        ON_START   => [
            StaticCacheBehaviour::class,
        ],
        ON_ROUTE    => [],
        ON_CHECK    => [],
        ON_DISPATCH => [
            StaticCacheBehaviour::class,
        ],
        ON_STOP     => [
            StaticCacheBehaviour::class,
        ],
    ];

    /**
     * 动态注册行为
     * @param string $tag 标签名称
     * @param mixed|array $behavior 行为名称,为array类型时将进行批量注册
     * @return void
     */
    public static function register($tag, $behavior) {
        if (!isset(self::$tags[$tag]))  self::$tags[$tag] = [];
        if (is_array($behavior)) {
            self::$tags[$tag] = array_merge(self::$tags[$tag], $behavior);
        } else {
            self::$tags[$tag][] = $behavior;
        }
    }

    /**
     * 监听标签的行为
     * @param string $tag 标签名称
     * @param mixed $params 传入回调闭包或者对象方法的参数
     * @return void
     */
    public static function listen($tag, $params = null) {
        if(SR_DEBUG_MODE_ON) Developer::status($tag);
        if (!empty(self::$tags[$tag])) {
            foreach(self::$tags[$tag] as $name){
                if (false === self::exec($name, $tag, $params)) break; // 如果返回false 则中断行为执行
            }
        }
    }

    /**
     * 执行某个行为
     * @param string $callableorclass 闭包或者类名称
     * @param string $tag 方法名（标签名）
     * @param Mixed $params 方法的参数
     * @return mixed
     * @throws ParameterInvalidException
     */
    private static function exec($callableorclass, $tag , $params = null) {
        static $_instances = [];
        if ($callableorclass instanceof \Closure) {
            //如果是闭包，则直接执行闭包函数
            return $callableorclass($params);
        }elseif(is_string($callableorclass)){
            if(!isset($_instances[$callableorclass])){
                $_instances[$callableorclass] = new $callableorclass();
            }
            $obj = $_instances[$callableorclass];
            if(!is_callable([$obj, $tag])) $tag = 'run';//tag默认是方法名称
            return $obj->$tag($params,$tag);
        }
        throw new ParameterInvalidException($callableorclass,['Closure','string']);
    }
}

/**
 * Class Developer
 * Developer tool to improve performance and debug
 * @package Sharin
 */
final class Developer {

    private static $showTrace = SR_DEBUG_MODE_ON;

    /**
     * @var array
     */
    private static $highlightes = [];
    /**
     * @var array
     */
    private static $_status = [];
    /**
     * @var array
     */
    private static $_traces = [];

    /**
     * Open the page trace
     * @return void
     */
    public static function openTrace(){
        self::$showTrace = true;
    }

    /**
     * Close the page trace
     * @return void
     */
    public static function closeTrace(){
        self::$showTrace = false;
    }

    /**
     * record the runtime's time and memory usage
     * @param null|string $tag tag of runtime point
     * @return void
     */
    public static function status($tag){
        SR_DEBUG_MODE_ON and self::$_status[$tag] = [
            microtime(true),
            memory_get_usage(),
        ];
    }

    /**
     * import status
     * @param string $tag
     * @param array $status
     */
    public static function import($tag,array $status){
        self::$_status[$tag] = $status;
    }

    /**
     * 记录下跟踪信息
     * @param string|mixed $message
     * @param ...
     * @return void
     */
    public static function trace($message=null){
        static $index = 0;
        if(!SR_DEBUG_MODE_ON) return;
        if(null === $message){
            self::$showTrace and Trace::show(self::$highlightes,self::$_traces,self::$_status);
        }else{
            $location = debug_backtrace();
            if(isset($location[0])){
                $location = "{$location[0]['file']}:{$location[0]['line']}";
            }else{
                $location = $index ++;
            }
            if(func_num_args() > 1) $message = var_export(func_get_args(),true);
            if(!is_string($message)) $message = var_export($message,true);
            if(isset(self::$_traces[$location])){
                $index ++;//it may called multi-times in some place
                $location = "$location ($index)";
            }
            self::$_traces[$location] = $message;
        }
    }
}

/**
 * Class ClassLoader
 * manage the class auto-loading and initialization
 * @package Sharin
 */
final class ClassLoader {
    /**
     * 加载规则
     * [
     *      0   => '',//加载路径，相对于SR_PATH_BASE
     *      1   => '',//文件后缀名称
     * ]
     * @var array
     */
    protected static $rules = [
        0           => [
            'path'  => 'Library',
        ],
        'Sharin'    => [
            'ext'   => 'srg',
        ],
        'Web'       => [
            'ext'   => 'class.php',
        ],
        'Explorer'  => [
            'ext'   => 'class.php',
        ],
    ];

    /**
     * @var array array of key-valure pairs (name to relative path)
     */
    private static $map = [];

    /**
     * import classes from outer
     * @param array $map
     */
    public static function import(array $map){
        $map and self::$map = array_merge(self::$map,$map);
    }

    public static function addRule(){

    }

    /**
     * default loader for this system
     * @param string $clsnm class name
     * @return void
     */
    public static function load($clsnm){
        if(isset(self::$map[$clsnm])) {
            include_once self::$map[$clsnm];
        }else{
            $pos = strpos($clsnm,'\\');
            if($pos === false){
                $rel = $clsnm;
                $index = 0;
            }else{
                $rel = str_replace('\\', '/', $clsnm);
                $index = substr($clsnm,0,$pos);
            }

            $path = isset(self::$rules[$index]['path'])? self::$rules[$index]['path'] : '';
            $ext  = isset(self::$rules[$index]['ext'])? self::$rules[$index]['ext'] : 'php';//兼容
            $path = SR_PATH_BASE."/{$path}/{$rel}.{$ext}";
            if(is_file($path)) include self::$map[$clsnm] = $path;
        }
        Utils::callStatic($clsnm,'__initializationize');
    }

    /**
     * register class autoloader
     * @param callable $autoloader
     * @throws SharinException
     */
    public static function register(callable $autoloader=null){
        null === $autoloader and $autoloader = [static::class,'load'];
        if(!spl_autoload_register($autoloader,false,true)){
            throw new SharinException('Faile to register class autoloader!');
        }
    }

}

/**
 * Class SharinException
 *
 * @package Sharin
 */
class SharinException extends Exception {

    /**
     * SharinException constructor.
     */
    public function __construct(){
        $args = func_get_args();
        if(count($args) > 1){
            $message = var_export($args,true);
        }else{
            $message = $args[0];
        }
        $this->message = is_string($message)?$message:var_export($message,true);
    }

    /**
     * 直接抛出异常信息
     * @param ...
     * @return mixed
     * @throws SharinException
     */
    public static function throwing(){
        $clsnm = static::class;//extend class name
        throw new $clsnm(func_get_args());
    }
}
/**
 * Trait C
 * manage the configuration of this class
 *
 * @synthesize array $config static
 *
 * @package Sharin
 */
trait C {

    /**
     * 类的静态配置
     * @var array
     */
    private static $_cs = [];

    /**
     * initialize the class with config
     * :eg the name of this method is much special to make class initialize automaticlly
     * @return void
     */
    public static function __initializationize(){
        $clsnm = static::class;
        if(!isset(self::$_cs[$clsnm])){
            //get convention
            self::$_cs[$clsnm] = Utils::constant($clsnm,'CONF_CONVENTION',[]);

            //load the outer config
            $conf = Configger::load($clsnm);
            $conf and self::$_cs[$clsnm] = Utils::merge(self::$_cs[$clsnm],$conf,true);
        }
        //auto init
        Utils::callStatic($clsnm,'__init');
    }

    /**
     * 获取该类的配置（经过用户自定义后）
     * @param string|null $name 配置项名称
     * @param mixed $replacement 找不到对应配置时的默认配置
     * @return array
     */
    final protected static function getConfig($name=null,$replacement=null){
        $clsnm = static::class;
        isset(self::$_cs[$clsnm]) or self::$_cs[$clsnm] = [];
        return isset($name) ? (isset(self::$_cs[$clsnm][$name])?self::$_cs[$clsnm][$name]:$replacement) : (isset(self::$_cs[$clsnm])?self::$_cs[$clsnm]:$replacement);
    }

    /**
     * 设置运行时配置
     * @todo:
     * @static
     * @param string $name
     * @param mixed $value
     * @return void
     */
    final protected static function setConfig($name,$value){}

}

/**
 * Trait I
 * manage the instantiation of this class
 * @package Sharin
 */
trait I {

    /**
     * @var array 驱动列表
     */
    private static $_is = [];

    /**
     * 更具驱动名称和参数获取驱动实例
     * Get instance of this class of special driver by config
     * @param array|int|float|string|null $config it will convered to identify
     * @param string $clsnm class name ,it will always be driver name if value set to re-null
     * @param string|int $identify Instance identify
     * @return object
     * @throws ParameterInvalidException
     */
    public static function getInstance($config=null,$clsnm=USE_DEFAULT,$identify=null){
        $clsnm === null and $clsnm = static::class;
        if(null === $identify){
            switch (gettype($config)){
                case TYPE_ARRAY:
                    $identify = Utils::dataSign($config);
                    break;
                case TYPE_FLOAT:
                case TYPE_INT:
                case TYPE_STR:
                    $identify = (string) $config;
                    break;
                case TYPE_NULL:
                    $identify = 0;
                    break;
                default:
                    throw new ParameterInvalidException($config);
            }
        }

        if(!isset(self::$_is[$clsnm][$identify])){
            self::$_is[$clsnm][$identify] = (null !== $config ? new $clsnm($config) :new $clsnm());
        }
        return self::$_is[$clsnm][$identify];
    }

    /**
     * 判断是否存在实例
     * @param array|int|float|string|null $config it will convered to identify
     * @param string $clsnm class name ,it will always be driver name if value set to re-null
     * @return bool
     * @throws ParameterInvalidException
     */
    public static function hasInstance($config=null,$clsnm=USE_DEFAULT){
        isset($clsnm) or $clsnm = static::class;
        if(!isset(self::$_is[$clsnm])){
            self::$_is[$clsnm] = [];
            return false;
        }
        //get identify
        switch (gettype($config)){
            case TYPE_ARRAY:
                $identify = Utils::dataSign($config);
                break;
            case TYPE_FLOAT:
            case TYPE_INT:
            case TYPE_STR:
                $identify = (string) $config;
                break;
            case TYPE_NULL:
                $identify = 0;
                break;
            default:
                throw new ParameterInvalidException($config);
        }
        return isset(self::$_is[$clsnm][$identify]);
    }
}


/**
 * Class Core
 *
 * @property array $config
 *  'sample class' => [
 *      'DRIVER_DEFAULT_INDEX' => 0,//默认驱动ID，类型限定为int或者string
 *      'DRIVER_CLASS_LIST' => [],//驱动类的列表
 *      'DRIVER_CONFIG_LIST' => [],//驱动类列表参数
 *  ]
 *
 * @package Sharin
 */
abstract class Core {

    use C,I;

    /**
     * 类实例的驱动
     * @var object
     */
    private static $_drivers = [
        /************************************
        'sample class' => Object
         ************************************/
    ];
    /**
     * @var array 当前类使用的默认驱动的角标
     */
    protected static $_current_using = [];

    /**
     * @param int|string $index 指派当前默认的角标以覆盖静态配置文件中固定的设置
     */
    public static function using($index){
        self::$_current_using[static::class] = $index;
    }

    /**
     * 获取Driver-based Instance
     * @param null $identify
     * @return object
     */
    public static function instance($identify=USE_DEFAULT){
        if($identify === USE_DEFAULT and isset(self::$_current_using[static::class])){
            $identify = self::$_current_using[static::class];
        }
        $instance = self::getInstance(null,null,$identify);
        $instance->setDriver(self::driver($identify));
        return $instance;
    }

    /**
     * it maybe a waste of performance
     * @param string|int|null $identify it will get the default index if set to null
     * @return object
     * @throws SharinException
     */
    public static function driver($identify=USE_DEFAULT){
        $clsnm = static::class;
        isset(self::$_drivers[$clsnm]) or self::$_drivers[$clsnm] = [];
        $config = null;

        //get default identify
        if(USE_DEFAULT === $identify) {
            $config = static::getConfig();
            if(isset($config[DRIVER_DEFAULT_INDEX])){
                $identify = $config[DRIVER_DEFAULT_INDEX];
            }else{
                throw new SharinException("找不到类'{$clsnm}'关于'{$identify}'的驱动！");
            }
        }

        //instance a driver for this identify
        if(!isset(self::$_drivers[$clsnm][$identify])){
            $config or $config = static::getConfig();
            if(isset($config[DRIVER_CLASS_LIST][$identify])){
                $driver_conf = isset($config[DRIVER_CONFIG_LIST][$identify])?$config[DRIVER_CONFIG_LIST][$identify]:null;
                $driver_class = $config[DRIVER_CLASS_LIST][$identify];
                self::$_drivers[$clsnm][$identify] = self::getInstance($driver_conf,$driver_class,$identify);
            }else{
                throw new SharinException("无法创建类'{$clsnm}'关于'{$identify}'的驱动！");
            }
        }
        return self::$_drivers[$clsnm][$identify];
    }

    /**
     * Use driver method as its static method
     * @param string $method method name
     * @param array $arguments method arguments
     * @return mixed
     * @throws SharinException
     */
    public static function __callStatic($method, $arguments) {
        $driver = static::driver();
        if(!method_exists($driver,$method)){
            $clsnm = static::class;
            throw new SharinException("方法'{$method}'不存在于驱动类'{$clsnm}'中!");
        }
        return call_user_func_array([$driver, $method], $arguments);
    }

    /**
     * @var object 实例的驱动实例，一实例一驱动
     */
    protected $_driver = null;

    /**
     * @return object
     */
    public function getDriver(){
        return $this->_driver;
    }
    /**
     * @param object $driver
     */
    public function setDriver($driver){
        $this->_driver = $driver;
    }

    /**
     * Use driver method as its instance method
     * @param string $method method name
     * @param array $arguments method arguments
     * @return mixed
     * @throws SharinException
     */
    public function __call($method, $arguments) {
        $clsnm = static::class;
        if(!isset($this->_driver)){
            throw new SharinException($clsnm);
        }
        if(!method_exists($this->_driver,$method)){
            throw new SharinException("方法'{$method}'不存在于驱动实例'{$clsnm}'中!");
        }
        return call_user_func_array([$this->_driver, $method], $arguments);
    }
}
