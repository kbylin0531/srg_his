<?php
/**
 * Created by linzhv@outlook.com
 * User: asus
 * Date: 16-6-14
 * Time: 10:53
 */
namespace Soya\Core;

use Soya\Util\Helper\StringHelper;
use Soya\Util\SEK;

/**
 * Class URI URI解析和创建
 *
 * 解析示例：
 * ① http://localhost:8080/Shep/Public/index.php/home/index/module-lost-fotset-nice-well.html
 *  array (
 *  'm' => 'home',
 *  'c' => 'index',
 *  'a' => 'module',
 *  'p' =>
 *      array (
 *          'lost' => 'fotset',
 *          'nice' => 'well',
 *      ),
 * )
 *
 * @package Soya\Core
 */
class URI extends \Soya{

    const CONF_NAME = 'uri';
    const CONF_CONVENTION = [
        //API模式，直接使用$_GET
        'API_MODE_ON'   => false,
        //API模式 对应的$_GET变量名称
        'API_MODULES_VARIABLE'   => '_m',//该模式下使用到多层模块时涉及'MM_BRIDGE'的配置
        'API_CONTROLLER_VARIABLE'   => '_c',
        'API_ACTION_VARIABLE'   => '_a',

        //普通模式
        'MASQUERADE_TAIL'   => '.html',
        //重写模式下 消除的部分，对应.htaccess文件下
        'REWRITE_HIDDEN'      => '/index.php',
        'MM_BRIDGE'     => '/',//模块与模块之间的连接
        'MC_BRIDGE'     => '/',
        'CA_BRIDGE'     => '/',
        //*** 必须保证操作与控制器之间的符号将是$_SERVER['PATH_INFO']字符串中第一个出现的,为了更好地显示URL，参数一般通过POST传递
        //特别注意的是若使用了问号，则后面的字符串将被认为是请求参数
        'AP_BRIDGE'     => '-',
        'PP_BRIDGE'     => '-',//参数与参数之间的连接桥
        'PKV_BRIDGE'    => '-',//参数的键值对之前的连接桥

        //是否开启域名部署（包括子域名部署）
        'DOMAIN_DEPLOY_ON'  => true,
        //子域名部署模式下 的 完整域名
        'DOMAIN_NAME'=>'xor.com',
        //是否将子域名段和模块进行映射
        'SUBDOMAIN_AUTO_MAPPING_ON' => false,
        //子域名部署规则
        //注意参与array_flip()函数,键值互换
        'SUBDOMAIN_MAPPINIG' => [],

        //是否对URI地址进行路由
        'URI_ROUTE_ON'          => true,//总开关
        'STATIC_ROUTE_ON'       => true,
        'STATIC_ROUTE_RULES'    => [],
        'WILDCARD_ROUTE_ON'     => true,
        'WILDCARD_ROUTE_RULES'  => [],
        'REGULAR_ROUTE_ON'      => true,
        'REGULAR_ROUTE_RULES'   => [],

        //使用的协议名称
        'SERVER_PROTOCOL' => 'http',
        //使用的端口号，默认为80时会显示为隐藏
        'SERVER_PORT' => 80,
    ];

    /**
     * 返回解析结果
     * @var array
     */
    protected $result = [
        'm' => null,
        'c' => null,
        'a' => null,
        'p' => null,
    ];

    /**
     * 解析URI
     * @param string $uri 请求的URI
     * @param string $hostname
     * @return $this
     */
    public function parse($uri=null,$hostname=null){
        $config = self::getConfig();
        //API模式下
        if($config['API_MODE_ON']){
            $this->parseInAPI();
        }else{
            $uri or $uri = SEK::getPathInfo(true);
            //解析域名部署
            if($config['DOMAIN_DEPLOY_ON']){
                $hostname or $hostname = $_SERVER['SERVER_NAME'];
                $this->parseHostname($hostname);//如果绑定了模块，之后的解析将无法指定模块
            }
            //检查、寻找和解析URI路由 'URI_ROUTE_ON'
            //普通模式下解析URI地址
            $this->parseInCommon($uri);
        }
//        self::trace($this->result);
        return $this->result;
    } 


