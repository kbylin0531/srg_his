<?php
/**
 * Created by linzhv@outlook.com
 * User: asus
 * Date: 16-6-6
 * Time: 下午10:51
 */
use Soya\Core\Dispatcher;
use Exception as E;
use Soya\Core\URI;
use Soya\Exception\FileNotFoundException;
use Soya\Exception\ParameterInvalidException;
use Soya\Extend\Response;
use Soya\Util\SEK;
use Soya\Core\Router;

require_once __DIR__.'/Common/constant.php';
require_once __DIR__.'/Common/function.php';


//\Soya\dumpout(preg_match('/[,\'\"\*\(\)`.\s]/','acfun')); // 这是什么情况

/**
 * Class Soya
 *
 * 预定义的类常量：
 * const CONF_NAME = '';
 * const CONF_CONVENTION = [];
 *
 * @package Shep
 */
class Soya {

    /**
     * 运行时的内存和时间状态
     * @var array
     */
    private static $_status = [];
    /**
     * 跟踪记录
     * @var array
     */
    private static $_traces = [];
    /**
     * 类名和类路径映射表
     * @var array
     */
    private static $_classes = [];

    /**
     * 类自动加载函数
     * @var callable
     */
    private static $_loader = null;

    /**
     * 错误处理函数
     * @var callable
     */
    private static $_errorhanler = null;

    /**
     * 异常处理函数
     * @var callable
     */
    private static $_exceptionhandler = null;

    /**
     * 脚本结束毁掉函数
     * @var callable
     */
    private static $_shutdownhandler = null;

    /**
     * @var bool
     */
    private static $_allowTrace = true;

    /**
     * 惯例配置
     * @var array
     */
    private static $_convention = [
        'APP_PATH'      => 'Application/',//应用程序目录(以斜杠结尾)
        'PARAMSET'      => '_PARAMS_',
        'LITE_ON'       => false,
        'INSPECT_ON'    => false,
        'ZONE'          => 'Asia/Shanghai',
        'AUTOLOADER'        => null,
        'ERROR_HANDLER'     => null,
        'EXCEPTION_HANDLER' => null,
        'SHUTDOWN_HANDLER'  => null,

    ];

    private static $_flags = [
        'INITED'    => false,
    ];

    /**
     * 初始化应用程序
     * @param array|null $config
     * @return void
     */
    final public static function init(array $config=null){
        self::$_flags['INITED'] and die('Initialization has done!');
        self::recordStatus('init_begin');
        $config and self::$_convention = array_merge(self::$_convention,$config);

        version_compare(PHP_VERSION,'5.4.0','<') and die('Require php >= 5.4 !');
        date_default_timezone_set(self::$_convention['ZONE']) or die('Date default timezone set failed!');

        //decompose the params from request
        if(isset($_REQUEST[self::$_convention['PARAMSET']])){
            $temp = [];
            parse_str($_REQUEST[self::$_convention['PARAMSET']],$temp);
            $_POST = array_merge($_POST,$temp);
            $_REQUEST = array_merge($_REQUEST,$temp);
            $_GET = array_merge($_GET,$temp);
            unset($_REQUEST[self::$_convention['PARAMSET']]);
        }

        defined('P_APP') or define('P_APP', PATH_BASE.self::$_convention['APP_PATH']);
        define('__PUBLIC__',dirname($_SERVER['SCRIPT_NAME']).'/');

        //error  display
        error_reporting(DEBUG_MODE_ON?-1:E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);//php5.3version use code: error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
        ini_set('display_errors',DEBUG_MODE_ON?1:0);

        self::registerAutoloader(self::$_convention['AUTOLOADER']);

        self::registerErrorHandler(self::$_convention['ERROR_HANDLER']);
        self::registerExceptionHandler(self::$_convention['EXCEPTION_HANDLER']);
        self::registerShutdownHandler(self::$_convention['SHUTDOWN_HANDLER']);
        self::$_flags['INITED'] = true;
    }

    /**
     * 开始执行应用程序
     * @param array|null $config
     */
    final public static function start(array $config=null){
        self::$_flags['INITED'] or self::init($config);
        $result = self::parseUri();
        self::dispatch($result['m'],$result['c'],$result['a']);
    }

