<<<<<<< HEAD
<?php
/**
 * User: linzh
 * Date: 2016/3/14
 * Time: 20:51
 */
use System\Core\LiteBuilder;
use System\Core\Storage;
use System\Utils\Network;
use System\Core\Router;
use System\Core\Dispatcher;
use System\Utils\Response;
use System\Core\Log;
use System\Utils\SEK;

const AJAX_JSON = 0;
const AJAX_XML = 1;

/**
 * PHP变量类型
 */
const TYPE_BOOL     = 'boolean';
const TYPE_INT      = 'integer';
const TYPE_FLOAT    = 'double';//由于历史原因，如果是 float 则返回“double”，而不是“float”
const TYPE_STR      = 'string';
const TYPE_ARRAY    = 'array';
const TYPE_OBJ      = 'object';
const TYPE_RESOURCE = 'resource';
const TYPE_NULL     = 'NULL';
const TYPE_UNKNOWN  = 'unknown type';

/**
 * 异常情况下返回还是抛出异常
 */
const MODE_RETURN = 0;
const MODE_EXCEPTION = 1;

date_default_timezone_set('Asia/Shanghai'); //避免使用date函数时的警告
defined('DEBUG_MODE_ON') or define('DEBUG_MODE_ON', true); //是否开启DUBUG模式
defined('PAGE_TRACE_ON') or define('PAGE_TRACE_ON', true); //是否开启TRACE界面

/**
 * Class Kbylin
 */
final class Kbylin {

    /**
     * 应用名称
     * @var string
     */
    private $appname = 'Kbylin';

    /**
     * 应用配置
     * @var array
     */
    private $_convention = [
        //-- 目录配置，相对于server.php的位置 --/
        'SYSTEM_DIR'    => 'System/',
        'APP_DIR'       => 'Application/',
        'CONFIG_DIR'    => 'Config/',
        'RUNTIME_DIR'   => 'Runtime/',
        'PUBLIC_DIR'    => 'Public/',
        'UPLOAD_DIR'    => 'Upload/',

        'CLASS_LOADER'      => null, //用户自定义错误处理函数
        'ERROR_HANDLER'     => null, //用户自定义错误处理函数
        'EXCEPTION_HANDLER' => null, //用户自定义异常处理函数

        //函数包列表
        'FUNC_PACK_LIST'    => [],
        'FUNC_PATH'         => 'Func/',//相对于BASE_PATH

        'REQUEST_PARAM_NAME'    => '_PARAMS_',
    ];
    /**
     * 类名与类路径的映射数组
     * array (
     *  完整类名称 => 类文件的完整路径
     * )
     * @var array
     */
    protected $_classes = [];

    /**
     * 标记是否加载lite文件
     * @var bool
     */
    protected $_liteon = false;

    /**
     * 标记类示例是否经理郭初始化
     * @var bool
     */
    private $_inited = false;

    /**
     * 状态跟踪信息
     * @var array
     */
    protected static $_status = [];

    /**
     * 获取运行时刻内存使用情况
     * @param null|string $tag 状态标签
     * @return void
     */
    public static function recordStatus($tag){
        DEBUG_MODE_ON and self::$_status[$tag] = [
            microtime(true),
            memory_get_usage(),
        ];
    }

    /**
     * Kbylin constructor.
     * @param null $appname
     * @param array|null $config
     */
    public function __construct($appname=null,array $config=null){
        self::recordStatus('construct_begin');
        version_compare(PHP_VERSION,'5.4.0','<') and die('Require PHP >= 5.4 !');
        null !== $appname and $this->appname = $appname;
        $this->init($config);
    }

