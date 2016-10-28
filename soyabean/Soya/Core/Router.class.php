<?php
/**
 * Created by linzhv@outlook.com
 * User: asus
 * Date: 16-6-14
 * Time: 10:53
 */

namespace Soya\Core;
use Soya\Util\SEK;

/**
 * Class Router
 * TODO:只管分配，不管创建
 * @package Soya\Core
 */
class Router extends \Soya{

    const CONF_NAME = 'route';
    const CONF_CONVENTION = [
        'STATIC_ROUTE_ON'   => true,
        //静态路由规则
        'STATIC_ROUTE_RULES' => [],
        'WILDCARD_ROUTE_ON' => false,
        //通配符路由规则,具体参考CodeIgniter
        'WILDCARD_ROUTE_RULES' => [],
        'REGULAR_ROUTE_ON'  => 'false',
        //正则表达式 规则
        'REGULAR_ROUTE_RULES' => [],
    ];
    /**
     * 类配置
     * @var array
     */
    private $config = [];

    /**
     * Router constructor.
     * @param false|null|string $identify
     */
    public function __construct($identify){
        parent::__construct($identify);
        $this->config = self::getConfig();
//        \Soya\dumpout($this->config);
    }

    /**
     * 解析路由规则
     * @param string|null $pathinfo 请求路径
     * @return array|null|string
     */
    public function parse($pathinfo=null){
        $pathinfo or $pathinfo = SEK::getPathInfo(true);

        //静态路由
        if($this->config['STATIC_ROUTE_ON'] and $this->config['STATIC_ROUTE_RULES']){
            if(isset($this->config['STATIC_ROUTE_RULES'][$pathinfo])){
                return $this->config['STATIC_ROUTE_RULES'][$pathinfo];
            }
        }
        //规则路由
        if($this->config['WILDCARD_ROUTE_ON'] and $this->config['WILDCARD_ROUTE_RULES']){
            foreach($this->config['WILDCARD_ROUTE_RULES'] as $pattern => $rule){
                // Convert wildcards to RegEx（from CI）
                //any对应非/的任何字符 num对应数字
                $pattern = str_replace(array('[any]', '[num]'), array('([^/]+)', '([0-9]+)'), $pattern);
//                $pattern = preg_replace('/\[.+?\]/','([^/\[\]]+)',$pattern);//非贪婪匹配
                $rst = $this->_matchRegular($pattern,$rule, $pathinfo);
                if(null !== $rst) return $rst;
            }
        }
        //正则路由
        if($this->config['REGULAR_ROUTE_ON'] and $this->config['REGULAR_ROUTE_RULES']){
            foreach($this->config['REGULAR_ROUTE_RULES'] as $pattern => $rule){
                $rst = $this->_matchRegular($pattern,$rule, trim($pathinfo,' /'));
                if(null !== $rst) return $rst;
            }
        }
        return null;
    }


    /**
     * 解析直接路由规则
     * 直接路由规则将分为三类：
     *  ①静态直接路由地址
     *  ②匹配符路由规则
     *  ③正则式路由规则
     * 优先级从高到低级排列
     * @param string $uri uri地址
     * @return array|null|string 返回array表示完整的解析结果
     *                           返回string将交给Router继续代替原始地址进行进一步的解析
     *                           返回null表示未找到匹配项目
     */
    public function parseDirectRules($uri){
        $target = $this->parseStatic($uri);
        if(null === $target){
            $target = $this->parseWildcard($uri);
            if(null === $target){
                $target = $this->parseRegular($uri);
            }
        }
        return  (isset($target) and is_array($target))?[
            'm' => isset($target[0])?$target[0]:null,
            'c' => isset($target[1])?$target[1]:null,
            'a' => isset($target[2])?$target[2]:null,
            'p' => isset($target[3])?$target[3]:null,
        ]:$target;
    }