    /**
     * 按照API模式进行解析(都组最快)
     * 保持原样
     * @return void
     */
    private function parseInAPI(){
        \Soya::recordStatus('fetchurl_in_topspeed_begin');
        $config = self::getConfig();
        $vars = [
            $config['API_MODULES_VARIABLE'],
            $config['API_CONTROLLER_VARIABLE'],
            $config['API_ACTION_VARIABLE'],
        ];
        //获取模块名称
        isset($_GET[$vars[0]]) and $this->result['m'] = $_GET[$vars[0]];
        //获取控制器名称
        isset($_GET[$vars[1]]) and $this->result['c'] = $_GET[$vars[1]];
        //获取操作名称，类方法不区分大小写
        isset($_GET[$vars[2]]) and $this->result['a'] = $_GET[$vars[2]];
        //参数为剩余的变量
        unset($_GET[$vars[0]],$_GET[$vars[1]],$_GET[$vars[2]]);
        $this->result['p'] = $_GET;

        \Soya::recordStatus('fetchurl_in_topspeed_end');
    }

    /**
     * 按照普通模式进行URI解析
     * @param string $uri 待解析的URI
     * @return void
     */
    private function parseInCommon($uri){
        \Soya::recordStatus('parseurl_in_common_begin');
        $config = self::getConfig();
        $bridges = [
            'mm'  => $config['MM_BRIDGE'],
            'mc'  => $config['MC_BRIDGE'],
            'ca'  => $config['CA_BRIDGE'],
            'ap'  => $config['AP_BRIDGE'],
            'pp'  => $config['PP_BRIDGE'],
            'pkv'  => $config['PKV_BRIDGE'],
        ];
        $this->stripMasqueradeTail($uri);

        \Soya::recordStatus('parseurl_in_pathinfo_getpathinfo_done');
        //-- 解析PATHINFO --//
        //截取参数段param与定位段local
        $papos          = strpos($uri,$bridges['ap']);
        $mcapart = null;
        $pparts = '';
        if(false === $papos){
            $mcapart  = trim($uri,'/');//不存在参数则认定PATH_INFO全部是MCA的部分，否则得到结果substr($uri,0,0)即空字符串
        }else{
            $mcapart  = trim(substr($uri,0,$papos),'/');
            $pparts   = substr($uri,$papos + strlen($bridges['ap']));
        }
//        UDK::dump($uri,$bridges['ap'],$mcapart,$pparts);

        //-- 解析MCA部分 --//
        //逆向检查CA是否存在衔接
        $mcaparsed = $this->parseMCA($mcapart,$bridges);
        $this->result = array_merge($this->result,$mcaparsed);
        \Soya::recordStatus('parseurl_in_pathinfo_getmac_done');

        //-- 解析参数部分 --//
        $this->result['p'] = SEK::toParametersArray($pparts,$bridges['pp'],$bridges['pkv']);
        \Soya::recordStatus('parseurl_in_common_end');
    }

    /**
     * 解析主机名
     * 如果找到了对应的主机名称，则绑定到对应的模块
     * @param string $hostname 访问的主机名
     * @return bool 返回是否绑定了模块
     */
    private function parseHostname($hostname){
        $config = self::getConfig();
        $subdomain = strstr($hostname,$config['DOMAIN_NAME'],true);
        if(false === $subdomain) return ;
        $subdomain = rtrim($subdomain,'.');
        if(isset($config['SUBDOMAIN_MAPPINIG'][$subdomain])){
            $this->result['m'] = $config['SUBDOMAIN_MAPPINIG'][$subdomain];
        }elseif($config['SUBDOMAIN_AUTO_MAPPING_ON']){
            if(false !== strpos($subdomain,'.')){
                $this->result['m'] = array_map(function ($val) {
                    return StringHelper::toJavaStyle($val);
                }, explode('.',$subdomain));
            }else{
                $this->result['m'] = ucfirst($subdomain);
            }
        }else{
            return ;
        }
    }