    /**
     * 初始化应用程序
     * 注意：这个过程中不可以调用其它类，真正的类加载过程是在start应用开始
     * @param array $config
     * @return void
     */
    public function init(array $config=null){
        self::recordStatus('init_begin');
        $this->_inited and die('实例已完成过初始化!');
        null !== $config and
            $this->_convention = array_merge($this->_convention,$config);//合并用户自定义配置和系统封默认配置
        date_default_timezone_set('Asia/Shanghai') or die('Date format set time zone failed!');

        //分解请求参数
        if(isset($this->_convention['REQUEST_PARAM_NAME'])){
            $temp = [];
            parse_str($this->_convention['REQUEST_PARAM_NAME'],$temp);
            $_POST = array_merge($_POST,$temp);
            $_REQUEST = array_merge($_REQUEST,$temp);
            $_GET = array_merge($_GET,$temp);
            unset($this->_convention['REQUEST_PARAM_NAME']);
        }

        //目录常量
        define('BASE_PATH',str_replace('\\','/',dirname(__DIR__)).'/');
        define('SYSTEM_PATH',BASE_PATH.$this->_convention['SYSTEM_DIR']);
        define('APP_PATH',BASE_PATH.$this->_convention['APP_DIR']);
        define('CONFIG_PATH',BASE_PATH.$this->_convention['CONFIG_DIR']);
        define('RUNTIME_PATH',BASE_PATH.$this->_convention['RUNTIME_DIR']);
        define('PUBLIC_PATH',BASE_PATH.$this->_convention['PUBLIC_DIR']);
        define('UPLOAD_PATH',BASE_PATH.$this->_convention['UPLOAD_DIR']);

        //布尔常量
        define('IS_WIN',false !== stripos(PHP_OS, 'WIN')); //运行环境
        define('IS_CLI', PHP_SAPI === 'cli');
        define('IS_AJAX', ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ));
        define('IS_POST',strtoupper($_SERVER['REQUEST_METHOD']) === 'POST');
        //其他常量
        define('APP_NAME',$this->appname);
        $script = dirname($_SERVER['SCRIPT_NAME']);
        IS_WIN and $script = str_replace('\\', '/', $script);
        define('BASE_URI',$script);
        define('PUBLIC_URI',BASE_URI.'/'.$this->_convention['PUBLIC_DIR']);

        self::recordStatus('init_behavior_begin');
        //错误显示和隐藏
        error_reporting(DEBUG_MODE_ON?-1:E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);//PHP5.3一下需要用这段 “error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);”
        ini_set('display_errors',DEBUG_MODE_ON?1:0);
        //其他行为
        false === spl_autoload_register(isset($this->_convention['CLASS_LOADER'])?
            $this->_convention['CLASS_LOADER']:[$this,'_autoLoad']) and die('spl autoload register failed!');
        set_error_handler(isset($this->_convention['ERROR_HANDLER'])?$this->_convention['EXCEPTION_HANDLER']:[$this,'_handleError']) ;
        set_exception_handler(isset($this->_convention['EXCEPTION_HANDLER'])?$this->_convention['EXCEPTION_HANDLER']:[$this,'_handleException']);
        register_shutdown_function([$this,'_onShutDown']);

        self::recordStatus('funcpack_load_begin');
        include SYSTEM_PATH.'Common/functions.php'; // 加载系统函数包
        if($this->_convention['FUNC_PACK_LIST']){
            foreach($this->_convention['FUNC_PACK_LIST'] as $packname){
                $filename = BASE_PATH.$this->_convention['FUNC_PATH']."{$packname}.php";
                if(is_file($filename)) include $filename;//使用include代替include_once提高效率
            }
        }

//        dumpout(BASE_URI,PUBLIC_URI);