    /**
     * parse the request and fetch the uri components from uri and hostname
     * @return array
     */
    final public static function parseUri(){
        $result = Router::getInstance(SINGLE_INSTANCE)->parse();
        if(!$result){
            $result = URI::getInstance(SINGLE_INSTANCE)->parse();
        }

        //URL中解析结果合并到$_GET中，$_GET的其他参数不能和之前的一样，否则会被解析结果覆盖
        //注意到$_GET和$_REQUEST并不同步，当动态添加元素到$_GET中后，$_REQUEST中不会自动添加
        empty($result['p']) or $_GET = array_merge($_GET,$result['p']);

        return $result;
    }

    /**
     * dispatch by uri components
     * @return mixed
     */
    /**
     * @param $modules
     * @param $ctrler
     * @param $action
     */
    final public static function dispatch($modules,$ctrler,$action){
//        \Soya\dumpout($modules,$ctrler,$action);
        $result = Dispatcher::getInstance(SINGLE_INSTANCE)->fill($modules,$ctrler,$action)->exec();
        return $result;
    }

    /**
     * register class loader
     * @param callable|null $loader
     * @return void
     */
    private static function registerAutoloader(callable $loader = null){
        $loader or $loader = function ($clsnm){
            if(isset(self::$_classes[$clsnm])) {
                include_once self::$_classes[$clsnm];
            }else{
                $pos = strpos($clsnm,'\\');
                if(false === $pos){
                    $file = PATH_BASE . "{$clsnm}.class.php";//class file place deside entrance file if has none namespace
                    if(is_file($file)) include_once $file;
                }else{
                    $path = PATH_BASE.str_replace('\\', '/', $clsnm).'.class.php';
                    IS_WINDOWS and $path = str_replace('/', '\\', realpath($path));
                    if(is_file($path)) include_once self::$_classes[$clsnm] = $path;
                }
            }
            //TODO：系统类加载时自动初始化
            //存在该方法，进行初始化
//            if(SEK::fetchClassConstant($clsnm,'CONF_NAME',null) === null){
//                call_user_func("{$clsnm}::initClass");//自动进行初始化
//            }
        };
        false === spl_autoload_register($loader) and die('自动加载函数注册失败');
        self::$_loader = $loader;
    }

    /**
     * register error handler for user error
     * @param callable|null $handler
     * @return void
     */
    private static function registerErrorHandler(callable $handler=null){
        /**
         * handel the error
         * @param int $errno error number
         * @param string $errstr error message
         * @param string $errfile error occurring file
         * @param int $errline error occurring file line number
         * @return void
         */
        $handler or $handler =  function ($errno,$errstr,$errfile,$errline){
            IS_REQUEST_AJAX and Response::failed([$errno,$errstr,$errfile,$errline]);
            Response::cleanOutput();

            if(!is_string($errstr)) $errstr = serialize($errstr);
            ob_start();
            debug_print_backtrace();
            $vars = [
                'message'   => "{$errno} {$errstr}",
                'position'  => "File:{$errfile}   Line:{$errline}",
                'trace'     => ob_get_clean(), //be careful
            ];
            if(DEBUG_MODE_ON){
                self::loadTemplate('error',$vars);
            }else{
                self::loadTemplate('user_error');
            }
            exit;
        };
        set_error_handler($handler) ;
        self::$_errorhanler= $handler;
    }

    /**
     * register exception handler
     * @param callable|null $handler
     * @return void
     */
    private static function registerExceptionHandler(callable $handler=null){
        /**
         * handler the exception throw by runtime-processror or user
         * @param E $e ParseError(newer in php7) or Exception
         * @return void
         */
        $handler or $handler = function ($e) {
            if(IS_REQUEST_AJAX){
                if($e instanceof E){
                    Response::failed($e->getMessage());
                }else{
                    Response::failed(var_export($e,true));
                }
            }
            Response::cleanOutput();

            $traceString = $e->getTraceAsString();
            $vars = [
                'message'   => get_class($e).' : '.$e->getMessage(),
                'position'  => 'File:'.$e->getFile().'   Line:'.$e->getLine(),
                'trace'     => $traceString,//be careful with the trace info , it may carry some important
            ];
            if(DEBUG_MODE_ON){
                self::loadTemplate('exception',$vars);
            }else{
                self::loadTemplate('user_error');
            }
            exit;
        };

        set_exception_handler($handler);
        self::$_exceptionhandler = $handler;
    }

