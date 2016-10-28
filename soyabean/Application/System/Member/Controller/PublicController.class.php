<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/29
 * Time: 21:33
 */
namespace Application\System\Member\Controller;
use Application\System\Common\Library\CommonController;
use Application\System\Member\Common\Logic\LoginLogic;
use Soya\Extend\Response;

/**
 * Class PublicController 公共可以访问的控制器
 * @package Application\System\Member\Controller
 */
class PublicController extends CommonController {
    /**
     * @param $username
     * @param $password
     * @param bool $remember
     */
    public function login($username=null,$password=null,$remember=false){
        if(IS_METHOD_POST){
            $result = LoginLogic::getInstance()->login($username,$password,$remember);
            if(is_string($result)){
                $this->redirect('/System/Member/Public/login#'.urlencode($result));
            }
            $this->redirect('/Admin/Index/index');
            exit();
        }
        $this->display();
    }

    /**
     * 注销登录
     */
    public function logout(){
        LoginLogic::getInstance()->logout();
        $this->redirect('/System/Member/Public/login');
    }



}