        $this->_inited = true;
    }

    /**
     * 检查目录是否存在以及进行一些默认的一些设置
     * @param bool $on 是否检查，默认是
     * @return $this
     */
    public function inspect($on=true){
        if($on){
            echo "系统正在检查目录文件设置";
        }
        return $this;
    }

    /**
     * 检查是否加载lite文件
     * @param bool $on 是否启用lite文件
     * @return $this
     */
    public function liten($on=false){
        if($on){
            $this->_liteon = true;
            define('LITE_FILE_NAME',RUNTIME_PATH.APP_NAME.'.lite.php');//运行时核心文件
            //考虑到云服务器上lite文件直接使用is_file判断和包含，需要手动上传
            self::recordStatus('load_lite_begin');
            if(is_file(LITE_FILE_NAME))  include LITE_FILE_NAME;//代替include文件，拥有更好的适应性？
            self::recordStatus('load_lite_end');
        }
        return $this;
    }

    /**
     * 开启应用
     */
    public function start(){
        //检查初始化情况(即直接new)
        $this->_inited or $this->init();
        self::recordStatus('app_begin');

        $uri = Network::getUri(true);
        $hostname = Network::getHostname();
        $result = Router::parse($uri,$hostname);

//        dumpout(
//            Router::create(),                                 '/cms/index.php/'
//            Router::create($result[0]),                       '/cms/index.php/Admin'
//            Router::create($result[0],$result[1]),            '/cms/index.php/Admin/User'
//            Router::create($result[0],$result[1],$result[2])  '/cms/index.php/Admin/User/index3'
//            );
        define('__APP__',Router::create());
        define('__MODULE__',Router::create($result[0]));
        define('__CONTROLLER__',Router::create($result[0],$result[1]));
        define('__ACTION__',Router::create($result[0],$result[1],$result[2]));

//        $result =
            Dispatcher::execute($result[0],$result[1],$result[2],$result[3]);
        //依情况对结果进行缓存
//        Log::write($result);
    }

    public function test(){
        $this->_inited or $this->init();
        require SYSTEM_PATH.'Test/index.php';
        exit();
    }

    /**
     * 脚本结束自动调用
     */
    public function _onShutDown(){
        self::recordStatus("_xor_exec_shutdown");
        if(DEBUG_MODE_ON and PAGE_TRACE_ON and !IS_AJAX){
             SEK::showTrace(self::$_status,6);//页面跟踪信息显示
        }

        if($this->_liteon and !is_file(LITE_FILE_NAME)){ //开启加载 并且Lite文件不存在时  ==> 重新生成
            self::recordStatus('create_lite_begin');
            Storage::write(LITE_FILE_NAME,LiteBuilder::compileInBatch($this->_classes));
            self::recordStatus('create_lite_begin');
        }
        \System\Utils\Response::flushOutput();
    }

    /**
     * 系统默认的类加载方法
     * 根目录下可以不设置命名空间
     * @param string $clsnm 类名称（包含命名空间）
     * @return void
     */
    public function _autoLoad($clsnm){
        if(isset($this->_classes[$clsnm])) {
            include_once $this->_classes[$clsnm];
        }else{
            $pos = strpos($clsnm,'\\');
            if(false === $pos){
                $file = BASE_PATH . "{$clsnm}.class.php";
                if(is_file($file)) include_once $file;
            }else{
                $path = $this->fetchPathByFullname($clsnm);
                if(is_file($path) and false !== strpos($path, "{$clsnm}.class.php")){
                    include_once $this->_classes[$clsnm] = $path;
                }
            }
        }
    }

    /**
     * 从类名称中解析出类文件的绝对路径
     * @param string $classname
     * @return string
     */
    public function fetchPathByFullname($classname){
        if(!isset($this->_classes[$classname])){
            $this->_classes[$classname] = BASE_PATH.str_replace('\\', '/', $classname).'.class.php';
            IS_WIN and $this->_classes[$classname] = str_replace('/', '\\', realpath($this->_classes[$classname]));
        }
        return $this->_classes[$classname];
    }

    /**
     * 系统默认的错误处理函数
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @return void
     */
    public function _handleError($errno,$errstr,$errfile,$errline){

        IS_AJAX and Response::failed($errstr);

        //错误信息
        if(!is_string($errstr)) $errstr = serialize($errstr);
        ob_start();
        debug_print_backtrace();
        $vars = [
            'message'   => "{$errno} {$errstr}",
            'position'  => "File:{$errfile}   Line:{$errline}",
            'trace'     => ob_get_clean(),  //回溯信息
        ];
        if(DEBUG_MODE_ON){
            SEK::loadTemplate('error',$vars);
        }else{
            SEK::loadTemplate('user_error');
        }
        //异常处理完成后仍然会继续执行，需要强制退出
        exit;
    }

    /**
     * 处理异常的发生
     * 开放模式下允许将Exception打印打浏览器中
     * 部署模式下不建议这么做，因为回退栈中可能保存敏感信息
     * @param Exception $e
     * @return void
     */
    public function _handleException(Exception $e){

        IS_AJAX and Response::failed($e->getMessage());

        Response::cleanOutput();
//        $trace = $e->getTrace();
        $traceString = $e->getTraceAsString();
        //错误信息
        $vars = [
            'message'   => get_class($e).' : '.$e->getMessage(),
            'position'  => 'File:'.$e->getFile().'   Line:'.$e->getLine(),
            'trace'     => $traceString,//回溯信息，可能会暴露数据库等敏感信息
        ];
        if(DEBUG_MODE_ON){
            SEK::loadTemplate('exception',$vars);
        }else{
            SEK::loadTemplate('user_error');
        }
        //异常处理完成后仍然会继续执行，需要强制退出
        exit;
    }