    /**
     * Registers a callback to be executed after script execution finishes or exit() is called
     * 按照注册的顺序依次调用
     * @param callable|null $handler
     * @return void
     */
    private static function registerShutdownHandler(callable $handler=null){
        /**
         * called when script shut down
         * @return void
         */
        $handler or $handler = function (){
            self::recordStatus("script_shutdown");
            PAGE_TRACE_ON and !IS_REQUEST_AJAX and self::showTrace();//show the trace info

            if(self::$_convention['LITE_ON']){ //rebuild if lite file not exist
                self::recordStatus('create_lite_begin');
//                Storage::write($this->_litepath,LiteBuilder::compileInBatch(self::$_classes));
                self::recordStatus('create_lite_begin');
            }
            Response::flushOutput();
        };
        register_shutdown_function($handler);
        self::$_shutdownhandler = $handler;
    }

    /**
     * 注销错误和异常处理回调函数
     * @return void
     */
    final public static function unregisterAll(){
        restore_exception_handler();
        restore_error_handler();
    }

//----------------------------------------------------------------------------------------------------------------------
//--------------------------------------   可调用     -------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------------------------

    /**
     * 加载显示模板
     * @param string $tpl template name in folder 'Tpl'
     * @param array|null $vars vars array to extract
     * @param bool $clean it will clean the output cache if set to true
     * @param bool $isfile 判断是否是模板文件
     */
    final public static function loadTemplate($tpl,array $vars=null, $clean=true, $isfile=false){
        $clean and Response::cleanOutput();
        $vars and extract($vars, EXTR_OVERWRITE);
        $path = ($isfile or is_file($tpl))?$tpl:PATH_FRAMEWORK."Tpl/{$tpl}.php";
        is_file($path) or $path = PATH_FRAMEWORK.'Tpl/systemerror.php';
        include $path;
    }

    /**
     * 记录运行时的内存和时间状态
     * @param null|string $tag tag of runtime point
     * @return void
     */
    final public static function recordStatus($tag){
        DEBUG_MODE_ON and self::$_status[$tag] = [
            microtime(true),
            memory_get_usage(),
        ];
    }

    /**
     * 记录下跟踪信息
     * @param string|mixed $message
     * @param ...
     * @return string
     */
    final public static function trace($message){
        $location = debug_backtrace();
        $location = "{$location[0]['file']}{$location[0]['line']}";
        if(func_num_args() > 1){
            $message = var_export(func_get_args(),true);
        }
        return self::$_traces[$location] = $message;
    }

    /**
     * 开启Trace
     * @return void
     */
    final public static function openTrace(){
        self::$_allowTrace = true;
    }

    /**
     * 关闭trace
     * @return void
     */
    final public static function closeTrace(){
        self::$_allowTrace = false;
    }