    /**
     * 解析"模块、控制器、操作"
     * @param $mcapart
     * @param $bridges
     * @return array
     */
    private function parseMCA($mcapart,$bridges){
        $parsed = ['m'=>null,'c'=>null,'a'=>null];
        $capos = strrpos($mcapart,$bridges['ca']);
//        SEK::dump($mcapart,$capos,self::$_convention['CA_BRIDGE']);
        if(false === $capos){
            //找不到控制器与操作之间分隔符（一定不存在控制器）
            //先判断位置部分是否为空字符串来决定是否有操作名称
            if(strlen($mcapart)){
                //位置字段全部是字符串的部分
                $parsed['a'] = $mcapart;
            }else{
                //没有操作部分，MCA全部使用默认的
            }
        }else{
            //apos+CA_BRIDGE 后面的部分全部算作action
            $parsed['a'] = substr($mcapart,$capos+strlen($bridges['ca']));

            //CA存在衔接符 则说明一定存在控制器
            $mcalen = strlen($mcapart);
            $mcpart = substr($mcapart,0,$capos-$mcalen);//去除了action的部分

//            SEK::dump($mcpart);

            if(strlen($mcapart)){
                $mcpos = strrpos($mcpart,$bridges['mc']);
//                SEK::dump($mcpart,$mcpos);
                if(false === $mcpos){
                    //不存在模块
                    if(strlen($mcpart)){
                        //全部是控制器的部分
                        $parsed['c'] = $mcpart;
                    }else{
                        //没有控制器部分，则使用默认的
                    }
                }else{
                    //截取控制器的部分
                    $parsed['c']   = substr($mcpart,$mcpos+strlen($bridges['mc']));

                    //既然存在MC衔接符 说明一定存在模块
                    $mpart = substr($mcpart,0,$mcpos-strlen($mcpart));//以下的全是模块部分的字符串
                    if(strlen($mpart)){
                        if(false === strpos($mpart,$bridges['mm'])){
                            $parsed['m'] = $mpart;
                        }else{
                            $parsed['m'] = explode($bridges['mm'],$mpart);
                        }
                    }else{
                        //一般存在衔接符的情况下不为空,但也考虑下特殊情况
                    }
                }
            }else{
                //一般存在衔接符的情况下不为空,但也考虑下特殊情况
            }
        }
        return $parsed;
    }
    /**
     * 删除伪装的url后缀
     * @param string|array $uri 需要去除尾巴的字符串或者字符串数组（当数组中存在其他元素时忽略）
     * @return void
     */
    private function stripMasqueradeTail(&$uri){
        $uri = trim($uri);
        $config = self::getConfig();
        $position = stripos($uri,$config['MASQUERADE_TAIL']);
        //$position === false 表示 不存在伪装的后缀或者相关带嫌疑的url部分
//        UDK::dumpout($position,$uri,$config['MASQUERADE_TAIL'],substr($uri,0,$position),
//            strlen($uri),$position,strlen($config['MASQUERADE_TAIL'])
//            );

        if(false !== $position and strlen($uri) === ($position + strlen($config['MASQUERADE_TAIL'])) ){
            //伪装的后缀存在且只出现在最后的位置时
            $uri = substr($uri,0,$position);
        }
    }



    /**
     * 创建URL
     * @param string|array $modules 模块序列
     * @param string $contler 控制器名称
     * @param string $action 操作名称
     * @param array|null $params 参数
     * @return string 可以访问的URI
     */
    public function create($modules=null,$contler=null,$action=null,array $params=null){

        $modules or $modules = REQUEST_MODULE;
        $contler or $contler = REQUEST_CONTROLLER;
        $action or $action = REQUEST_ACTION;

        $config = self::getConfig();
        if($config['API_MODE_ON']){
            $uri = self::getBasicUrl().$this->createInAPI($modules,$contler,$action,$params);
        }else{
            //反向域名地址
            $moduleUsed = false;
            if($config['DOMAIN_DEPLOY_ON']){
                $hostname = $this->createHostname($modules,$moduleUsed);//如果绑定了模块，之后的解析将无法指定模块
            }else{
                $hostname = $_SERVER['SERVER_NAME'];
            }
//            \Soya\dumpout($modules);
            $uri = self::getBasicUrl(null,$hostname).'/'.
                $this->createInCommon($moduleUsed?null:$modules,$contler,$action,$params);
        }
        return $uri;
    }


    /**
     * 按照API模式创建URL地址
     * @param array|string $modules
     * @param string $contler
     * @param string $action
     * @param array|null $params
     * @return string
     */
    public function createInAPI($modules,$contler,$action,array $params=null){
        $config = self::getConfig();
        is_array($modules) and $modules = SEK::toModulesString($modules,$config['MM_BRIDGE']);
        empty($params) and $params = [];
        return '?'.http_build_query(array_merge($params,array(
            $config['API_MODULES_VARIABLE']       => $modules,
            $config['API_CONTROLLER_VARIABLE']    => $contler,
            $config['API_ACTION_VARIABLE']        => $action,
        )));
    }