    /**
     * 解析静太路由规则,解析时忽略大小写
     * 返回非null值时表示
     * @param string $url url地址
     * @return array|string|null
     */
    protected function parseStatic($url){
        if(isset($this->config['STATIC_ROUTE_RULES'])){
            foreach($this->config['STATIC_ROUTE_RULES'] as $rule => $target){
                if(0 === strcasecmp($url,trim($rule))){
                    if(is_callable($target)){
                        $target = call_user_func_array($target,[$rule]);
                    }
                    if(is_string($target) or is_array($target)){
                        return $target;
                    }else{
                        //匹配了但是规则不符合，直接报错退出
                        Exception::throwing('Unexpect parameter!'.var_export($target,true));
                    }
                }
            }
        }
        return null;
    }


    /**
     * 解析通配符路由规则
     * 实际上通过正则表达式简介实现
     * @param string $uri 待匹配的URI地址
     * @return array|string|null
     */
    protected function parseWildcard($uri){
        if(isset($this->config['WILDCARD_ROUTE_RULES'])){
            foreach($this->config['WILDCARD_ROUTE_RULES'] as $rule => $target){
                $rule = preg_replace('/\[.+?\]/','([^/\[\]]+)',$rule);//非贪婪匹配
                $rst = $this->_matchRegular($rule,$target, trim($uri,' /'));
                if(isset($rst)){
                    return $rst;
                }
            }
        }
        return null;
    }

    /**
     * 解析通配符正则表达式路由规则
     * @param string $uri 待匹配的URI地址
     * @return mixed|null
     */
    protected function parseRegular($uri){
        if(isset($this->config['REGULAR_ROUTE_RULES'])){
            foreach($this->config['REGULAR_ROUTE_RULES'] as $rule => $target){
                $rst = $this->_matchRegular($rule,$target, trim($uri,' /'));
                if(isset($rst)){
                    return $rst;
                }
            }
        }
        return null;
    }


    /**
     * 使用正则表达式匹配uri
     * @param string $pattern 路由规则
     * @param array|string|callable $rule 路由导向结果
     * @param string $uri 传递进来的URL字符串
     * @return array|string|null
     */
    private function _matchRegular($pattern, $rule, $uri){
        $result = null;
        // Does the RegEx match? use '#' to ignore '/'
//        \Soya\dumpout($pattern, $rule, $uri,preg_match('#^'.$pattern.'$#', $uri, $matches));
        if (preg_match('#^'.$pattern.'$#', $uri, $matches)) {
            if(is_array($rule)){
                $len = count($matches);
                for($i = 1; $i < $len; $i++){
                    $key = '$'.$i;
                    if(isset($rule['$'.$i])){
                        $v = (string)$rule[$key];
                        if(strpos($v,'.')){
                            $a = explode('.',$v);
                            empty($rule[$a[0]]) and $rule[$a[0]] = [];
                            $rule[$a[0]][$a[1]] = $matches[$i];
                        }else{
                            $rule[$v] = $matches[$i];
                        }
                    }else{
                        empty($rule['o']) and $rule['o'] = [];
                        $rule['o'][] = $matches[$i];
                    }
                    unset($rule[$key]);
                }
                $result = $rule;
//                \Soya\dumpout($matches,$rule);
//                if(isset($rule[3])){
//                    $index = 1;//忽略第一个匹配（全匹配）
//                    foreach($rule[3] as $pname=>&$pval){
//                        if(isset($matches[$index])){
//                            $pval = $matches[$index];
//                        }
//                        ++$index;
//                    }
//                    $_GET = array_merge($rule[3],$_GET);// 优先使用$_GET覆盖
//                }else{
//                    //未设置参数项，不作动作
//                }
            }elseif(is_string($rule)){
                $result = preg_replace('#^'.$pattern.'$#', $rule, $uri);//参数一代表的正则表达式从参数三的字符串中寻找匹配并替换到参数二代表的字符串中
            }elseif(is_callable($rule)){
                array_shift($matches);
                // Execute the callback using the values in matches as its parameters.
                $result = call_user_func_array($rule, $matches);//参数二是完整的匹配
                if($result === true){
                    //返回true表示直接完成
                    exit();
                }elseif(!is_string($rule) and !is_array($rule)){
                    //要求结果必须返回string或者数组
                    return null;
                }
            }
        }
        return $result;
    }

}