    /**
     * 显示trace页面
     * @return true 实际返回void
     */
    final protected static function showTrace(){
        if(!self::$_allowTrace) return ;//如果被禁止了trace页面,则不显示该页面
        //吞吐率  1秒/单次执行时间
        if(count(self::$_status) > 1){
            $last  = end(self::$_status);
            $first = reset(self::$_status);            //注意先end后reset
            $stat = [
                1000*round($last[0] - $first[0], 6),
                number_format(($last[1] - $first[1]), 6)
            ];
        }else{
            $stat = [0,0];
        }
        $reqs = empty($stat[0])?'Unknown':1000*number_format(1/$stat[0],8).' req/s';

        //包含的文件数组
        $files  =  get_included_files();
        $info   =   [];
        foreach ($files as $key=>$file){
            $info[] = $file.' ( '.number_format(filesize($file)/1024,2).' KB )';
        }

        //运行时间与内存开销
        $fkey = null;
        $cmprst = [
            'Total' => "{$stat[0]}ms",//一共花费的时间
        ];
        foreach(self::$_status as $key=>$val){
            if(null === $fkey){
                $fkey = $key;
                continue;
            }
            $cmprst["[$fkey --> $key]    "] =
                number_format(1000 * floatval(self::$_status[$key][0] - self::$_status[$fkey][0]),6).'ms&nbsp;&nbsp;'.
                number_format((floatval(self::$_status[$key][1] - self::$_status[$fkey][1])/1024),2).' KB';
            $fkey = $key;
        }
        $vars = [
            'trace' => [
                'General'       => [
                    'Request'   => date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']).' '.$_SERVER['SERVER_PROTOCOL'].' '.$_SERVER['REQUEST_METHOD'],
                    'Time'      => "{$stat[0]}ms",
                    'QPS'       => $reqs,//吞吐率
                    'SessionID' => session_id(),
                    'Cookie'    => var_export($_COOKIE,true),
                    'Obcache-Size'  => number_format((ob_get_length()/1024),2).' KB (Unexpect Trace Page!)',//不包括trace
//                    'LastSQL'       => Model::getLastSql(),
//                    'LastInputs'    => Model::getLastInputs(),
                ],
                'Trace'         => self::$_traces,
                'Files'         => array_merge(['Total'=>count($info)],$info),
                'Status'        => $cmprst,
                'GET'           => $_GET,
                'POST'          => $_POST,
                'SERVER'        => $_SERVER,
                'FILES'         => $_FILES,
                'ENV'           => $_ENV,
                'SESSION'       => isset($_SESSION)?$_SESSION:['SESSION state disabled'],//session_start()之后$_SESSION数组才会被创建
                'IP'            => [
                    '$_SERVER["HTTP_X_FORWARDED_FOR"]'  =>  isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:'NULL',
                    '$_SERVER["HTTP_CLIENT_IP"]'  =>  isset($_SERVER['HTTP_CLIENT_IP'])?$_SERVER['HTTP_CLIENT_IP']:'NULL',
                    '$_SERVER["REMOTE_ADDR"]'  =>  $_SERVER['REMOTE_ADDR'],
                    'getenv("HTTP_X_FORWARDED_FOR")'  =>  getenv('HTTP_X_FORWARDED_FOR'),
                    'getenv("HTTP_CLIENT_IP")'  =>  getenv('HTTP_CLIENT_IP'),
                    'getenv("REMOTE_ADDR")'  =>  getenv('REMOTE_ADDR'),
                ],
            ],
        ];
        self::loadTemplate('trace',$vars,false);//参数三表示不清空之前的缓存区
    }

    /**
     * 读取用户配置
     * @param string|array $name config item name,mapping to filename(not include suffix,and be careful with '.',it will replace with '/')
     * @param string $confpath path of configs,default to constant 'KL_CONFIG_PATH'
     * @return array 返回配置数组，配置文件不存在是返回空数组
     * @throws FileNotFoundException 配置文件不存在时抛出
     * @throws ParameterInvalidException 参数不正确时抛出
     */
    final protected static function loadConfig($name, $confpath=PATH_CONFIG) {
        $result = [];

        $type = gettype($name);
        switch ($type){
            case 'array'://for multiple config
                foreach($name as $item){
                    $temp = self::loadConfig($item);
                    $temp and SEK::merge($result,$temp);
                }
                break;
            case 'string':
                if(false !== strpos('.', $name)){//if == 0,it will worked nice
                    $name = str_replace('.', '/' ,$name);
                }
                $path = $confpath."{$name}.php";
                is_file($path) and $result = include $path;
                break;
            default:
                throw new ParameterInvalidException('string/array(multiple)',$name);
        }
        return $result;
    }

//----------------------------------------------------------------------------------------------------------------------
//--------------------------------------   可继承     -------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------------------------

    /**
     * 类实例库
     * @var array
     */
    private static $_instances = [];
    /**
     * 类的静态配置
     * @var array
     */
    private static $_config = [
        /************************************
        'sample class' => [
        'PRIOR_INDEX' => 0,//默认驱动ID，类型限定为int或者string
        'DRIVER_CLASS_LIST' => [],//驱动类的列表
        'DRIVER_CONFIG_LIST' => [],//驱动类列表参数
        ]
         ************************************/
    ];
    /**
     * 类实例的驱动
     * @var object
     */
    protected $_driver = null;

    /**
     * 获取指定标识符的实例
     * @param string|null $identify 驱动ID，为null时表示获取默认值
     * @return object
     */
    public static function getInstance($identify=null){
        self::checkInit(true);
        $clsnm = static::class;
        isset(self::$_instances[$clsnm]) or self::$_instances[$clsnm] = [];
        if(!isset(self::$_instances[$clsnm][$identify])){
            self::$_instances[$clsnm][$identify] = new $clsnm($identify);
        }
        return self::$_instances[$clsnm][$identify];
    }