    /**
     * 获取主机名称
     * @param string|array $modules
     * @param bool $flag
     * @return null|string
     */
    public function createHostname($modules,&$flag){
        $config = self::getConfig();
        //模块标识符
        $mid = is_array($modules)?SEK::toModulesString($modules,$config['MM_BRIDGE']):$modules;
        $rmapping = array_flip($config['SUBDOMAIN_MAPPINIG']);
        if(isset($rmapping[$mid])){
            $hostname = $rmapping[$mid];
        }elseif($config['SUBDOMAIN_AUTO_MAPPING_ON']){
            if(is_string($modules)){
                $modules = strtolower(str_replace('/','.',$modules));
            }else{
                $modules = implode('.',$modules);
            }
            $hostname = $modules;
        }else{
            return $_SERVER['SERVER_NAME'];
        }
        $flag = true;//标注模块信息已经注入到域名中了
        return $hostname.'.'.$config['DOMAIN_NAME'];
    }

    /**
     * @param null $modules
     * @param null $contler
     * @param null $action
     * @param array|null $params
     * @return string
     */
    public function createInCommon($modules=null,$contler=null,$action=null,array $params=null){
        $uri = '';
        $config = self::getConfig();
        $modules and $uri .= is_array($modules)?implode($config['MM_BRIDGE'],$modules):$modules;
//        \Soya\dumpout($modules,$uri);
        $contler and $uri .= ''===$uri?$contler:$config['MC_BRIDGE'].$contler;
        $action and $uri .= $config['CA_BRIDGE'].$action;
        $params and $uri .= $config['AP_BRIDGE'].SEK::toParametersString($params,$config['PP_BRIDGE'],$config['PKV_BRIDGE']);
        return $uri;
    }

    /**
     * 获取基础URI
     * 当端口号为80时隐藏之
     * @param string|null $protocol 协议
     * @param string|null $hostname 主机名称
     * @param bool $full 是否取完整
     * @return string 返回URI的基础部分
     */
    public static function getBasicUrl($protocol=null,$hostname=null,$full=false){
        static $uri = [];
        $key = md5($protocol . '' . $hostname);
        if(!isset($uri[$key])){
            $uri[$key] = $full?
                (isset($protocol)?$protocol:$_SERVER['REQUEST_SCHEME']) .'://'. (isset($hostname)?$hostname:$_SERVER['SERVER_NAME']).
                    (80 == $_SERVER['SERVER_PORT']?'':':'.$_SERVER['SERVER_PORT']).$_SERVER['SCRIPT_NAME']:
                $_SERVER['SCRIPT_NAME'];
        }
        return $uri[$key];
    }

    /**
     * 判断是否是重定向链接
     * 判断依据：
     *  ①以http或者https开头
     *  ②以'/'开头的字符串
     * @param string $link 链接地址
     * @return bool
     */
    public static function isRedirectLink($link){
        $link = trim($link);
        return (0 === strpos($link, 'http')) or (0 === strpos($link,'/')) or (0 === strpos($link, 'https'));
    }

    /**
     * 重定向
     * @param string $url 重定向地址
     * @param int $time
     * @param string $message
     * @return void
     */
    public static function redirect($url,$time=0,$message=''){
        //多行URL地址支持
        $url = str_replace(['\n','\r'], '', $url);
        $message or $message = "系统将在{$time}秒之后自动跳转到{$url}！";

        if(headers_sent()){//检查头部是否已经发送
            exit("<meta http-equiv='Refresh' content='{$time};URL={$url}'>{$message}");
        }else{
            if(0 === $time){
                header('Location: ' . $url);
            }else{
                header("refresh:{$time};url={$url}");
                exit($message);
            }
        }
    }

    /**
     * $url规则如：
     *  .../Ma/Mb/Cc/Ad
     * 依次从后往前解析出操作，控制器，模块(如果存在模块将被认定为完整的模块路径)
     * @param string $url 快速创建的URL字符串
     * @param array $params GET参数数组
     * @return string
     */
    public static function url($url=null,array $params=[]){
        //解析参数中的$url
        empty($params) and $params = [];
        if(!$url){
            return self::getInstance()->create(null,null,null,$params);
        }
        $parts = @explode('/',trim($url,'/'));

//        \Soya\dumpout($parts);
        //调用URLHelper创建URL
        $action  = array_pop($parts);
        $ctler   = $action?array_pop($parts):null;
        $modules = $ctler?$parts:null;
        $url = self::getInstance()->create($modules,$ctler,$action,$params);
//        \Soya\dumpout($modules,$ctler,$action,$url);
        return $url;
    }

}