=======
<?php
/**
 * User: linzh
 * Date: 2016/3/14
 * Time: 20:51
 */
use System\Core\LiteBuilder;
use System\Core\Storage;
use System\Utils\Network;
use System\Core\Router;
use System\Core\Dispatcher;
use System\Utils\Response;
use System\Core\Log;
use System\Utils\SEK;

const AJAX_JSON = 0;
const AJAX_XML = 1;

/**
 * PHP变量类型
 */
const TYPE_BOOL     = 'boolean';
const TYPE_INT      = 'integer';
const TYPE_FLOAT    = 'double';//由于历史原因，如果是 float 则返回“double”，而不是“float”
const TYPE_STR      = 'string';
const TYPE_ARRAY    = 'array';
const TYPE_OBJ      = 'object';
const TYPE_RESOURCE = 'resource';
const TYPE_NULL     = 'NULL';
const TYPE_UNKNOWN  = 'unknown type';

/**
 * 异常情况下返回还是抛出异常
 */
const MODE_RETURN = 0;
const MODE_EXCEPTION = 1;

date_default_timezone_set('Asia/Shanghai'); //避免使用date函数时的警告
defined('DEBUG_MODE_ON') or define('DEBUG_MODE_ON', true); //是否开启DUBUG模式
defined('PAGE_TRACE_ON') or define('PAGE_TRACE_ON', true); //是否开启TRACE界面

/**
 * Class Kbylin
 */
final class Kbylin {

    /**
     * 应用名称
     * @var string
     */
    private $appname = 'Kbylin';

    /**
     * 应用配置
     * @var array
     */
    private $_convention = [
        //-- 目录配置，相对于server.php的位置 --/
        'SYSTEM_DIR'    => 'System/',
        'APP_DIR'       => 'Application/',
        'CONFIG_DIR'    => 'Config/',
        'RUNTIME_DIR'   => 'Runtime/',
        'PUBLIC_DIR'    => 'Public/',
        'UPLOAD_DIR'   => 'Upload/',

        'CLASS_LOADER'      => null, //用户自定义错误处理函数
        'ERROR_HANDLER'     => null, //用户自定义错误处理函数
        'EXCEPTION_HANDLER' => null, //用户自定义异常处理函数

        //函数包列表
        'FUNC_PACK_LIST'    => [],
        'FUNC_PATH'         => 'Func/',//相对于BASE_PATH

        'REQUEST_PARAM_NAME'    => '_PARAMS_',
    ];
    /**
     * 类名与类路径的映射数组
     * array (
     *  完整类名称 => 类文件的完整路径
     * )
     * @var array
     */
    protected $_classes = [];

    /**
     * 标记是否加载lite文件
     * @var bool
     */
    protected $_liteon = false;

    /**
     * 标记类示例是否经理郭初始化
     * @var bool
     */
    private $_inited = false;

    /**
     * 状态跟踪信息
     * @var array
     */
    protected static $_status = [];

    /**
     * 获取运行时刻内存使用情况
     * @param null|string $tag 状态标签
     * @return void
     */
    public static function recordStatus($tag){
        DEBUG_MODE_ON and self::$_status[$tag] = [
            microtime(true),
            memory_get_usage(),
        ];
    }

    /**
     * Kbylin constructor.
     * @param null $appname
     * @param array|null $config
     */
    public function __construct($appname=null,array $config=null){
        self::recordStatus('construct_begin');
        version_compare(PHP_VERSION,'5.4.0','<') and die('Require PHP >= 5.4 !');
        null !== $appname and $this->appname = $appname;
        $this->init($config);
    }