    /**
     * Herd继承类的驱动构造参数
     *
     * 仅仅是获得该类的实例的途径:
     *  ① 将构造参数声明为 布尔值'false' 或者 常量'SINGLE_INSTANCE'(值为false)
     *  ② 设置惯例配置时不设置配置项 'PRIOR_INDEX',如果需要设置特定的驱动需要显示声明构造的参数
     *
     * @param string|null|false $identify 驱动ID，为null时表示不适用驱动
     */
    public function __construct($identify=null) {
        self::checkInit(true);
        if(SINGLE_INSTANCE === $identify) return;
        $config = static::getConfig();
        if(!isset($config['DRIVER_CLASS_LIST'][$identify])){
            if(isset($config['PRIOR_INDEX'])){
                $identify = $config['PRIOR_INDEX'];
            }else{
                return ;
            }
        }
//        $identify = isset($config['DRIVER_CLASS_LIST'][$identify])?$identify:
//            (isset($config['PRIOR_INDEX'])?$config['PRIOR_INDEX']:0);
//            \Soya\dump($config,$identify,isset($config['DRIVER_CLASS_LIST'][$identify]));
        //获取驱动类名称
        $driver = $config['DRIVER_CLASS_LIST'][$identify];
        //设置实例驱动
        if(isset($config['DRIVER_CONFIG_LIST'][$identify])){
            $this->_driver = new $driver($config['DRIVER_CONFIG_LIST'][$identify]);
        }else{
            $this->_driver = new $driver();
        }
    }

    /**
     * 初始化类的配置
     * @param null|string $clsnm 类名称
     * @param string|array|null $conf config name of config array.if set to null, it will refer to class constant 'CONF_NAME'
     * @return true
     */
    public static function initClass($clsnm=null,$conf=null){
        $clsnm or $clsnm = static::class;
        if(!isset(self::$_config[static::class])){
            //get convention
            self::$_config[$clsnm] = SEK::fetchClassConstant($clsnm,'CONF_CONVENTION',[]);

            //load the outer config
            if(null === $conf) $conf = SEK::fetchClassConstant($clsnm,'CONF_NAME',null);//outer constant name
            if(is_string($conf)) $conf = self::loadConfig($conf);
//            \Soya\dumpout($conf,self::$_config[$clsnm]);
            is_array($conf) and SEK::merge(self::$_config[$clsnm],$conf,true);
        }
        return true;
    }

    /**
     * 获取该类的配置（经过用户自定义后）
     * @param string|null $key 配置项名称
     * @param mixed $replacement 如果参数一指定的配置项不存在时默认代替的配置项
     * @return array
     */
    protected static function getConfig($key=null,$replacement=null){
        self::checkInit(true);
        isset(self::$_config[static::class]) or self::$_config[static::class] = [];
        if(null !== $key){
            $conf = &self::$_config[static::class];
            if(strpos($key,'.')){//存在且在大于0的位置
                $keys = explode('.',$key);
                $len = count($keys);
                for($i = 0; $i < $len; $i++){
                    if(isset($conf[$key])){
                        if($i === $len - 1){//最后一项
                            return isset($conf[$key])?$conf[$key]:$replacement;
                        }
                    }else{
                        return $replacement;
                    }
                    $conf = & $conf[$key];
                }
            }else{
                return isset($conf[$key])?$conf[$key]:$replacement;
            }
        }
        return self::$_config[static::class];
    }

    /**
     * 设置临时配置，下次请求将会清空
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    protected static function setConfig($key, $value){
        self::checkInit(true);
        isset(self::$_config[static::class]) or self::$_config[static::class] = [];
        if(strpos($key,'.')){//存在且在大于0的位置
            $keys = explode('.',$key);
            $len = count($keys);
            $conf = &self::$_config[static::class];
            for($i = 0; $i < $len; $i++){
                if(!isset($conf[$key])){
                    if($i === $len - 1){
                        //最后一项
                        $conf[$key] = $value;
                    }else{
                        $conf[$key] = [];
                    }
                }
                $conf = & $conf[$key];
            }
        }else{
            self::$_config[static::class][$key] = $value;
        }
    }

    /**
     * 检查类的初始化
     * @param bool $do 未初始化时是否自动初始化
     * @return bool 是否初始化
     */
    protected static function checkInit($do=true){
        return isset(self::$_config[static::class])?true:$do?static::initClass():false;
    }

}