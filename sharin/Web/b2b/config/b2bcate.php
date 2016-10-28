<?php

define('SCRIPT_URL',    $_SERVER['SCRIPT_NAME']);
define('BASE_URL',      dirname($_SERVER['SCRIPT_NAME']));
define('PATH_BASE',     dirname(__DIR__).'/');
define('PATH_PUBLIC',   dirname($_SERVER['SCRIPT_FILENAME']).'/');//脚本所在目录即公共目录
//数据目录
define('PATH_DATA',     PATH_BASE.'data/');//关键数据
define('PATH_RUNTIME',  PATH_BASE.'runtime/');//运行时临时数据
define('PATH_COOKIE',   PATH_RUNTIME.'cookie/');
define('PATH_DOWNLOAD', PATH_RUNTIME.'download/');

define('IS_HTTPS',   isset ($_SERVER ['HTTPS']) and $_SERVER ['HTTPS'] === 'on');
define('HTTP_PREFIX',IS_HTTPS ? 'https://' : 'http://' );
$script_dir = rtrim(dirname($_SERVER['SCRIPT_NAME']),'/');
define('PUBLIC_URL',HTTP_PREFIX.$_SERVER['SERVER_NAME'].((80 == $_SERVER['SERVER_PORT'])?
        $script_dir :
        ":{$_SERVER['SERVER_PORT']}{$script_dir}"));
define('NOW',$_SERVER['REQUEST_TIME']);

const CATE_CACHE = true;//获取分类时是否使用缓存
const SIMILAR_SCALA = 70;//行业名称相似度 70%
const CATE_DATA_SAVER = LocalMappingSaver::class;
/**
 * 分类配置
 * [
 *  0   => '实现类的名称',
 *  1   => '平台地址',
 *  2   => '存在的问题描述，文本被设置时或者值为true时将禁止一部分的缓存行为，不包括分类缓存',
 * ]
 */
const CATE_CONF = [
    0   => [
        'BossgooCategory',
        'bossgoo.com',
    ],
    1   => [
        'DiyTradeCategory',
        'diytrade.com',
    ],
    2   => [
        'WtexpoCategory',
        'wtexpo.com'
    ],
    3   => [
        'WjwCategory',
        'wjw.com',
        ''
    ],
    4   => [
        'WeikuCategory',
        'weiku.com',
        ''
    ],
    5   => [
        'EcvvCategory','ecvv.com'
    ],
    6   => [
        'WdtradeCategory','wdtrade.com'
    ],
//    7   => [
//        'TradettCategory','tradett.com','登录无法实现',
//    ],
    8   => [
        'TtnetCategory','ttnet.net'
    ],
    9   => [
        'Ec21Category','ec21.com',
    ],
//    10  => [
//        'TraderscityCategory','tradeprince.com','需要登陆，待...'
//    ],
    11  => [
        'EnChinaCategory','en.china.cn'
    ],
    12  => [
        'AllProductsCategory','allproducts.com'
    ],
    13  => [
        'AsianPdCategory','asianproducts.com'
    ],
];

//spl_autoload_register(function ($clsnm){
//    static $_map = [];
//    if(false !== strpos(ltrim($clsnm,'\\'),'Library\\')){
//        //类名称以Library开头的都认为可能属于该类库
//        $path = PUBE_BASE_DIR.str_replace('\\', '/', $clsnm).'.class.php';
//        if(is_readable($path)) include_once $_map[$clsnm] = $path;
//    }
//},true,true);