    /**
     * 初始化应用程序
     * 注意：这个过程中不可以调用其它类，真正的类加载过程是在start应用开始
     * @param array $config
     * @return void
     */
    public function init(array $config=null){
        self::recordStatus('init_begin');
        $this->_inited and die('实例已完成过初始化!');
        null !== $config and
            $this->_convention = array_merge($this->_convention,$config);//合并用户自定义配置和系统封默认配置
        date_default_timezone_set('Asia/Shanghai') or die('Date format set time zone failed!');

        //分解请求参数
        if(isset($this->_convention['REQUEST_PARAM_NAME'])){
            $temp = [];
            parse_str($this->_convention['REQUEST_PARAM_NAME'],$temp);
            $_POST = array_merge($_POST,$temp);
            $_REQUEST = array_merge($_REQUEST,$temp);
            $_GET = array_merge($_GET,$temp);
            unset($this->_convention['REQUEST_PARAM_NAME']);
        }

        //目录常量
        define('BASE_PATH',str_replace('\\','/',dirname(__DIR__)).'/');
        define('SYSTEM_PATH',BASE_PATH.$this->_convention['SYSTEM_DIR']);
        define('APP_PATH',BASE_PATH.$this->_convention['APP_DIR']);
        define('CONFIG_PATH',BASE_PATH.$this->_convention['CONFIG_DIR']);
        define('RUNTIME_PATH',BASE_PATH.$this->_convention['RUNTIME_DIR']);
        define('PUBLIC_PATH',BASE_PATH.$this->_convention['PUBLIC_DIR']);
        define('UPLOAD_PATH',BASE_PATH.$this->_convention['UPLOAD_DIR']);

        //布尔常量
        define('IS_WIN',false !== stripos(PHP_OS, 'WIN')); //运行环境
        define('IS_CLI', PHP_SAPI === 'cli');
        define('IS_AJAX', ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ));
        define('IS_POST',strtoupper($_SERVER['REQUEST_METHOD']) === 'POST');
        //其他常量
        define('APP_NAME',$this->appname);
        $script = dirname($_SERVER['SCRIPT_NAME']);
        IS_WIN and $script = str_replace('\\', '/', $script);
        define('BASE_URI',$script);
        define('PUBLIC_URI',BASE_URI.$this->_convention['PUBLIC_DIR']);

        self::recordStatus('init_behavior_begin');
        //错误显示和隐藏
        error_reporting(DEBUG_MODE_ON?-1:E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);//PHP5.3一下需要用这段 “error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);”
        ini_set('display_errors',DEBUG_MODE_ON?1:0);
        //其他行为
        false === spl_autoload_register(isset($this->_convention['CLASS_LOADER'])?
            $this->_convention['CLASS_LOADER']:[$this,'_autoLoad']) and die('spl autoload register failed!');
        set_error_handler(isset($this->_convention['ERROR_HANDLER'])?$this->_convention['EXCEPTION_HANDLER']:[$this,'_handleError']) ;
        set_exception_handler(isset($this->_convention['EXCEPTION_HANDLER'])?$this->_convention['EXCEPTION_HANDLER']:[$this,'_handleException']);
        register_shutdown_function([$this,'_onShutDown']);

        self::recordStatus('funcpack_load_begin');
        include SYSTEM_PATH.'Common/functions.php'; // 加载系统函数包
        if($this->_convention['FUNC_PACK_LIST']){
            foreach($this->_convention['FUNC_PACK_LIST'] as $packname){
                $filename = BASE_PATH.$this->_convention['FUNC_PATH']."{$packname}.php";
                if(is_file($filename)) include $filename;//使用include代替include_once提高效率
            }
        }

//        dumpout(BASE_URI,PUBLIC_URI);

