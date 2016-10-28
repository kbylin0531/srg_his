<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/29
 * Time: 21:10
 */

namespace Application\System\Common\Library;
use Application\System\Config\Model\MenuModel;
use Application\System\Member\Common\Logic\LoginLogic;
use Soya\Core\Configger;
use Soya\Core\Dispatcher;
use Soya\Util\SEK;

/**
 * Class AdminController
 * @package Application\System\Common\Library
 */
abstract class AdminController extends CommonController{
    /**
     * AdminController constructor.
     */
    public function __construct(){
        parent::__construct(null);
        define('REQUEST_PATH','/'.REQUEST_MODULE.'/'.REQUEST_CONTROLLER.'/'.REQUEST_ACTION);
        if(!LoginLogic::getInstance()->isLogin()){
            $this->go('/System/Member/Public/login');
        }
    }

    /**
     * @param string|null $template 如果是null,将自动获取调用本方法的名称并去掉开头的Page前缀
     * @param null $cache_id
     * @param null $compile_id
     * @param null $parent
     */
    protected function show($template = null, $cache_id = null, $compile_id = null, $parent = null){
        //加载模块和菜单
        $this->assign('infos',json_encode([
            'cdn'   => $this->getCDN(),//加载CDN
            'page'  => $this->getPageInfo(),
            'user'  => $this->getUserInfo(),
        ]));

        //获取调用自己的函数
        null === $template and $template = SEK::backtrace(SEK::ELEMENT_FUNCTION,SEK::PLACE_FORWARD);
        $this->display($template /* substr($template,4) 第五个字符开始 */, $cache_id , $compile_id, $parent);
    }

    /**
     * 加载CDN方案
     * @return array
     */
    private function getCDN(){
        $solution = Dispatcher::load('cdn',Configger::TYPE_PHP);
//        \Soya\dumpout($solution);
        return $solution['solution_list'][$solution['active_index']];
    }

    /**
     * @return array|null
     */
    protected function getUserInfo(){
        $usrinfo = LoginLogic::getInstance()->getLoginInfo(false);
        if(null === $usrinfo){
            $this->redirect('/System/Member/Public/login#'.urlencode('无法从会话中读取登录信息，请登录！'));//跳转到登录界面
        }
        return $usrinfo;
    }
    /**
     * 分配管理员页面信息
     */
    protected function assignAdminPage(){
        $this->assignUserInfoList();
        $this->assignModulesList();
        $this->assignActionsList();
    }

    /**
     * 加载页面参数
     * @return array
     */
    private function getPageInfo(){
        $memuModel = new MenuModel();
        $pageinfo = [
            //head部分
            'title' => 'KbylinFramework',
            'coptright' => ' 2014 © YZ',
            //body部分
            'logo'  => 'Soya',
            'request_path'   => REQUEST_PATH,//for finding his parent ???
            'header_menu'   => $memuModel->getHeaderMenu(),
            'sidebar_menu'  => $memuModel->getSidebarMenu(),
            'user_menu'     => [
                [
                    'title' => '解除登录',
                    'href'  => '/System/Member/Public/logout',
                ]
            ],
        ];
//        \Soya\dumpout($pageinfo['sidebar_menu']);
        return $pageinfo;
    }

    /**
     * 分配用户信息列表
     */
    protected function assignUserInfoList(){}

    /**
     *  分配模块信息列表
     */
    protected function assignModulesList(){}

    /**
     * 分配模块下的操作信息列表
     */
    protected function assignActionsList(){}



}