<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/7/16
 * Time: 11:51 AM
 */

namespace Application\System\Common\Library;
use Application\System\Member\Common\Logic\LoginLogic;
use Soya\Extend\View;
use Soya\Util\SEK;

class HomeController extends CommonController {

    /**
     * IndexController constructor.
     * @param null $identify
     */
    public function __construct($identify=null){
        define('REQUEST_PATH','/'.REQUEST_MODULE.'/'.REQUEST_CONTROLLER.'/'.REQUEST_ACTION);


    }

    protected function __checkLogin(){
        if(!LoginLogic::getInstance()->isLogin()){
            $this->go('/Home/User/login');
        }
    }

    public function index(){
        echo 'Hello Soya!';
    }

    /**
     * @param $str
     * @param $replacement
     */
    protected function registerParsingString($str,$replacement=null){
        static $view = null;
        if(!$view){
            $view = View::getInstance();
        }
        $view->registerParsingString($str,$replacement);
    }

    protected function show($template=null){
        $this->registerParsingString([
            'WEB_SITE_KEYWORD'  => 'weiphp',
            'WEB_SITE_DESCRIPTION'  => '',
            'WEB_SITE_TITLE'    => 'WeiPHP3',
            //路径
            '__CSS__'       => __PUBLIC__.'/assets/app/home/css',
            '__JS__'        => __PUBLIC__.'/assets/app/home/js',
            '__IMG__'       => __PUBLIC__.'/assets/app/home/images',
            '__STATIC__'    => __PUBLIC__.'/assets/app/static',
        ]);
        //获取调用自己的函数
        null === $template and $template = SEK::backtrace(SEK::ELEMENT_FUNCTION,SEK::PLACE_FORWARD);
        $this->display($template /* substr($template,4) 第五个字符开始 */);
    }

}