        $this->_inited = true;
    }

    /**
     * 检查目录是否存在以及进行一些默认的一些设置
     * @param bool $on 是否检查，默认是
     * @return $this
     */
    public function inspect($on=true){
        if($on){
            echo "系统正在检查目录文件设置";
        }
        return $this;
    }

    /**
     * 检查是否加载lite文件
     * @param bool $on 是否启用lite文件
     * @return $this
     */
    public function liten($on=false){
        if($on){
            $this->_liteon = true;
            define('LITE_FILE_NAME',RUNTIME_PATH.APP_NAME.'.lite.php');//运行时核心文件
            //考虑到云服务器上lite文件直接使用is_file判断和包含，需要手动上传
            self::recordStatus('load_lite_begin');
            if(is_file(LITE_FILE_NAME))  include LITE_FILE_NAME;//代替include文件，拥有更好的适应性？
            self::recordStatus('load_lite_end');
        }
        return $this;
    }

    /**
     * 开启应用
     */
    public function start(){
        //检查初始化情况(即直接new)
        $this->_inited or $this->init();
        self::recordStatus('app_begin');

        $uri = Network::getUri(true);
        $hostname = Network::getHostname();
        $result = Router::parse($uri,$hostname);

//        dumpout(
//            Router::create(),                                 '/cms/index.php/'
//            Router::create($result[0]),                       '/cms/index.php/Admin'
//            Router::create($result[0],$result[1]),            '/cms/index.php/Admin/User'
//            Router::create($result[0],$result[1],$result[2])  '/cms/index.php/Admin/User/index3'
//            );
        define('__APP__',Router::create());
        define('__MODULE__',Router::create($result[0]));
        define('__CONTROLLER__',Router::create($result[0],$result[1]));
        define('__ACTION__',Router::create($result[0],$result[1],$result[2]));

//        $result =
            Dispatcher::execute($result[0],$result[1],$result[2],$result[3]);
        //依情况对结果进行缓存
//        Log::write($result);
    }

    public function test(){
        $this->_inited or $this->init();
        require SYSTEM_PATH.'Test/index.php';
        exit();
    }

    /**
     * 脚本结束自动调用
     */
    public function _onShutDown(){
        self::recordStatus("_xor_exec_shutdown");
        if(DEBUG_MODE_ON and PAGE_TRACE_ON and !IS_AJAX){
             SEK::showTrace(self::$_status,6);//页面跟踪信息显示
        }

        if($this->_liteon and !is_file(LITE_FILE_NAME)){ //开启加载 并且Lite文件不存在时  ==> 重新生成
            self::recordStatus('create_lite_begin');
            Storage::write(LITE_FILE_NAME,LiteBuilder::compileInBatch($this->_classes));
            self::recordStatus('create_lite_begin');
        }
        \System\Utils\Response::flushOutput();
    }

    /**
     * 系统默认的类加载方法
     * 根目录下可以不设置命名空间
     * @param string $clsnm 类名称（包含命名空间）
     * @return void
     */
    public function _autoLoad($clsnm){
        if(isset($this->_classes[$clsnm])) {
            include_once $this->_classes[$clsnm];
        }else{
            $pos = strpos($clsnm,'\\');
            if(false === $pos){
                $file = BASE_PATH . "{$clsnm}.class.php";
                if(is_file($file)) include_once $file;
            }else{
                $path = $this->fetchPathByFullname($clsnm);
                if(is_file($path) and false !== strpos($path, "{$clsnm}.class.php")){
                    include_once $this->_classes[$clsnm] = $path;
                }
            }
        }
    }

    /**
     * 从类名称中解析出类文件的绝对路径
     * @param string $classname
     * @return string
     */
    public function fetchPathByFullname($classname){
        if(!isset($this->_classes[$classname])){
            $this->_classes[$classname] = BASE_PATH.str_replace('\\', '/', $classname).'.class.php';
            IS_WIN and $this->_classes[$classname] = str_replace('/', '\\', realpath($this->_classes[$classname]));
        }
        return $this->_classes[$classname];
    }

    /**
     * 系统默认的错误处理函数
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @return void
     */
    public function _handleError($errno,$errstr,$errfile,$errline){

        IS_AJAX and Response::failed($errstr);

        //错误信息
        if(!is_string($errstr)) $errstr = serialize($errstr);
        ob_start();
        debug_print_backtrace();
        $vars = [
            'message'   => "{$errno} {$errstr}",
            'position'  => "File:{$errfile}   Line:{$errline}",
            'trace'     => ob_get_clean(),  //回溯信息
        ];
        if(DEBUG_MODE_ON){
            SEK::loadTemplate('error',$vars);
        }else{
            SEK::loadTemplate('user_error');
        }
        //异常处理完成后仍然会继续执行，需要强制退出
        exit;
    }

    /**
     * 处理异常的发生
     * 开放模式下允许将Exception打印打浏览器中
     * 部署模式下不建议这么做，因为回退栈中可能保存敏感信息
     * @param Exception $e
     * @return void
     */
    public function _handleException(Exception $e){

        IS_AJAX and Response::failed($e->getMessage());

        Response::cleanOutput();
//        $trace = $e->getTrace();
        $traceString = $e->getTraceAsString();
        //错误信息
        $vars = [
            'message'   => get_class($e).' : '.$e->getMessage(),
            'position'  => 'File:'.$e->getFile().'   Line:'.$e->getLine(),
            'trace'     => $traceString,//回溯信息，可能会暴露数据库等敏感信息
        ];
        if(DEBUG_MODE_ON){
            SEK::loadTemplate('exception',$vars);
        }else{
            SEK::loadTemplate('user_error');
        }
        //异常处理完成后仍然会继续执行，需要强制退出
        exit;
    }


>>>>>>> 5074fdd666065b44da9222b2537f8dec20deeb5f
}